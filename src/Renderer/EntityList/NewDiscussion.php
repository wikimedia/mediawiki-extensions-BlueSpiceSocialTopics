<?php

namespace BlueSpice\Social\Topics\Renderer\EntityList;

use BlueSpice\Renderer\Params;
use Config;
use Html;
use IContextSource;
use MediaWiki\Linker\LinkRenderer;
use Title;

class NewDiscussion extends \BlueSpice\Social\Renderer\EntityList {

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '' ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$talkPageTarget = $this->services->getNamespaceInfo()
			->getTalkPage( $this->getContext()->getTitle() );
		$talkPage = Title::newFromLinkTarget( $talkPageTarget );
		if ( !$talkPage->exists() ) {
			$this->args[static::PARAM_CLASS] .= ' nodiscussion';
		} else {
			$this->args[static::PARAM_CLASS] .= ' nodiscussionpage';
		}
	}

	protected function initializeArgs() {
		$talkPage = $this->services->getNamespaceInfo()
			->getTalkPage( $this->getContext()->getTitle() );
		$userCanEdit = $this->services->getPermissionManager()
			->userCan( 'edit', $this->getContext()->getUser(), $talkPage );
		$this->args[ "usercanedit" ] = $userCanEdit;
		parent::initializeArgs();
	}

	/**
	 * Returns a rendered template as HTML markup
	 * @return string - HTML
	 */
	public function render() {
		$content = '';
		if ( $this->args[ static::PARAM_SHOW_HEADLINE ] ) {
			$content .= $this->renderEntityListHeadline();
		}
		$content .= $this->getOpenTag();
		$content .= $this->makeTagContent();
		$content .= $this->getCloseTag();
		if ( $this->args[ static::PARAM_SHOW_ENTITY_LIST_MORE ] ) {
			$content .= $this->renderEntityListMore();
		}
		return $content;
	}

	protected function makeTagContent() {
		$content = '';
		$content .= Html::openElement( 'li' );

		$talkPageTarget = $this->services->getNamespaceInfo()
			->getTalkPage( $this->getContext()->getTitle() );
		$talkPage = Title::newFromLinkTarget( $talkPageTarget );
		if ( !$talkPage->exists() ) {
			$content .= $this->renderNewDiscussionPage();
		} else {
			$content .= $this->renderNewDiscussion();
		}

		$content .= Html::closeElement( 'li' );
		return $content;
	}

	protected function renderNewDiscussionPage() {
		$renderer = $this->services->getService( 'BSRendererFactory' )->get(
			'social-topics-createnewdiscussionpage',
			new Params( [ 'context' => $this->getContext() ] )
		);
		return $renderer->render();
	}

	protected function renderNewDiscussion() {
		$renderer = $this->services->getService( 'BSRendererFactory' )->get(
			'social-topics-createnewdiscussion',
			new Params( [ 'context' => $this->getContext() ] )
		);
		return $renderer->render();
	}

}
