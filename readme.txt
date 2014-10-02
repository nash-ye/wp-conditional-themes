=== Conditional Themes ===
Contributors: alex-ye
Tags: theme, themes, theme-switcher, switch, api
Requires at least: 3.4
Tested up to: 4.0
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple API to switch the themes on certain conditions.

== Description ==

Conditional Themes is an API to switch the themes on certain conditions.

= How to use it? =
Write an another plugin file, and use the Conditional Themes API as the example below:

`
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
`

= Contributing =
Developers can contribute to the source code on the [Github Repository](https://github.com/nash-ye/WP-Conditional-Themes).

== Installation ==

1. Upload and install the plugin
2. Use the plugin API to powerful your project.

== Changelog ==

= 0.2 =
* Cleaner code and minor fixes.

= 0.1 =
* The Initial version.