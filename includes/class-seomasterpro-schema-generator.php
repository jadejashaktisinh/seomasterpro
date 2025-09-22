<?php


class Schema_Generator {



	function __construct() {
		add_action( 'wp_head', array( $this, 'schema_generator' ) );
	}

	public function schema_generator() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_the_ID();

		$types = get_post_meta( $post_id, '_base_schema_types', true );
		if ( empty( $types ) || ! is_array( $types ) ) {
			return false;
		}

		$schema_fields = array(
			'Thing'        => array(
				'name'        => get_the_title( $post_id ),
				'description' => get_the_excerpt( $post_id ),
			),
			'Article'      => array(
				'headline'      => get_the_title( $post_id ),
				'author'        => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
				'datePublished' => get_the_date( 'c', $post_id ),
				'dateModified'  => get_the_modified_date( 'c', $post_id ),
			),
			'BlogPosting'  => array(
				'headline'      => get_the_title( $post_id ),
				'author'        => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
				'datePublished' => get_the_date( 'c', $post_id ),
				'dateModified'  => get_the_modified_date( 'c', $post_id ),
			),
			'Product'      => array(
				'name'     => get_the_title( $post_id ),
				'price'    => '0.00',
				'currency' => 'USD',
				'sku'      => '',
			),
			'Event'        => array(
				'name'      => get_the_title( $post_id ),
				'startDate' => '',
				'endDate'   => '',
				'location'  => '',
				'address'   => '',
			),
			'Recipe'       => array(
				'name'               => get_the_title( $post_id ),
				'description'        => get_the_excerpt( $post_id ),
				'recipeIngredient'   => array(),
				'recipeInstructions' => array(),
			),
			'CreativeWork' => array(
				'name'          => get_the_title( $post_id ),
				'creator'       => array(
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
				),
				'datePublished' => get_the_date( 'c', $post_id ),
			),
			'Organization' => array(
				'name' => get_bloginfo( 'name' ),
				'url'  => get_home_url(),
			),
			'Person'       => array(
				'name' => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
			),
			'Book'         => array(
				'name'          => get_the_title( $post_id ),
				'author'        => '',
				'datePublished' => '',
				'isbn'          => '',
			),
			'Course'       => array(
				'name'     => get_the_title( $post_id ),
				'provider' => array(
					'@type' => 'Organization',
					'name'  => get_bloginfo( 'name' ),
				),
			),
			'FAQPage'      => array(
				'mainEntity' => array(),
			),
		);

		$all_schemas = array();

		foreach ( $types as $type ) {

			if ( ! array_key_exists( $type, $schema_fields ) ) {
				error_log( "Schema type {$type} not defined, skipping." );
				continue;
			}

			$current_schema = array(
				'@context'         => 'https://schema.org',
				'@type'            => $type,
				'mainEntityOfPage' => get_permalink( $post_id ),

			);
			foreach ( $schema_fields[ $type ] as $key => $default_value ) {
				if ( in_array( $type, array( 'Thing', 'Article', 'Product', 'Event' ) ) ) {
					$meta_value             = get_post_meta( $post_id, "schema_{$type}_{$key}", true );
					$current_schema[ $key ] = $meta_value !== '' ? $meta_value : $default_value;
				} else {
					$current_schema[ $key ] = $default_value;
				}
			}

			$all_schemas[] = $current_schema;
		}

		foreach ( $all_schemas as $schema ) {
			// error_log(print_r($schema,true));
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>';
		}
	}
}
