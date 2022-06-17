<?php
/**
 * Plugin Name: Easy Yandex Metrica
 * Plugin URI:  https://ab-wp.com/plugins/easy-yandex-metrica/
 * Description: Easily add statistics display Yandex.Metrica to the Wordpress admin panel.
 * Version:     1.2.0
 * Author:      AB-WP
 * Author URI:  https://ab-wp.com/
 * Text Domain: easy-yandex-metrica
 * Domain Path: /languages
 * Requires at least: 3.9
 * Tested up to: 6.0
 * License: GPLv2 (or later)
**/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;


if ( !class_exists( 'ABWP_easy_yandex_metrica' ) ) {
	class ABWP_easy_yandex_metrica
	{
		
		const VERSION = '1.2';
		
		public function __construct()
		{
			//
			register_activation_hook(__FILE__, array($this, 'plugin_activation'));
			register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));
			//
			if ( is_admin() ) { // admin actions
				$this->load_dependencies();
				$this->define_admin_hooks();
				//
				$version = get_option('abwp_eym_plugin_version');
				if (self::VERSION != $version) {
					$this->plugin_update();
				}
				//
				require_once plugin_dir_path( __FILE__ ) . 'includes/dashboard.php';
				$dashboardWidget = new EasyYandexMetricaDashboard();
			}
			add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
		}

		private function load_dependencies() 
		{
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin-metrica.php';
		}

		private function define_admin_hooks() 
		{
			add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
			add_action('admin_menu', array($this, 'admin_menu'));
			add_action('admin_init', array($this, 'admin_init'));
			//add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		}

		public function load_plugin_textdomain() 
		{
			load_plugin_textdomain( 'easy-yandex-metrica', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		public function admin_menu()
		{
			$admin_metrica = new ABWP_Admin_Metrica();
			
			$page = add_menu_page(
				__( 'Yandex.Metrica', 'easy-yandex-metrica' ), 
				__( 'Yandex.Metrica', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym', 
				array( $admin_metrica, 'view' ), 
				'dashicons-chart-bar',
				3 );
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Traffic', 'easy-yandex-metrica' ), 
				__( 'Traffic', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym', 
				array( $admin_metrica, 'view' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Summary', 'easy-yandex-metrica' ), 
				__( 'Sources, Summary', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym_view_sources_summary', 
				array( $admin_metrica, 'view_data_sources' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Search engine', 'easy-yandex-metrica' ), 
				__( 'Sources, Search engine', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym_view_sources_engines', 
				array( $admin_metrica, 'view_data_sources_engines' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Sites', 'easy-yandex-metrica' ), 
				__( 'Sources, Sites', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym_view_sources_sites', 
				array( $admin_metrica, 'view_data_sources_sites' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Social Network', 'easy-yandex-metrica' ), 
				__( 'Sources, Social Network', 'easy-yandex-metrica' ), 
				'eym_view_metrica_reports', 
				'abwp_eym_view_sources_social', 
				array( $admin_metrica, 'view_data_sources_social' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Settings', 'easy-yandex-metrica' ), 
				__( 'Settings', 'easy-yandex-metrica' ), 
				'manage_options', 
				'abwp_eym_settings', 
				array( $admin_metrica, 'view_settings' ));
			//add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
		}



		public function plugin_action_links($actions, $file) 
		{		
			static $plugin;
	
			$plugin = plugin_basename( __FILE__ );
	
			if ( $file == $plugin ) {
				// put settings link at start
				array_unshift($actions, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php' ).'?page=abwp_eym_settings', __('Settings', 'easy-yandex-metrica')));
			}
	
			return $actions;
		}



        public function admin_scripts()
		{
            wp_enqueue_script( 'chart', plugins_url( 'js/Chart.min.js', __FILE__ ) );
		}

		public function admin_init()
		{
            register_setting( 'abwp-eym-options-group', 'abwp_eym_token');
            register_setting( 'abwp-eym-options-group', 'abwp_eym_counter_id');
			register_setting( 'abwp-eym-options-group', 'abwp_eym_plugin_version');
		}
		
		public function plugin_activation() 
		{
			if ( ! function_exists( 'get_editable_roles' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}
			//
			$roles = get_editable_roles();
			foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
				if (isset($roles[$key]) && $role->has_cap('manage_options')) {
					$role->add_cap('eym_view_metrica_reports');
				}
			}
			//
			update_option('abwp_eym_plugin_version', self::VERSION);
		}
		
		public function plugin_update() 
		{
			$this->plugin_activation();
		}
		
		public function plugin_deactivation() 
		{
			$roles = get_editable_roles();
			foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
				if (isset($roles[$key]) && $role->has_cap('eym_view_metrica_reports')) {
					$role->remove_cap('eym_view_metrica_reports');
				}
			}
		}
	}

	$ABWP_simple_counter = new ABWP_easy_yandex_metrica();
}