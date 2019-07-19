<?php

/**
 * Tne Conditional Themes Switcher class.
 *
 * @since 0.1
 */
class Conditional_Themes_Switcher
{
    /**
     * The original theme.
     *
     * @var   WP_Theme
     * @since 0.3
     */
    private $original_theme = null;

    /**
     * The switched theme.
     *
     * @var   WP_Theme
     * @since 0.1
     */
    private $switched_theme = null;


    /** Methods ***************************************************************/

    /**
     * A dummy constructor to prevent the switcher from being loaded more than once.
     *
     * @since 1.0
     */
    private function __construct()
    {
    }

    /**
     * A dummy magic method to prevent the switcher from being cloned
     *
     * @return void
     * @since  0.1
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '0.1');
    }

    /**
     * A dummy magic method to prevent the switcher from being unserialized
     *
     * @return void
     * @since  0.1
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), '0.1');
    }

    /**
     * Get the switched template (parent) theme directory name.
     * Used as a callback for 'template' and 'pre_option_template' WP filters.
     *
     * @return string
     * @since  0.4
     */
    public function filter_template($template = '')
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $template = $theme->get_template();
        }

        return $template;
    }

    /**
     * Get the switched stylesheet (child) theme directory name.
     * Used as a callback for 'stylesheet' and 'pre_option_stylesheet' WP filters.
     *
     * @return string
     * @since  0.4
     */
    public function filter_stylesheet($stylesheet = '')
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $stylesheet = $theme->get_stylesheet();
        }

        return $stylesheet;
    }

    /**
     * Get the switched theme name.
     * Used as a callback for 'current_theme' WP filter.
     *
     * @return string
     * @since  0.4
     */
    public function filter_current_theme($current_theme = '')
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $current_theme = $theme->display('Name');
        }

        return $current_theme;
    }

    /**
     * Get the switched template (parent) theme root path.
     * Used as a callback for 'pre_option_template_root' WP filter.
     *
     * @return string
     * @since  0.4
     */
    public function filter_template_root($template_root = '')
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $template = $theme->get_template();

            if (! empty($template)) {
                $template_root = get_raw_theme_root($template, true);
            }
        }

        return $template_root;
    }

    /**
     * Get the switched stylesheet (child) theme root path.
     * Used as a callback for 'pre_option_stylesheet_root' WP filter.
     *
     * @return string
     * @since  0.4
     */
    public function filter_stylesheet_root($stylesheet_root = '')
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $stylesheet = $theme->get_stylesheet();

            if (! empty($stylesheet)) {
                $stylesheet_root = get_raw_theme_root($stylesheet, true);
            }
        }

        return $stylesheet_root;
    }

    /**
     * Get the switched theme sidebars widgets.
     * Used as a callback for 'pre_option_sidebars_widgets' WP filter.
     *
     * @return array
     * @since  0.4
     */
    public function filter_sidebars_widgets($sidebars_widgets)
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $mod = get_theme_mod('sidebars_widgets', array());

            if (isset($mod['data']) && is_array($mod['data'])) {
                $sidebars_widgets = $mod['data'];
            }
        }

        return $sidebars_widgets;
    }

    /**
     * Update the switched theme sidebars widgets.
     * Used as a callback for 'pre_update_option_sidebars_widgets' WP filter.
     *
     * @author Laurens Offereins https://github.com/lmoffereins
     * @return array
     * @since  0.5
     */
    public function update_sidebars_widgets($sidebars_widgets, $old_value)
    {
        $theme = $this->get_switched_theme();

        if ($theme instanceof WP_Theme) {
            $mod = get_theme_mod('sidebars_widgets', array());
            $mod['data'] = $sidebars_widgets;

            set_theme_mod('sidebars_widgets', $mod);

            // Return old value in order to bail the original update logic.
            $sidebars_widgets = $old_value;
        }

        return $sidebars_widgets;
    }

    /**
     * Get the switched theme object.
     *
     * @return WP_Theme|bool
     * @since  0.1
     */
    public function get_switched_theme()
    {
        if (! is_null($this->switched_theme)) {
            return $this->switched_theme;
        }

        $cts = Conditional_Themes_Manager::get_all();

        if (is_array($cts) && ! empty($cts)) {
            foreach ($cts as $ct) {
                if (! is_null($this->original_theme)) {
                    if ($ct->theme === $this->original_theme->get_stylesheet()) {
                        continue;
                    }
                }

                if (! empty($ct->condition) && is_callable($ct->condition)) {
                    $ct->condition = call_user_func($ct->condition);
                    $ct->condition = (bool) $ct->condition;
                }

                if (is_bool($ct->condition) && $ct->condition) {
                    $theme = wp_get_theme($ct->theme);

                    if ($theme->exists() && $theme->is_allowed()) {
                        $this->switched_theme = $theme;
                        break;
                    }
                }
            }
        }

        if (empty($this->switched_theme)) {
            $this->switched_theme = false;
        }

        return $this->switched_theme;
    }

    /**
     * Hook the WordPress system to setup the switched theme.
     *
     * @return void
     * @since  0.1
     */
    public function setup_switched_theme()
    {
        $this->original_theme = wp_get_theme();
        $switched_theme = $this->get_switched_theme();

        if (! $switched_theme instanceof WP_Theme) {
            return;
        }

        if (Conditional_Themes_Manager::get_option('persistent')) {
            // Switch the theme.
            switch_theme($switched_theme->get_stylesheet());
        } else {
            add_filter('template', array( $this, 'filter_template' ), 1);
            add_filter('stylesheet', array( $this, 'filter_stylesheet' ), 1);

            add_filter('pre_option_template', array( $this, 'filter_template' ));
            add_filter('pre_option_stylesheet', array( $this, 'filter_stylesheet' ));
            add_filter('pre_option_current_theme', array( $this, 'filter_current_theme' ));

            add_filter('pre_option_template_root', array( $this, 'filter_template_root' ));
            add_filter('pre_option_stylesheet_root', array( $this, 'filter_stylesheet_root' ));
            add_filter('pre_option_sidebars_widgets', array( $this, 'filter_sidebars_widgets' ));

            add_filter('pre_update_option_sidebars_widgets', array( $this, 'update_sidebars_widgets' ), 10, 2);
        }
    }

    /**
     * Run the conditional themes switch.
     *
     * @since 0.1
     */
    public function maybe_switch()
    {
        add_action('setup_theme', array( $this, 'setup_switched_theme' ));
    }


    /** Singleton *************************************************************/

    /**
     * @var Conditional_Themes_Switcher
     * @since 0.1
     */
    private static $instance = null;

    /**
     * Main Conditional Themes Switcher Instance
     *
     * @since 0.1
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Conditional_Themes_Switcher;
            self::$instance->maybe_switch();
        }

        return self::$instance;
    }
}
