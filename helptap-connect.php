<?php
/**
 * @version 1.0
 */
/*
Plugin Name: HelpTap Connect
Plugin URI: http://wordpress.org/plugins/helptap-connect/
Description: WP HelpTap Connect helps you automatically add your tapname widget on your WordPress websites.
Author: helptap
Version: 1.0
Author URI: https://helptap.com
*/

if(!class_exists('WP_HelpTap_Connect')){

    class WP_HelpTap_Connect {
        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions
			// Initialize
			add_action( 'admin_init', array($this, 'admin_init') );
			add_action( 'admin_menu', array($this, 'add_menu') );
			add_filter( 'wp_footer', array($this, 'create_helptap_connect_view') );
        } 
    
        /**
         * Activate the plugin
         */
        public static function activate() {
            // Do nothing
        } 
    
        /**
         * Deactivate the plugin
         */     
        public static function deactivate() {
            delete_option( 'helptap_options' );
        } 

		/**
		 * hook into WP's admin_init action hook
		 */
        public function admin_init() {
    		// Set up the settings for this plugin
    		$this->init_settings();
    		// Possibly do additional admin_init tasks
		}

		/**
		 * Initialize some custom settings
		 */     
		public function init_settings() {
		    // register the settings for this plugin
		    register_setting( 'wp_helptap_connect-group', 'helptap_options', array( $this, 'save_tapname_and_image' ) );
		    // register_setting('wp_helptap_connect-group', 'tap_full_snippet');
		}

		/**
		 * add a menu
		 */     
		public function add_menu() {
		    add_options_page(
		    	'WP HelpTap Connect Settings',
		    	'WP HelpTap Connect',
		    	'manage_options',
		    	'wp_helptap_connect',
		    	array(
		    		$this,
		    		'helptap_connect_settings_page'
	    		)
    		);
		}

		/**
		 * Menu Callback
		 */     
		public function helptap_connect_settings_page() {

		    if( !current_user_can('manage_options') ) {
		        wp_die( __( 'You do not have sufficient permissions to access this page.' ));
		    }

		    // Render the settings template
		    include( sprintf( "%s/templates/settings.php", dirname(__FILE__) ) );
		}

		public function save_tapname_and_image( $input ) {

			$tapname = $input['tapname'];
			$placement = $input['placement'];
			$args = array( 
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
				),
				'body' => 'username=' . $tapname
			);
			$response = wp_remote_post( 'https://api.helptap.com/api/other/username/yourProfile', $args );
			$body = wp_remote_retrieve_body( $response );
			$body = json_decode( $body, true );

			if( $body['result'] ) {
				$tap_image = get_option( 'helptap_options' )['tap_image'];
				$tap_image = $body['result']['profileImage'];
				$update_array = array(
					'tap_image' => $tap_image,
					'tapname' => $tapname,
					'placement' => $placement
				); 
				// add_settings_error('helptap_print_notice_identifier', esc_attr('settings_updated'),__('Tapname saved'), 'updated');
				// add_action('admin_notices', array( $this, 'print_notices'));
				return $update_array;
			} else {
				add_settings_error( 'helptap_print_notice_identifier', esc_attr( 'settings_updated' ), __( $body['error']['errorMessage'] ), 'error' );
		        add_action( 'admin_notices', array( $this, 'print_notices' ) );
		        return null;
			}
		}

		public function print_notices() {
		    settings_errors( 'helptap_print_notice_identifier' );
		}

		public function create_helptap_connect_view() {

			if( !is_admin() ) {
				$helptap_options = get_option( 'helptap_options' );
				if( is_array( $helptap_options ) ) {
					$tapname = $helptap_options['tapname'];
					$placement = $helptap_options['placement'];
					$tap_image = $helptap_options['tap_image'];
					$content = '<div id="wp_helptap_connect" style="position:fixed; z-index: 100002; right: 20px; bottom: 20px;">' .
						'<a href="https://helptap.com/'. $tapname .'" rel="nofollow" target="_blank" ' .
							'style="background-image: url(\'https://d3ke52d0l2d5vx.cloudfront.net/frequent/web/common/snippet.svg\');background-position: center center;position: relative;height: 75px; width: 75px;display: block; background-repeat: no-repeat;">' .
							'<img src="' . $tap_image .'" style="border-radius: 50%; position: absolute; bottom: 4px;left:4px; width: 45px; height:45px;object-fit:cover; -o-object-fit: cover;">' .
						'</a>' .
						'</div>';
					echo $content;
				}
			}

		}
    }
} 

if( class_exists('WP_HelpTap_Connect') ) {
    // Installation and uninstallation hooks

    register_activation_hook( __FILE__, array( 'WP_HelpTap_Connect', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'WP_HelpTap_Connect', 'deactivate' ) );

    // instantiate the plugin class
    $wp_helptap_connect = new WP_HelpTap_Connect();

    // Add a link to the settings page onto the plugin page
	if( isset( $wp_helptap_connect ) ) {
	    // Add the settings link to the plugins page
	    function plugin_settings_link( $links ) { 
	        $settings_link = '<a href="options-general.php?page=wp_helptap_connect">Settings</a>'; 
	        array_unshift( $links, $settings_link ); 
	        return $links; 
	    }

	    $plugin = plugin_basename( __FILE__ ); 
	    add_filter( "plugin_action_links_$plugin", 'plugin_settings_link' );
	}

}


?>