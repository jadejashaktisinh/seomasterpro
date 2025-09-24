(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

		
	$( '#ai-seo-analyze, #ai-seo-improve' ).click(
		function () {
			console.log("ljlkjlk");
			
			var actionType = $( this ).attr( 'id' );
			$( '#ai-seo-results' ).html( '⏳ Analyzing...' );

			$.post(
				AI_SEO.ajaxurl,
				{
					action: 'ai_seo_analyze',
					_wpnonce: AI_SEO.nonce,
					post_id: AI_SEO.post_id,
				},
				function (res) {
					if ( ! res.success) {
						$( '#ai-seo-results' ).html( '❌ Error' );
						return;
					}

					let data  = res.data;
					let color = '#e74c3c';
					if (data.score >= 80) {
						color = '#f39c12';
					}
					if (data.score >= 50) {
						color = '#27ae60';
					}
					$( '#ai-seo-results' ).html(
						` < div style     = "margin-bottom:10px; padding:10px; border-radius:6px; background:#f8f9fa; display:flex; align-items:center; gap:10px;" >
							< div style   = "width:14px; height:14px; border-radius:50%; background:<?php echo $score_color; ?>;" > < / div >
							< div >
								< b > SEO Score: < / b >
								< span id = "seo-score" style = "color:${color}; font-weight:bold;" >
									${data.score} / 100
								< / span >
							< / div >
						< / div > ` +
						'<div style="padding:15px; border-radius:8px; background:#f9fafb; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-top:15px;">' +
						'<h3 style="margin:0 0 10px; font-size:15px; font-weight:600; color:#111827;">💡 Suggestions</h3>' +
						'<ul style="margin:0; padding-left:20px; list-style-type:disc; color:#374151; line-height:1.6; font-size:14px;">' +
						data.suggestions.map( s => '<li style="margin-bottom:6px;">' + s + '</li>' ).join( '' ) +
						'</ul>' +
						'</div>'
					);

					if (actionType === 'ai-seo-improve') {
						if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch( 'core/editor' )) {
							console.log( 'wp  undefined' );
							wp.data.dispatch( 'core/editor' ).editPost(
								{
									content: data.improved_content,
									title: data.title
								}
							);
						} else {
							console.warn( 'Not in Gutenberg editor' );
						}

					}
				}
			);
		}
	);

})( jQuery );
