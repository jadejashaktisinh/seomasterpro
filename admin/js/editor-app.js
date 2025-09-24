(function() {
    const el = wp.element.createElement;
    const RawHTML = wp.element.RawHTML; // for rendering HTML safely
    const useState = wp.element.useState;
    const createRoot = wp.element.createRoot;
    const Button = wp.components.Button;

    function SeoModalApp() {
        const stateOpen = useState(false);
        const isOpen = stateOpen[0];
        const setIsOpen = stateOpen[1];

        const stateLoading = useState(false);
        const loading = stateLoading[0];
        const setLoading = stateLoading[1];

        const stateData = useState(null);
        const seoData = stateData[0];
        const setSeoData = stateData[1];

        const stateError = useState(false);
        const isError = stateError[0];
        const setIsError = stateError[1];

        function analyzeSEO() {
            setLoading(true);
            setSeoData(null);
            setIsError(false);

            fetch(AI_SEO.ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: new URLSearchParams({
                    action: 'ai_seo_analyze',
                    _wpnonce: AI_SEO.nonce,
                    post_id: AI_SEO.post_id
                }),
            })
            .then(r => r.json())
            .then(function(res) {
                if (!res.success) {
                    setSeoData({ error: true });
                    setIsError(true);
                } else {
                    setSeoData(res.data);
                }
            })
            .catch(function(err) {
                console.error(err);
                setIsError(true);
            })
            .finally(function() {
                setLoading(false);
            });
        }

        function confirmInsert() {
            if (seoData && wp.data && wp.data.dispatch) {
                wp.data.dispatch('core/editor').editPost({
                    content: seoData.improved_content,
                    title: seoData.title
                });
                setIsOpen(false);
            }
        }

        return el('div', null,
            el(Button, {
                isPrimary: true,
                id:"ai-seo-analyze",
                style: { width: '100%', marginBottom: '5px' , },
                onClick: function() { setIsOpen(true); analyzeSEO(); }
            }, 'Analyze & Improve SEO'),

            isOpen && el('div', {
                style: {
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    backgroundColor: 'rgba(0,0,0,0.4)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    zIndex: 9999
                }
            },
                el('div', {
                    style: {
                        background: '#fff',
                        padding: '30px',
                        borderRadius: '8px',
                        width: '50%',
                        maxHeight: '70%',
                        overflowY: 'auto',
                        boxShadow: '0 4px 12px rgba(0,0,0,0.2)',
                        textAlign: 'left'
                    }
                },
                    el('h2', null, 'Improved Content Preview'),

                    loading && el('p', null, '⏳ Loading...'),
                    isError && el('p', { style:{ color:'red' } }, '❌ Error fetching data'),

                    seoData && !isError && el('div', null,
                        el('h3', null, 'Title:'),
                        el('p', { style:{ fontWeight:'bold', fontSize:'16px', marginBottom:'15px' } }, seoData.title),

                        el('h3', null, 'Content Preview:'),
                        el(RawHTML, null, seoData.improved_content), // renders HTML as it appears in editor

                        el('div', { style:{ marginTop:'20px', display:'flex', gap:'10px', justifyContent:'flex-end' } },
                            el(Button, { isPrimary:true, onClick:confirmInsert }, 'Confirm Insert'),
                            el(Button, { isSecondary:true, onClick:function(){ setIsOpen(false); } }, 'Cancel')
                        )
                    )
                )
            )
        );
    }

    wp.domReady(function(){
        const mountNode = document.getElementById('react-editor-root');
        if(mountNode){
            const root = createRoot(mountNode);
            root.render(el(SeoModalApp));
        } else {
            console.warn('Mount point #react-editor-root not found');
        }
    });

})();
