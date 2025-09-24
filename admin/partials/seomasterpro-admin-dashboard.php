<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$avg_score = $wpdb->get_var(
	"
    SELECT AVG(meta_value+0)
    FROM $wpdb->postmeta pm
    INNER JOIN $wpdb->posts p ON p.ID = pm.post_id
    WHERE pm.meta_key = '_ai_seo_score'
      AND p.post_status = 'publish'
"
);

$missing_meta = $wpdb->get_results(
	"
    SELECT p.ID, p.post_title
    FROM {$wpdb->posts} AS p
    LEFT JOIN {$wpdb->postmeta} AS pm 
        ON p.ID = pm.post_id AND pm.meta_key = '_ai_seo_meta_description'
    WHERE (pm.meta_value IS NULL OR pm.meta_value = '')
      AND p.post_type NOT IN ('wp_navigation','wp_global_styles','nav_menu_item', 'custom_css', 'wp_block', 'revision', 'acf-field', 'acf-field-group')
      AND p.post_status = 'publish'
    LIMIT 10
"
);

$low_score_posts = $wpdb->get_results(
	"
    SELECT p.ID, p.post_title, pm.meta_value as score
    FROM $wpdb->posts p
    INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
    WHERE pm.meta_key = '_ai_seo_score'
      AND pm.meta_value+0 < 60
      AND p.post_status = 'publish'
    LIMIT 10
"
);
$outdated_posts  = $wpdb->get_results(
	"
    SELECT ID, post_title, post_date
    FROM $wpdb->posts
    WHERE post_status = 'publish'
      AND post_type IN ('post', 'page')
      AND post_date < DATE_SUB(NOW(), INTERVAL 365 DAY)
    LIMIT 10
"
);

?>

<div class="wrap">
	<h1>SEO Master Pro - Dashboard</h1>

	<div style="padding:20px; background:#fff; border:1px solid #ddd; border-radius:8px; margin-bottom:20px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
		<h2>📊 Sitewide SEO Score</h2>
		<p style="font-size:24px; font-weight:bold;">
			<?php echo $avg_score ? round( $avg_score ) : 0; ?> / 100
		</p>
		<div style="background:#e5e7eb; border-radius:6px; height:20px; width:300px; overflow:hidden;">
			<div style="background:#4caf50; height:20px; width:<?php echo $avg_score; ?>%;"></div>
		</div>
	</div>

	<div style="display:flex; gap:20px; flex-wrap:wrap;">

		<div style="flex:1; min-width:300px; padding:15px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
			<h3>⚠️ Missing Meta Descriptions</h3>
			<ul>
				<?php if ( $missing_meta ) : ?>
					<?php foreach ( $missing_meta as $post ) : ?>
						<li><a href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></a></li>
					<?php endforeach; ?>
				<?php else : ?>
					<li>✅ All posts have meta descriptions.</li>
				<?php endif; ?>
			</ul>
		</div>

		<div style="flex:1; min-width:300px; padding:15px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
			<h3>🔻 Low Score Posts (<60) </h3>
			<ul>
				<?php if ( $low_score_posts ) : ?>
					<?php foreach ( $low_score_posts as $post ) : ?>
						<li>
							<a href="<?php echo get_edit_post_link( $post->ID ); ?>">
								<?php echo esc_html( $post->post_title ); ?>
							</a> (<?php echo intval( $post->score ); ?>)
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li>✅ No low-score posts found.</li>
				<?php endif; ?>
			</ul>
		</div>

		<div style="flex:1; min-width:300px; padding:15px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
			<h3>📅 Outdated Posts (1+ Year Old)</h3>
			<ul>
				<?php if ( $outdated_posts ) : ?>
					<?php foreach ( $outdated_posts as $post ) : ?>
						<li>
							<a href="<?php echo get_edit_post_link( $post->ID ); ?>">
								<?php echo esc_html( $post->post_title ); ?>
							</a> (<?php echo date( 'M Y', strtotime( $post->post_date ) ); ?>)
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li>✅ No outdated posts.</li>
				<?php endif; ?>
			</ul>
		</div>

	</div>

	<!-- Chart -->
	<div style="margin-top:30px; padding:20px; background:#fff; border:1px solid #ddd; border-radius:8px;">
		<h3>📈 SEO Performance Overview</h3>
		<div class="seo-dashboard-charts">
	<canvas id="seoLineChart" width="400"  height="300"></canvas>
	<canvas id="seoPieChart" width="400" height="300"></canvas>
</div>
	</div>
</div>
