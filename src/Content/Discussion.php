<?php
namespace BlueSpice\Social\Topics\Content;

use BlueSpice\Services;

class Discussion extends \WikitextContent {

	public $mModelID = CONTENT_MODEL_WIKITEXT;
	public function getModel() {
		return CONTENT_MODEL_WIKITEXT;
	}

	public function __construct( $text, $modelId = CONTENT_MODEL_BSSOCIALDISCUSSION ) {
		parent::__construct( $text, CONTENT_MODEL_WIKITEXT );
	}

	/**
	 * Returns a ParserOutput object containing information derived from this content.
	 * Most importantly, unless $generateHtml was false, the return value contains an
	 * HTML representation of the content.
	 *
	 * Subclasses that want to control the parser output may override this, but it is
	 * preferred to override fillParserOutput() instead.
	 *
	 * Subclasses that override getParserOutput() itself should take care to call the
	 * ContentGetParserOutput hook.
	 *
	 * @since 1.24
	 *
	 * @param \Title $title Context title for parsing
	 * @param int|null $revId Revision ID (for {{REVISIONID}})
	 * @param \ParserOptions|null $options Parser options
	 * @param bool $generateHtml Whether or not to generate HTML
	 *
	 * @return ParserOutput Containing information derived from this content.
	 */
	public function getParserOutput( \Title $title, $revId = null, \ParserOptions $options = null, $generateHtml = true, $bForceOrigin = false ) {
		if ( $options === null ) {
			$options = $this->getContentHandler()->makeParserOptions( 'canonical' );
		}

		$po = new \ParserOutput();

		if ( \Hooks::run( 'ContentGetParserOutput',
			[ $this, $title, $revId, $options, $generateHtml, &$po ] ) ) {

			// Save and restore the old value, just in case something is reusing
			// the ParserOptions object in some weird way.
			$oldRedir = $options->getRedirectTarget();
			$options->setRedirectTarget( $this->getRedirectTarget() );
			$this->fillParserOutput( $title, $revId, $options, $generateHtml, $po, $bForceOrigin );
			$options->setRedirectTarget( $oldRedir );
		}

		\Hooks::run( 'ContentAlterParserOutput', [ $this, $title, $po ] );

		return $po;
	}

	/**
	 * Set the HTML and add the appropriate styles
	 *
	 *
	 * @param \Title $title
	 * @param int $revId
	 * @param \ParserOptions $options
	 * @param bool $generateHtml
	 * @param ParserOutput $output
	 */
	protected function fillParserOutput( \Title $title, $revId, \ParserOptions $options, $generateHtml, \ParserOutput &$output, $bForceOrigin = false ) {
		parent::fillParserOutput(
			$title,
			$revId,
			$options,
			$generateHtml,
			$output
		);
		if( $bForceOrigin ) {
			return $output;
		}
		if( !$title ) {
			return $output;
		}
		$factory = Services::getInstance()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle( $title );
		if( !$entity ) {
			return $output;
		}

		$output->setTitleText(
			strip_tags( $entity->getHeader()->parse() )
		);
		$output->setText( $entity->getRenderer()->render( 'Page' ) );
		return $output;
	}
}