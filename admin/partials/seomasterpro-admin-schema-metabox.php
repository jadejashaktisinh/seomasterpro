<?php

global $post;
wp_nonce_field( 'save_schema_meta', 'schema_meta_nonce' );

$selected_schemas = (array) get_post_meta( $post->ID, '_base_schema_types', true );

if ( ! empty( $selected_schemas ) ) {
	$selected_schemas = array(
		'Thing',
		'Article',
		'Organization',
	);
}
$base_schemas  = array(
	'Thing'        => 'Thing (Default)',
	'Article'      => 'Article',
	'BlogPosting'  => 'BlogPosting',
	'Product'      => 'Product',
	'Event'        => 'Event',
	'Recipe'       => 'Recipe',
	'CreativeWork' => 'CreativeWork',
	'Organization' => 'Organization',
	'Person'       => 'Person',
	'Book'         => 'Book',
	'Course'       => 'Course',
	'FAQPage'      => 'FAQPage',
);
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


echo '<p><strong>Select Schema Types:</strong></p>';
foreach ( $base_schemas as $key => $label ) {
	printf(
		'<label style="display:block; margin-bottom:4px;">
                <input type="checkbox" name="base_schema_types[]" value="%s" %s> %s
            </label>',
		esc_attr( $key ),
		checked( in_array( $key, $selected_schemas ), true, false ),
		esc_html( $label )
	);
}

foreach ( $schema_fields as $type => $fields ) {
	$visible = in_array( $type, $selected_schemas ) ? '' : 'style="display:none;"';
	echo "<div class='schema-settings schema-settings-{$type}' {$visible}>";
	echo "<h4>{$type} Fields</h4>";
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, 'schema_' . $type . '_' . $key, true );
		echo "<p><label>{$label}:</label><br>
              <input type='text' name='schema_{$type}_{$key}' value='" . esc_attr( $value ) . "' style='width:100%;'></p>";
	}
	echo '</div>';
}
