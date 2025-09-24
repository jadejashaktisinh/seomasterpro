<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jadejashaktisinh.com
 * @since      1.0.0
 *
 * @package    Seomasterpro
 * @subpackage Seomasterpro/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Seomasterpro
 * @subpackage Seomasterpro/includes
 * @author     jadeja shaktisinh <jadejashakti5483@gmail.com>
 */
class Seomasterpro {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Seomasterpro_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SEOMASTERPRO_VERSION' ) ) {
			$this->version = SEOMASTERPRO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'seomasterpro';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Seomasterpro_Loader. Orchestrates the hooks of the plugin.
	 * - Seomasterpro_i18n. Defines internationalization functionality.
	 * - Seomasterpro_Admin. Defines all hooks for the admin area.
	 * - Seomasterpro_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 *
		 *  classes used responsible for defining all actions
		*/

		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-metabox-handler.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-seo-analyze-handler.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-admin-handler.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-sitemap-generator.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-schema-generator.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-technical-scan-handler.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-ai-handler.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-chart-ajax.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-socialcard-handler.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-seomasterpro-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-seomasterpro-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-seomasterpro-public.php';

		$this->loader = new Seomasterpro_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Seomasterpro_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Seomasterpro_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin       = new Seomasterpro_Admin( $this->get_plugin_name(), $this->get_version() );
		$metabox_handler    = new MetaBox_Handler();
		$ai_seo             = new AI_SEO_Analyzer();
		$admin_handler      = new Admin_Handler();
		$sitemap_generator  = new Sitemap_Generator();
		$schema_generator   = new Schema_Generator();
		$technical_scan     = new Technical_Scan_Handler();
		$chart_ajax         = new Chart_Ajax();
		$ai_handler         = new Ai_Handler();
		$socialcard_handler = new Socialcard_Handler();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'add_meta_boxes', $metabox_handler, 'regiter_meta_box' );
		$this->loader->add_action( 'save_post', $metabox_handler, 'save_post_meta' );

		$this->loader->add_action( 'wp_ajax_ai_seo_analyze', $ai_seo, 'ajax_ai_seo_analyze' );
		$this->loader->add_action( 'save_post', $ai_seo, 'save_post_ai_seo_analyze' );
		$this->loader->add_action( 'cron_ai_analyze', $ai_seo, 'analyze_post' );

		$this->loader->add_action( 'admin_menu', $admin_handler, 'register_nav_menu' );
		$this->loader->add_action( 'init', $admin_handler, 'add_custom_column' );

		$this->loader->add_action( 'init', $sitemap_generator, 'register_rewrite_rules' );
		$this->loader->add_action( 'admin_init', $sitemap_generator, 'save_sitemap_setting' );
		$this->loader->add_action( 'template_redirect', $sitemap_generator, 'serve_sitemap' );
		$this->loader->add_action( 'wp_head', $schema_generator, 'schema_generator' );

		$this->loader->add_filter( 'do_technical_scan', $technical_scan, 'do_technical_scan', 10, 2 );
		$this->loader->add_action( 'wp_ajax_seo_master_get_chart_data', $chart_ajax, 'get_chart_data' );
		$this->loader->add_action( 'init', $ai_handler, 'save_ai_setting' );
		$this->loader->add_action( 'wp_head', $socialcard_handler, 'add_meta_tags', 1 );
		$this->loader->add_action( 'rank_math/head', $socialcard_handler, 'remove_action' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Seomasterpro_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Seomasterpro_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
