<?php 

namespace Themeic\MotionUI_Addons;

use Themeic\MotionUI_Addons\Inc\Classes as MotionUiClasses;

defined( 'ABSPATH' ) || die();

/**
 * Main Plugin Base Class
 * * Responsible for initializing the plugin, registering autoloaders,
 * and handling Elementor integrations.
 */
class Base {

    /** @var Base|null Single instance of this class */
    private static $instance = null;

    /** @var object|null Appsero SDK instance */
    public $appsero = null;

    /**
     * Singleton Instance
     *
     * Ensures only one instance of the class is loaded.
     * * @return Base
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     * * Sets up autoloading, file inclusion, and main WordPress/Elementor hooks.
     */
    private function __construct(){
        $this->autoload();
        $this->include_files();
        $this->hook_manager();

        add_action('init', [$this, 'i18n']);
        $this->init();
    }

    /**
     * Load Text Domain
     * * Makes the plugin translation-ready.
     */
    public function i18n(){
        load_plugin_textdomain('gsap-addons-for-elementor', false, dirname(THEMEIC_MUIA_FILE) . '/languages');
    }

    /**
     * Initialize Plugin Hooks
     * * Registers actions specifically for Elementor categories and controls.
     */
    public function init(){
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
        add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
    }

    /**
     * Add Elementor Category
     * * Creates a custom group in the Elementor widget panel for GSAP Addons.
     * * @param \Elementor\Elements_Manager $manager
     */
    public function add_category($manager){
        $manager->add_category(
            'gsap_addons',
            [
                'title' => __( 'MotionUi Addons', 'motionui-addons' ),
                'icon' => 'th-gsap-addons',
            ]
        );
    }

    /**
     * Hook Manager
     * * Centralized place to manage various WordPress hooks like admin menus.
     */
    public function hook_manager(){ 
        add_action( 'admin_menu', [ MotionUiClasses\Dashboard::class, 'add_menu' ] );
        add_action('admin_enqueue_scripts', [MotionUiClasses\Dashboard::class, 'enqueue_scripts']);
    }

    /**
     * Register Custom Controls
     * * Placeholder for registering custom Elementor controls.
     */
    public function register_controls(){
        // Custom control logic goes here
    }

    /**
     * Get Short Class Name
     * * Extracts the class name without the namespace.
     * * @param string $class_str Full namespaced class string.
     * @return string
     */
    public static function get_class_name($class_str) {
        $last_slash_pos = strrpos($class_str, '\\');
        if ($last_slash_pos !== false) {
            $class_name = substr($class_str, $last_slash_pos + 1);
        } else {
            $class_name = $class_str;
        }
        return $class_name;
    }

    /**
     * Include Class Files (Autoload Callback)
     * * Maps namespaced class names to physical file paths and includes them.
     * * @param string $class_name
     */
    public function include_class_files($class_name){
        // Ensure we only process classes belonging to this plugin
        if(strpos($class_name, __NAMESPACE__) !== 0){
            return;
        }
        
        $relative_class = self::get_class_name($class_name);
        
        // Convert Namespace/Class_Name to path/class-name.php format
        $file_name = strtolower(
            str_replace(
                [__NAMESPACE__ . '\\', '_', '\\'],
                ['', '-', '/'],
                $class_name
            )
        );

        // Targeted include for classes inside the inc/classes folder
        if(strpos($class_name, 'Themeic\MotionUI_Addons\Inc\Classes') === 0){
            $file_dir = THEMEIC_MUIA_DIR_PATH . '/' . $file_name . '.php';
            if(!class_exists($class_name) && is_readable($file_dir)){
                include_once $file_dir;
            }
        }
    }

    /**
     * Register Autoloader
     * * Uses spl_autoload_register to handle automatic class loading.
     */
    public function autoload(){
        spl_autoload_register([$this, 'include_class_files']);
    }

    /**
     * Include Essential Files
     * * Loads required components immediately during construction.
     */
    public function include_files(){
        
    }
}

// Kickstart the plugin
Base::instance();