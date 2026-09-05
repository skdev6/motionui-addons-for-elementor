<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets_Manager{

    const WIDGET_DB_KEY = 'muia_pro_inactive_widgets';

    public static function get_inactive_widgets(){
        return get_option(self::WIDGET_DB_KEY, []);
    }
    public static function update_inactive_widgets($widgets = []){
        update_option(self::WIDGET_DB_KEY, $widgets);
    }
    /**
     * Map key => class name suffix, e.g. glow-button => Glow_Button.
     */
    public static function get_class_suffix($widget_key){
        return str_replace('-', '_', ucwords($widget_key, '-'));
    }

    /**
     * Resolve the class that should render a widget.
     *
     * Only bundled and pro widgets are resolved here. Widgets bought from the
     * library are separate plugins that register themselves — see the
     * muia/widgets/register action in register_widgets() — so a companion
     * plugin points this at its own class through the filter below.
     *
     * @return string|false Class name, or false when nothing is installed.
     */
    public static function resolve_widget_class($widget_key, $widget_data = []){

        $suffix     = self::get_class_suffix($widget_key);
        $class_name = '\Themeic\MotionUI_Addons\Widgets\\' . $suffix;
        $resolved   = class_exists($class_name) ? $class_name : false;
        /**
         * Filter the class that renders a widget.
         *
         * @param string|false $resolved    Class name, or false when not installed.
         * @param string       $widget_key  Widget key from the catalog.
         * @param array        $widget_data Catalog entry.
         */
        return apply_filters( 'muia_resolve_widget_class', $resolved, $widget_key, $widget_data );
    }

    public static function get_widgets_map(){
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $local_widgets = self::local_widgets_map();

        foreach ($get_inactive_widgets as $key) {
            if (isset($local_widgets[$key])) {
                $local_widgets[$key]['is_active'] = false;
            }
        }

        return $local_widgets;
    }
    public static function get_pro_widgets_map(){
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $pro_widgets = self::get_pro_widgets();

        foreach ($get_inactive_widgets as $key) {
            if (isset($pro_widgets[$key])) {
                $pro_widgets[$key]['is_active'] = false;
            }
        }

        return $pro_widgets;
    }
    public static function get_active_widgets() {
        return array_filter(
            self::get_widgets_map(),
            function( $widget ) {
                // Loose check: an entry added through the muia_widgets_map
                // filter may use 1 or 'yes' rather than a real boolean.
                return ! empty( $widget['is_active'] );
            }
        );
    }

    public static function get_pro_widgets() {
        return array_filter(
            self::local_widgets_map(),
            function( $widget ) {
                return ! empty( $widget['is_pro'] );
            }
        );
    }

    /**
     * Every widget the dashboard knows about.
     *
     * The list lives in inc/widgets-map.php. A companion plugin adds the
     * widgets it ships through the muia_widgets_map filter, so the library can
     * grow without a release here.
     *
     * Built once per request: the map is asked for several times while the
     * dashboard renders.
     */
    public static function local_widgets_map(){

        static $map = null;

        if ( null !== $map ) {
            return $map;
        }

        $file = THEMEIC_MUIA_DIR_PATH . 'inc/widgets-map.php';

        // require, not require_once: the file returns the array, and
        // require_once would hand back true on any later call.
        $bundled = is_readable( $file ) ? (array) require $file : [];

        /**
         * Filter the full widget catalog.
         *
         * @param array $bundled Widget key => entry.
         */
        $map = apply_filters( 'muia_widgets_map', $bundled );

        if ( ! is_array( $map ) ) {
            $map = $bundled;
        }

        // An entry with no file behind it is not ready yet, whatever the
        // catalog says. Marking it here keeps the dashboard honest without
        // having to remember to flip is_upcoming by hand on release day.
        foreach ( $map as $key => $widget ) {

            if ( ! empty( $widget['is_upcoming'] ) ) {
                continue;
            }

            if ( ! self::has_widget_file( $key, ! empty( $widget['is_pro'] ) ) ) {
                $map[ $key ]['is_upcoming'] = true;
            }
        }

        return $map;
    }

    /**
     * Is the file that implements a widget on disk?
     *
     * Free widgets live in this plugin, Pro widgets in the Pro plugin. When Pro
     * is not installed there is nothing to look at, so the entry is left alone
     * and the dashboard keeps offering it as a Pro widget to buy.
     */
    private static function has_widget_file( $widget_key, $is_pro ){

        if ( ! $is_pro ) {
            return is_readable( THEMEIC_MUIA_DIR_PATH . 'widgets/' . $widget_key . '.php' );
        }

        if ( ! defined( 'THEMEIC_MUIA_PRO_DIR_PATH' ) ) {
            return true;
        }

        return is_readable( THEMEIC_MUIA_PRO_DIR_PATH . 'widgets/' . $widget_key . '.php' );
    }
    public static function register_widgets($widgets_manager = null){

        if(!$widgets_manager){
            return;
        }

        $active_widgets = self::get_active_widgets();

        foreach ($active_widgets as $widget_key => $widget_data) {

            $class_name = self::resolve_widget_class($widget_key, $widget_data);

            if($class_name){
                $widgets_manager->register(new $class_name());
            }

        }

        /**
         * Register widgets that ship as their own plugin.
         *
         * Widgets bought from the library are separate plugins; they hook here
         * to add themselves. Fired after the bundled widgets so an add-on can
         * replace one by registering the same name.
         *
         * @param \Elementor\Widgets_Manager $widgets_manager
         */
        do_action( 'muia/widgets/register', $widgets_manager );
    }
    public static function save_widgets($widgets = []){
        update_option( self::WIDGET_DB_KEY, $widgets);
    }
}
