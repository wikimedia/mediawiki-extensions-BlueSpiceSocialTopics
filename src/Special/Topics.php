<?php

namespace BlueSpice\Social\Topics\Special;

use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Renderer\Entity as Renderer;
use BlueSpice\Social\Topics\EntityListContext\SpecialTopics;
use BlueSpice\Social\Topics\Entity\Topic as TopicEntity;

class Topics extends \BlueSpice\SpecialPage {

	public function __construct() {
		parent::__construct( 'Topics', 'read' );
	}

	public function execute( $par ) {
		$this->checkPermissions();

		$this->getOutput()->setPageTitle(
			wfMessage( 'bs-socialtopics-special-topics-heading' )->plain()
		);

		$context = new SpecialTopics(
			new Context(
				$this->getContext(),
				$this->getConfig()
			),
			$this->getConfig(),
			$this->getContext()->getUser()
		);

		if( $entity = $this->extractEntity( $par ) ) {
			$this->getOutput()->addBacklinkSubtitle(
				$entity->getRelatedTitle()
			);
			$msg = $this->msg(
				'bs-socialtopics-special-topics-heading-entry'
			);
			$msg->params( strip_tags( $entity->getHeader()->parse() ) );
			$this->getOutput()->setPageTitle( $msg->text() );
			$this->getOutput()->addHTML(
				$entity->getRenderer( $context )->render(
					Renderer::RENDER_TYPE_PAGE
				)
			);
			return;
		}

		$renderer = Services::getInstance()->getBSRendererFactory()->get(
			'entitylist',
			new Params( [ 'context' => $context ] )
		);

		$this->getOutput()->addHTML( $renderer->render() );
	}

	protected function extractEntity( $param = '' ) {
		if( empty( $param ) ) {
			return false;
		}
		$title = \Title::makeTitle( NS_SOCIALENTITY, $param );
		if( !$title || !$title->exists() ) {
			return false;
		}		
		$factory =  Services::getInstance()->getService(
			'BSSocialWikiPageEntityFactory'
		);
		$entity = $factory->newFromSourceTitle( $title );
		if( !$entity instanceof TopicEntity || !$entity->exists() ) {
			return false;
		}
		return $entity;
	}
}
