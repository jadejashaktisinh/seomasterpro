<div class="tab-content" style="margin-top:20px;">

	<h2>Technical SEO Checks</h2>
	<p>Scan your posts and pages for common issues like broken links, missing alt tags, and canonical tags.</p>

	<?php
	$post_types    = get_post_types( array( 'public' => true ), 'names' );
	$selected_type = isset( $_GET['select_type'] ) ? sanitize_text_field( $_GET['select_type'] ) : 'post';
	?>

	<p>
<select id="select-type" onchange="location.href='<?php echo esc_url( add_query_arg( 'select_type', '=', admin_url( 'admin.php?page=technical-checks' ) ) ); ?>' + this.value;">
			<?php
			foreach ( $post_types as $post_type ) {
				if ( 'attachment' === $post_type ) {
					continue;
				}
				$selected = $selected_type === $post_type ? 'selected' : '';
				echo "<option value='$post_type' $selected>$post_type</option>";
			}
			?>
		</select>
	</p>

	<div style="margin-top:20px;">
		<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th>Post Title</th>
					<th>Post Type</th>
					<th>SEO Score</th>
					<th>Issues</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
				$args  = array(
					'post_type'      => $selected_type,
					'posts_per_page' => 20,
					'post_status'    => 'publish',
					'paged'          => 0,
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						$post_id   = get_the_ID();
						$issues    = get_post_meta( $post_id, '_technical_issues', true );
						$seo_score = get_post_meta( $post_id, '_ai_seo_score', true ) ?: 'N/A';
						?>
						<tr>
							<td><?php the_title(); ?></td>
							<td><?php echo esc_html( get_post_type( $post_id ) ); ?></td>
							<td><?php echo esc_html( $seo_score ); ?></td>
							<td><?php echo esc_html( $issues ); ?></td>
							<td><a href="<?php echo get_edit_post_link( $post_id ); ?>">Edit</a></td>
						</tr>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<tr>
						<td colspan="5">No posts found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
		$pagination_args = array(
			'base'      => add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'current'   => $paged,
			'total'     => $query->max_num_pages,
			'prev_text' => '&laquo; Prev',
			'next_text' => 'Next &raquo;',
		);
		echo '<div class="tablenav"><div class="tablenav-pages">';
		echo paginate_links( $pagination_args );
		?>
	</div>
</div>
