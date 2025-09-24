<?php


class Socialcard_Handler {


	public function remove_action(){
		remove_all_actions( 'rank_math/opengraph/facebook' );
		remove_all_actions( 'rank_math/opengraph/twitter' );
	}
	public function add_meta_tags() {

		if ( is_singular() ) {
			global $post;
			$post_id  = $post->ID;
			$og_title = get_post_meta( $post_id, '_og_title', true );
			$og_desc  = get_post_meta( $post_id, '_og_description', true );
			$og_type  = is_singular( 'post' ) ? 'article' : 'website';
			$tw_title = get_post_meta( $post_id, '_twitter_title', true );
			$tw_desc  = get_post_meta( $post_id, '_twitter_description', true );

			$featured_image = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';

			if ( $og_title ) {
				echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
			}
			if ( $og_desc ) {
				echo '<meta property="og:description" content="' . esc_attr( $og_desc ) . '">' . "\n";
			}

			if ( $tw_title ) {
				echo '<meta name="twitter:title" content="' . esc_attr( $tw_title ) . '">' . "\n";
			}
			if ( $tw_desc ) {
				echo '<meta name="twitter:description" content="' . esc_attr( $tw_desc ) . '">' . "\n";
			}
				echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

			echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
			if ( $featured_image ) {
				echo '<meta property="og:image" content="' . esc_url( $featured_image ) . '">' . "\n";
				echo '<meta name="twitter:image" content="' . esc_url( $featured_image ) . '">' . "\n";
			}

			echo '<meta property="og:url" content="' . esc_url( get_permalink( $post_id ) ) . '">' . "\n";
		}
	}
}
