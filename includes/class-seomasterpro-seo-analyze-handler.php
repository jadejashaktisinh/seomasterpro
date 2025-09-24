<?php


class AI_SEO_Analyzer {


	public function analyze_post( $post_id ) {

		$post = get_post( $post_id );
		if ( ! $post ) {
			return false;
		}

		$title   = get_the_title( $post_id );
		$content = apply_filters( 'the_content', $post->post_content );

		if ( empty( $title ) || empty( $content ) ) {
			return;
		}

		$keyword            = get_post_meta( $post_id, '_focus_keyword', true );
		$meta_title         = get_post_meta( $post_id, '_yoast_wpseo_title', true );
		$meta_description   = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
		$auto_generate_meta = get_post_meta( $post->ID, '_auto_generate_meta', true );

		global $post;
		$backup_post  = $post;
		$current_post = get_post( $post_id );
		setup_postdata( $current_post );
		ob_start();
		do_action( 'wp_head' );
		$head_content = ob_get_clean();

		$post = $backup_post;
		wp_reset_postdata();

$prompt = "
Analyze the following WordPress post content for SEO compliance and generate an improved version.

Post Details:
- Title: {$title}
- Content (HTML): {$content}
- Focus Keyword: {$keyword}
- Meta Title: {$meta_title}
- Meta Description: {$meta_description}
- Head Section (HTML): {$head_content}

SEO Rules to Apply:
1. Focus Keyword Usage: Must appear in title, slug/URL, meta description, first paragraph, headings, and body text.
2. Readability: Check sentence length, paragraph length, passive voice usage, transition words, readability score.
3. Content Length: Minimum 300 words, ideally 800-1500.
4. Meta Data: Title length (50-60 chars), description length (150-160 chars), uniqueness.
5. Images: Ensure alt text is present, add the focus keyword to alt attributes if missing, and file size is optimal.
6. Links: Internal links, external links, broken link detection.
7. Headings: Single H1, proper hierarchy, keyword in H2.
8. URL/Slug: Short, keyword-rich, avoid stop words.
9. Content Freshness: Warn if post is older than 12 months.
10. Technical Checks: Canonical tag, OpenGraph, Twitter cards.
11. Duplicate Check: Warn if duplicate title/meta across posts.
12. SEO Score System: Weighted scoring system with green/orange/red results.

Instructions:
- Preserve all original HTML, shortcodes, embeds, galleries, and formatting exactly as in the original content.
- Identify all SEO issues and fix them in the `improved_content` field.
- Ensure `improved_content` is fully WordPress editor compatible (Gutenberg/classic).
- All quotes in HTML must be escaped as \\\".
- All newlines in HTML must be escaped as \\n.
- Return **ONLY a single valid JSON object** with these fields:
  - `score` (integer 0-100)
  - `suggestions` (array of improvement suggestions)
  - `improved_meta` (array: [Improved Meta Title, Improved Meta Description, Focus Keyword])
  - `title` (string: optimized title, do not include in `improved_content`)
  - `improved_content` (string: full optimized HTML content with all SEO fixes applied, fully escaped for WordPress)
- Do not include any extra text, explanation, or formatting outside the JSON object.
- Make sure the JSON is valid and `improved_content` will render correctly in WordPress without breaking tags, images, galleries, or shortcodes.
";


		$body     = array(
			'model'    => 'meta-llama/llama-3.3-8b-instruct:free',
			'messages' => array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
		);
		$api_key  = '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';
		$response = wp_remote_post(
			'https://openrouter.ai/api/v1/chat/completions',
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => wp_json_encode( $body ),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}
		$ai_result = $this->parse_ai_json($response);
		if(!$ai_result){
		
		update_post_meta($post_id,'_cron_status','error');
			return false;
		}
		$technical_issue = apply_filters( 'do_technical_scan', array(), $post_id );
		update_post_meta( $post_id, '_ai_seo_score', $ai_result['score'] ? $ai_result['score'] - $technical_issue[1] : 0 );
		update_post_meta( $post_id, '_ai_seo_suggestions', $ai_result['suggestions'] ?? array() );
		if ( $auto_generate_meta ) {
			update_post_meta( $post_id, '_ai_seo_meta_title', $ai_result['improved_meta'][0] );
			update_post_meta( $post_id, '_ai_seo_meta_description', $ai_result['improved_meta'][1] );
			update_post_meta( $post_id, '_ai_seo_focus_keyword', $ai_result['improved_meta'][2] );

		}
		update_post_meta( $post_id, '_ai_seo_improved_meta', $ai_result['improved_meta'] ?? array() );
		update_post_meta( $post_id, '_technical_issues', $technical_issue[0] ?? '' );
		update_post_meta($post_id,'_cron_status','finhised');
		return $ai_result;
	}

	public function ajax_ai_seo_analyze() {
		check_ajax_referer( 'ai_seo_ajax_nonce', '_wpnonce' );
		$post_id   = intval( $_POST['post_id'] ?? 0 );
		$ai_result = $this->analyze_post( $post_id );

		if ( ! $ai_result ) {
			wp_send_json_error( 'Analysis failed' );
		}
		wp_send_json_success( $ai_result );
	}


	public function save_post_ai_seo_analyze( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( get_post_status( $post_id ) === 'auto-draft' ) {
			return; 
		}

		if ( ! wp_next_scheduled( 'cron_ai_analyze', array( $post_id ) ) ) {
			update_post_meta($post_id,'_cron_status','analyzing');
			wp_schedule_single_event( time() + 5, 'cron_ai_analyze', array( $post_id ) );
		}
	}

	public function parse_ai_json( $response ) {
    $data       = json_decode( wp_remote_retrieve_body( $response ), true );
    $ai_content = $data['choices'][0]['message']['content'] ?? '';

	error_log(print_r($ai_content,true));
    $ai_content = preg_replace( '/^```[a-zA-Z0-9]*\s*/', '', $ai_content );
    $ai_content = preg_replace( '/```$/', '', $ai_content );

    $ai_content = preg_replace( '/[\x00-\x1F\x7F]/u', '', $ai_content );

    $ai_content = str_replace( ['“','”','‘','’'], ['"','"',"'", "'"], $ai_content );

    $ai_content = preg_replace( '/,(\s*[}\]])/', '$1', $ai_content );

    $ai_content = trim( $ai_content );
    $ai_content = rtrim( $ai_content, ';' );

    $ai_result = json_decode( $ai_content, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        $ai_content_fixed = preg_replace_callback(
            '/"([^"]*)"/',
            function ( $matches ) {
                return '"' . addslashes( $matches[1] ) . '"';
            },
            $ai_content
        );

        $ai_result = json_decode( $ai_content_fixed, true );
    }

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        error_log( 'JSON decode error: ' . json_last_error_msg() );
        error_log( 'AI content: ' . $ai_content );
        return false;
    }

    return $ai_result;
}
}
