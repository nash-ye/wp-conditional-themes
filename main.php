<?php
/**
 * Plugin Name: Conditional Themes
 * Plugin URI: https://github.com/nash-ye/WP-Conditional-Themes
 * Description: A simple API to switch the themes on certain conditions.
 * Author: Nashwan Doaqan
 * Author URI: https://profiles.wordpress.org/alex-ye/
 * Version: 0.6
 *
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright (c) 2013 - 2019 Nashwan Doaqan.  All rights reserved.
 */

/**
 * The plugin version.
 *
 * @var   string
 * @since 0.5
 */
define('ND_ERZA_VERSION', '0.6');

/**
 * The plugin codename.
 *
 * @var   string
 * @since 0.5
 */
define('ND_ERZA_CODENAME', 'erza');

/**
 * The plugin directory path.
 *
 * @var   string
 * @since 0.5
 */
define('ND_ERZA_DIR_PATH', plugin_dir_path(__FILE__));

// Load the Conditional_Themes_Manager class.
require ND_ERZA_DIR_PATH . 'includes/class-conditional-themes-manager.php';

// Load the Conditional_Themes_Switcher class.
require ND_ERZA_DIR_PATH . 'includes/class-conditional-themes-switcher.php';

// Instance the Conditional_Themes_Switcher.
add_action('plugins_loaded', array( 'Conditional_Themes_Switcher', 'instance' ), 99);
