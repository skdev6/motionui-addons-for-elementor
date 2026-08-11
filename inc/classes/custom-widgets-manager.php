<?php
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Widgets_Manager {

    const MUIA_UPLOAD_NONCE  = 'muia-upload-custom-widget';
    const MUIA_UPLOAD_ACTION = 'muia_upload_custom_widget';
    const MUIA_UPLOAD_FOLDER = 'motionui-addons-for-elementor/widgets';

    /** Maximum allowed zip size in bytes (5 MB). */
    const MUIA_MAX_ZIP_SIZE = 5242880;

    /** Maximum number of files allowed inside one widget zip. */
    const MUIA_MAX_FILES = 200;

    /**
     * Absolute path to uploads/motionui-addons-for-elementor (no trailing slash).
     */
    public static function get_upload_dir() {
        $upload = wp_upload_dir();
        return trailingslashit( $upload['basedir'] ) . self::MUIA_UPLOAD_FOLDER;
    }

    /**
     * List of uploaded custom widget folders.
     *
     * @return array [ 'widget-slug' => '/abs/path/to/widget-slug', ... ]
     */
    public static function get_custom_widgets() {
        $base    = self::get_upload_dir();
        $widgets = [];

        if ( ! is_dir( $base ) ) {
            return $widgets;
        }

        foreach ( glob( $base . '/*', GLOB_ONLYDIR ) as $dir ) {
            // Only folders that look like a Themeic widget (must ship a themeic-widget.php).
            if ( file_exists( $dir . '/themeic-widget.php' ) ) {
                $widgets[ basename( $dir ) ] = wp_normalize_path( $dir );
            }
        }

        return $widgets;
    }

    /** @var bool Whether the uploaded widget files have already been included. */
    private static $files_loaded = false;

    /**
     * Include every uploaded themeic-widget.php once.
     *
     * Folder and class names are independent — a folder named
     * glow-buttons-elementor-widget may declare Glow_Button — so the class
     * cannot be derived from the path. The files are loaded instead and PHP
     * is asked afterwards which classes exist.
     */
    public static function load_widget_files() {

        if ( self::$files_loaded ) {
            return;
        }

        self::$files_loaded = true;

        foreach ( self::get_custom_widgets() as $widget_dir ) {
            $file = $widget_dir . '/themeic-widget.php';

            if ( is_readable( $file ) ) {
                include_once $file;
            }
        }
    }

    /**
     * Every widget class declared by the uploaded files.
     *
     * @return string[] Fully qualified class names.
     */
    public static function get_widget_classes() {

        self::load_widget_files();

        $classes = [];

        foreach ( get_declared_classes() as $class_name ) {

            if ( 0 !== strpos( $class_name, 'Themeic\\CustomWidget\\' ) ) {
                continue;
            }

            // Skip helpers or base classes shipped alongside a widget.
            if ( ! is_subclass_of( $class_name, '\Elementor\Widget_Base' ) ) {
                continue;
            }

            try {
                if ( ( new \ReflectionClass( $class_name ) )->isAbstract() ) {
                    continue;
                }
            } catch ( \ReflectionException $e ) {
                continue;
            }

            $classes[] = $class_name;
        }

        return $classes;
    }

    /**
     * Folder that holds the file declaring a custom widget class.
     *
     * @return string|false
     */
    public static function get_widget_dir( $class_name ) {

        if ( ! class_exists( $class_name ) ) {
            return false;
        }

        try {
            $file = ( new \ReflectionClass( $class_name ) )->getFileName();
        } catch ( \ReflectionException $e ) {
            return false;
        }

        return $file ? wp_normalize_path( dirname( $file ) ) : false;
    }

    /**
     * Widget folder path => class name, for every uploaded widget.
     */
    public static function get_widget_class_map() {

        $map = [];

        foreach ( self::get_widget_classes() as $class_name ) {
            $dir = self::get_widget_dir( $class_name );

            if ( $dir ) {
                $map[ $dir ] = $class_name;
            }
        }

        return $map;
    }

    /**
     * Register each uploaded widget's css/js so Elementor can enqueue them.
     *
     * Handles come from the widget's own get_style_depends() / get_script_depends()
     * and map to files of the same name inside the widget folder:
     * handle "themeic-minimal-button" => themeic-minimal-button.css / .js.
     */
    public static function register_assets() {

        $upload   = wp_upload_dir();
        $base_url = trailingslashit( $upload['baseurl'] ) . self::MUIA_UPLOAD_FOLDER;

        foreach ( self::get_widget_classes() as $class_name ) {

            $widget_dir = self::get_widget_dir( $class_name );

            if ( ! $widget_dir ) {
                continue;
            }

            $widget     = new $class_name();
            $widget_url = $base_url . '/' . basename( $widget_dir ) . '/';

            if ( method_exists( $widget, 'get_style_depends' ) ) {
                foreach ( (array) $widget->get_style_depends() as $handle ) {

                    if ( wp_style_is( $handle, 'registered' ) ) {
                        continue;
                    }

                    // Prefer the minified file when both exist.
                    foreach ( [ $handle . '.min.css', $handle . '.css' ] as $file_name ) {
                        $file = $widget_dir . '/' . $file_name;

                        if ( file_exists( $file ) ) {
                            wp_register_style( $handle, $widget_url . $file_name, [], (string) filemtime( $file ) );
                            break;
                        }
                    }
                }
            }

            if ( method_exists( $widget, 'get_script_depends' ) ) {
                foreach ( (array) $widget->get_script_depends() as $handle ) {

                    if ( wp_script_is( $handle, 'registered' ) ) {
                        continue;
                    }

                    // Prefer the minified file when both exist.
                    foreach ( [ $handle . '.min.js', $handle . '.js' ] as $file_name ) {
                        $file = $widget_dir . '/' . $file_name;

                        if ( file_exists( $file ) ) {
                            wp_register_script( $handle, $widget_url . $file_name, [ 'jquery' ], (string) filemtime( $file ), true );
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * File extensions a widget zip is allowed to contain.
     */
    public static function get_allowed_extensions() {
        return apply_filters( 'muia_custom_widget_allowed_extensions', [
            'php', 'js', 'css', 'json', 'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp',
            'woff', 'woff2', 'ttf', 'eot', 'otf', 'map', 'txt', 'md', 'mo', 'po',
        ] );
    }

    /**
     * Block direct web access to the uploads/motionui-addons-for-elementor folder.
     *
     * Widget files are loaded via PHP include; nothing inside should ever be
     * requested by URL. Covers Apache (.htaccess) and IIS (web.config); Nginx
     * needs a server-level rule.
     */
    private static function protect_upload_dir() {
        $upload   = wp_upload_dir();
        $root_dir = trailingslashit( $upload['basedir'] ) . strtok( self::MUIA_UPLOAD_FOLDER, '/' );

        if ( ! is_dir( $root_dir ) ) {
            return;
        }

        // Block direct execution of PHP only — css/js/image assets must stay
        // reachable because Elementor enqueues them by URL from this folder.
        $htaccess = $root_dir . '/.htaccess';
        $rules    = "# Deny direct web access to Themeic widget PHP files.\n";
        $rules   .= "<FilesMatch \"\\.php$\">\n";
        $rules   .= "\t<IfModule mod_authz_core.c>\n\t\tRequire all denied\n\t</IfModule>\n";
        $rules   .= "\t<IfModule !mod_authz_core.c>\n\t\tOrder deny,allow\n\t\tDeny from all\n\t</IfModule>\n";
        $rules   .= "</FilesMatch>\n";
        if ( ! file_exists( $htaccess ) || file_get_contents( $htaccess ) !== $rules ) {
            @file_put_contents( $htaccess, $rules );
        }

        $web_config = $root_dir . '/web.config';
        $rules      = "<?xml version=\"1.0\"?>\n<configuration>\n\t<system.webServer>\n";
        $rules     .= "\t\t<security>\n\t\t\t<requestFiltering>\n\t\t\t\t<fileExtensions>\n";
        $rules     .= "\t\t\t\t\t<add fileExtension=\".php\" allowed=\"false\" />\n";
        $rules     .= "\t\t\t\t</fileExtensions>\n\t\t\t</requestFiltering>\n\t\t</security>\n";
        $rules     .= "\t</system.webServer>\n</configuration>\n";
        if ( ! file_exists( $web_config ) || file_get_contents( $web_config ) !== $rules ) {
            @file_put_contents( $web_config, $rules );
        }

        $index = $root_dir . '/index.php';
        if ( ! file_exists( $index ) ) {
            @file_put_contents( $index, "<?php\n// Silence is golden.\n" );
        }
    }

    /**
     * Validate the extracted zip contents before installing.
     *
     * Rejects zips with too many files or with file types outside the
     * allowlist (blocks .htaccess overrides, shell scripts, nested zips, etc).
     *
     * @param  string $dir Extracted temp directory.
     * @return string|null Error status code, or null when valid.
     */
    private static function validate_contents( $dir ) {

        $allowed    = self::get_allowed_extensions();
        $file_count = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $dir, \FilesystemIterator::SKIP_DOTS )
        );

        foreach ( $iterator as $item ) {

            if ( ! $item->isFile() ) {
                continue;
            }

            $file_count++;
            if ( $file_count > self::MUIA_MAX_FILES ) {
                return 'too_many_files';
            }

            $extension = strtolower( $item->getExtension() );

            if ( '' === $extension || ! in_array( $extension, $allowed, true ) ) {
                return 'invalid_contents';
            }
        }

        return null;
    }

    /**
     * Flatten $_FILES into a list of uploaded files.
     *
     * Accepts both shapes so the form works with or without the [] suffix
     * on the field name.
     *
     * @return array[] [ [ 'name' => ..., 'tmp_name' => ..., 'size' => ... ], ... ]
     */
    private static function get_uploaded_files() {

        if ( empty( $_FILES['muia_widget_zip'] ) ) {
            return [];
        }

        $input = $_FILES['muia_widget_zip'];
        $files = [];

        // Multiple upload: every key holds an array.
        if ( is_array( $input['name'] ) ) {

            foreach ( array_keys( $input['name'] ) as $index ) {

                if ( empty( $input['name'][ $index ] ) || ! is_uploaded_file( $input['tmp_name'][ $index ] ) ) {
                    continue;
                }

                $files[] = [
                    'name'     => $input['name'][ $index ],
                    'tmp_name' => $input['tmp_name'][ $index ],
                    'size'     => isset( $input['size'][ $index ] ) ? (int) $input['size'][ $index ] : 0,
                ];
            }

            return $files;
        }

        if ( ! empty( $input['name'] ) && is_uploaded_file( $input['tmp_name'] ) ) {
            $files[] = [
                'name'     => $input['name'],
                'tmp_name' => $input['tmp_name'],
                'size'     => isset( $input['size'] ) ? (int) $input['size'] : 0,
            ];
        }

        return $files;
    }

    /**
     * Handle the zip upload(s) from the dashboard (admin-post.php).
     *
     * Any number of zips can be sent at once; each is installed independently
     * so one bad file does not discard the rest.
     */
    public static function handle_upload() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to upload widgets.', 'motionui-addons-for-elementor' ) );
        }

        check_admin_referer( self::MUIA_UPLOAD_NONCE, 'muia_custom_widget_nonce' );

        $files = self::get_uploaded_files();

        if ( empty( $files ) ) {
            self::redirect_back( 'no_file' );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();

        $first_error = '';

        foreach ( $files as $file ) {

            $status = self::install_widget_zip( $file );

            // Report the first problem, but keep installing the rest.
            if ( 'success' !== $status && ! $first_error ) {
                $first_error = $status;
            }
        }

        self::redirect_back( $first_error ? $first_error : 'success' );
    }

    /**
     * Validate and install one uploaded zip.
     *
     * @param  array $file [ 'name', 'tmp_name', 'size' ]
     * @return string Status code, 'success' when installed.
     */
    private static function install_widget_zip( $file ) {

        global $wp_filesystem;

        $file_name = sanitize_file_name( $file['name'] );
        $file_type = wp_check_filetype( $file_name, [ 'zip' => 'application/zip' ] );

        if ( 'zip' !== $file_type['ext'] ) {
            return 'invalid_type';
        }

        $file_size = ! empty( $file['size'] ) ? (int) $file['size'] : (int) filesize( $file['tmp_name'] );
        if ( $file_size > self::MUIA_MAX_ZIP_SIZE ) {
            return 'too_large';
        }

        $base_dir = self::get_upload_dir();

        if ( ! wp_mkdir_p( $base_dir ) ) {
            return 'mkdir_failed';
        }

        self::protect_upload_dir();

        // Extract to a temp folder first so we can validate before installing.
        $temp_dir = $base_dir . '/tmp-' . wp_generate_password( 8, false );
        $unzipped = unzip_file( $file['tmp_name'], $temp_dir );

        if ( is_wp_error( $unzipped ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            return 'unzip_failed';
        }

        // Reject zips containing unexpected file types or too many files.
        $contents_error = self::validate_contents( $temp_dir );
        if ( $contents_error ) {
            $wp_filesystem->delete( $temp_dir, true );
            return $contents_error;
        }

        // The zip may contain the widget folder itself, or the widget files at its root.
        $entries = array_values( array_diff( scandir( $temp_dir ), [ '.', '..' ] ) );

        if ( 1 === count( $entries ) && is_dir( $temp_dir . '/' . $entries[0] ) ) {
            $source = $temp_dir . '/' . $entries[0];
            $slug   = sanitize_key( $entries[0] );
        } else {
            $source = $temp_dir;
            $slug   = sanitize_key( pathinfo( $file_name, PATHINFO_FILENAME ) );
        }

        if ( empty( $slug ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            return 'invalid_name';
        }

        // A valid Themeic widget folder must contain themeic-widget.php.
        if ( ! file_exists( $source . '/themeic-widget.php' ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            return 'missing_index';
        }

        $destination = $base_dir . '/' . $slug;

        // Replace an existing copy of the same widget.
        if ( is_dir( $destination ) ) {
            $wp_filesystem->delete( $destination, true );
        }

        $moved = $wp_filesystem->move( $source, $destination, true );

        // Clean up the temp folder (no-op when $source === $temp_dir and move succeeded).
        $wp_filesystem->delete( $temp_dir, true );

        return $moved ? 'success' : 'move_failed';
    }

    /**
     * Delete an uploaded custom widget folder (AJAX).
     */
    public static function delete_widget() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'motionui-addons-for-elementor' ) );
        }

        if ( ! check_ajax_referer( Dashboard::MUIA_NONCE, 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid Nonce', 'motionui-addons-for-elementor' ) );
        }

        $widget_slug = isset( $_POST['widget'] ) ? sanitize_key( wp_unslash( $_POST['widget'] ) ) : '';
        $widgets     = self::get_custom_widgets();

        // Only folders known as installed widgets can be deleted.
        if ( empty( $widget_slug ) || ! isset( $widgets[ $widget_slug ] ) ) {
            wp_send_json_error( __( 'Widget not found.', 'motionui-addons-for-elementor' ) );
        }

        global $wp_filesystem;
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();

        if ( ! $wp_filesystem->delete( $widgets[ $widget_slug ], true ) ) {
            wp_send_json_error( __( 'The widget could not be deleted. Please try again.', 'motionui-addons-for-elementor' ) );
        }

        wp_send_json_success( [ 'widget' => $widget_slug ] );
    }

    /**
     * Redirect back to the Custom Widgets tab with a status code.
     */
    private static function redirect_back( $status ) {
        $url = admin_url( 'admin.php?page=' . Motionui::get_admin_menu_slug() );

        // Only errors carry a status back; success redirects to a clean URL.
        if ( 'success' !== $status ) {
            $url = add_query_arg( 'muia_upload', $status, $url );
        }

        wp_safe_redirect( $url . '#muia-custom-widgets' );
        exit;
    }

    /**
     * Generic "official Themeic widgets only" message with a linked brand name.
     */
    private static function get_official_widget_message() {
        return sprintf(
            /* translators: %s: linked "Themeic" brand name */
            __( 'Upload a widget purchased from %s as a zip file. Only official Themeic widgets are supported.', 'motionui-addons-for-elementor' ),
            '<a href="https://themeic.com/" target="_blank" rel="noopener noreferrer">Themeic</a>'
        );
    }

    /**
     * Status code => user-facing message map for the dashboard notice.
     */
    public static function get_status_messages() {
        $official_message = self::get_official_widget_message();

        return [
            'no_file'       => [ 'error', __( 'No file was uploaded. Please choose a zip file.', 'motionui-addons-for-elementor' ) ],
            'invalid_type'  => [ 'error', $official_message ],
            'invalid_name'  => [ 'error', __( 'Could not determine a valid widget folder name.', 'motionui-addons-for-elementor' ) ],
            'mkdir_failed'  => [ 'error', __( 'Could not create the widgets folder in uploads.', 'motionui-addons-for-elementor' ) ],
            'unzip_failed'  => [ 'error', __( 'The zip file could not be extracted.', 'motionui-addons-for-elementor' ) ],
            'too_large'     => [ 'error', sprintf(
                /* translators: %s: maximum file size, e.g. 5 MB */
                __( 'The file is too large. The maximum allowed size is %s.', 'motionui-addons-for-elementor' ),
                size_format( self::MUIA_MAX_ZIP_SIZE )
            ) ],
            'too_many_files'   => [ 'error', $official_message ],
            'invalid_contents' => [ 'error', $official_message ],
            'missing_index' => [ 'error', $official_message ],
            'move_failed'   => [ 'error', __( 'The widget could not be installed. Please try again.', 'motionui-addons-for-elementor' ) ],
        ];
    }
}
