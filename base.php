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
        // Include Essential Files
        $this->autoload();
        $this->init();
    }
    /**
     * Initialize Plugin Hooks
     * * Registers actions specifically for Elementor categories and controls.
     */
    public function init(){     
        // Elementor Hooks
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
       // Admin Menu and Scripts
        add_action( 'admin_menu', [ MotionUiClasses\Dashboard::class, 'add_menu' ] );
        add_action('admin_enqueue_scripts', [MotionUiClasses\Dashboard::class, 'enqueue_scripts']);
        // Frontend Scripts 
        add_action('elementor/frontend/after_register_scripts', [MotionUiClasses\Assets::class, 'register_scripts']);
        add_action('elementor/frontend/after_register_scripts', [MotionUiClasses\Assets::class, 'enqueue_scripts']);   
        add_action('elementor/frontend/after_enqueue_styles', [MotionUiClasses\Assets::class, 'enqueue_styles']);    
        // Register Widgets
        add_action( 'elementor/widgets/widgets_registered', [ MotionUiClasses\Widgets_Manager::class, 'register_widgets'] );
        add_action( 'wp_ajax_muia_dashboard', [ MotionUiClasses\Dashboard::class, 'save_data' ] );
        add_action( 'elementor/init', [ MotionUiClasses\Extensions_Manager::class, 'init' ] );
    }

    /**
     * Add Elementor Category
     * * Creates a custom group in the Elementor widget panel for GSAP Addons.
     * * @param \Elementor\Elements_Manager $manager
     */
    public function add_category($manager){
        $manager->add_category(
            'motionui_addons',
            [
                'title' => __( 'MotionUi Addons', 'motionui-addons-for-elementor' ),
                'icon' => 'th-gsap-addons',
            ]
        );
    }

    /**
     * Hook Manager
     * * Centralized place to manage various WordPress hooks like admin menus.
     */
    public function hook_manager(){    
 
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
        // Targeted include for extensions inside the inc/classes folder
        if(strpos($class_name, 'Themeic\MotionUI_Addons\Inc\Extensions') === 0){
            $file_dir = THEMEIC_MUIA_DIR_PATH . '/' . $file_name . '.php';
            if(!class_exists($class_name) && is_readable($file_dir)){
                include_once $file_dir;
            }
        }
        // Targeted include for widgets inside the widgets folder
        if(strpos($class_name, 'Themeic\MotionUI_Addons\Widgets') === 0){
            $file_dir = THEMEIC_MUIA_DIR_PATH . '/' . $file_name . '.php';
            if(!class_exists($class_name) && is_readable($file_dir)){
                include_once $file_dir;
            }
        }
        // Targeted include for traits inside the traits folder
        if(strpos($class_name, 'Themeic\MotionUI_Addons\Traits') === 0){
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
        include_once THEMEIC_MUIA_DIR_PATH . 'inc/functions.php';
        spl_autoload_register([$this, 'include_class_files']);
    }
}

// Kickstart the plugin
Base::instance();