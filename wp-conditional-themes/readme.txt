=== Conditional Themes ===
Contributors: alex-ye
Tags: theme, themes, theme-switcher, switch, api
Requires at least: 3.4
Tested up to: 3.7
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple API to switch the themes on certain conditions.

== Description ==

= Important Notes: =
1. This plugin is for developers, not general users.

Conditional Themes is an API to switch the themes on certain conditions.
This plugin doesn't have a GUI yet, I will design it if the users need it!

= Basic Examples =
You can use this plugin in many ways depending on your needs, this examples only for learning purposes:

`
// Switch to Twenty Eleven theme if the visitor use Internet Explorer.
Conditional_Themes_Manager::register( 'twentyeleven', function() {
    global $is_IE;
    return (bool) $is_IE;
} );
`

`
// Switch to Twenty Thirteen theme if the user has administrator role.
Conditional_Themes_Manager::register( 'twentythirteen', function() {
    return current_user_can( 'administrator' );
} );
`

`
// Switch to a custom theme if the visitor use a mobile device.
Conditional_Themes_Manager::register( 'mobile', 'wp_is_mobile' );
`

= Contributing =
If you love this plugin and want to make it better:

https://github.com/nash-ye/WP-Conditional-Themes

== Installation ==

1. Upload and install the plugin
2. Use the simple API to powerful your plugin.

== Changelog ==

= 0.1 =
* The Initial version.