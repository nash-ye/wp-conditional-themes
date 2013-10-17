WP Conditional Themes
=====================
A simple API to switch the themes on certain conditions.

Basic Examples
==============

`
// Switch to Twenty Thirteen theme on the morning only.
Conditional_Themes_Manager::register( 'twentythirteen', function() {
	
	if ( current_time( 'H' ) < 12 )
		return true;
		
	return false;

} );
`

`
// Switch to Twenty Ten theme when the user use Internet Explorer.
Conditional_Themes_Manager::register( 'twentyten', function() {

	global $is_IE;
	return (bool) $is_IE;

} );
`
