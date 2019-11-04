<?php

$IP = dirname( dirname( dirname( __DIR__ ) ) );

require_once "$IP/maintenance/Maintenance.php";

use BlueSpice\Services;
use BlueSpice\Context;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\Sort;
use BlueSpice\Data\FieldType;
use BlueSpice\Data\Filter\StringValue;
use BlueSpice\Social\Data\Entity\Store;
use BlueSpice\Social\Topics\Entity\Topic;

class BSExportTopicsToDiscussionPagesXML extends Maintenance {

	/**
	 *
	 * @var Config
	 */
	private $config = null;

	/**
	 *
	 * @var XmlDumpWriter
	 */
	private $writer = null;

	/**
	 *
	 * @var resource
	 */
	private $handle = null;

	public function __construct() {
		parent::__construct();
		$this->addDescription(
			'Converts BlueSpice Social topics to standard discussion pages and exports them to an XML file.'
		);

		$this->addArg( 'file', 'A file to write the XML to (see docs/sitelist.txt). ' .
			'Use "php://stdout" to write to stdout.', true
		);

		$this->requireExtension( "BlueSpiceFoundation" );
		$this->requireExtension( "BlueSpiceSocial" );
		$this->requireExtension( "BlueSpiceSocialTopics" );
	}

	/**
	 *
	 */
	public function execute() {
		$file = $this->getArg( 0 );
		$this->writer = new XmlDumpWriter();
		$this->handle = fopen( $file, 'w' );

		if ( !$this->handle ) {
			$this->fatalError( "Failed to open $file for writing.\n" );
		}
		$this->output( "Collecting topics..." );

		$res = $this->getStore()->getReader( $this->getContext() )->read(
			new ReaderParams( $this->getParams() )
		);
		$this->output( "OK\n" );

		$count = count( $res->getRecords() );
		$this->output( "$count entries found..." );

		$discussions = [];
		foreach ( $res->getRecords() as $record ) {
			$entity = Services::getInstance()->getBSEntityFactory()
				->newFromObject( $record->getData() );
			if ( !$entity instanceof Topic ) {
				continue;
			}
			if ( !$entity->exists() || $entity->isArchived() ) {
				continue;
			}
			if ( !isset( $discussions[$entity->get( Topic::ATTR_DISCUSSION_TITLE_ID )] ) ) {
				$discussions[$entity->get( Topic::ATTR_DISCUSSION_TITLE_ID )] = [];
			}
			$discussions[$entity->get( Topic::ATTR_DISCUSSION_TITLE_ID )][] = $entity;
		}
		$count = count( $discussions );
		$this->output( "in $count discussion pages\n" );

		fwrite( $this->handle, $this->writer->openStream() . "\n" );

		$queryInfo = $this->getServices()->getRevisionStore()->getQueryInfo();
		foreach ( $discussions as $articleID => $entities ) {
			$title = Title::newFromID( $articleID );
			if ( !$title ) {
				$this->output( "\n* invalid title for id $articleID ...ERROR\n" );
				continue;
			}
			$row = $this->getDB( DB_REPLICA )->selectRow(
				'page',
				'*',
				[ 'page_id' => $title->getArticleID() ],
				__METHOD__
			);
			$this->output( "\n* {$title->getFullText()}" );
			fwrite( $this->handle, $this->writer->openPage( $row ) . "\n" );

			$res = $this->getDB( DB_REPLICA )->select(
				$queryInfo['tables'],
				$queryInfo['fields'],
				[ 'rev_page' => $title->getArticleID() ],
				__METHOD__,
				[],
				$queryInfo['joins']
			);
			foreach ( $res as $row ) {
				fwrite( $this->handle, $this->writer->writeRevision( $row ) . "\n" );
			}
			$lastRev = Revision::newFromRow( $row );
			$lastRevTS = DateTime::createFromFormat(
				'YmdHis',
				$lastRev->getTimestamp(),
				new DateTimeZone( 'UTC' )
			);
			$text = BsPageContentProvider::getInstance()->getWikiTextContentFor( $title );
			$secondsCount = 0;
			foreach ( $entities as $entity ) {
				$secondsCount++;
				$this->output( "\n** {$entity->get( Topic::ATTR_TOPIC_TITLE )}" );
				$entityTS = DateTime::createFromFormat(
					'YmdHis',
					$entity->get( Topic::ATTR_TIMESTAMP_CREATED ),
					new DateTimeZone( 'UTC' )
				);
				if ( $lastRevTS > $entityTS ) {
					$entityTS = $lastRevTS;
					$entityTS->modify( "+$secondsCount seconds" );
				}

				fwrite( $this->handle, $this->writeRevision( $entity, $title, $text, $entityTS ) . "\n" );
			}
			fwrite( $this->handle, $this->writer->closePage() . "\n" );
			$this->output( "\n" );
		}

		fwrite( $this->handle, $this->writer->closeStream() . "\n" );

		fclose( $this->handle );

		$this->output( "\n\nDONE, GG" );
	}

