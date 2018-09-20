<?php

namespace BlueSpice\Social\Topics\Renderer\EntityList;

use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;

class NewDiscussion extends \BlueSpice\Social\Renderer\EntityList {

	/**
	 *
	 * @param \Config $config
	 * @param Params $params
	 * @param LinkRenderer $linkRenderer
	 */
	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->args[static::PARAM_CLASS] .= ' nodiscussion';
	}

	/**
	 * Returns a rendered template as HTML markup
	 * @return string - HTML
	 */
	public function render() {
		$content = '';
		if( $this->args[ static::PARAM_SHOW_HEADLINE ] ) {
			$content .= $this->renderEntityListHeadline();
		}
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();
		if( $this->args[ static::PARAM_SHOW_ENTITY_LIST_MORE ] ) {
			$content .= $this->renderEntityListMore();
		}

		return $content;
	}

	protected function makeTagContent() {
		$content = '';
		$content .= \Html::openElement( 'li' );
		$title = $this->getContext()->getTitle()->getTalkPage();
		$msg = \Message::newFromKey(
			'bs-socialtopics-entitylist-nodiscussion'
		);

		if( !$title->userCan( 'create', $this->getUser() ) ) {
			$content .= new \OOUI\LabelWidget( [
				'label' => $msg->pLain(),
			] );
			$content .= \Html::closeElement( 'li' );
			return $content;
		}
		
		$btn = new \OOUI\ButtonWidget( [
			'infusable' => false,
			'label' => \Message::newFromKey(
				'bs-socialtopics-entitydiscussion-header-create'
			)->plain(),
			'href' => '#',
			'flags' => [
				'primary',
				'progressive'
			],
			'href' => $title->getLocalURL( [
				'action' => 'edit',
			] )
		] );
		$btn->addClasses( [
			'bs-socialtopics-discussionpage-create'
		] );
		$label = new \OOUI\LabelWidget( [
			'label' => $msg->plain(),
			'input' => $btn
		] );
		$content .= $label;
		$content .= $btn;
		$content .= \Html::closeElement( 'li' );
		return $content;
	}

}
