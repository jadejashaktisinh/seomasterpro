<div class="tab-content" style="margin-top:20px;">
	<?php
	$selected = get_option( 'custom_sitemap_types', array() );
	?>
	<h2>XML Sitemap Generator</h2>
	<p>Select which sitemap types you want to include in your sitemap index.</p>
	<form method="post">
		<input type="hidden" name="generate_sitemap" value="1">

		<?php wp_nonce_field( 'save_sitemap', 'sitemap' ); ?>
		
		<p><strong>Post Types</strong></p>
		<?php foreach ( get_post_types( array( 'public' => true ), 'names' ) as $pt ) : ?>
			<label>
				<input type="checkbox" name="sitemap_types[]" value="<?php echo esc_attr( $pt ); ?>"
					<?php checked( true, in_array( $pt, $selected ) ); ?>>
				<?php echo ucfirst( $pt ); ?>
			</label><br>
		<?php endforeach; ?>

		<p><strong>Taxonomies</strong></p>
		<?php foreach ( get_taxonomies( array( 'public' => true ), 'names' ) as $tax ) : ?>
			<label>
				<input type="checkbox" name="sitemap_types[]" value="<?php echo esc_attr( $tax ); ?>"
					<?php checked( true, in_array( $tax, $selected ) ); ?>>
				<?php echo ucfirst( $tax ); ?>
			</label><br>
		<?php endforeach; ?>

		<p><strong>Other</strong></p>
		<label>
			<input type="checkbox" name="sitemap_types[]" value="archive" <?php echo in_array( 'archive', $selected ) ? 'checked' : ''; ?>>
			Archives
		</label><br>

		<p>
			<input type="submit" class="button button-primary" value="Save Sitemap Settings">
			<a href="<?php echo home_url( '/sitemap.xml' ); ?>" target="_blank" class="button">View Sitemap</a>
		</p>
	</form>
</div>