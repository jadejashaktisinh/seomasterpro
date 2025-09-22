<?php

	$all_auto_generate = get_option( 'all_meta_generate' );
	error_log( $all_auto_generate );
?>


<div class="wrap">
	<h1>AI settings</h1>

	<form method="post" >
		<input type="hidden" name="ai">
		<?php wp_nonce_field( 'save_ai', 'ai' ); ?>
		<input type="checkbox" name="all_auto_generate" id="all_auto_generate" <?php echo $all_auto_generate ? 'checked' : ''; ?>>
		<label>Generate Meta for all posts</label><br>

		<input type="submit" class="button button-primary" value="Save AI Settings">

	</form>