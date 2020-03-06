<?php

namespace BlueSpice\Social\Topics\Renderer;

use IContextSource;
use Config;
use OutputPage;
use BlueSpice\Services;
use MediaWiki\Linker\LinkRenderer;
use BlueSpice\Renderer\Params;
use BlueSpice\EntityFactory;
use BlueSpice\Social\Topics\Entity\Topic;

class CreateFirstTopicAfterContent extends \BlueSpice\Renderer {

	/**
	 *
	 * @var EntityFactory
	 */
	protected $factory = null;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Params $params
	 * @param LinkRenderer|null $linkRenderer
	 * @param IContextSource|null $context
	 * @param string $name | ''
	 * @param EntityFactory|null $factory
	 */
	protected function __construct( Config $config, Params $params,
		LinkRenderer $linkRenderer = null, IContextSource $context = null,
		$name = '', EntityFactory $factory = null ) {
		parent::__construct( $config, $params, $linkRenderer, $context, $name );

		$this->factory = $factory;
	}

	/**
	 *
	 * @param string $name
	 * @param Services $services
	 * @param Config $config
	 * @param Params $params
	 * @param IContextSource|null $context
	 * @param LinkRenderer|null $linkRenderer
	 * @param EntityFactory|null $factory
	 * @return Renderer
	 */
	public static function factory( $name, Services $services, Config $config, Params $params,
		IContextSource $context = null, LinkRenderer $linkRenderer = null,
		EntityFactory $factory = null ) {
		if ( !$context ) {
			$context = $params->get(
				static::PARAM_CONTEXT,
				false
			);
			if ( !$context instanceof IContextSource ) {
				$context = \RequestContext::getMain();
			}
		}
		if ( !$linkRenderer ) {
			$linkRenderer = $services->getLinkRenderer();
		}
		if ( !$factory ) {
			$factory = $services->getService( 'BSEntityFactory' );
		}

		return new static( $config, $params, $linkRenderer, $context, $name, $factory );
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
		OutputPage::setupOOUI();

		$title = $this->getContext()->getTitle()->getTalkPage();
		$entity = $this->factory->newFromObject( (object)[
			Topic::ATTR_TYPE => Topic::TYPE,
			Topic::ATTR_DISCUSSION_TITLE_ID => $title->getArticleID(),
		] );
		if ( !$entity->userCan( 'create', $this->getContext()->getUser() )->isOK() ) {
			$msg = $this->msg( 'bs-socialtopics-notopicsadded' );
			$content .= new \OOUI\LabelWidget( [
				'label' => $msg->plain(),
			] );
			return $content;
		}

		$label = $this->msg(
			'bs-social-entitytopic-header-create'
		)->plain();
		$btn = new \OOUI\ButtonWidget( [
			'infusable' => false,
			'label' => $label,
			'title' => $label,
			'framed' => false,
			'icon' => 'add',
			'invisibleLabel' => true,
			'flags' => [
				'primary',
				'progressive'
			],
			'href' => $title->getLocalURL()
		] );
		$btn->addClasses( [
			'bs-socialtopics-topic-create-first'
		] );
		$btn->setValue(
			$this->getContext()->getTitle()->getArticleID()
		);
		$content .= $btn;
		return $content;
	}

}
