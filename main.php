<?php
/**
 * Plugin Name: Conditional Themes
 * Plugin URI: https://github.com/nash-ye/WP-Conditional-Themes
 * Description: A simple API to switch the themes on certain conditions.
 * Author: Nashwan Doaqan
 * Author URI: http://nashwan-d.com
 * Version: 0.4
 *
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright (c) 2013 - 2015 Nashwan Doaqan.  All rights reserved.
 */

add_action( 'plugins_loaded', array( 'Conditional_Themes_Switcher', 'instance' ), 99 );

/**
 * Tne Conditional Themes Switcher class.
 *
 * @since 0.1
 */
class Conditional_Themes_Switcher {

	/**
	 * The original theme.
	 *
	 * @var WP_Theme
	 * @since 0.3
	 */
	private $original_theme = NULL;

	/**
	 * The switched theme.
	 *
	 * @var WP_Theme
	 * @since 0.1
	 */
	private $switched_theme = NULL;


	/** Methods ***************************************************************/

	/**
	 * A dummy constructor to prevent the switcher from being loaded more than once.
	 *
	 * @since 1.0
	 */
	private function __construct() {}

	/**
	 * A dummy magic method to prevent the switcher from being cloned
	 *
	 * @return void
	 * @since 0.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '0.1' );
	}

	/**
	 * A dummy magic method to prevent the switcher from being unserialized
	 *
	 * @return void
	 * @since 0.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '0.1' );
	}

	/**
	 * Get the switched template (parent) theme directory name.
	 * Used as a callback for 'template' and 'pre_option_template' WP filters.
	 *
	 * @return string
	 * @since 0.4
	 */
	public function filter_template( $template = '' ) {

		$theme = $this->get_switched_theme();

		if ( $theme instanceof WP_Theme ) {
			$template = $theme->get_template();
		}

		return $template;

	}

	/**
	 * Get the switched stylesheet (child) theme directory name.
	 * Used as a callback for 'stylesheet' and 'pre_option_stylesheet' WP filters.
	 *
	 * @return string
	 * @since 0.4
	 */
	public function filter_stylesheet( $stylesheet ='' ) {

		$theme = $this->get_switched_theme();

		if ( $theme instanceof WP_Theme ) {
			$stylesheet = $theme->get_stylesheet();
		}

		return $stylesheet;

	}

	/**
	 * Get the switched theme name.
	 * Used as a callback for 'current_theme' WP filter.
	 *
	 * @return string
	 * @since 0.4
	 */
	public function filter_current_theme( $current_theme = '' ) {

		$theme = $this->get_switched_theme();

		if ( $theme instanceof WP_Theme ) {
			$current_theme = $theme->display( 'Name' );
		}

		return $current_theme;

	}

	/**
	 * Get the switched template (parent) theme root path.
	 * Used as a callback for 'pre_option_template_root' WP filter.
	 *
	 * @return string
	 * @since 0.4
	 */
	public function filter_template_root( $template_root = '' ) {

		$template = $this->get_template();

		if ( ! empty( $template ) ) {
			$template_root = get_raw_theme_root( $template, true );
		}

		return $template_root;

	}

	/**
	 * Get the switched stylesheet (child) theme root path.
	 * Used as a callback for 'pre_option_stylesheet_root' WP filter.
	 *
	 * @return string
	 * @since 0.4
	 */
	public function filter_stylesheet_root( $stylesheet_root = '' ) {

		$stylesheet = $this->get_stylesheet();

		if ( ! empty( $stylesheet ) ) {
			$stylesheet_root = get_raw_theme_root( $stylesheet, true );
		}

		return $stylesheet_root;

	}

	/**
	 * Get the switched theme sidebars widgets.
	 * Used as a callback for 'pre_option_sidebars_widgets' WP filter.
	 *
	 * @return array
	 * @since 0.4
	 */
	public function filter_sidebars_widgets( $sidebars_widgets = FALSE ) {

		if ( FALSE === $sidebars_widgets ) {

			$current_sidebars_widgets = get_theme_mod( 'sidebars_widgets' );

			if ( is_array( $current_sidebars_widgets ) ) {
				$sidebars_widgets = $current_sidebars_widgets['data'];
			}

		}

		return $sidebars_widgets;

	}

	/**
	 * Get the switched theme object.
	 *
	 * @return WP_Theme|bool
	 * @since 0.1
	 */
	public function get_switched_theme() {

		if ( ! is_null( $this->switched_theme ) ) {
			return $this->switched_theme;
		}

		$cts = Conditional_Themes_Manager::get_all();

		if ( is_array( $cts ) && ! empty( $cts ) ) {

			foreach( $cts as $ct ) {

				if ( ! is_null( $this->original_theme ) ) {

					if ( $ct->theme === $this->original_theme->get_stylesheet() ) {
						continue;
					}

				}

				if ( ! empty( $ct->condition ) && is_callable( $ct->condition ) ) {

					$ct->condition = call_user_func( $ct->condition );
					$ct->condition = (bool) $ct->condition;

				}

				if ( is_bool( $ct->condition ) && $ct->condition ) {

					$theme = wp_get_theme( $ct->theme );

					if ( $theme->exists() && $theme->is_allowed() ) {

						$this->switched_theme = $theme;
						break;

					}

				}

			}

		}

		if ( empty( $this->switched_theme ) ) {
			$this->switched_theme = FALSE;
		}

		return $this->switched_theme;

	}

