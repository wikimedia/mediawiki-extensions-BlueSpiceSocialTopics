<?php

namespace BlueSpice\Social\Topics\Renderer\Entity;

use BlueSpice\Services;
use BlueSpice\Context;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Topics\EntityListContext\DiscussionPage;

class Discussion extends \BlueSpice\Social\Renderer\Entity\Page {

	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->args['basetitlecontent'] = '';
	}

	protected function render_userimage( $val ) {
		return \Html::element( 'div', [ 'class' => 'bs-social-icon-discussion bs-social-entity-icon' ] );
	}

	protected function render_children( $val ) {
		if( $this->renderType !== static::RENDER_TYPE_PAGE ) {
			return '';
		}

		if( !$this->getEntity()->exists() ) {
			return '';
		}

		$context = new DiscussionPage(
			new Context(
				$this->getContext(),
				$this->getEntity()->getConfig()
			),
			$this->getEntity()->getConfig(),
			$this->getContext()->getUser(),
			$this->getEntity()
		);
		$renderer = $this->getServices()->getBSRendererFactory()->get(
			'entitylist',
			new Params( [ 'context' => $context ])
		);
		return $renderer->render();
	}
}