
$( document ).bind( 'BSSocialEntityListInit', function( event, EntityList, $el ) {
	new bs.social.EntityList.CreateDiscussionPage(
		$el,
		EntityList
	);
});

$( document ).bind( 'BSSocialEntityListInit', function( event, EntityList, $el ) {
	new bs.social.CreateDiscussion(
		$el,
		EntityList
	);
});

