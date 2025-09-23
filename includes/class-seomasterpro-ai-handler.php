<?php

class Ai_Handler {


	function __construct() {
		add_action( 'init', array( $this, 'save_ai_setting' ) );
	}

	public function save_ai_setting() {
		if ( ! isset( $_POST['ai'] ) || ! wp_verify_nonce( $_POST['ai'], 'save_ai' ) ) {
			return;
		}
		if ( isset( $_POST['ai'] ) ) {
			$all_meta_generate = isset( $_POST['all_auto_generate'] ) ?? false;
			update_option( 'all_meta_generate', $all_meta_generate );
		}

		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);
		$selected_post_types = array();
		foreach($post_types as $pt){
			if( isset($_POST[$pt]) ){
				$selected_post_types[] = $pt;
			}
		}
		update_option('generate_post_types',$selected_post_types);
	}
}
