<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jadejashaktisinh.com
 * @since             1.0.0
 * @package           Seomasterpro
 *
 * @wordpress-plugin
 * Plugin Name:       SEO master pro
 * Plugin URI:        https://seomasteropro.com
 * Description:       for seo with ai powered stuff
 * Version:           1.0.0
 * Author:            jadeja shaktisinh
 * Author URI:        https://jadejashaktisinh.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       seomasterpro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEOMASTERPRO_VERSION', '1.0.0' );
define( 'INCLUDEPATH', plugin_dir_path( __FILE__ ) . '/includes/' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-seomasterpro-activator.php
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seomasterpro-activator.php';
function activate_seomasterpro() {
	Seomasterpro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-seomasterpro-deactivator.php
 */
function deactivate_seomasterpro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-seomasterpro-deactivator.php';
	Seomasterpro_Deactivator::deactivate();
}

add_action(
	'init',
	function () {
		Seomasterpro_Activator::activate();
	}
);
register_activation_hook( __FILE__, 'activate_seomasterpro' );
register_deactivation_hook( __FILE__, 'deactivate_seomasterpro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-seomasterpro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

require_once INCLUDEPATH . 'class-seomasterpro-metabox-handler.php';
require_once INCLUDEPATH . 'class-seomasterpro-seo-analyze-handler.php';
require_once INCLUDEPATH . 'class-seomasterpro-admin-handler.php';
require_once INCLUDEPATH . 'class-seomasterpro-sitemap-generator.php';
require_once INCLUDEPATH . 'class-seomasterpro-schema-generator.php';
require_once INCLUDEPATH . 'class-seomasterpro-technical-scan-handler.php';
require_once INCLUDEPATH . 'class-seomasterpro-ai-handler.php';
require_once INCLUDEPATH . 'class-seomasterpro-chart-ajax.php';

function run_seomasterpro() {
	$plugin = new Seomasterpro();
	$plugin->run();
}
run_seomasterpro();
