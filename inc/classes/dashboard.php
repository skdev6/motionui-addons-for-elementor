<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Dashboard{

    const MUIA_NONCE = 'muia-dashboard-save-data';

    public static function add_menu(){ 

        $page_slug = Motionui::get_admin_menu_slug();
        $menu_name = Motionui::get_admin_name();

        add_menu_page(
            $menu_name,
            $menu_name,
            'manage_options',
            $page_slug,
            [__CLASS__, "init_page"],
            THEMEIC_MUIA_ASSETS . "/img/motionui-logo-white.svg",
            60
        );
        add_submenu_page(
            $page_slug,
            esc_html__("Settings", 'motionui-addons-for-elementor'),
            esc_html__("Settings", 'motionui-addons-for-elementor'),
            "manage_options",
            $page_slug,
            [__CLASS__, "init_page"]
        );
    }
    public static function enqueue_scripts($hook){
        $screen = get_admin_page_parent();
        $page_slug = Motionui::get_admin_menu_slug();


        if (Motionui::is_motion_admin_page($hook)) {
            wp_enqueue_style(
                'themeic-das-motionui', 
                THEMEIC_MUIA_ASSETS . 'css/themeic-dasboard.min.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            wp_enqueue_style(
                'th-icon-basic', 
                THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            
            wp_enqueue_script('muia-dashboard', THEMEIC_MUIA_ASSETS . 'js/dashboard.js', ['jquery'], THEMEIC_MUIA_VERSION, true);

            wp_localize_script(
                'muia-dashboard',
                'muiaDashboard',
                [
                    'nonce' => wp_create_nonce(self::MUIA_NONCE),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'action' => 'muia_dashboard',
                    'saveChangesLabel' => esc_html__( 'Save Settings', 'motionui-addons-for-elementor' ),
                    'savedLabel' => esc_html__( 'Changes Saved', 'motionui-addons-for-elementor' ),
                ]
            );
        }
        wp_enqueue_style(
            'themeic-das-main', 
            THEMEIC_MUIA_ASSETS . 'css/style.css', 
            null, 
            THEMEIC_MUIA_VERSION
        );

    }
    public static function init_page(){

        $dasboard_file = THEMEIC_MUIA_DIR_PATH . 'templates/dashboard.php';

        if(is_readable($dasboard_file)){
            include_once $dasboard_file;
        }

    }
    public static function save_widgets_data($data) {

        $widgets_to_remove = !empty($data['widgets']) ? (array) $data['widgets'] : [];
        $real_map = Widgets_Manager::local_widgets_map();
        $filtered_map = array_diff_key($real_map, array_flip($widgets_to_remove));

        Widgets_Manager::save_widgets(array_keys($filtered_map));    

    }
    public static function save_extensions_data($data){

        $extensions_to_remove = !empty($data['extensions']) ? $data['extensions'] : [];
        $real_map = Extensions_Manager::local_extensions_map();
        $filtered_map = array_diff_key($real_map, array_flip($extensions_to_remove));

        Extensions_Manager::save_extensions(array_keys($filtered_map));

    }
    public static function save_data(){  
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        if ( ! check_ajax_referer(self::MUIA_NONCE, 'nonce' ) ) {
            wp_send_json_error( 'Invalid Nonce' );
        }

        $raw_data = isset($_POST['data']) ? $_POST['data'] : '';
        $type     = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

        $parsed_data = [];
        parse_str( $raw_data, $parsed_data );

        $final_data = muia_sanitize_array_recursively($parsed_data);
        $saved_all  = false;

        if($type === 'widgets'){
            self::save_widgets_data($final_data);
        }
        if($type === 'extensions'){
            self::save_extensions_data($final_data);
        }
        wp_send_json_success(array(  
            'message' => __( 'Settings saved successfully!', 'motionui-addons-for-elementor' ),
            'type'    => $type,
        ));
    }
    public static function switch_card($muia_widgets_map){

		foreach ( $muia_widgets_map as $muia_widget_slug => $muia_widget ) :

			// Sanitize slug used in attributes and IDs.
			$muia_widget_slug = sanitize_key( $muia_widget_slug );

			// Bail if slug is empty after sanitization.
			if ( empty( $muia_widget_slug ) ) {
				continue;
			}

			// Safely extract and sanitize individual widget fields.
			$muia_title      = isset( $muia_widget['title'] )     && is_string( $muia_widget['title'] )    ? $muia_widget['title']              : '';
			$muia_category   = '';
			$muia_icon       = isset( $muia_widget['icon'] )      && is_string( $muia_widget['icon'] )     ? $muia_widget['icon']               : '';
			$muia_demo_url   = isset( $muia_widget['demo'] )      && is_string( $muia_widget['demo'] )     ? $muia_widget['demo']               : '';
			$muia_tutorial   = isset( $muia_widget['tutorial'] )  && is_string( $muia_widget['tutorial'] ) ? $muia_widget['tutorial']           : '';
			$muia_is_active  = isset( $muia_widget['is_active'] ) ? (bool) $muia_widget['is_active']       : false;
			$muia_is_pro     = isset( $muia_widget['is_pro'] )    ? (bool) $muia_widget['is_pro']          : false;
			$muia_upcoming   = isset( $muia_widget['is_upcoming'] ) ? (bool) $muia_widget['is_upcoming']   : false;
            $is_active_pro = Motionui::is_active_pro();
            $is_lock = $muia_is_pro && ! $is_active_pro;
            
            if ( isset( $muia_widget['category'] ) ) {

                // If category is array.
                if ( is_array( $muia_widget['category'] ) ) {

                    $muia_category = implode(
                        ' ',
                        array_map(
                            function( $category ) {
                                return 'muia-cat-' . sanitize_key( $category );
                            },
                            $muia_widget['category']
                        )
                    );

                // If category is string.
                } elseif ( is_string( $muia_widget['category'] ) ) {

                    $muia_category = 'muia-cat-' . sanitize_key( $muia_widget['category'] );
                }
            }

            if(!$is_active_pro && $muia_is_pro){
                $muia_is_active = false;
            }
			// Sanitize and validate demo/tutorial URLs — only allow http/https or empty.
			$muia_demo_url = ! empty( $muia_demo_url ) ? esc_url( $muia_demo_url ) : '';
			$muia_tutorial = ! empty( $muia_tutorial ) ? esc_url( $muia_tutorial ) : '';

			// Build CSS classes for the card (used by JS isotope/filter).
			$muia_card_classes = array( 'th-widget-card', $muia_widget_slug );
			if ( ! empty( $muia_category ) ) {
				$muia_card_classes[] = $muia_category;
			}
			if ( $muia_is_pro ) {
				$muia_card_classes[] = 'is-pro';
			}
			if ( $muia_upcoming ) {
				$muia_card_classes[] = 'is-upcoming';
			}
			if ( $is_lock ) {
				$muia_card_classes[] = 'not-active-pro';
			}
		?>

		<div class="<?php echo esc_attr( implode( ' ', $muia_card_classes ) ); ?>">

			<div class="icon-wrap" aria-hidden="true">
				<?php if ( ! empty( $muia_icon ) ) : ?>
					<i class="<?php echo esc_attr( $muia_icon ); ?>"></i>
				<?php endif; ?>
			</div>
            <?php if ($is_lock) : ?>
                <span class="muia-badge muia-badge-pro">
                    <?php esc_html_e( 'Pro', 'motionui-addons-for-elementor' ); ?>
                </span>
            <?php endif; ?>
			<div class="card-con">

				<h4 class="title">
					<?php echo esc_html( $muia_title ); ?>

					<?php if ( $muia_upcoming ) : ?>
						<span class="muia-badge muia-badge-upcoming">
							<?php esc_html_e( 'Upcoming', 'motionui-addons-for-elementor' ); ?>
						</span>
					<?php endif; ?>
				</h4>
				<?php if(! empty( $muia_demo_url ) || ! empty( $muia_tutorial )): ?>
				<div class="gap-2 d-flex align-items-center">

					<?php if ( ! empty( $muia_demo_url ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_demo_url ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: widget title */
								printf( esc_attr__( 'View demo for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-link" aria-hidden="true"></i>
							<?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $muia_tutorial ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_tutorial ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: widget title */
								printf( esc_attr__( 'Watch tutorial for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-video" aria-hidden="true"></i>
							<?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php endif; ?>

				</div>
				<?php endif; ?>
			</div><!-- .card-con -->

			<div class="th-switch-control d-flex align-items-center ml-auto">
				<input
					type="checkbox"
					id="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"
					name="widgets[]"
					value="<?php echo esc_attr( $muia_widget_slug ); ?>"
					<?php checked( $muia_is_active, true ); ?>
					<?php disabled( $muia_upcoming || $is_lock, true ); ?>
					aria-label="<?php
						/* translators: %s: widget title */
						printf( esc_attr__( 'Toggle %s widget', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
					?>"
				/>
                <?php echo $is_lock ? '<i class="pro-icon eicon-upgrade-crown"></i>' : ''; ?>
				<label
					class="switch-label"
					for="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"
					aria-hidden="true"
				>
            </label>
			</div><!-- .th-switch-control -->

		</div><!-- .th-widget-card -->

		<?php endforeach;
    }
    public static function pro_html( $upgrade_url = 'https://motionuiaddons.com/' ) {
        ?>
        <div class="muia-popup-wrap muia-pro-popup-wrap">
        <div class="backdrop"></div>
        <div class="muia-pro-card">
            <div class="muia-close-btn eicon-close"></div>
            <div class="muia-pro-crown-wrap">
                <i class="eicon-upgrade-crown" aria-hidden="true"></i>
            </div>
            <h2><?php esc_html_e( 'Unlock the MotionUI Addons PRO Features to Elevate every experience', 'motionui-addons-for-elementor' ); ?></h2>
            <p><?php esc_html_e( 'Upgrade to MotionUI PRO and gain access to advanced elements and functionalities to build websites more efficiently.', 'motionui-addons-for-elementor' ); ?></p>
            <ul class="muia-pro-features">
                <li>
                    <i class="eicon-check-circle" aria-hidden="true"></i>
                    <span><?php esc_html_e( 'Customization flexibility in design with premium creative elements.', 'motionui-addons-for-elementor' ); ?></span>
                </li>
                <li>
                    <i class="eicon-check-circle" aria-hidden="true"></i>
                    <span><?php esc_html_e( 'Advanced animation effects like Reveal Random, Symbolab & more.', 'motionui-addons-for-elementor' ); ?></span>
                </li>
                <li>
                    <i class="eicon-check-circle" aria-hidden="true"></i>
                    <span><?php esc_html_e( 'Cutting-edge extensions like custom JS, content protection & more.', 'motionui-addons-for-elementor' ); ?></span>
                </li>
            </ul>
            <div class="muia-pro-divider"></div>
            <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="muia-btn-pro">
                <i class="eicon-upgrade-crown" aria-hidden="true"></i>
                <?php esc_html_e( 'Upgrade to PRO', 'motionui-addons-for-elementor' ); ?>
            </a>
        </div>
        </div>
        <?php
    }
    public static function delete_html() {
        ?>
        <div class="muia-popup-wrap muia-delete-popup-wrap">
        <div class="backdrop"></div>
        <div class="muia-pro-card">
            <div class="muia-close-btn eicon-close"></div>
            <div class="muia-pro-crown-wrap muia-delete-icon-wrap">
                <i class="eicon-library-delete" aria-hidden="true"></i>
            </div>
            <h2><?php esc_html_e( 'Delete this widget?', 'motionui-addons-for-elementor' ); ?></h2>
            <p>
                <?php esc_html_e( 'You are about to delete', 'motionui-addons-for-elementor' ); ?>
                <strong class="muia-delete-widget-name"></strong>.
                <?php esc_html_e( 'Its files will be removed from your site and any page using this widget will stop rendering it. This cannot be undone.', 'motionui-addons-for-elementor' ); ?>
            </p>
            <div class="muia-pro-divider"></div>
            <div class="d-flex gap-2 align-items-center justify-content-center">
                <button type="button" class="th-das-btn btn-sm muia-cancel-delete">
                    <?php esc_html_e( 'Cancel', 'motionui-addons-for-elementor' ); ?>
                </button>
                <button type="button" class="th-das-btn btn-sm muia-confirm-delete">
                    <?php esc_html_e( 'Delete Widget', 'motionui-addons-for-elementor' ); ?>
                </button>
            </div>
        </div>
        </div>
        <?php
    }
    public static function is_all_active_switch( $muia_widgets_map ) {
        $muia_all_active = ! empty( $muia_widgets_map ) && ! array_filter(
            $muia_widgets_map,
            function( $muia_widget ) {
                return empty( $muia_widget['is_active'] );
            }
        );
        return $muia_all_active;
    }
    public static function get_unique_categories( $muia_widgets_map ) { 

        $muia_categories = array();

        foreach ( $muia_widgets_map as $muia_widget ) {

            if ( empty( $muia_widget['category'] ) ) {
                continue;
            }

            $categories = $muia_widget['category'];

            // Convert single string to array.
            if ( is_string( $categories ) ) {
                $categories = array( $categories );
            }

            // Skip invalid values.
            if ( ! is_array( $categories ) ) {
                continue;
            }

            foreach ( $categories as $category ) {

                if ( empty( $category ) || ! is_string( $category ) ) {
                    continue;
                }

                $category_key = sanitize_key( $category );

                if ( ! isset( $muia_categories[ $category_key ] ) ) {
                    $muia_categories[ $category_key ] = ucfirst( $category_key );
                }
            }
        }

        return $muia_categories;
    } 
}