	/**
	 *
	 * @param Topic $entity
	 * @param Title $title
	 * @param string &$text
	 * @param DateTime $date
	 * @return string
	 */
	protected function writeRevision( Topic $entity, Title $title, &$text, DateTime $date ) {
		$out = "    <revision>\n";

		$out .= $this->writer->writeTimestamp(
			$date->format( 'YmdHis' )
		);

		$out .= "        " . Xml::element( 'model', [], 'wikitext' ) . "\n";
		$out .= "        " . Xml::element( 'format', [], 'text/x-wiki' ) . "\n";
		$user = $entity->getOwner();
		$out .= $this->writer->writeContributor(
			$user ? $user->getId() : 0,
			$user ? $user->getName() : ''
		);
		$text = $text . "\n" . $this->generateWikiText( $entity );
		$out .= "        " . Xml::elementClean(
			'text',
			[ 'bytes' => strlen( $text ) ],
			strval( $text )
		) . "\n";

		$out .= "    </revision>\n";

		return $out;
	}

	/**
	 *
	 * @param Topic $entity
	 * @return string
	 */
	protected function generateWikiText( Topic $entity ) {
		$username = $entity->getOwner()->getName();
		$date = DateTime::createFromFormat(
			'YmdHis',
			$entity->get( Topic::ATTR_TIMESTAMP_CREATED ),
			new DateTimeZone( 'UTC' )
		);
		$ts = $date->format( 'd.m.Y H:i:s' );
		return <<<EOT
=={$entity->get( Topic::ATTR_TOPIC_TITLE )}==

{$entity->get( Topic::ATTR_TEXT )}

[[User:$username]] $ts (UTC)
EOT;
	}

	/**
	 *
	 * @return array
	 */
	protected function getParams() {
		return [
			ReaderParams::PARAM_LIMIT => ReaderParams::LIMIT_INFINITE,
			ReaderParams::PARAM_FILTER => $this->getFilter(),
			ReaderParams::PARAM_SORT => $this->getSort(),
		];
	}

	/**
	 *
	 * @return int
	 */
	protected function getSort() {
		return [ (object)[
			Sort::KEY_PROPERTY => Topic::ATTR_TIMESTAMP_CREATED,
			Sort::KEY_DIRECTION => Sort::ASCENDING
		] ];
	}

	/**
	 *
	 * @return array
	 */
	protected function getFilter() {
		return [ (object)[
			StringValue::KEY_PROPERTY => Topic::ATTR_TYPE,
			StringValue::KEY_TYPE => FieldType::STRING,
			StringValue::KEY_COMPARISON => StringValue::COMPARISON_EQUALS,
			StringValue::KEY_VALUE => Topic::TYPE,
		] ];
	}

	/**
	 *
	 * @return Store
	 */
	protected function getStore() {
		return new Store;
	}

	/**
	 *
	 * @return IContextSource
	 */
	protected function getContext() {
		$context = RequestContext::getMain();
		$context->setUser( $this->getUser() );
		return new Context( $context, $this->getConfig() );
	}

	/**
	 *
	 * @return User
	 */
	protected function getUser() {
		return $this->getServices()->getBSUtilityFactory()->getMaintenanceUser()
			->getUser();
	}

	/**
	 *
	 * @return Services
	 */
	protected function getServices() {
		return Services::getInstance();
	}

	/**
	 *
	 * @return Config
	 */
	public function getConfig() {
		if ( $this->config !== null ) {
			return $this->config;
		}
		$this->config = $this->getServices()->getBSEntityConfigFactory()->newFromType(
			Topic::TYPE
		);

		return $this->config;
	}
}

$maintClass = 'BSExportTopicsToDiscussionPagesXML';
require_once RUN_MAINTENANCE_IF_MAIN;
