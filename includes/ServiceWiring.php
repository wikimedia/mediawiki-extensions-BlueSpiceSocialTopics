<?php

use MediaWiki\MediaWikiServices;

return [

	'BSSocialDiscussionEntityFactory' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\Social\Topics\DiscussionFactory(
			$services->getService( 'BSEntityRegistry' ),
			$services->getService( 'BSEntityConfigFactory' ),
			$services->getConfigFactory()->makeConfig( 'bsg' )
		);
	},

];
