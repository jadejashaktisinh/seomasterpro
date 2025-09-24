<?php
wp_nonce_field('ai_social_meta_box_nonce', 'ai_social_meta_box_nonce_field');

global $post;
$og_title       = get_post_meta($post->ID, '_og_title', true);
$og_description = get_post_meta($post->ID, '_og_description', true);
$tw_title       = get_post_meta($post->ID, '_twitter_title', true);
$tw_description = get_post_meta($post->ID, '_twitter_description', true);
$featured_image = has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'full' ) : '';

error_log($og_title);
?>

<div style="display:flex; gap:20px;">
    <div style="flex:1">
        <h2>Open Graph Tags</h2>
        <input type="hidden" name="social-meta" value="social">
        <label>OG Title</label><br>
        <input type="text" id="og_title" name="og_title" value="<?php echo esc_attr($og_title); ?>" style="width:100%;" /><br><br>

        <label>OG Description</label><br>
        <textarea id="og_description" name="og_description" style="width:100%;"><?php echo esc_textarea($og_description); ?></textarea><br><br>
        <h2>Twitter Card Tags</h2>
        <label>Twitter Title</label><br>
        <input type="text" id="tw_title" name="twitter_title" value="<?php echo esc_attr($tw_title); ?>" style="width:100%;" /><br><br>

        <label>Twitter Description</label><br>
        <textarea id="tw_description" name="twitter_description" style="width:100%;"><?php echo esc_textarea($tw_description); ?></textarea><br><br>
     </div>

   
    <div style="flex:1; border:1px solid #ccc; padding:10px; border-radius:5px;">
        <h3>Preview</h3>
        <div id="card_preview" style="border:1px solid #ddd; padding:10px; border-radius:5px;">
            <h3>Open Ghraph</h3>
            <strong id="preview_title"><?php echo esc_html($og_title ?: 'Title Here'); ?></strong><br>
            <span id="preview_desc"><?php echo esc_html($og_description ?: 'Description here'); ?></span><br>
            <img id="preview_image" src="<?php echo esc_url($featured_image); ?>" style="max-width:100%; margin-top:10px; display:<?php echo $featured_image ? 'block' : 'none'; ?>" />
        </div>
        <div id="card_preview" style="border:1px solid #ddd; padding:10px; border-radius:5px;">
            <h3>Twitter Card</h3>
            <strong id="preview_title"><?php echo esc_html($tw_title ?: 'Title Here'); ?></strong><br>
            <span id="preview_desc"><?php echo esc_html($tw_description ?: 'Description here'); ?></span><br>
            <img id="preview_image" src="<?php echo esc_url($featured_image); ?>" style="max-width:100%; margin-top:10px; display:<?php echo $featured_image ? 'block' : 'none'; ?>" />
        </div>
    </div>
</div>

