const { registerPlugin }                           = wp.plugins;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { PanelBody, TextControl, Button }           = wp.components;
const { useSelect, useDispatch }                   = wp.data;
const { Fragment }                                 = wp.element;

const AI_SEO_Sidebar   = () => {
	const postContent  = useSelect( select => select( "core/editor" ).getEditedPostContent(), [] );
	const postTitle    = useSelect( select => select( "core/editor" ).getEditedPostAttribute( "title" ), [] );
	const { editPost } = useDispatch( "core/editor" );

	const [keyword, setKeyword] = wp.element.useState( "" );
	const [results, setResults] = wp.element.useState( "" );

	const analyze      = async() => {
		setResults( "⏳ Analyzing..." );
		const response = await fetch(
			aiSEO.ajaxUrl,
			{
				method: "POST",
				headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
				body: new URLSearchParams(
					{
						action: "ai_seo_analyze",
						nonce: aiSEO.nonce,
						title: postTitle,
						content: postContent,
						meta_desc: "",
						keyword: keyword
					}
				)
			}
		);
		const res      = await response.json();
	if (res.success) {
		try {
			const data = JSON.parse( res.data.choices[0].message.content );
			setResults( `Score: ${data.score}\nSuggestions: ${data.suggestions.join( ", " )}` );
		} catch (e) {
			setResults( "⚠️ Failed to parse AI response" );
		}
	} else {
		setResults( "❌ Error: " + res.data.error );
	}
	};

	const improve = () => {
		// Replace editor content with improved content (same logic)
		// editPost({ content: improved_content_from_AI });
	};

	return (
		< Fragment >
			< PluginSidebarMoreMenuItem target = "seo-master-pro" >
				AI SEO Analyzer
			< / PluginSidebarMoreMenuItem >
			< PluginSidebar
				name                           = "ai-seo-sidebar"
				title                          = "AI SEO Analyzer"
			>
				< PanelBody >
					< TextControl
						label                  = "Focus Keyword"
						value                  = {keyword}
						onChange               = {setKeyword}
					/ >
					< Button isPrimary onClick = {analyze} style = {{width:"100%", marginBottom:"5px"}} > Analyze SEO < / Button >
					< Button onClick           = {improve} style = {{width:"100%"}} > Improve Content < / Button >
					< div style                = {{marginTop:"10px", whiteSpace:"pre-line"}} > {results} < / div >
				< / PanelBody >
			< / PluginSidebar >
		< / Fragment >
	);
};

registerPlugin( 'seo-master-pro', { render: AI_SEO_Sidebar } );
