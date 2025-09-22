<?php

global $post;
wp_nonce_field( 'ai_seo_meta_box_nonce', 'ai_seo_meta_box_nonce_field' );
$focus_keyword      = get_post_meta( $post->ID, '_ai_seo_focus_keyword', true ) ?? '';
$meta_title         = get_post_meta( $post->ID, '_ai_seo_meta_title', true ) ?? '';
$meta_description   = get_post_meta( $post->ID, '_ai_seo_meta_description', true ) ?? '';
$seo_score          = get_post_meta( $post->ID, '_ai_seo_score', true ) ?? '';
$seo_suggestions    = get_post_meta( $post->ID, '_ai_seo_suggestions', true ) ?? '';
$seo_improved_meta  = get_post_meta( $post->ID, '_ai_seo_improved_meta', true ) ?? '';
$auto_generate_meta = get_post_meta( $post->ID, '_auto_generate_meta', true ) ?: get_option( 'all_meta_generate' );
?>

<label for="ai-seo-meta-title"><b>Meta Title:</b></label>
<input type="text" id="ai-seo-meta-title" name="ai_seo_meta_title" value="<?php echo esc_attr( $meta_title ); ?>" style="width:100%; margin-bottom:5px;" />
<label for="ai-seo-meta-description"><b>Meta Description:</b></label>
<textarea id="ai-seo-meta-description" name="ai_seo_meta_description" style="width:100%; margin-bottom:5px;" rows="3"><?php echo esc_textarea( $meta_description ); ?></textarea>
<label for="ai-seo-keyword"><b>Focus Keyword:</b></label>
<input type="text" id="ai-seo-keyword" name="ai_seo_keyword" value="<?php echo esc_attr( $focus_keyword ); ?>" style="width:100%; margin-bottom:5px;" />
<input type="checkbox" id="ai-seo-checkbox" name="ai-seo-checkbox" <?php echo $auto_generate_meta ? 'checked' : ''; ?> > <label>Auto generate meta feild</label>
<button type="button" class="button button-primary" id="ai-seo-analyze" style="width:100%; margin-bottom:5px;">Analyze SEO</button>
<button type="button" class="button" id="ai-seo-improve" style="width:100%; margin-bottom:5px;">Improve Content</button>
<p>*last saved content will be analyzed</p>
<div id="ai-seo-results" style="margin-top:15px; font-size:14px; font-family:Arial, sans-serif;">
	<?php if ( $seo_score ) : ?>
		<?php
		$score_color = '#e74c3c';
		if ( $seo_score >= 80 ) {
			$score_color = '#27ae60';
		} elseif ( $seo_score >= 50 ) {
			$score_color = '#f39c12';
		}
		?>
		<div style="margin-bottom:10px; padding:10px; border-radius:6px; background:#f8f9fa; display:flex; align-items:center; gap:10px;">
			<div style="width:14px; height:14px; border-radius:50%; background:<?php echo $score_color; ?>;"></div>
			<div>
				<b>SEO Score:</b>
				<span id="seo-score" style="color:<?php echo $score_color; ?>; font-weight:bold;">
					<?php echo esc_html( $seo_score ); ?>/100
				</span>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $seo_suggestions ) : ?>
	<div style="
		padding:15px;
		border-radius:8px;
		background:#f9fafb;
		border:1px solid #e5e7eb;
		box-shadow:0 1px 3px rgba(0,0,0,0.08);
		margin-top:15px;
	">

		<h3 style="
			margin:0 0 10px;
			font-size:15px;
			font-weight:600;
			color:#111827;
		">
			💡 Meta Suggestions
		</h3>
		<ul id="seo-suggestion" style="
			margin:0;
			padding-left:20px;
			list-style-type:disc;
			color:#374151;
			line-height:1.6;
			font-size:14px;
		">
			<?php
			$meta_array = array( 'Meta Title', 'Meta Descripation', 'Focus Keyword' );
			$i          = 0;
			foreach ( $seo_improved_meta as $im ) :
				?>
				<li style="margin-bottom:6px;">  
					<?php echo esc_html( $meta_array[ $i ] . ':' ); ?><br>
					<?php echo esc_html( $im ); ?>

				</li>
				<?php
				++$i;
endforeach;
			?>
		</ul>
		<h3 style="
			margin:0 0 10px;
			font-size:15px;
			font-weight:600;
			color:#111827;
		">
			💡 Suggestions
		</h3>
		<ul id="seo-suggestion" style="
			margin:0;
			padding-left:20px;
			list-style-type:disc;
			color:#374151;
			line-height:1.6;
			font-size:14px;
		">
			<?php foreach ( $seo_suggestions as $s ) : ?>
				<li style="margin-bottom:6px;"><?php echo esc_html( $s ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

</div>