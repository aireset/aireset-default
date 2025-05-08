( function($) {
    "use strict";

	/**
	 * Activate fields
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 */
	jQuery(document).ready( function($) {
		// Checks if there is a hash in the URL
		let url_hash = window.location.hash;
		let active_tab_index = localStorage.getItem('aireset_default_get_admin_tab_index');

		if (url_hash) {
			// If there is a hash in the URL, activate the corresponding tab
			let target_tab = $('.aireset-default-wrapper a.nav-tab[href="' + url_hash + '"]');

			if (target_tab.length) {
				target_tab.click();
			}
		} else if (active_tab_index !== null) {
			// If there is no hash, activate the saved tab in localStorage
			$('.aireset-default-wrapper a.nav-tab').eq(active_tab_index).click();
		} else {
			// If there is no hash and localStorage is null, activate the general tab
			$('.aireset-default-wrapper a.nav-tab[href="#general"]').click();
		}
	});

	$(document).on('click', '.aireset-default-wrapper a.nav-tab', function() {
		// Stores the index of the active tab in localStorage
		let tab_index = $(this).index();
		
		localStorage.setItem('aireset_default_get_admin_tab_index', tab_index);

		let attr_href = $(this).attr('href');

		$('.aireset-default-wrapper a.nav-tab').removeClass('nav-tab-active');
		$('.aireset-default-form .nav-content').removeClass('active');
		$(this).addClass('nav-tab-active');
		$('.aireset-default-form').find(attr_href).addClass('active');

		return false;
	});


	/**
	 * Hide toast on click button or after 3 seconds
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 */
	jQuery( function($) {
		$(document).on('click', '.hide-toast', function() {
			$('.updated-option-success, .update-notice-aireset-default, .toast').fadeOut('fast');
		});

		setTimeout( function() {
			$('.update-notice-aireset-default').fadeOut('fast');
		}, 3000);
	});


	/**
	 * Send options to backend in AJAX
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 */
	jQuery(document).ready( function($) {
		let settings_form = $('form[name="aireset-default"]');
		let original_values = settings_form.serialize();
		var notification_delay;
		var debounce_timeout;
		let form_data = settings_form.serializeArray();
		let changed_data = {};
	
		/**
		 * Salva apenas os campos alterados via AJAX
		 */
		function ajax_save_options() {
			form_data = settings_form.serializeArray();
			changed_data = {};

			// 2) Garante que todo .toggle-switch apareça em form_data
			settings_form.find('.toggle-switch').each(function() {
				const name  = this.name;
				const value = this.checked ? 'yes' : 'no';

				// console.log(name)
				// console.log(value)
		
				// procura no array se já existe
				const existing = form_data.find(field => field.name === name);
				if ( existing ) {
					// se existe (estava marcado), atualiza o valor
					existing.value = value;
				} else {
					// se não existe (estava desmarcado), adiciona com “no”
					form_data.push({ name: name, value: value });
				}
			});

			// console.log(form_data)
	
			// Verifica quais campos foram alterados comparando com os valores originais
			/*$.each(form_data, function(_, field) {
				if (original_values.indexOf(field.name + "=" + encodeURIComponent(field.value)) === -1) {
					changed_data[field.name] = field.value;
				}
			});
			console.log(changed_data)
	
			// Se nenhum campo foi alterado, não envia AJAX
			if ($.isEmptyObject(changed_data)) {
				return;
			}*/
	
			$.ajax({
				url: aireset_default_params.ajax_url,
				type: 'POST',
				data: {
					action: 'aireset_default_admin_ajax_save_options',
					// form_data: changed_data,
					form_data: form_data,
				},
				success: function(response) {
					try {
						if (response.status === 'success') {
							original_values = settings_form.serialize();
	
							$('.aireset-default-wrapper').before(`
								<div class="toast toast-save-options toast-success show">
									<div class="toast-header bg-success text-white">
										<span class="me-auto">${response.toast_header_title}</span>
										<button class="btn-close btn-close-white ms-2 hide-toast" type="button"></button>
									</div>
									<div class="toast-body">${response.toast_body_title}</div>
								</div>
							`);
	
							if (notification_delay) {
								clearTimeout(notification_delay);
							}
	
							notification_delay = setTimeout(function() {
								$('.toast-save-options').fadeOut('fast', function() {
									$(this).remove();
								});
							}, 3000);
						}
						if (response.status === 'warning') {
							original_values = settings_form.serialize();
	
							$('.aireset-default-wrapper').before(`
								<div class="toast toast-save-options toast-warning show">
									<div class="toast-header bg-warning text-white">
										<span class="me-auto">${response.toast_header_title}</span>
										<button class="btn-close btn-close-white ms-2 hide-toast" type="button"></button>
									</div>
									<div class="toast-body">${response.toast_body_title}</div>
								</div>
							`);
	
							if (notification_delay) {
								clearTimeout(notification_delay);
							}
	
							notification_delay = setTimeout(function() {
								$('.toast-save-options').fadeOut('fast', function() {
									$(this).remove();
								});
							}, 3000);

						}
						if (response.status === 'error') {
							original_values = settings_form.serialize();
	
							$('.aireset-default-wrapper').before(`
								<div class="toast toast-save-options toast-error show">
									<div class="toast-header bg-error text-white">
										<span class="me-auto">${response.toast_header_title}</span>
										<button class="btn-close btn-close-white ms-2 hide-toast" type="button"></button>
									</div>
									<div class="toast-body">${response.toast_body_title}</div>
								</div>
							`);
	
							if (notification_delay) {
								clearTimeout(notification_delay);
							}
	
							notification_delay = setTimeout(function() {
								$('.toast-save-options').fadeOut('fast', function() {
									$(this).remove();
								});
							}, 3000);

						}
					} catch (error) {
						console.log(error);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error("AJAX request failed:", textStatus, errorThrown);
				}
			});
		}
	
		/**
		 * Monitora mudanças e aplica debounce antes de salvar
		 */
		settings_form.on('change input', 'input, select, textarea', function() {
			if (debounce_timeout) {
				clearTimeout(debounce_timeout);
			}
			debounce_timeout = setTimeout(ajax_save_options, 500);
		});
	});
		


	/**
	 * Control visibility to elements on change toggle switch
	 * 
	 * @since 1.0.0
	 * @version 3.8.0
	 */
	jQuery(document).ready( function() {
		visibility_controller('#enable_auto_apply_coupon_code', '.show-coupon-code-enabled');
		visibility_controller('#enable_inter_bank_pix_api', '.inter-bank-pix');
		visibility_controller('#enable_inter_bank_ticket_api', '.inter-bank-slip');
		visibility_controller('#enable_fill_address', '.require-auto-fill-address');
		visibility_controller('#enable_manage_fields', '.step-checkout-fields-container');
		visibility_controller('#enable_field_masks', '.require-input-mask');
		visibility_controller('#email_providers_suggestion', '.require-email-suggestions-enabled');
		visibility_controller('#enable_inter_bank_pix_api', '.require-enabled-inter-pix');
		visibility_controller('#enable_inter_bank_ticket_api', '.require-enabled-inter-slip-bank');
	});


	/**
	 * Allow only number bigger or equal 1 in inputs
	 * 
	 * @since 1.0.0
	 */
	jQuery( function($) {
		let inputField = $('.allow-numbers-be-1');
		
		inputField.on('input', function() {
			let inputValue = $(this).val();
		
			if (inputValue > 1) {
				$(this).val(inputValue);
			} else {
				$(this).val(1);
			}
		});
	});


	/**
	 * Allow insert only numbers, dot and dash in design tab
	 * 
	 * @since 1.0.0
	 */
	jQuery( function($) {
		$('.design-parameters').keydown( function(e) {
			let key = e.charCode || e.keyCode || 0;

			return (
				(key >= 96 && key <= 105) || // numbers (numeric keyboard)
				(key >= 48 && key <= 57) || // numbers (top keyboard)
				key == 190 || // dot
				key == 189 || key == 109 || // dash
				key == 8 // backspace
			);
		});
	});


	/**
	 * Open WordPress midia library popup on click
	 * 
	 * @since 1.0.0
	 */
	jQuery(document).ready( function($) {
		var file_frame;
	
		$('#aireset-default-search-header-logo').on('click', function(e) {
			e.preventDefault();
	
			// If the media frame already exists, reopen it
			if (file_frame) {
				file_frame.open();
				return;
			}
	
			// create midia frame
			file_frame = wp.media.frames.file_frame = wp.media({
				title: aireset_default_params.set_logo_modal_title,
				button: {
					text: aireset_default_params.use_this_image_title,
				},
				multiple: false
			});
	
			// When an image is selected, execute the callback function
			file_frame.on('select', function() {
				var attachment = file_frame.state().get('selection').first().toJSON();
				var imageUrl = attachment.url;
			
				// Update the input value with the URL of the selected image
				$('input[name="search_image_header_checkout"]').val(imageUrl).trigger('change'); // Force change
			});

			file_frame.open();
		});
	});


	/**
	 * Display popups
	 * 
	 * @since 2.3.0
	 * @version 3.8.0
	 */
	jQuery( function($) {
		display_popup( $('#inter_bank_credencials_settings'), $('#inter_bank_credendials_container'), $('#inter_bank_credendials_close') );
		display_popup( $('#inter_bank_pix_settings'), $('#inter_bank_pix_container'), $('#inter_bank_pix_close') );
		display_popup( $('#inter_bank_slip_settings'), $('#inter_bank_slip_container'), $('#inter_bank_slip_close') );
		display_popup( $('#require_inter_bank_module_trigger'), $('#require_inter_bank_module_container'), $('#require_inter_bank_module_close') );
		display_popup( $('.require-pro'), $('#popup-pro-notice'), $('.require-pro-close') );
		display_popup( $('#set_ip_api_service_trigger'), $('.set-api-service-container'), $('.set-api-service-close') );
		display_popup( $('#add_new_checkout_fields_trigger'), $('.add-new-checkout-fields-container'), $('.add-new-checkout-fields-close') );
		display_popup( $('#auto_fill_address_api_trigger'), $('.auto-fill-address-api-container'), $('.auto-fill-address-api-close') );
		display_popup( $('#set_new_font_family_trigger'), $('#set_new_font_family_container'), $('#close_new_font_family') );
		display_popup( $('#fcw_reset_settings_trigger'), $('#fcw_reset_settings_container'), $('#fcw_close_reset') );
		display_popup( $('#add_new_checkout_condition_trigger'), $('#add_new_checkout_condition_container'), $('#close_add_new_checkout_condition') );
		display_popup( $('#set_email_providers_trigger'), $('#set_email_providers_container'), $('#close_set_email_providers') );
	});


	/**
	 * Helper color selector
	 * 
	 * @since 2.4.0
	 * @version 2.6.0
	 */
	jQuery(document).ready( function($) {
		$('.get-color-selected').on('input', function() {
			var color_value = $(this).val();
	
			$(this).closest('.color-container').find('.form-control-color').val(color_value);
		});
	
		$('.form-control-color').on('input', function() {
			var color_value = $(this).val();
	
			$(this).closest('.color-container').find('.get-color-selected').val(color_value);
		});

		$('.reset-color').on('click', function(e) {
			e.preventDefault();
			var color_value = $(this).data('color');

			$(this).closest('.color-container').find('.form-control-color').val(color_value);
			$(this).closest('.color-container').find('.get-color-selected').val(color_value).change();
		});
	});



	/**
	 * Before active license actions
	 * 
	 * @since 3.0.0
	 * @version 3.8.0
	 */
	jQuery( function($) {
		$('.pro-version').prop('disabled', true);

		$('#active_license_form').on('click', function() {
			$('#popup-pro-notice').removeClass('show');
			$('.aireset-default-wrapper a.nav-tab[href="#about"]').click();
			window.scrollTo(0, 0);
		});
	});


	/**
	 * Include Bootstrap date picker
	 * 
	 * @since 3.2.0
	 */
	jQuery(document).ready( function($) {
		/**
		 * Initialize Bootstrap datepicker
		 */
		$('.dateselect').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true,
			language: 'pt-BR',
		});
		
		$(document).on('focus', '.dateselect', function() {
			if ( ! $(this).data('datepicker') ) {
				$(this).datepicker({
					format: 'dd/mm/yyyy',
					todayHighlight: true,
					language: 'pt-BR',
				});
			}
		});
	});

	/**
	 * Clear activation cache process
	 * 
	 * @since 3.8.0
	 */
	jQuery(document).ready( function($) {
		$('#aireset_default_clear_activation_cache').on('click', function(e) {
			e.preventDefault();

			let btn = $(this);
			let btn_html = btn.html();
			let btn_width = btn.width();
			let btn_height = btn.height();

			console.log(btn_html);

			// keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);

			// Add spinner inside button
			btn.html('<span class="spinner-border spinner-border-sm"></span>');

			$.ajax({
				url: aireset_default_params.ajax_url,
				type: 'POST',
				data: {
					action: 'aireset_default_clear_activation_cache_action',
				},
				success: function(response) {
					try {
						if ( response.status === 'success' ) {
							btn.html(btn_html);

							$('.aireset-default-wrapper').before(`<div class="toast toast-clear-cache toast-success show">
								<div class="toast-header bg-success text-white">
									<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
									<span class="me-auto">${response.toast_header_title}</span>
									<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
								</div>
								<div class="toast-body">${response.toast_body_title}</div>
							</div>`);

							setTimeout( function() {
								$('.toast-clear-cache').fadeOut('fast');
							}, 3000);

							setTimeout( function() {
								$('.toast-clear-cache').remove();
							}, 3500);
						}
					} catch (error) {
						console.log(error);
					}
				}
			});
		});
	});


	/**
	 * Display modal reset plugin
	 * 
	 * @since 3.8.0
	 */
	jQuery(document).ready( function($) {
		$(document).on('click', '#confirm_reset_settings', function(e) {
			e.preventDefault();
			
			let btn = $(this);
			let btn_html = btn.html();
			let btn_width = btn.width();
			let btn_height = btn.height();

			// keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);

			// Add spinner inside button
			btn.html('<span class="spinner-border spinner-border-sm"></span>');

			$.ajax({
				url: aireset_default_params.ajax_url,
				type: 'POST',
				data: {
					action: 'aireset_default_reset_plugin_action',
				},
				success: function(response) {
					try {
						if ( response.status === 'success' ) {
							btn.html(btn_html);

							$('#fcw_close_reset').click();

							$('.aireset-default-wrapper').before(`<div class="toast toast-reset-plugin toast-success show">
								<div class="toast-header bg-success text-white">
									<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
									<span class="me-auto">${response.toast_header_title}</span>
									<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
								</div>
								<div class="toast-body">${response.toast_body_title}</div>
							</div>`);

							setTimeout( function() {
								location.reload();
							}, 1000);
						} else {
							$('.aireset-default-wrapper').before(`<div class="toast toast-reset-plugin-error toast-success show">
								<div class="toast-header bg-success text-white">
									<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
									<span class="me-auto">${response.toast_header_title}</span>
									<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
								</div>
								<div class="toast-body">${response.toast_body_title}</div>
							</div>`);

							setTimeout( function() {
								$('.toast-reset-plugin-error').fadeOut('fast');
							}, 3000);

							setTimeout( function() {
								$('.toast-reset-plugin-error').remove();
							}, 3500);
						}
					} catch (error) {
						console.log(error);
					}
				}
			});
		});
	});


	/**
	 * Display toast on offline connection
	 * 
	 * @since 4.5.0
	 */
	jQuery(document).ready( function($) {
		function show_offline_toast() {
			const offline_toast = `<div class="toast toast-offline-connection toast-warning show">
					<div class="toast-header bg-warning text-white">
						<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
						<span class="me-auto">${aireset_default_params.offline_toast_header}</span>
						<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
					</div>
					<div class="toast-body">${aireset_default_params.offline_toast_body}</div>
				</div>`;
	
			$('.aireset-default-wrapper').before(offline_toast);
		}
	
		function update_online_status() {
			if (navigator.onLine) {
				$('.toast-offline-connection').remove();
			} else {
				show_offline_toast();
			}
		}
	
		// check connectivity on load page
		update_online_status();
	
		// listener connectivity changes
		window.addEventListener('online', update_online_status);
		window.addEventListener('offline', update_online_status);
	});


	/**
	 * Install external modules (plugins) in AJAX
	 * 
	 * @since 3.8.2
	 */
	jQuery(document).ready( function($) {
		$('.install-module').on('click', function(e) {
			e.preventDefault();
	
			let btn = $(this);
			let plugin_url = btn.data('plugin-url');
			let plugin_slug = btn.data('plugin-slug');
			let btn_html = btn.html();
			let btn_width = btn.width();
			let btn_height = btn.height();
	
			// Keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);
			btn.html('<span class="spinner-border spinner-border-sm"></span>');
			btn.prop('disabled', true);
	
			$.ajax({
				type: 'POST',
				url: aireset_default_params.ajax_url,
				data: {
					action: 'aireset_default_install_modules_action',
					plugin_url: plugin_url,
					plugin_slug: plugin_slug,
				},
				success: function(response) {
					if (response.status === 'success') {
						btn.removeClass('btn-primary').addClass('btn-success');
						btn.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#fff" d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>');
	
						$('.aireset-default-wrapper').before(`<div class="toast toast-success show">
							<div class="toast-header bg-success text-white">
								<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
								<span class="me-auto">${response.toast_header}</span>
								<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
							</div>
							<div class="toast-body">${response.toast_body}</div>
						</div>`);
	
						setTimeout( function() {
							location.reload();
						}, 1000);
					} else {
						btn.html(btn_html);
	
						$('.aireset-default-wrapper').before(`<div class="toast toast-module-error toast-danger show">
							<div class="toast-header bg-danger text-white">
								<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
								<span class="me-auto">${response.toast_header}</span>
								<button class="btn-close btn-close-white ms-2 hide-toast" type="button" aria-label="Fechar"></button>
							</div>
							<div class="toast-body">${response.toast_body}</div>
						</div>`);
	
						setTimeout( function() {
							$('.toast-module-error').fadeOut('fast');
						}, 3000);
	
						setTimeout( function() {
							$('.toast-module-error').remove();
						}, 3500);
					}
					
					btn.prop('disabled', false);
				},
				error: function(response) {
					btn.html(btn_html);
					btn.prop('disabled', false);
					alert('Error installing the plugin: ' + response.responseText);
				}
			});
		});
	
	
		/**
		 * Activate plugin when installed
		 * 
		 * @since 3.8.2
		 */
		$('.activate-plugin').on('click', function(e) {
			e.preventDefault();
	
			let btn = $(this);
			let plugin_slug = btn.data('plugin-slug');
			let btn_html = btn.html();
			let btn_width = btn.width();
			let btn_height = btn.height();
	
			// Keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);
			btn.html('<span class="spinner-border spinner-border-sm"></span>');
			btn.prop('disabled', true);
	
			$.ajax({
				type: 'POST',
				url: aireset_default_params.ajax_url,
				data: {
					action: 'aireset_default_activate_plugin_action',
					plugin_slug: plugin_slug,
				},
				success: function(response) {
					if (response.status === 'success') {
						btn.removeClass('btn-primary').addClass('btn-success');
						btn.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#fff" d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>');
	
						$('.aireset-default-wrapper').before(`<div class="toast toast-success show">
							<div class="toast-header bg-success text-white">
								<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
								<span class="me-auto">${response.toast_header}</span>
								<button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
							</div>
							<div class="toast-body">${response.toast_body}</div>
						</div>`);
	
						setTimeout( function() {
							location.reload();
						}, 1000);
					} else {
						btn.html(btn_html);
	
						$('.aireset-default-wrapper').before(`<div class="toast toast-module-error toast-danger show">
							<div class="toast-header bg-danger text-white">
								<svg class="icon icon-white me-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
								<span class="me-auto">${response.toast_header}</span>
								<button class="btn-close btn-close-white ms-2 hide-toast" type="button" aria-label="Fechar"></button>
							</div>
							<div class="toast-body">${response.toast_body}</div>
						</div>`);
	
						setTimeout( function() {
							$('.toast-module-error').fadeOut('fast');
						}, 3000);
	
						setTimeout( function() {
							$('.toast-module-error').remove();
						}, 3500);
					}
					btn.prop('disabled', false);
				},
				error: function() {
					btn.html(btn_html);
					alert('Erro ao ativar o plugin.');
					btn.prop('disabled', false).text('Ativar módulo');
				}
			});
		});
	});

})(jQuery);