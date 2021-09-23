<?php

namespace BlueSpice\Social\Topics\Component;

use BlueSpice\Context;
use BlueSpice\IRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Entity;
use BlueSpice\Social\Topics\EntityListContext\AfterContent as EntityListContext;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use RequestContext;

class AfterContent extends Literal {

	/**
	 *
	 * @var Entity
	 */
	private $entity = null;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct(
			'social-topics',
			''
		);
	}

	/**
	 * Raw HTML string
	 *
	 * @return string
	 */
	public function getHtml() : string {
		$html = '';
		$renderer = $this->getTimeLineRenderer();
		if ( $renderer instanceof IRenderer ) {
			$html = $renderer->render();
		}
		return $html;
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( $context ) : bool {
		if ( !$context->getTitle()->exists() ) {
			return false;
		}

		if ( !$this->getConfig()->get( 'SocialTopicsTimelineAfterContentShow' ) ) {
			return false;
		}

		$namespace = $context->getTitle()->getNamespace();
		$nsBlackList = $this->getConfig()->get(
			'SocialTopicsTimelineAfterContentNamespaceBlackList'
		);
		if ( in_array( $namespace, $nsBlackList ) ) {
			return false;
		}

		if ( $context->getTitle()->isTalkPage() ) {
			return false;
		}

		$action = $context->getRequest()->getVal( 'action', 'view' );
		if ( $action != 'view' && $action != 'submit' ) {
			return false;
		}

		$prop = $this->getServices()->getService( 'BSUtilityFactory' )
			->getPagePropHelper( $context->getTitle() )
			->getPageProp( 'bs_nodiscussion' );
		if ( $prop !== null ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 * @return EntityListContext
	 */
	private function getContext() {
		$context = RequestContext::getMain();

		return new EntityListContext(
			new Context(
				$context,
				$this->getConfig()
			),
			$this->getConfig(),
			$context->getUser(),
			$this->getEntity(),
			$context->getTitle()
		);
	}

	/**
	 *
	 * @return Entity|null
	 */
	private function getEntity() {
		if ( $this->entity ) {
			return $this->entity;
		}
		$factory = $this->getServices()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$this->entity = $factory->newFromDiscussionTitle(
			RequestContext::getMain()->getTitle()->getTalkPageIfDefined()
		);
		return $this->entity;
	}

	/**
	 *
	 * @return IRenderer
	 */
	private function getTimeLineRenderer() {
		return $this->getServices()->getService( 'BSRendererFactory' )->get(
			$this->getContext()->getRendererName(),
			new Params( [ 'context' => $this->getContext() ] )
		);
	}

	/**
	 *
	 * @return MediaWikiServices
	 */
	private function getServices() {
		return MediaWikiServices::getInstance();
	}

	/**
	 *
	 * @return \Config
	 */
	private function getConfig() {
		return $this->getServices()->getConfigFactory()->makeConfig( 'bsg' );
	}
}
