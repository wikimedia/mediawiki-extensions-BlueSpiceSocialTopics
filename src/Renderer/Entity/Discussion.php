<?php

namespace BlueSpice\Social\Topics\Renderer\Entity;

use IContextSource;
use Config;
use BlueSpice\Services;
use BlueSpice\Context;
use BlueSpice\Utility\CacheHelper;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Topics\EntityListContext\DiscussionPage;

class Discussion extends \BlueSpice\Social\Renderer\Entity\Page {

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 * @param CacheHelper $cacheHelper
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', CacheHelper $cacheHelper = null ) {
		parent::__construct(
			$config,
			$params,
			$linkRenderer,
			$context,
			$name,
			$cacheHelper
		);

		$this->args['basetitlecontent'] = '';
	}

	/**
	 *
	 * @param mixed $val
	 * @return string
	 */
	public function render_basetitlecontent( $val ) {
		if( $this->renderType !== static::RENDER_TYPE_PAGE ) {
			return '';
		}

		return $this->getEntity()->getBaseTitleContent();
	}

	/**
	 *
	 * @param string $renderType
	 * @param bool $noCache
	 * @return string
	 * @throws \MWException
	 */
	public function render( $renderType = 'Default', $noCache = false ) {
		if ( $renderType === static::RENDER_TYPE_PAGE && !$this->getEntity()->exists() ) {
			return $this->renderNewDiscussion();
		}
		return parent::render( $renderType, $noCache );
	}

	protected function renderNewDiscussion() {
		$renderer = Services::getInstance()->getBSRendererFactory()->get(
			'social-topics-createnewdiscussion',
			new Params( [ 'context' => $this->getContext() ] )
		);
		return $renderer->render();
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