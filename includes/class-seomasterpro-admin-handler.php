<?php


class Admin_Handler {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_nav_menu' ) );
		add_action( 'init', array( $this, 'add_custom_column' ) );
	}

	public function register_nav_menu() {
		add_menu_page(
			'SEO Master Pro',
			'SEO Master Pro',
			'manage_options',
			'tech-seo',
			function () {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-dashboard.php';
			},
			'dashicons-admin-site',
			25
		);
		add_submenu_page(
			'tech-seo',
			'Dashboard',
			'Dashboard',
			'manage_options',
			'tech-seo',
			function () {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-dashboard.php';
			},
			25
		);
		add_submenu_page(
			'tech-seo',
			'Sitemap Setting',
			'Sitemap Setting',
			'manage_options',
			'sitemap-setting',
			function () {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-tech-seo-sitemap.php';
			}
		);
		add_submenu_page(
			'tech-seo',
			'Technical checks',
			'Technical checks',
			'manage_options',
			'technical-checks',
			function () {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-tech-seo-technical.php';
			}
		);
		add_submenu_page(
			'tech-seo',
			'AI Settings',
			'AI Settings',
			'manage_options',
			'ai-settings',
			function () {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-ai-setting.php';
			}
		);
	}

	public function add_custom_column() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $pt ) {
			add_filter( "manage_{$pt}_posts_columns", array( $this, 'ai_seo_add_admin_column' ) );
			add_action( "manage_{$pt}_posts_custom_column", array( $this, 'ai_seo_render_admin_column' ), 10, 2 );
		}
	}
	public function ai_seo_add_admin_column( $columns ) {
		$columns['ai_seo'] = __( 'SEO', 'ai-seo' );
		return $columns;
	}
	public function ai_seo_render_admin_column( $column, $post_id ) {
		if ( 'ai_seo' === $column ) {
			$score       = get_post_meta( $post_id, '_ai_seo_score', true );
			$keyword = get_post_meta( $post_id, '_ai_seo_focus_keyword', true );

			if ( $score ) {
				echo '<strong>' . intval( $score ) . '/100</strong>';
			} else {
				echo '<em>Not analyzed</em>';
			}

			if ( ! empty( $keyword ) ) {
				echo '<br><big> <strong>Keyword: </strong>' . esc_html( $keyword ) . '</big>'; 
			}
		}
	}
}
