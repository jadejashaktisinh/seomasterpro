<?php

class Chart_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_seo_master_get_chart_data', array( $this, 'get_chart_data' ) );
	}

	public function get_chart_data() {
			global $wpdb;

		$scores = $wpdb->get_results(
			"
			SELECT DATE(post_date) as date, AVG(pm.meta_value) as avg_score
			FROM wp_posts AS p
			JOIN wp_postmeta AS pm ON p.ID = pm.post_id
			WHERE pm.meta_key = '_ai_seo_score'
			AND p.post_status = 'publish'
			GROUP BY DATE(post_date)
			ORDER BY date ASC;
	    "
		);

		// Get post type distribution
		$types = $wpdb->get_results(
			"
        SELECT post_type, COUNT(*) as count
        FROM $wpdb->posts AS p
        WHERE post_status = 'publish'
		AND p.post_type NOT IN ('wp_navigation','wp_global_styles','nav_menu_item', 'custom_css', 'wp_block', 'revision', 'acf-field', 'acf-field-group')
        GROUP BY post_type
    "
		);

		wp_send_json(
			array(
				'scores' => $scores,
				'types'  => $types,
			)
		);
	}
}
