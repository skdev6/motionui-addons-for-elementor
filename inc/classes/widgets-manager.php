<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets_Manager{

    const WIDGET_DB_KEY = 'muia_pro_inactive_widgets';
    /** Remote catalog of library widgets, so new ones need no plugin update. */
    const CATALOG_URL      = 'https://raw.githubusercontent.com/skdev6/cdn-js/refs/heads/main/widgets.json';
    const CATALOG_FALLBACK = 'muia_widget_catalog';
    const CATALOG_ACTION   = 'muia_update_widget_catalog';

    /**
     * Library widgets described by the remote catalog.
     *
     * PHP never fetches. The dashboard pulls the catalog in the browser and
     * posts it back through save_catalog(), so no request — front end or
     * admin — can ever block on an outbound connection.
     *
     * Empty until that first fetch lands. Nothing is lost meanwhile:
     * local_widgets_map() also reads bundled_widgets_map(), which ships a seed
     * copy of the library.
     */
    public static function remote_widgets_map(){

        $stored = get_option( self::CATALOG_FALLBACK, [] );

        return ( ! empty( $stored ) && is_array( $stored ) ) ? $stored : [];
    }

    /**
     * Store a catalog the dashboard fetched in the browser (AJAX).
     *
     * The payload arrives from the client, so it goes through exactly the same
     * sanitiser a server-side fetch would have used — the browser is treated as
     * an untrusted relay, never as a source of truth.
     */
    public static function save_catalog(){

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'motionui-addons-for-elementor' ) );
        }

        if ( ! check_ajax_referer( Dashboard::MUIA_NONCE, 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid Nonce', 'motionui-addons-for-elementor' ) );
        }

        $raw = isset( $_POST['catalog'] ) ? wp_unslash( $_POST['catalog'] ) : '';
        $map = self::sanitize_catalog( json_decode( $raw, true ) );

        if ( empty( $map ) ) {
            wp_send_json_error( __( 'Catalog was empty or malformed.', 'motionui-addons-for-elementor' ) );
        }

        // Only write when something actually changed, so opening the dashboard
        // does not cost a database write every single time. Nothing changed
        // means the markup on screen is already correct, so no html is sent.
        if ( get_option( self::CATALOG_FALLBACK ) === $map ) {
            wp_send_json_success( [ 'updated' => false, 'count' => count( $map ) ] );
        }

        // Autoloaded: the front end reads this on every request to register
        // widgets, so it should ride along with the other autoloaded options.
        update_option( self::CATALOG_FALLBACK, $map, true );

        wp_send_json_success( [
            'updated' => true,
            'count'   => count( $map ),
            // Rendered after the option is written, so the template picks up
            // the catalog that was just stored.
            'html'    => self::render_widgets_template(),
        ] );
    }

    /**
     * Render templates/widgets.php to a string.
     *
     * The template reads the widgets map itself and takes nothing from the
     * caller, so buffering a plain require is enough.
     */
    private static function render_widgets_template(){

        $file = THEMEIC_MUIA_DIR_PATH . 'templates/widgets.php';

        if ( ! is_readable( $file ) ) {
            return '';
        }

        ob_start();
        require $file;

        return ob_get_clean();
    }

    /**
     * Whitelist and clean every field. This data reaches HTML — the key becomes
     * a CSS class and drives class resolution, icon becomes a class, and the
     * URLs become hrefs — so nothing is passed through untouched.
     */
    private static function sanitize_catalog( $raw ){

        if ( ! is_array( $raw ) ) {
            return [];
        }

        // Accepts the { schema, widgets } envelope or a bare map.
        $list = ( isset( $raw['widgets'] ) && is_array( $raw['widgets'] ) ) ? $raw['widgets'] : $raw;

        $clean = [];

        foreach ( $list as $key => $widget ) {

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
                'title'               => isset( $widget['title'] ) ? sanitize_text_field( $widget['title'] ) : ucwords( str_replace( '-', ' ', $key ) ),
                'category'            => $category,
                'is_active'           => ! empty( $widget['is_active'] ),
                'is_pro'              => ! empty( $widget['is_pro'] ),
                'is_upcoming'         => ! empty( $widget['is_upcoming'] ),
                'is_in_custom_widget' => ! empty( $widget['is_in_custom_widget'] ),
                'icon'                => isset( $widget['icon'] ) ? preg_replace( '/[^A-Za-z0-9_ -]/', '', $widget['icon'] ) : '',
                'demo'                => isset( $widget['demo'] ) ? esc_url_raw( $widget['demo'] ) : '',
                'tutorial'            => isset( $widget['tutorial'] ) ? esc_url_raw( $widget['tutorial'] ) : '',
            ];
        }

        return $clean;
    }

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
     * The inverse: class name => map key, e.g.
     * \Themeic\CustomWidget\Glow_Button => glow-button.
     *
     * Any namespace is dropped, so a class resolves to the same key whether it
     * came from the plugin or from an uploaded widget.
     */
    public static function get_key_from_class($class_name){
        $short = substr( strrchr( '\\' . ltrim( (string) $class_name, '\\' ), '\\' ), 1 );

        return strtolower( str_replace( '_', '-', $short ) );
    }

    /**
     * Resolve the class that should render a widget.
     *
     * A bundled/pro class always wins. An uploaded custom widget is only used
     * as a fallback, and only for entries flagged with is_in_custom_widget.
     *
     * @return string|false Class name, or false when nothing is installed.
     */
    public static function resolve_widget_class($widget_key, $widget_data = []){

        $suffix = self::get_class_suffix($widget_key);

        // 1st priority: bundled or pro widget.
        $class_name = '\Themeic\MotionUI_Addons\Widgets\\' . $suffix;

        if(class_exists($class_name)){
            return $class_name;
        }

        // 2nd priority: uploaded custom widget.
        if(!empty($widget_data['is_in_custom_widget'])){
            $custom_class = '\Themeic\CustomWidget\\' . $suffix;

            if(class_exists($custom_class)){
                return $custom_class;
            }
        }

        return false;
    }

    public static function get_widgets_map(){
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $local_widgets = self::local_widgets_map();

        foreach ($get_inactive_widgets as $key) {
            if (isset($local_widgets[$key])) {
                $local_widgets[$key]['is_active'] = false;
            }
        }

        // Flag custom widgets the user has not installed (or has deleted).
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
     * The shipped file is the baseline; the remote catalog is layered on top,
     * so a widget can be added or re-described without a wp.org release and a
     * fetched entry always wins over the seed copy of the same key.
     */
    public static function local_widgets_map(){
        return array_merge( self::bundled_widgets_map(), self::remote_widgets_map() );
    }

    /**
     * Widgets described by the file shipped with the plugin.
     *
     * Everything the plugin knows about out of the box lives in
     * assets/data/widgets.json — the bundled widgets, plus a seed copy of the
     * library so a fresh install lists it before anyone opens the dashboard.
     * Adding a widget is a JSON edit, not a code edit.
     *
     * Read once per request: the map is asked for several times while the
     * dashboard renders, and the file never changes mid-request.
     */
    public static function bundled_widgets_map(){

        static $map = null;

        if ( null !== $map ) {
            return $map;
        }

        $file = THEMEIC_MUIA_DIR_PATH . 'assets/data/widgets.json';

        // Run through the same sanitiser as the remote catalog, so both
        // sources produce identically shaped entries and every field the
        // template expects is present.
        $map = is_readable( $file )
            ? self::sanitize_catalog( json_decode( file_get_contents( $file ), true ) )
            : [];

        return $map;
    }
    public static function register_widgets($widgets_manager = null){

        if(!$widgets_manager){
            return;
        }

        $active_widgets = self::get_active_widgets();

        foreach ($active_widgets as $widget_key => $widget_data) {

            // Bundled/pro first, uploaded custom widget as fallback.
            $class_name = self::resolve_widget_class($widget_key, $widget_data);

            if($class_name){
                $widgets_manager->register(new $class_name());
            }

        }

        // Uploaded widgets with no entry in the map still register, so a widget
        // bought before the plugin knew about it remains usable. Entries that
        // ARE mapped are skipped here — the loop above already decided, based
        // on the toggle, so a widget switched off must not sneak back in.
        //
        // Matched on the derived key rather than the class name: the declared
        // class may be Pricing_Switcher, pricing_switcher or Pricing_switcher,
        // and PHP treats those as the same class while a string compare does
        // not. get_key_from_class() lowercases, so every spelling agrees.
        $widgets_map = self::local_widgets_map();

        foreach (Custom_Widgets_Manager::get_widget_classes() as $custom_class) {

            $custom_key = self::get_key_from_class($custom_class);

            if(isset($widgets_map[$custom_key])){
                continue;
            }

            $widgets_manager->register(new $custom_class());
        }
        if ( ! muia_has_pro() && ! empty( self::get_pro_widgets() ) ) {
            // foreach ( self::get_pro_widgets() as $name => $widget ) {
            //     $widgets_manager->register(
            //         new \Themeic\MotionUI_Addons\Widgets\Motionui_Placeholder( $name, $widget )
            //     );
            // }
        }
    }
    public static function save_widgets($widgets = []){
        update_option( self::WIDGET_DB_KEY, $widgets);
    }
}
