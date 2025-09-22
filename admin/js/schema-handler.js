jQuery( document ).ready(
	function ($) {
		$( 'input[name="base_schema_types[]"]' ).on(
			'change',
			function () {
				var type = $( this ).val();
				if ($( this ).is( ':checked' )) {
					$( '.schema-settings-' + type ).slideDown();
				} else {
					$( '.schema-settings-' + type ).slideUp();
				}
			}
		);
	}
);