	/**
	 * Hook the WordPress system to setup the switched theme.
	 *
	 * @return void
	 * @since 0.1
	 */
	public function setup_switched_theme() {

		$this->original_theme = wp_get_theme();

		if ( Conditional_Themes_Manager::get_option( 'persistent' ) ) {

			$theme = $this->get_switched_theme();

			if ( ! empty( $theme ) ) {
				switch_theme( $theme->get_stylesheet() );
			}

		} else {

			add_filter( 'template', array( $this, 'filter_template' ), 1 );
			add_filter( 'stylesheet', array( $this, 'filter_stylesheet' ), 1 );

			add_filter( 'pre_option_template', array( $this, 'filter_template' ) );
			add_filter( 'pre_option_stylesheet', array( $this, 'filter_stylesheet' ) );
			add_filter( 'pre_option_current_theme', array( $this, 'filter_current_theme' ) );

			add_filter( 'pre_option_template_root', array( $this, 'filter_template_root' ) );
			add_filter( 'pre_option_stylesheet_root', array( $this, 'filter_stylesheet_root' ) );
			add_filter( 'pre_option_sidebars_widgets', array( $this, 'filter_sidebars_widgets' ) );

		}

	}

	/**
	 * Run the conditional themes switch.
	 *
	 * @since 0.1
	 */
	public function maybe_switch() {
		add_action( 'setup_theme', array( $this, 'setup_switched_theme' ) );
	}


	/** Singleton *************************************************************/

	/**
	 * @var Conditional_Themes_Switcher
	 * @since 0.1
	 */
	private static $instance = NULL;

	/**
	 * Main Conditional Themes Switcher Instance
	 *
	 * @since 0.1
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new Conditional_Themes_Switcher;
			self::$instance->maybe_switch();

		}

		return self::$instance;

	}

}

/**
 * Tne Conditional Themes Manager class.
 *
 * @since 0.1
 */
class Conditional_Themes_Manager {

	/**
	 * The conditional themes list.
	 *
	 * @var array
	 * @since 0.1
	 */
	protected static $themes = array();

	/**
	 * @var array
	 * @since 0.4
	 */
	protected static $options = array();


	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor.
	 *
	 * @since 0.1
	 */
	private function __construct() {}


	//*** Static Methods ******************************************************/

	/**
	 * Get all registered conditional themes.
	 *
	 * @return array
	 * @since 0.3
	 */
	public static function get_all() {
		return self::$themes;
	}

	/**
	 * Retrieve all options.
	 *
	 * @return array
	 * @since 0.4
	 */
	public static function get_options() {
		return self::$options;
	}

	/**
	 * Retrieve an option value.
	 *
	 * @return mixed
	 * @since 0.4
	 */
	public static function get_option( $key ) {

		if ( isset( self::$options[ $key ] ) ) {
			return self::$options[ $key ];
		}

	}

	/**
	 * Set an option value.
	 *
	 * @return void
	 * @since 0.4
	 */
	public static function set_option( $key, $value ) {
		self::$options[ $key ] = $value;
	}

	/**
	 * Set the options.
	 *
	 * @return void
	 * @since 0.4
	 */
	public static function set_options( array $options ) {
		self::$options = $options;
	}

	/**
	 * Register a conditional theme.
	 *
	 * @return bool
	 * @since 0.1
	 */
	public static function register( $theme, $condition, $priority = 10 ) {

		if ( empty( $theme ) ) {
			return false;
		}

		$priority = (int) $priority;

		if ( $theme instanceof WP_Theme ) {
			$theme = $theme->get_stylesheet();
		}

		if ( ! is_bool( $condition ) && ! is_callable( $condition, true ) ) {
			return false;
		}

		self::$themes[ $theme ] = (object) array(
			'condition' => $condition,
			'priority'  => $priority,
			'theme'     => $theme,
		);

		uasort( self::$themes, array( __CLASS__, 'cmp_priorities' ) );

		return true;

	}

	protected static function cmp_priorities( $a,$b ) {

		$p1 = (int) $a->priority;
		$p2 = (int) $b->priority;

		if ( $p1 === $p2 ) {
			return 0;
		}

		return ( $p1 > $p2 ) ? +1 : -1;

	}

	/**
	 * Deregister a conditional theme.
	 *
	 * @return bool
	 * @since 0.1
	 */
	public static function deregister( $theme ) {

		if ( empty( $theme ) ) {
			return false;
		}

		if ( $theme instanceof WP_Theme ) {
			$theme = $theme->get_stylesheet();
		}

		unset( self::$themes[ $theme ] );

		return true;

	}

}