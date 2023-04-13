import $ from "jquery";
import { __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';

/**
 * Override Wordpress default ImageEdit library
 * 
 * - Override "open" and "save" functions to open our php file
 * - Auto create crop area by x and y value sended by our php file
 */
domReady( function () {
	
    if ( ! window.imageEdit ){
        console.log( "Wordpress 'image-edit.js' not yet initialized." );
    }
    
	//console.log( "Global imageEdit", window.imageEdit );
	

	/**
     * Override default imageEdit.open with 1 line of code
     * change 'ajax.action' to our action to load our php file
     * 
     * @see https://github.com/WordPress/WordPress/blob/master/wp-admin/js/image-edit.js
     * @see https://atimmer.github.io/wordpress-jsdoc/-_enqueues_lib_image-edit.js.html
     * 
     */
	window.imageEdit.open = function( postid, nonce, view ){

		console.log( "CUSTOM IMAGE EDITOR OPENED" );

		this._view = view;

		const elem = $( '#image-editor-' + postid );
		const head = $( '#media-head-' + postid );
		const btn = $( '#imgedit-open-btn-' + postid );
		const spin = btn.siblings( '.spinner' );

		/*
		* Instead of disabling the button, which causes a focus loss and makes screen
		* readers announce "unavailable", return if the button was already clicked.
		*/
		if ( btn.hasClass( 'button-activated' ) ) {
			return;
		}

		spin.addClass( 'is-active' );

		const data = {
			'action': 'pronotron_image_editor', // --> ONLY CHANGED PART
			'_ajax_nonce': nonce,
			'postid': postid,
			'do': 'open'
		};

		const dfd = $.ajax( {
			url:  ajaxurl,
			type: 'post',
			data: data,
			beforeSend: function() {
				btn.addClass( 'button-activated' );
			}
		} ).done( function( response ) {
			var errorMessage;

			if ( '-1' === response ) {
				errorMessage = __( 'Could not load the preview image.' );
				elem.html( '<div class="notice notice-error" tabindex="-1" role="alert"><p>' + errorMessage + '</p></div>' );
			}

			if ( response.data && response.data.html ) {
				elem.html( response.data.html );
			}

			head.fadeOut( 'fast', function() {
				elem.fadeIn( 'fast', function() {
					if ( errorMessage ) {
						$( document ).trigger( 'image-editor-ui-ready' );
					}
				} );
				btn.removeClass( 'button-activated' );
				spin.removeClass( 'is-active' );
			} );

			// Initialise the Image Editor now that everything is ready.
			window.imageEdit.init( postid );
		} );

		return dfd;
	};



    /**
     * Override default imageEdit.open with 1 line of code
     * change 'ajax.action' to our action to load our php file
     * 
     * @see https://github.com/WordPress/WordPress/blob/master/wp-admin/js/image-edit.js
     * @see https://atimmer.github.io/wordpress-jsdoc/-_enqueues_lib_image-edit.js.html
     * 
     */
	window.imageEdit.save = function( postid, nonce ){

		console.log( "CUSTOM IMAGE EDITOR SAVED" );

		var data,
			target = this.getTarget( postid ),
			history = this.filterHistory( postid, 0 ),
			self = this;

		if ( '' === history ) {
			return false;
		}

		this.toggleEditor( postid, 1 );

		data = {
			'action': 'pronotron_image_editor',  // --> ONLY CHANGED PART
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': history,
			'target': target,
			'context': $( '#image-edit-context' ).length ? $( '#image-edit-context' ).val() : null,
			'do': 'save'
		};

		// Post the image edit data to the backend.
		$.post( ajaxurl, data, function( response ) {
			// If a response is returned, close the editor and show an error.
			if ( response.data.error ) {
				$( '#imgedit-response-' + postid )
					.html( '<div class="notice notice-error" tabindex="-1" role="alert"><p>' + response.data.error + '</p></div>' );

				imageEdit.close(postid);
				wp.a11y.speak( response.data.error );
				return;
			}

			if ( response.data.fw && response.data.fh ) {
				$( '#media-dims-' + postid ).html( response.data.fw + ' &times; ' + response.data.fh );
			}

			if ( response.data.thumbnail ) {
				$( '.thumbnail', '#thumbnail-head-' + postid ).attr( 'src', '' + response.data.thumbnail );
			}

			if ( response.data.msg ) {
				$( '#imgedit-response-' + postid )
					.html( '<div class="notice notice-success" tabindex="-1" role="alert"><p>' + response.data.msg + '</p></div>' );

				wp.a11y.speak( response.data.msg );
			}

			if ( self._view ) {
				self._view.save();
			} else {
				imageEdit.close( postid );
			}
		});
	};


    /**
     * Set ratios of defined image sizes
     * @param {number} postid - editing attachment id 
     * @param {string} nonce - wp nonce
     * @param {number} x - x ratio
     * @param {number} y - y ratio
     * 
     */
    window.imageEdit.ySetRatioSelection = function( postid, nonce, x, y ) {

        const img = $( '#image-preview-' + postid );
        const height = img.innerHeight();
        const width = img.innerWidth();

		const data = {};

		/** Landscape */
		if ( x > y ){
			data.minWidth = width;
			data.minHeight = width / x * y;

			data.leftX = 0;
			data.leftY = ( height - data.minHeight ) / 2;
			data.rightX = width;
			data.rightY = data.leftY + data.minHeight;
		} 
		/** Portrait */
		else {
			data.minWidth = height / y * x;
			data.minHeight = height;

			data.leftX = ( width - data.minWidth ) / 2;
			data.leftY = 0;
			data.rightX = data.leftX + data.minWidth;
			data.rightY = height;
		}
		// console.log(data)


        this.iasapi.setOptions({
            //aspectRatio: x + ':' + y,
            minWidth: data.minWidth,
			maxWidth: data.minWidth,
            minHeight: data.minHeight,
			maxHeight: data.minHeight,
            show: true,
        });

		/**
		 * Set the current selection
		 *
		 * @param x1
		 *            X coordinate of the upper left corner of the selection area
		 * @param y1
		 *            Y coordinate of the upper left corner of the selection area
		 * @param x2
		 *            X coordinate of the lower right corner of the selection area
		 * @param y2
		 *            Y coordinate of the lower right corner of the selection area
		 * @param noScale
		 *            If set to <code>true</code>, scaling is not applied to the
		 *            new selection
		 */
        this.iasapi.setSelection( data.leftX, data.leftY, data.rightX, data.rightY, true );
        //this.iasapi.update();
	};

});