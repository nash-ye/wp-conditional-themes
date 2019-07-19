<?php

/**
 * Tne Conditional Themes Manager class.
 *
 * @since 0.1
 */
class Conditional_Themes_Manager
{
    /**
     * The conditional themes list.
     *
     * @var   array
     * @since 0.1
     */
    protected static $themes = array();

    /**
     * @var   array
     * @since 0.4
     */
    protected static $options = array();


    /** Magic Methods *********************************************************/

    /**
     * A dummy constructor.
     *
     * @since 0.1
     */
    private function __construct()
    {
    }


    /*** Static Methods *******************************************************/

    /**
     * Get all registered conditional themes.
     *
     * @return array
     * @since  0.3
     */
    public static function get_all()
    {
        return self::$themes;
    }

    /**
     * Retrieve all options.
     *
     * @return array
     * @since  0.4
     */
    public static function get_options()
    {
        return self::$options;
    }

    /**
     * Retrieve an option value.
     *
     * @return mixed
     * @since  0.4
     */
    public static function get_option($key)
    {
        if (isset(self::$options[ $key ])) {
            return self::$options[ $key ];
        }
    }

    /**
     * Set an option value.
     *
     * @return void
     * @since  0.4
     */
    public static function set_option($key, $value)
    {
        self::$options[ $key ] = $value;
    }

    /**
     * Set the options.
     *
     * @return void
     * @since  0.4
     */
    public static function set_options(array $options)
    {
        self::$options = $options;
    }

    /**
     * Register a conditional theme.
     *
     * @return bool
     * @since  0.1
     */
    public static function register($theme, $condition, $priority = 10)
    {
        if (empty($theme)) {
            return false;
        }

        $priority = (int) $priority;

        if ($theme instanceof WP_Theme) {
            $theme = $theme->get_stylesheet();
        }

        if (! is_bool($condition) && ! is_callable($condition, true)) {
            return false;
        }

        self::$themes[ $theme ] = (object) array(
            'condition' => $condition,
            'priority'  => $priority,
            'theme'     => $theme,
        );

        uasort(self::$themes, array( __CLASS__, 'cmp_priorities' ));

        return true;
    }

    protected static function cmp_priorities($a, $b)
    {
        $p1 = (int) $a->priority;
        $p2 = (int) $b->priority;

        if ($p1 === $p2) {
            return 0;
        }

        return ($p1 > $p2) ? +1 : -1;
    }

    /**
     * Deregister a conditional theme.
     *
     * @return bool
     * @since  0.1
     */
    public static function deregister($theme)
    {
        if (empty($theme)) {
            return false;
        }

        if ($theme instanceof WP_Theme) {
            $theme = $theme->get_stylesheet();
        }

        unset(self::$themes[ $theme ]);

        return true;
    }
}
