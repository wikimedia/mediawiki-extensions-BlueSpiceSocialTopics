<?php

namespace BlueSpice\Social\Topics\Renderer;

use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;

class CreateNewDiscussionPage extends \BlueSpice\Renderer {
	const PARAM_CONTEXT = 'context';

	/**
	 *
	 * @param \Config $config
	 * @param Params $params
	 * @param LinkRenderer $linkRenderer
	 */
	public function __construct( \Config $config, Params $params, LinkRenderer $linkRenderer = null ) {
		parent::__construct( $config, $params, $linkRenderer );
		$this->context = $params->get(
			static::PARAM_CONTEXT,
			false
		);
		if ( !$this->context instanceof \IContextSource ) {
			$this->context = \RequestContext::getMain();
		}
	}

	/**
	 * Returns a rendered template as HTML markup
	 * @return string - HTML
	 */
	public function render() {
		$content = '';
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();

		return $content;
	}

	protected function makeTagContent() {
		$content = '';
		\OutputPage::setupOOUI();
		$msg = \Message::newFromKey(
			'bs-socialtopics-nodiscussionpage'
		);
		$title = $this->getContext()->getTitle()->getTalkPage();
		if( !$title->userCan( 'create', $this->getContext()->getUser() ) ) {
			$content .= new \OOUI\LabelWidget( [
				'label' => $msg->pLain(),
			] );
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
		return $content;
	}

	/**
	 *
	 * @return \IContextSource
	 */
	public function getContext() {
		return $this->context;
	}

}
