<?php

class Technical_Scan_Handler {

	public function __construct() {
		add_filter( 'do_technical_scan', array( $this, 'do_technical_scan' ), 1 );
	}

	public function do_technical_scan( $post_id ) {

		$issues      = array();
		$missing_alt = $this->check_missing_alt( $post_id );
		$deduct      = 0;
		if ( $missing_alt ) {

			if ( count( $missing_alt ) > 5 ) {
				$deduct = 5;
			} else {
				$deduct = count( $missing_alt );
			}
			$issues[] = count( $missing_alt ) . ' images missing alt';
		}

		$broken_links = $this->check_broken_links( $post_id );
		if ( $broken_links ) {
			if ( count( $broken_links ) > 5 ) {
					$deduct = 15;
			} else {
				$deduct = count( $broken_links ) * 3;
			}
			$issues[] = count( $broken_links ) . ' broken links';
		}

		if ( ! $this->check_canonical( $post_id ) ) {
			$issues[] = 'Missing canonical tag';
		}
		$issues[]    = $this->check_social_meta_tags( $post_id );
		$issues_text = ! empty( $issues ) ? implode( ', ', $issues ) : 'None';
		return array(
			$issues_text,
			$deduct,
		);
	}
	public function check_missing_alt( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );
		preg_match_all( '/<img\s+[^>]*>/i', $content, $matches );
		$images = $matches[0];

		$missing_alt = array();
		foreach ( $images as $img ) {
			if ( ! preg_match( '/alt=["\'][^"\']*["\']/', $img ) ) {
				$missing_alt[] = $img;
			}
		}
		return $missing_alt;
	}
	public function check_broken_links( $post_id ) {
		$content = get_post_field( 'post_content', $post_id );
		preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']*)["\']/i', $content, $matches );
		$urls = $matches[1];

		$broken_links = array();
		foreach ( $urls as $url ) {
			if ( strpos( $url, '#' ) === 0 ) {
				continue;
			}
			$response = wp_remote_head( $url, array( 'timeout' => 5 ) );
			if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				$broken_links[] = $url;
			}
		}
		return $broken_links;
	}
	public function check_canonical( $post_id ) {
		$canonical = get_post_meta( $post_id, '_yoast_wpseo_canonical', true );
		if ( ! $canonical ) {
			$canonical = get_permalink( $post_id );
		}
		return $canonical ? true : false;
	}
	public function check_social_meta_tags( $post_id ) {

		global $post;
		$backup_post = $post;
		$post        = get_post( $post_id );

		ob_start();
		do_action( 'wp_head' );
		$head = ob_get_clean();
		$post = $backup_post;

		$issues = array();
		if ( ! preg_match( '/<meta property=["\']og:title["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing OG title';
		}
		if ( ! preg_match( '/<meta property=["\']og:description["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing OG description';
		}
		if ( ! preg_match( '/<meta property=["\']og:image["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing OG image';
		}
		if ( ! preg_match( '/<meta name=["\']twitter:title["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing Twitter title';
		}
		if ( ! preg_match( '/<meta name=["\']twitter:description["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing Twitter description';
		}
		if ( ! preg_match( '/<meta name=["\']twitter:image["\'] content=["\']([^"\']+)["\']/', $head ) ) {
			$issues[] = 'Missing Twitter image';
		}

		if ( ! empty( $issues ) ) {

			return implode( ',', $issues );
		}
		return '';
	}
}
