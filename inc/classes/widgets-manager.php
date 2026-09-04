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

        // Library widgets are sold separately and ship as their own plugin, so
        // flag the ones with nothing installed to render them. The dashboard
        // shows those as "Get Widget" instead of a toggle.
        foreach ($local_widgets as $key => $widget) {

            if(empty($widget['is_in_custom_widget'])){
                continue;
            }

            if(!self::resolve_widget_class($key, $widget)){
                $local_widgets[$key]['widget_not_installed'] = true;
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
                return isset( $widget['is_active'] ) && $widget['is_active'] === true;
            }
        );
    }

    public static function get_pro_widgets() {
        return array_filter(
            self::local_widgets_map(),
            function( $widget ) {
                return isset( $widget['is_pro'] ) && $widget['is_pro'] === true;
            }
        );
    }

    /**
     * Every widget the dashboard knows about.
     *
     * The bundled list is the baseline. A companion plugin adds the widgets it
     * ships through the filter, so the library can grow without a release here.
     */
    public static function local_widgets_map(){

        /**
         * Filter the full widget catalog.
         *
         * @param array $map Widget key => entry.
         */
        $map = apply_filters( 'muia_widgets_map', self::bundled_widgets_map() );

        return is_array( $map ) ? self::normalize_map( $map ) : self::bundled_widgets_map();
    }

    /**
     * Guarantee every entry has the fields the templates read.
     *
     * The bundled list below is written by hand and always complete; this only
     * has to cover entries a companion plugin adds through the filter, which
     * may leave optional keys out.
     */
    private static function normalize_map( $map ){

        $clean = [];

        foreach ( $map as $key => $widget ) {

            $key = sanitize_key( $key );

            if ( '' === $key || ! is_array( $widget ) ) {
                continue;
            }

            $category = [];

            foreach ( (array) ( isset( $widget['category'] ) ? $widget['category'] : [] ) as $cat ) {
                $cat = sanitize_key( $cat );

                if ( '' !== $cat ) {
                    $category[] = $cat;
                }
            }

            $clean[ $key ] = [
                'title'               => isset( $widget['title'] ) ? $widget['title'] : ucwords( str_replace( '-', ' ', $key ) ),
                'category'            => $category,
                'is_active'           => ! empty( $widget['is_active'] ),
                'is_pro'              => ! empty( $widget['is_pro'] ),
                'is_upcoming'         => ! empty( $widget['is_upcoming'] ),
                // Sold separately, as its own plugin.
                'is_in_custom_widget' => ! empty( $widget['is_in_custom_widget'] ),
                'icon'                => isset( $widget['icon'] ) ? preg_replace( '/[^A-Za-z0-9_ -]/', '', $widget['icon'] ) : '',
                'demo'                => isset( $widget['demo'] ) ? esc_url_raw( $widget['demo'] ) : '',
                'tutorial'            => isset( $widget['tutorial'] ) ? esc_url_raw( $widget['tutorial'] ) : '',
            ];
        }

        return $clean;
    }

    /**
     * Every widget shipped with the plugin, plus the library widgets sold
     * separately so the dashboard can list them.
     *
     * The list itself lives in inc/widgets-map.php, which returns the array.
     * Read once per request: the map is asked for several times while the
     * dashboard renders, and the file cannot change mid-request.
     */
    public static function bundled_widgets_map(){

        static $map = null;

        if ( null !== $map ) {
            return $map;
        }

        $file = THEMEIC_MUIA_DIR_PATH . 'inc/widgets-map.php';

        // require, not require_once: the file returns the array, and
        // require_once would hand back true on any later call.
        $map = is_readable( $file ) ? (array) require $file : [];

        return $map;
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
