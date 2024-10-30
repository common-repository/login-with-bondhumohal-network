/**
 * bondhumohal Admin Settings Save process
 */
jQuery( document ).ready( function () {

    jQuery( document ).on( 'click', '#bondhumohal-details', function ( e ) {

        e.preventDefault();

        var _app_id = jQuery( '#bondhumohal-app-id' ).val(),
            _app_seccret = jQuery( '#bondhumohal-app-secret' ).val(),
            _callback_url = jQuery( '#bondhumohal-callback-url' ).val(),
			_button_type = jQuery( '#bondhumohal-button-type' ).val();
		
        jQuery.ajax( {
            url: bondhumohal_admin.ajax_url,
            type: 'post',
            data: {
                action: 'bondhumohal_admin_settings',
                security: bondhumohal_admin._nonce,
				app_id: _app_id,
                app_secret: _app_seccret,
                callback_url: _callback_url,
				button_type: _button_type
            },
            success: function ( response ) {
                alert(response);
            }
        } );

    } );

} );
