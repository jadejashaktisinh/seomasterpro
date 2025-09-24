<?php

class Ai_Handler {

	public function save_ai_setting() {
		if ( ! isset( $_POST['ai'] ) || ! wp_verify_nonce( $_POST['ai'], 'save_ai' ) ) {
			return;
		}
		if ( ! isset( $_POST['ai'] ) ) {
			return;
		}

		$all_meta_generate = isset( $_POST['all_auto_generate'] ) ?? false;
		update_option( 'all_meta_generate', $all_meta_generate );
		error_log("all meta ".$all_meta_generate);
		if(! $all_meta_generate){
			delete_option('generate_post_types');
			return;
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
