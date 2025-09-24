<?php

class Technical_Scan_Handler {


	public $content = '';

	public function do_technical_scan( $issues, $post_id ) {

		$post          = get_post( $post_id );
		$this->content = file_get_contents( $post->guid );

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

		preg_match_all( '/<img\s+[^>]*>/i', $this->content, $matches );
		$images = $matches[0];

		$missing_alt = array();
		foreach ( $images as $img ) {
			if ( ! preg_match( '/alt=["\'].+["\']/', $img ) ) {
				$missing_alt[] = $img;
			}
		}
		return $missing_alt;
	}
	public function check_broken_links( $post_id ) {
		preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']*)["\']/i', $this->content, $matches );
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

		$head = $this->content;

		$issues = array();

		$meta_checks = array(
			'OG title'            => '/<meta\s+[^>]*(property|name)=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i',
			'OG description'      => '/<meta\s+[^>]*(property|name)=["\']og:description["\'][^>]*content=["\']([^"\']+)["\']/i',
			'OG image'            => '/<meta\s+[^>]*(property|name)=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i',
			'Twitter title'       => '/<meta\s+[^>]*name=["\']twitter:title["\'][^>]*content=["\']([^"\']+)["\']/i',
			'Twitter description' => '/<meta\s+[^>]*name=["\']twitter:description["\'][^>]*content=["\']([^"\']+)["\']/i',
			'Twitter image'       => '/<meta\s+[^>]*name=["\']twitter:image["\'][^>]*content=["\']([^"\']+)["\']/i',
		);

		foreach ( $meta_checks as $label => $pattern ) {
			if ( ! preg_match( $pattern, $head ) ) {
				$issues[] = 'Missing ' . $label;
			}
		}
		error_log(implode( ', ', $issues ));
		return ! empty( $issues ) ? implode( ', ', $issues ) : '';
	}
}
