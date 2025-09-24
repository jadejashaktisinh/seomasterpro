<?php


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jadejashaktisinh.com
 * @since      1.0.0
 *
 * @package    Seomasterpro
 * @subpackage Seomasterpro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Seomasterpro
 * @subpackage Seomasterpro/admin
 * @author     jadeja shaktisinh <jadejashakti5483@gmail.com>
 */
class Seomasterpro_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Seomasterpro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Seomasterpro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/seomasterpro-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Seomasterpro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Seomasterpro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/seomasterpro-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script(
			'seo-analyzer.js',
			plugin_dir_url(__FILE__) . 'js/seo-analyzer.js',
			array('jquery'),
			'1.0',
			true
		);
		wp_localize_script(
			'seo-analyzer.js',
			'AI_SEO',
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('ai_seo_ajax_nonce'),
				'post_id' => get_the_ID(),
			)
		);
		wp_enqueue_script(
			'schema-handler.js',
			plugin_dir_url(__FILE__) . 'js/schema-handler.js',
			array('jquery'),
			'1.0',
			true
		);
		wp_enqueue_script('seo-charts', plugin_dir_url(__FILE__) . 'js/dashboard-charts.js', array('jquery'), '1.0', true);
		wp_localize_script(
			'seo-charts',
			'seoChartsAjax',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
			)
		);
		wp_enqueue_script(
			'chartjs',
			'https://cdn.jsdelivr.net/npm/chart.js',
			array(),
			'4.4.0',
			true
		);
		wp_enqueue_script(
			'react-editor-app',
			dirname(plugin_dir_url(__FILE__)) . '/build/editor-app.js',
			array('wp-element', 'wp-data','wp-components','wp-dom-ready', 'wp-edit-post'),
			'1.0',
			true
		);
	}
}
