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
                $widgets[ basename( $dir ) ] = $dir;
            }
        }

        return $widgets;
    }

    /**
     * Register uploaded Themeic widgets with Elementor.
     *
     * Folder name maps to the class name inside the Themeic\CustomWidget
     * namespace, e.g. themeic-minimal-button-widget =>
     * \Themeic\CustomWidget\Themeic_Minimal_Button_Widget.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public static function register_widgets( $widgets_manager = null ) {

        if ( ! $widgets_manager ) {
            return;
        }

        foreach ( self::get_custom_widgets() as $widget_slug => $widget_dir ) {

            $class_name = '\Themeic\CustomWidget\\' . str_replace( '-', '_', ucwords( $widget_slug, '-' ) );

            // class_exists() triggers the plugin autoloader (see Base::include_class_files).
            if ( class_exists( $class_name ) ) {
                $widgets_manager->register( new $class_name() );
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

        $htaccess = $root_dir . '/.htaccess';
        if ( ! file_exists( $htaccess ) ) {
            $rules  = "# Deny direct web access to Themeic widget files.\n";
            $rules .= "<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n";
            $rules .= "<IfModule !mod_authz_core.c>\n\tOrder deny,allow\n\tDeny from all\n</IfModule>\n";
            @file_put_contents( $htaccess, $rules );
        }

        $web_config = $root_dir . '/web.config';
        if ( ! file_exists( $web_config ) ) {
            $rules  = "<?xml version=\"1.0\"?>\n<configuration>\n\t<system.webServer>\n";
            $rules .= "\t\t<authorization>\n\t\t\t<deny users=\"*\" />\n\t\t</authorization>\n";
            $rules .= "\t</system.webServer>\n</configuration>\n";
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
     * Handle the zip upload from the dashboard (admin-post.php).
     */
    public static function handle_upload() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to upload widgets.', 'motionui-addons-for-elementor' ) );
        }

        check_admin_referer( self::MUIA_UPLOAD_NONCE, 'muia_custom_widget_nonce' );

        if ( empty( $_FILES['muia_widget_zip'] ) || ! is_uploaded_file( $_FILES['muia_widget_zip']['tmp_name'] ) ) {
            self::redirect_back( 'no_file' );
        }

        $file      = $_FILES['muia_widget_zip'];
        $file_name = sanitize_file_name( $file['name'] );
        $file_type = wp_check_filetype( $file_name, [ 'zip' => 'application/zip' ] );

        if ( 'zip' !== $file_type['ext'] ) {
            self::redirect_back( 'invalid_type' );
        }

        $file_size = ! empty( $file['size'] ) ? (int) $file['size'] : (int) filesize( $file['tmp_name'] );
        if ( $file_size > self::MUIA_MAX_ZIP_SIZE ) {
            self::redirect_back( 'too_large' );
        }

        global $wp_filesystem;
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();

        $base_dir = self::get_upload_dir();

        if ( ! wp_mkdir_p( $base_dir ) ) {
            self::redirect_back( 'mkdir_failed' );
        }

        self::protect_upload_dir();

        // Extract to a temp folder first so we can validate before installing.
        $temp_dir = $base_dir . '/tmp-' . wp_generate_password( 8, false );
        $unzipped = unzip_file( $file['tmp_name'], $temp_dir );

        if ( is_wp_error( $unzipped ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            self::redirect_back( 'unzip_failed' );
        }

        // Reject zips containing unexpected file types or too many files.
        $contents_error = self::validate_contents( $temp_dir );
        if ( $contents_error ) {
            $wp_filesystem->delete( $temp_dir, true );
            self::redirect_back( $contents_error );
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
            self::redirect_back( 'invalid_name' );
        }

        // A valid Themeic widget folder must contain themeic-widget.php.
        if ( ! file_exists( $source . '/themeic-widget.php' ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            self::redirect_back( 'missing_index' );
        }

        $destination = $base_dir . '/' . $slug;

        // Replace an existing copy of the same widget.
        if ( is_dir( $destination ) ) {
            $wp_filesystem->delete( $destination, true );
        }

        $moved = $wp_filesystem->move( $source, $destination, true );

        // Clean up the temp folder (no-op when $source === $temp_dir and move succeeded).
        $wp_filesystem->delete( $temp_dir, true );

        if ( ! $moved ) {
            self::redirect_back( 'move_failed' );
        }

        self::redirect_back( 'success' );
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
