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

Another example, With enabling persistent mode.

```
add_action( 'plugins_loaded', 'my_conditional_themes_setup', 100 );

function my_conditional_themes_setup() {

    // Enable the switcher persistent mode.
    Conditional_Themes_Manager::set_option( 'persistent', TRUE );

    // Switch to Twenty Sixteen theme when we being on 2016.
    Conditional_Themes_Manager::register( 'twentysixteen', function() {
        return ( date( 'Y' ) == 2016 );
    } );

    // Switch to Twenty Fifteen theme when the site reaches 500 post.
    Conditional_Themes_Manager::register( 'twentyfifteen', function() {
        return ( (int) wp_count_posts() > 500 );
    } );

}
```
