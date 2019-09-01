<?php

use MediaWiki\MediaWikiServices;
use BlueSpice\ExtensionAttributeBasedRegistry;

return [

	'BSSocialDiscussionEntityFactory' => function ( MediaWikiServices $services ) {
		$registry = new ExtensionAttributeBasedRegistry(
			'BlueSpiceFoundationEntityRegistry'
		);
		return new \BlueSpice\Social\Topics\DiscussionFactory(
			$registry,
			$services->getService( 'BSEntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

];
