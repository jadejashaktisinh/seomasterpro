<?php

/**
 * Fired during plugin activation
 *
 * @link       https://jadejashaktisinh.com
 * @since      1.0.0
 *
 * @package    Seomasterpro
 * @subpackage Seomasterpro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Seomasterpro
 * @subpackage Seomasterpro/includes
 * @author     jadeja shaktisinh <jadejashakti5483@gmail.com>
 */
class Seomasterpro_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

			register_post_type(
				'service',
				array(
					'labels'       => array(
						'name' => 'service',
					),
					'public'       => true,
					'show_ui'      => true,
					'show_in_menu' => true,
					'show_in_rest' => true
				)
			);
	}
}
