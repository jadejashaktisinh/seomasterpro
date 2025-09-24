<?php

class MetaBox_Handler {


	public function regiter_meta_box() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );
		error_log(print_r($post_types,true));
		foreach ( $post_types as $pt ) {
			add_meta_box(
				'ai_seo_meta_box',
				'AI SEO Analyzer',
				array( $this, 'show_seo_meta_boxes' ),
				$pt,
				'side',
				'high'
			);

			add_meta_box(
				'schema_meta_box',
				'Schema',
				array( $this, 'show_schema_meta_box' ),
				$pt,
				'side',
				'high'
			);
			add_meta_box(
				'social_meta_box',
				'Social Card',
				array( $this, 'show_social_meta_box' ),
				$pt,
			);
		}
	}

	public function show_seo_meta_boxes($post) {
		include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-metabox.php';
	}
	public function show_schema_meta_box() {
		include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-schema-metabox.php';
	}

	public function save_post_meta( $post_id ) {
		$this->save_seo_meta( $post_id );
		$this->save_schema_meta( $post_id );
		$this->save_social_meta( $post_id );
	}
	public function show_social_meta_box() {
				include_once dirname( plugin_dir_path( __FILE__ ) ) . '/admin/partials/seomasterpro-admin-social-metabox.php';
	}
	public function save_seo_meta( $post_id ) {

		$post = get_post( $post_id );
		if ( ! isset( $_POST['ai_seo_meta_box_nonce_field'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['ai_seo_meta_box_nonce_field'], 'ai_seo_meta_box_nonce' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['ai_seo_keyword'] ) ) {
			update_post_meta( $post_id, '_ai_seo_focus_keyword', sanitize_text_field( $_POST['ai_seo_keyword'] ) );
		}
		if ( isset( $_POST['ai_seo_meta_title'] ) ) {
			update_post_meta( $post_id, '_ai_seo_meta_title', sanitize_text_field( $_POST['ai_seo_meta_title'] ) );
		}
		if ( isset( $_POST['ai_seo_meta_description'] ) ) {
			update_post_meta( $post_id, '_ai_seo_meta_description', sanitize_textarea_field( $_POST['ai_seo_meta_description'] ) );
		}
		if ( isset( $_POST['ai-seo-checkbox'] ) ) {
			update_post_meta( $post_id, '_auto_generate_meta', true );
		} else {
			update_post_meta( $post_id, '_auto_generate_meta', false );
		}
	}

	public function save_schema_meta( $post_id ) {
		if ( ! isset( $_POST['schema_meta_nonce'] ) || ! wp_verify_nonce( $_POST['schema_meta_nonce'], 'save_schema_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['base_schema_types'] ) && is_array( $_POST['base_schema_types'] ) ) {
			$schemas = array_map( 'sanitize_text_field', $_POST['base_schema_types'] );
			update_post_meta( $post_id, '_base_schema_types', $schemas );
			$schema_fields = array(
				'Thing'   => array(
					'name'        => 'Name',
					'description' => 'Description',
				),
				'Article' => array(
					'headline'      => 'Headline',
					'author'        => 'Author',
					'datePublished' => 'Date Published',
				),
				'Product' => array(
					'name'     => 'Product Name',
					'price'    => 'Price',
					'currency' => 'Currency',
					'sku'      => 'SKU',
				),
				'Event'   => array(
					'name'      => 'Event Name',
					'startDate' => 'Start Date',
					'endDate'   => 'End Date',
					'location'  => 'Location',
					'address'   => 'Address',
				),
			);

			foreach ( $schemas as $schema ) {

				if ( ! array_key_exists( $schema, $schema_fields ) ) {
					continue;
				}
				$cuurent_schema_fields = $schema_fields[ $schema ];

				foreach ( $cuurent_schema_fields as $cuurent_schema_field_key => $cuurent_schema_field_value ) {
					if ( isset( $_POST[ "schema_{$schema}_{$cuurent_schema_field_key}" ] ) ) {
						update_post_meta(
							$post_id,
							"schema_{$schema}_{$cuurent_schema_field_key}",
							$_POST[ "schema_{$schema}_{$cuurent_schema_field_key}" ]
						);
					}
				}
			}
		} else {
			delete_post_meta( $post_id, '_base_schema_types' );
		}
	}
	public function save_social_meta( $post_id ) {

		if ( ! isset( $_POST['social-meta'] ) ) {
				return;
		}
		error_log($_POST['og_title']);
		update_post_meta( $post_id, '_og_title', $_POST['og_title'] ?? 'k' );
		update_post_meta( $post_id, '_og_description', $_POST['og_description'] ?? 'k' );
		update_post_meta( $post_id, '_twitter_title', $_POST['twitter_title'] ?? 'k' );
		update_post_meta( $post_id, '_twitter_description', $_POST['twitter_description'] ?? 'k' );
	}
}
