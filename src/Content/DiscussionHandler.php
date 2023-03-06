<?php

namespace BlueSpice\Social\Topics\Content;

use Content;
use MediaWiki\Content\Renderer\ContentParseParams;
use MediaWiki\MediaWikiServices;
use ParserOutput;
use Title;

class DiscussionHandler extends \WikitextContentHandler {

	/**
	 *
	 * @param string $modelId
	 */
	public function __construct( $modelId = CONTENT_MODEL_BSSOCIALDISCUSSION ) {
		parent::__construct( $modelId );
	}

	/**
	 * @return string
	 */
	public function getContentClass() {
		return "\\BlueSpice\\Social\\Topics\\Content\\Discussion";
	}

		/**
		 * @param Content $content
		 * @param ContentParseParams $cpoParams
		 * @param ParserOutput &$output The output object to fill (reference).
		 * @return ParserOutput
		 */
	protected function fillParserOutput(
		Content $content,
		ContentParseParams $cpoParams,
		ParserOutput &$output
	) {
		$dbKey = $cpoParams->getPage()->getDBkey();
		$title = Title::newFromDBkey( $dbKey );
		if ( $output->getExtensionData( 'ForceOrigin' ) ) {
			return $output;
		}
		if ( !$title ) {
			return $output;
		}
		$factory = MediaWikiServices::getInstance()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle( $title );
		if ( !$entity ) {
			return $output;
		}

		$output->setTitleText(
			strip_tags( $entity->getHeader()->parse() )
		);
		$output->setText( $entity->getRenderer()->render( 'Page' ) );
		return $output;
	}
}
