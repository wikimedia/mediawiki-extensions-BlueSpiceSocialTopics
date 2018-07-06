<?php

namespace BlueSpice\Social\Topics\Special;

use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Topics\EntityListContext\SpecialTopics;

class Topics extends \BsSpecialPage {

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
		$renderer = Services::getInstance()->getBSRendererFactory()->get(
			'entitylist',
			new Params( [ 'context' => $context ] )
		);

		$this->getOutput()->addHTML( $renderer->render() );
	}
}