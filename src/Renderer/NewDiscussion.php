<?php

namespace BlueSpice\Social\Topics\Renderer;

use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\EntityListContext;

class NewDiscussion extends \BlueSpice\Renderer {
	const PARAM_CONTEXT = 'context';
	const PARAM_USER = 'user';

	/**
	 *
	 * @var EntityListContext
	 */
	protected $context = null;

	/**
	 *
	 * @var User
	 */
	protected $user = null;

	/**
	 *
	 * @var Params
	 */
	protected $params = null;

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
		if( !$this->context instanceof EntityListContext ) {
			$this->context = new EntityListContext(
				\RequestContext::getMain(),
				$config,
				\RequestContext::getMain()->getUser(),
				null
			);
		}
		$this->user = $params->get(
			static::PARAM_USER,
			false
		);
		if( !$this->user instanceof \User ) {
			$this->user = $this->context->getUser();
		}
		$this->params = $params;
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

		return $content;
	}

	/**
	 *
	 * @return \User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 *
	 * @return EntityListContext
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * 
	 * @return array
	 */
	public function getArgs() {
		return $this->args;
	}

}
