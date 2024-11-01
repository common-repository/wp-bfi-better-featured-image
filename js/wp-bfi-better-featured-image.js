jQuery(document).ready(function($) {

	//	SETS AND REPLACES FEATURED IMAGE
	//	----------------------------------------------------------------------------------------------------

		$('.button[data-action="set"], .button[data-action="set-default"], .button[data-action="replace"], .button[data-action="replace-default"]').on('click', function(e) {

			e.preventDefault();

			var action = $(this).data('action');
			var that = $(this).closest('tr');
			var ajax_action, button_string, mediaUploader;

			switch(action) {
				case 'replace':
					ajax_action = 'wp_bfi_ajax_featured_image_set';
					button_string = wp_bfi_lang.featured_image_replace;
					break;
				case 'replace-default':
					ajax_action = 'wp_bfi_ajax_featured_image_set_default';
					button_string = wp_bfi_lang.featured_image_replace_default;
					break;
				case 'set':
					ajax_action = 'wp_bfi_ajax_featured_image_set';
					button_string = wp_bfi_lang.featured_image_set;
					break;
				case 'set-default':
					ajax_action = 'wp_bfi_ajax_featured_image_set_default';
					button_string = wp_bfi_lang.featured_image_set_default;
					break;
				default:
					button_string = wp_bfi_lang.featured_image;
			}

			mediaUploader = wp.media.frames.file_frame = wp.media({
				title: button_string,
				button: { text: button_string },
				multiple: false
			});

			mediaUploader.on('select', function() {

				attachment = mediaUploader.state().get('selection').first().toJSON();

	      		var data = {
					'action': ajax_action,
					'attachment-id': attachment.id,
					'post-id': that.prop('id').replace(/\D/g, ''),
					'post-type': that.data('post-type')
				};

				if( action == 'replace' || action == 'replace-default' ) {

					$.post(ajaxurl, data, function(response) {
						that.find('.wp-bfi-featured-image-info').fadeOut(500);
						that.find('.wp-bfi-featured-image').fadeOut(500, function() {
	        				that.find('.wp-bfi-featured-image').attr({ src: response.featured_image_url, alt: response.featured_image_title, title: response.featured_image_title }).removeAttr('sizes srcset');
	        				that.find('.button[data-action^="view"]').attr('href', response.featured_image_url);
	    				}).fadeIn(500);
					}, 'json');

				} else if( action == 'set' || action == 'set-default' ) {

					$.post(ajaxurl, data, function(response) {
						that.find('.wp-bfi-featured-image-info').fadeOut(500);
						that.find('.button[data-action^="set"]').fadeOut(500, function() {
							that.find('.wp-bfi-featured-image').remove();
							that.find('.wp-bfi-featured-image-info').before('<img src="' + response.featured_image_url + '" class="wp-bfi-featured-image wp-post-image" alt="' + response.featured_image_title + '" title="' + response.featured_image_title + '" />');
							that.find('.button[data-action^="view"]').attr('href', response.featured_image_url);
							that.find('.wp-bfi-wrapper').fadeIn(500);
						});
					}, 'json');

				}

			});

			mediaUploader.open();

		});


	//	DISPLAYS FEATURED IMAGE INFORMATION
	//	----------------------------------------------------------------------------------------------------

		$('.button[data-action="info"], .button[data-action="info-default"]').on('click', function(e) {

			e.preventDefault();

			var action = $(this).data('action');
			var that = $(this).closest('tr');

			if( action == 'info' ) {

				var data = {
					'action': 'wp_bfi_ajax_featured_image_info',
					'post-id': that.prop('id').replace(/\D/g, '')
				};

			} else if( action == 'info-default' ) {

				var data = {
					'action': 'wp_bfi_ajax_featured_image_info_default',
					'post-type': that.data('post-type')
				};

			}

			$.post(ajaxurl, data, function(response) {
				that.find('.wp-bfi-featured-image-info').html(response).slideToggle({duration:500});
			});

		});


	//	UNLINKS FEATURED IMAGE FOR SPECIFIED POST
	//	----------------------------------------------------------------------------------------------------

		$('.button[data-action="unlink"], .button[data-action="unlink-default"]').on('click', function(e) {

			e.preventDefault();

			var action = $(this).data('action');
			var that = $(this).closest('tr');
			var confirm_prompt;

			switch(action) {
				case 'unlink':
					confirm_prompt = wp_bfi_lang.unlink_confirm;
					break;
				case 'unlink-default':
					confirm_prompt = wp_bfi_lang.unlink_default_confirm;
					break;
				default:
					break;
			}

			if( confirm(confirm_prompt) ) {

				if( action == 'unlink' ) {

					var data = {
						'action': 'wp_bfi_ajax_featured_image_unlink',
						'post-id': that.prop('id').replace(/\D/g, '')
					};

				} else if( action == 'unlink-default' ) {

					var data = {
						'action': 'wp_bfi_ajax_featured_image_unlink_default',
						'post-type': that.data('post-type')
					};

				}

				$.post(ajaxurl, data, function() {
					that.find('.wp-bfi-wrapper').fadeOut(500, function() {
						that.find('.wp-bfi-featured-image').remove();
						that.find('.wp-bfi-featured-image-info').empty().removeAttr('style');
						that.find('.button[data-action^="view"]').attr('href', '#');
						that.find('.button[data-action^="set"]').fadeIn(500);
					});
				});

			}

		});


	//	UNLINKS FEATURED IMAGES FOR SPECIFIED POST TYPES
	//	----------------------------------------------------------------------------------------------------

		$('.button[data-action="unlink-all"]').on('click', function(e) {

			e.preventDefault();

			var that = $(this).closest('tr');

			if( confirm(wp_bfi_lang.unlink_all_confirm) ) {

				that.find('.spinner').css('visibility', 'visible');

				var data = {
					'action': 'wp_bfi_ajax_featured_image_unlink_all',
					'post-type': that.data('post-type')
				};

				$.post(ajaxurl, data, function() {
					that.find('.spinner').css('visibility', 'hidden');
					that.find('.num-with-featured-image').html('0');
				});

			}

		});


	//	DISABLE WP BFI FOR POST TYPES
	//	----------------------------------------------------------------------------------------------------

		$('.wp-bfi-featured-image-disabled').on('click', function(e) {

			var disabled = $(this).is(':checked') ? 1 : 0;
			var that = $(this).closest('tr');

			var data = {
				'action': 'wp_bfi_ajax_wp_bfi_disabled',
				'post-type': that.data('post-type'),
				'disabled': disabled
			};

			$.post(ajaxurl, data);

		});


});
