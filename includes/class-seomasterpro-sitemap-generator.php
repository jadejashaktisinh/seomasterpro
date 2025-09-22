<?php

class Sitemap_Generator {




	function __construct() {

		add_action( 'init', array( $this, 'register_rewrite_rules' ) );
		add_action( 'admin_init', array( $this, 'save_sitemap_setting' ) );
		add_action( 'template_redirect', array( $this, 'serve_sitemap' ) );
	}

	public function register_rewrite_rules() {
		add_rewrite_rule( 'sitemap\.xml$', 'index.php?sitemap=index', 'top' );
		add_rewrite_rule( 'sitemap-([^/]+)\.xml$', 'index.php?sitemap=$matches[1]', 'top' );
		add_rewrite_tag( '%sitemap%', '([^&]+)' );
	}

	public function serve_sitemap() {
		$sitemap = get_query_var( 'sitemap' );
		if ( ! $sitemap ) {
			return;
		}

		error_log( $sitemap );
		header( 'Content-Type: application/xml; charset=UTF-8' );

		if ( $sitemap === 'index' ) {
			echo $this->generate_sitemap_index();
		} else {
			echo $this->generate_child_sitemap( $sitemap );
		}
		exit;
	}

	function generate_sitemap_index() {
		$selected = get_option( 'custom_sitemap_types', array() );
		$xml      = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>' );
		foreach ( $selected as $type ) {
			$sitemap = $xml->addChild( 'sitemap' );
			$sitemap->addChild( 'loc', home_url( "/sitemap-$type.xml" ) );
			$sitemap->addChild( 'lastmod', date( 'c' ) );

		}

		return $xml->asXML();
	}


	function generate_child_sitemap( $type ) {
		$xml = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		if ( post_type_exists( $type ) ) {
			$query = new WP_Query(
				array(
					'post_type'      => $type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				)
			);
			foreach ( $query->posts as $post ) {
				$url = $xml->addChild( 'url' );
				$url->addChild( 'loc', get_permalink( $post ) );
				$url->addChild( 'lastmod', get_the_modified_time( 'c', $post ) );
			}
		}
		// Taxonomies
		elseif ( taxonomy_exists( $type ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $type,
					'hide_empty' => true,
				)
			);
			foreach ( $terms as $term ) {
				$url = $xml->addChild( 'url' );
				$url->addChild( 'loc', get_term_link( $term ) );
				$url->addChild( 'lastmod', date( 'c' ) );
			}
		}
		// Archives
		elseif ( $type === 'archive' ) {
			global $wpdb;
			$months = $wpdb->get_results(
				"
            SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
            FROM $wpdb->posts
            WHERE post_status='publish' AND post_type='post'
            ORDER BY post_date DESC
        "
			);
			foreach ( $months as $m ) {
				$url = $xml->addChild( 'url' );
				$url->addChild( 'loc', get_month_link( $m->year, $m->month ) );
				$url->addChild( 'lastmod', date( 'c' ) );
			}
		}

		return $xml->asXML();
	}

	public function save_sitemap_setting() {
		if ( ! isset( $_POST['sitemap'] ) || ! wp_verify_nonce( $_POST['sitemap'], 'save_sitemap' ) ) {
			return;
		}
		if ( isset( $_POST['generate_sitemap'] ) ) {
			$selected = isset( $_POST['sitemap_types'] ) ? array_map( 'sanitize_text_field', $_POST['sitemap_types'] ) : array();
			update_option( 'custom_sitemap_types', $selected );
		}
	}
}
