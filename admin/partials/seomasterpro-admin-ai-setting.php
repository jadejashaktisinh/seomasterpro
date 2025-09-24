<?php

	$all_auto_generate = get_option( 'all_meta_generate' );
	$selected_generate_post_types = get_option('generate_post_types',array());
	$post_types = get_post_types(array(
		'public' => true
	));
?>


<div class="wrap">
	<h1>AI settings</h1>
	<form method="post" >
		<input type="hidden" name="ai">
		<?php wp_nonce_field( 'save_ai', 'ai' ); ?>
		<input type="checkbox" name="all_auto_generate" id="all_auto_generate" <?php echo $all_auto_generate ? 'checked' : ''; ?>>
		<label>Generate Meta for all posts</label><br>

		<div id="geneate_select_post_type_container" <?php echo $all_auto_generate ? '' : 'hidden'; ?>>
			<?php
			foreach($post_types as $pt){
				?>
					<input 
						type="checkbox" 
						value=<?php echo $pt ?> 
						name=<?php echo esc_attr($pt)?> 
						class="generate_select_post_type" 
						<?php echo in_array($pt,$selected_generate_post_types) ? 'checked' : '' ?>
					>
					<label><?php echo esc_html($pt) ?></label><br>
				<?php
			}
		?>
		</div>
		<input type="submit" class="button button-primary" value="Save AI Settings">
	</form>