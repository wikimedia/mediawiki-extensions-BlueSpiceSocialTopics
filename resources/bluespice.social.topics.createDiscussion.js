
$( document ).bind( 'BSSocialEntityListInit', function( event, EntityList, $el ) {
	new bs.social.EntityList.CreateDiscussion(
		$el,
		EntityList
	);
});