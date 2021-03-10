<?php
/**
 * Plugin Name: Easy Yandex Metrica
 * Plugin URI:  https://ab-wp.com/plugins/easy-yandex-metrica/
 * Description: Easily add statistics display Yandex.Metrica to the Wordpress admin panel.
 * Version:     1.0.6
 * Author:      AB-WP
 * Author URI:  https://ab-wp.com/
 * Text Domain: easy-yandex-metrica
 * Domain Path: /languages
 * Requires at least: 3.9
 * Tested up to: 5.7
 * License: GPLv2 (or later)
**/
if ( !class_exists( 'ABWP_easy_yandex_metrica' ) ) {
	class ABWP_easy_yandex_metrica
	{
		
		public function __construct()
		{
			if ( is_admin() ) { // admin actions
				$this->load_dependencies();
				$this->define_admin_hooks();
				//$this->admin_scripts();
			}
		}

		private function load_dependencies() 
		{
			require_once plugin_dir_path( __FILE__ ) . 'includes/admin-metrica.php';
		}

		private function define_admin_hooks() 
		{
			add_action('plugins_loaded', array($this,'load_plugin_textdomain'));
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
				'administrator', 
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
				'administrator', 
				'abwp_eym', 
				array( $admin_metrica, 'view' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Summary', 'easy-yandex-metrica' ), 
				__( 'Sources, Summary', 'easy-yandex-metrica' ), 
				'administrator', 
				'abwp_eym_view_sources_summary', 
				array( $admin_metrica, 'view_data_sources' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Search engine', 'easy-yandex-metrica' ), 
				__( 'Sources, Search engine', 'easy-yandex-metrica' ), 
				'administrator', 
				'abwp_eym_view_sources_engines', 
				array( $admin_metrica, 'view_data_sources_engines' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Sites', 'easy-yandex-metrica' ), 
				__( 'Sources, Sites', 'easy-yandex-metrica' ), 
				'administrator', 
				'abwp_eym_view_sources_sites', 
				array( $admin_metrica, 'view_data_sources_sites' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Sources, Social Network', 'easy-yandex-metrica' ), 
				__( 'Sources, Social Network', 'easy-yandex-metrica' ), 
				'administrator', 
				'abwp_eym_view_sources_social', 
				array( $admin_metrica, 'view_data_sources_social' ));
			add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
			$page = add_submenu_page(
				'abwp_eym', 
				__( 'Settings', 'easy-yandex-metrica' ), 
				__( 'Settings', 'easy-yandex-metrica' ), 
				'administrator', 
				'abwp_eym_settings', 
				array( $admin_metrica, 'view_settings' ));
			//add_action( 'load-' . $page, array($this, 'admin_scripts'));
			//***
		}


        public function admin_scripts()
		{
            wp_enqueue_script( 'chart', plugins_url( 'js/Chart.min.js', __FILE__ ) );
		}

		public function admin_init()
		{
            register_setting( 'abwp-eym-options-group', 'abwp_eym_token');
            register_setting( 'abwp-eym-options-group', 'abwp_eym_counter_id');
		}
	}

	$ABWP_simple_counter = new ABWP_easy_yandex_metrica();
}