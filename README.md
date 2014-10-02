WP Conditional Themes
=====================
A simple API to switch the themes on certain conditions.

How to use it?
==============
Write an another plugin file, and use the Conditional Themes API as the example below:

```
add_action( 'plugins_loaded', 'my_conditional_themes_setup', 100 );

function my_conditional_themes_setup() {

    // Switch to Twenty Eleven theme if the visitor use Internet Explorer.
    Conditional_Themes_Manager::register( 'twentyeleven', function() {
        global $is_IE;
        return (bool) $is_IE;
    } );

    // Switch to Twenty Thirteen theme if the user has administrator role.
    Conditional_Themes_Manager::register( 'twentythirteen', function() {
        return current_user_can( 'administrator' );
    } );

    // Switch to a custom theme if the visitor use a mobile device.
    Conditional_Themes_Manager::register( 'mobile', 'wp_is_mobile' );

}
```