<?php

namespace BlueSpice\Social\Topics\Renderer\EntityList;

use BlueSpice\Services;
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
		if( !$this->getContext()->getTitle()->getTalkPage()->exists() ) {
			$this->args[static::PARAM_CLASS] .= ' nodiscussion';
		} else {
			$this->args[static::PARAM_CLASS] .= ' nodiscussionpage';
		}
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

		if( !$this->getContext()->getTitle()->getTalkPage()->exists() ) {
			$content .= $this->renderNewDiscussionPage();
		} else {
			$content .= $this->renderNewDiscussion();
		}

		$content .= \Html::closeElement( 'li' );
		return $content;
	}

	protected function renderNewDiscussionPage() {
		$out = '';
		$msg = \Message::newFromKey(
			'bs-socialtopics-entitylist-nodiscussionpage'
		);
		$title = $this->getContext()->getTitle()->getTalkPage();
		if( !$title->userCan( 'create', $this->getUser() ) ) {
			$out .= new \OOUI\LabelWidget( [
				'label' => $msg->pLain(),
			] );
			return $out;
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
		$out .= $label;
		$out .= $btn;
		return $out;
	}

	protected function renderNewDiscussion() {
		$out = '';
		$msg = \Message::newFromKey( 'bs-socialtopics-nodiscussion' );
		$title = $this->getContext()->getTitle()->getTalkPage();
		$factory = Services::getInstance()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle( $title );
		if( !$entity->userCan( 'create', $this->getUser() ) ) {
			$out .= new \OOUI\LabelWidget( [
				'label' => $msg->pLain(),
			] );
			return $out;
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
			'bs-socialtopics-discussion-create'
		] );
		$label = new \OOUI\LabelWidget( [
			'label' => $msg->plain(),
			'input' => $btn
		] );
		$out .= $label;
		$out .= $btn;
		return $out;
	}
}
