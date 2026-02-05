/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	blockAjax = false;

$(document).ready(function() {

	activateSortable();

	$(document).on('change', '.hookSelector', function() {
		var hook_name = $(this).val();
		$('.hook-content#'+hook_name).addClass('active').siblings().removeClass('active');
		$('.callSettings.active').click();
		scrollUpAllCarousels();
	});

	$('.hookSelector').change();

	$(document).on('click', '.callSettings', function(e) {
		e.preventDefault();
		scrollUpAllCarousels();
		$el = $(this);
		if ($el.hasClass('active')) {
			$('#settings-content').slideUp(function() {
				$(this).html('');
				$('.callSettings').removeClass('active');
			});
			return;
		}
		$('#settings-content').hide().html('');
		$('.callSettings').removeClass('active');
		var settings_type = $(this).data('settings');
		var hook_name = $(this).closest('form').find('.hookSelector').val();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=CallSettingsForm&settings_type='+settings_type+'&hook_name='+hook_name,
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if ('form_html' in r){
					$('#settings-content').html(r.form_html).slideDown().tooltip({selector: '.label-tooltip'});
					$el.addClass('active');
				}
			},
			error: function(r) {
				$('#settings-content').hide().html('');
				console.warn(r.responseText);
			}
		});
	})

	$(document).on('submit', '.w-settings-form', function(e){
		e.preventDefault();
	}).on('focusin', '.save-on-the-fly', function(e) {
		$(this).data('initial-value', $(this).val());
	}).on('focusout keyup', '.save-on-the-fly', function(e) {
		var $el = $(this),
			$form = $el.closest('form'),
			formData = $form.serialize(),
			$wrapper = $form.closest('.c-wrapper');
		if ((e.type == 'keyup' && e.which != 13) || $el.val() == $el.data('initial-value')) {
			return;
		}
		$('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveWrapperSettings',
			data: {
				form_data: formData,
				ids_in_wrapper: getOrderedIds($wrapper),
			},
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if ('errors' in r) {
					var errorHTML = '<div class="wrapper-settings-error">'+r.errors+'</div>';
					$form.before(errorHTML);
				} else if ('saved' in r) {
					$.growl.notice({title: '', message: savedTxt});
					$el.parent().addClass('just-saved');
					setTimeout(function(){
						$el.parent().removeClass('just-saved');
					}, 1000);
				}
				if (r.id_wrapper_new) {
					updateWrapperId($wrapper, r.id_wrapper_new);
				}
			},
			error: function(r) {
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.chk-action', function(e){
		e.preventDefault();
		var $checkboxes = $(this).closest('#settings_content').find('input[type="checkbox"]');
		if ($(this).hasClass('checkall')){
			$checkboxes.each(function(){
				$(this).prop('checked', true);
			});
		}
		else if ($(this).hasClass('uncheckall')){
			$checkboxes.each(function(){
				$(this).prop('checked', false);
			});
		}
		else if ($(this).hasClass('invert')){
			$checkboxes.each(function(){
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	});

	$(document).on('change', 'select.exc', function() {
		var value = $(this).val(),
			hasIds = (value != 0) && ($(this).hasClass('customer') || value.lastIndexOf('_all') == -1);
		$(this).closest('.exceptions-block').toggleClass('has-ids', hasIds);
	}).on('click', '.device-type', function(e){
		e.preventDefault();
		var device = $(this).data('type');
		$(this).closest('.form-horizontal').attr('class', 'form-horizontal '+device).data('device', device).attr('data-device', device);
		toggleExternalTplFields();
	}).on('click', '.lock-device-settings', function(e){
		e.preventDefault();
		$(this).closest('.device-input-wrapper').toggleClass('locked unlocked');
	});

	$(document).on('click', '.saveHookSettings', function(e) {
		e.preventDefault();
		let $parent = $('#settings-content'),
			params = 'action=SaveHookSettings&'+$(this).closest('form').serialize(),
			response = function(r) {
				if (r.errors) {
					$parent.find('form').before('<div class="thrown-errors">'+r.errors+'</div>');
				} else if (r.saved) {
					$parent.slideUp(function() {
						$('.callSettings').removeClass('active');
						$(this).html('');
						$.growl.notice({title: '', message: savedTxt});
					});
				}
			};
		$parent.find('.thrown-errors').remove();
		ecb.ajaxRequest(params, response);
	});

	$(document).on('click', '.hide-settings', function(){
		$('.callSettings.active').click();
	});

	$(document).on('click', '.chk-action', function(e){
		e.preventDefault();
		var $checkboxes = $(this).closest('#settings-content').find('input[type="checkbox"]');
		if ($(this).hasClass('checkall')){
			$checkboxes.each(function(){
				$(this).prop('checked', true);
			});
		}
		else if ($(this).hasClass('uncheckall')){
			$checkboxes.each(function(){
				$(this).prop('checked', false);
			});
		}
		else if ($(this).hasClass('invert')){
			$checkboxes.each(function(){
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	});

	$(document).on('click', '.bulk-select, .bulk-unselect', function(e){
		e.preventDefault();
		var checked = $(this).hasClass('bulk-select');
		$('.c-item:visible .carousel-box').prop('checked', checked);
	});

	$(document).on('click', '.addCarousel', function(e) {
		scrollUpAllCarousels();
		$('.c-item[data-id="0"]').remove(); // make sure there is only one carousel with id=0
		let $cWrapper = $(this).closest('.c-wrapper'),
			$cList = $cWrapper.find('.carousel-list'),
			hook_name = $(this).closest('.hook-content').attr('id'),
			id_wrapper = $cWrapper.attr('data-id');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=CallCarouselForm&id_carousel=0&hook_name='+hook_name+'&id_wrapper='+id_wrapper,
			dataType : 'json',
			success: function(r) {
				$newItem = $(r.html).prependTo($cList);
				ecb.prepareCarouselForm($newItem);
				let carousels_num = $('#'+hook_name).find('.c-item:visible').length;
				$('.hookSelector').find('option[value="'+hook_name+'"]').text(hook_name+' ('+carousels_num+')');
			},
			error: function(r) {
				console.warn(r.responseText);
			}
		});
	});

	$(document).on('click', '.addWrapper', function(e){
		// at least one wrapper is always availabe in .hook-content
		$wOrig = $('.hook-content:visible').find('.c-wrapper').first();
		$wOrig.clone().insertBefore($wOrig).addClass('empty').attr('data-id', 0)
		.find('.carousel-list').removeClass('ui-sortable')
		.find('.c-item').remove();
		activateSortable();
	}).on('click', '.deleteWrapper', function(e){
		// button is available only in empty wrappers
		$wrapper = $(this).closest('.c-wrapper');
		if (!$wrapper.siblings().length || $wrapper.find('.c-wrapper').length) {
			alert('This wrapper can not be removed');
		} else {
			$wrapper.remove();
		}

	});

	$(document).on('click', '.editCarousel', function(e) {
		let $item = $(this).closest('.c-item'),
			callForm = !$item.hasClass('open'),
			id = $item.attr('data-id'),
			id_wrapper = $item.closest('.c-wrapper').attr('data-id');
		scrollUpAllCarousels();
		if (callForm) {
			let params = 'action=CallCarouselForm&id_carousel='+id+'&id_wrapper='+id_wrapper,
				response = function(r) {
					$item.html($(r.html).html());
					ecb.prepareCarouselForm($item);
				};
			ecb.ajaxRequest(params, response);
		}
	}).on('click', '.deleteCarousel', function() {
		if (confirm(areYouSureTxt)) {
			let params = {
					action: 'DeleteCarousel',
					id_carousel: $(this).closest('.c-item').attr('data-id'),
					clear_cache_for_hook: $('.hookSelector').val(),
				},
				response = function(r) {
					if (r.deleted) {
						removeCarouselRows(params.id_carousel);
					}
				};
			ecb.ajaxRequest(params, response);
		}
	});

	$(document).on('change', 'select[name="settings[carousel][type]"]', function() {
		$(this).closest('.carousel-settings').toggleClass('no-scripts', $(this).val() != 1);
	});

	function getItemType(carouselType){
		var otherTypes = {manufacturers:'m', suppliers:'s', categories:'c', subcategories:'c'};
		return (carouselType in otherTypes) ? otherTypes[carouselType] : 'p';
	}

	$(document).on('change', '#carousel_type', function() {
		var carouselType = $(this).val(),
			itemType = getItemType(carouselType),
			isProductType = itemType == 'p';
		$('.current-p-option').removeClass('current-p-option').removeClass('hidden');
		$('.p-option').toggleClass('hidden', !isProductType);
		$('.special-settings, .special_option').addClass('hidden');
		$('.special_option.'+carouselType).removeClass('hidden').closest('.special-settings').removeClass('hidden');
		$('.not-for-some'+(!isProductType ? ':not(.p-option)' : '')).removeClass('hidden').filter('.not-for-'+carouselType).addClass('hidden');
		$('.not-for-some-types').removeClass('hidden').filter('.not-for-'+itemType).addClass('hidden');
		$('.select_image_type').each(function() {
			let $visibleOptions = $(this).find('option').not('.hidden'),
				currentValue = $(this).val(),
				updValue = false;
			if (!$(this).hasClass('ready')) { // #carousel_type is changed when carousel form is opened
				$(this).addClass('ready').data('saved-value', currentValue);
			}
			if (!$visibleOptions.filter('option[value="'+currentValue+'"]').length) {
				$.each([$(this).data('saved-value'), 'home_default', 'medium_default',
					$visibleOptions.first().val()], function(i, anotherPossibleValue) {
					if ($visibleOptions.filter('option[value="'+anotherPossibleValue+'"]').length) {
						updValue = anotherPossibleValue;
						return false;
					}
				});
			}
			if (updValue !== false) {
				$(this).val(updValue).change();
			}
		});
		if (isProductType) {
			$('.tpl-settings').find('.form-group').not('.hidden').addClass('current-p-option');
			toggleExternalTplFields();
		} else {
			hideEmptyColumns();
		}
		toggleSalesDaysOpton();
		// update name field if it is not saved yet
		if ($('.name-not-saved').length) {
			$('.name-not-saved').val($.trim($(this).find('option:selected').text().split(' (')[0]));
		}
	}).on('change', '.select_order_by', function() {
		toggleSalesDaysOpton();
	}).on('change', '.select_external_tpl', function() {
		toggleExternalTplFields();
	});

	function toggleSalesDaysOpton() {
		let type = $('#carousel_type').val(),
			show = type == 'bestsellers' || type == 'boughttogether'
				|| $('.select_order_by:visible').val() == 'sales';
		$('.sales-days-option').toggleClass('hidden', !show);
	}

	function hideEmptyColumns() {
		$('.f-col').each(function(){
			$(this).removeClass('hidden').toggleClass('hidden', !$(this).find('.form-group:visible').length);
		});
	}

	function toggleExternalTplFields() {
		var deviceType = $('.c-item.open').find('.form-horizontal').data('device'),
			desktopCustom = $('.select_external_tpl').first().val() == 1,
			custom = desktopCustom;
		if (deviceType != 'desktop') {
			var $externalTplWrapper = $('.form-group.external-tpl').find('.device-input-wrapper.'+deviceType),
				$externalPathWrapper = $('.form-group.external-tpl-path').find('.device-input-wrapper.'+deviceType);
			if (!$externalTplWrapper.hasClass('locked')) {
				custom = $externalTplWrapper.find('.select_external_tpl').val() == 1;
				if (custom && !desktopCustom && $externalPathWrapper.hasClass('locked')) {
					$externalPathWrapper.find('.lock-device-settings').click();  // make sure custom path is editable
				}
			}
		}
		$('.current-p-option').not('.external-tpl, .custom-class, .view-all-link').toggleClass('hidden', custom)
		.filter('.external-tpl-path').toggleClass('hidden', !custom);
		hideEmptyColumns();
	}

	$(document).on('click', '#saveCarousel', function(e) {
		e.preventDefault();
		var $item = $(this).closest('.c-item'),
			id = $item.attr('data-id'),
			$wrapper = $item.closest('.c-wrapper'),
			$hookContent = $item.closest('.hook-content');
		$item.find('textarea.mce-activated').each(function() {
			var html_content = tinyMCE.get($(this).attr('id')).getContent();
			$(this).val(html_content);
		});
		// don't submit locked values
		$item.find('.device-input-wrapper.locked').find('[name]').each(function() {
			var name = $(this).attr('name');
			$(this).data('real-name', name).attr('name', 'nosubmit');
		})
		$item.find('.ajax_errors').hide().html('');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=saveCarousel',
			data: {
				id_carousel: id,
				carousel_data: $item.find('form').serialize(),
				hook_name: $hookContent.attr('id'),
				ids_in_hook: getOrderedIds($hookContent),
				ids_in_wrapper: getOrderedIds($wrapper),
			},
			dataType : 'json',
			success: function(r) {
				$item.find('[name="nosubmit"]').each(function(){
					$(this).attr('name', $(this).data('real-name'));
				});
				if ('errors' in r) {
					$item.find('.ajax_errors').show().append(r.errors);
					$('html, body').animate({
						scrollTop: $item.offset().top - 130
					}, 500);
					return;
				} else {
					$.growl.notice({title: '', message: r.responseText});
					$item.find('form').slideUp(function() {
						ecb.mce.removeInstances($(this));
						$item.replaceWith(r.updated_form_header);
					});
					markEmptyWrappers();
					if (r.id_wrapper_new) {
						updateWrapperId($wrapper, r.id_wrapper_new);
					}
				}
			},
			error: function(r) {
				console.warn(r.responseText);
				$item.find('[name="nosubmit"]').each(function() {
					$(this).attr('name', $(this).data('real-name'));
				});
			}
		});
	});

	$(document).on('click', '.importer .import', function() {
		$('input[name="carousels_data_file"]').click();
	}).on('change', 'input[name="carousels_data_file"]', function() {
		if (!this.files || this.files[0].type != 'text/plain') {
			return;
		}
		$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
		var data = new FormData();
		data.append($(this).attr('name'), $(this).prop('files')[0]);
		$('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ImportCarousels',
			dataType : 'json',
			processData: false,
			contentType: false,
			data: data,
			success: function(r) {
				if ('errors' in r) {
					var errorsHTML = '<div class="thrown-errors">'+r.errors+'</div>';
					$('.all-carousels').before(errorsHTML);
				} else if ('upd_html' in r) {
					$upd = $('<div>'+r.upd_html+'</div>');
					$('.all-carousels').replaceWith($upd.find('.all-carousels'));
					$('.all-carousels').find('.hookSelector').change();
					$('.all-carousels').before($upd.find('.module_confirmation'));
					$('.panel.customcode').replaceWith($upd.find('.panel.customcode'));
					$('.nav-tab-name.first').click();
					activateSortable();
					customCode.activate();
				}
				$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
			},
			error: function(r) {
				console.warn(r.responseText);
				$('.importer .import i').toggleClass('icon-download icon-refresh icon-spin');
			}
		});
	});

	$('.install-override, .uninstall-override').on('click', function() {
		var $parent = $(this).closest('.override-item'),
			override_action = $(this).hasClass('install-override') ? 'addOverride' : 'removeOverride',
			class_name = $(this).data('clname');
		$parent.find('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ProcessOverride&override_action='+override_action+'&class_name='+class_name,
			dataType : 'json',
			success: function(r) {
				if ('errors' in r) {
					$parent.prepend(r.errors);
				} else if (r.processed) {
					$.growl.notice({title: '', message: savedTxt});
					$parent.toggleClass('installed not-installed');
				} else {
					$.growl.error({title: '', message: failedTxt});
				}
				console.dir(r);
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$(document).on('click', '.close-parent', function(e){
		e.preventDefault();
		$(this).closest('.parent').remove();
	});

	// ajax progress
	$('body').append('<div id="re-progress"><div class="progress-inner"></div></div>');
	$(document).ajaxStart(function(){
		$('#re-progress .progress-inner').width(0).fadeIn('fast').animate({'width':'70%'},500);
	})
	.ajaxSuccess(function(){
		$('#re-progress .progress-inner').animate({'width':'100%'},500,function(){
			$(this).fadeOut('fast');
		});
	});

	ecb.init();
});

var ecb = {
	init: function() {
		ecb.horizontalTabs();
		ecb.bindGeneralEvents();
		ecb.libraryForm();
		customCode.activate();
	},
	horizontalTabs: function() {
		let $t = $('.ec.horizontal-tabs').on('click', '.nav-tab-name', function(e) {
			e.preventDefault();
			$(this).addClass('active').siblings().removeClass('active');
			$($(this).attr('href')).addClass('active').siblings().removeClass('active');
		});
		$('.page-head').last().append($t);
		if ($t.hasClass('ps-16')) {
			ecb.retroAdjustHorizontalTabs($t);
		}
		$('#content').css('padding-top', ($t.offset().top+30)+'px');
		let customTab = ecb.getUrlParam('tab');
		if (customTab) {
			$('.nav-tab-name[href="#'+customTab).click();
		}
	},
	retroAdjustHorizontalTabs: function($t) {
		$t.css({'margin': ($('.page-head').outerHeight()-10)+'px 0 0 '+$('#nav-sidebar').outerWidth()+'px'});
		if (!ecb.retroSidebarEventsReady) {
			$('#nav-sidebar').find('.menu-collapse').on('click', function() {
				setTimeout(function() {
					ecb.retroAdjustHorizontalTabs($t);
				}, 0);
			});
			ecb.retroSidebarEventsReady = 1;
		}
	},
	bindGeneralEvents: function() {
		$(document).on('click', 'a[href="#"]', function(e) {
			e.preventDefault();
		}).on('change', '.switch-select', function() {
			let yes = $(this).hasClass('reverse') ? $(this).val() == 0 : $(this).val() != 0;
			$(this).toggleClass('yes', yes);
		}).on('click', '.lang_switcher', function() {
			let id_lang = $(this).attr('data-id-lang');
			$('.multilang').addClass('hidden');
			$('.multilang.lang_'+id_lang).removeClass('hidden');
			ecb.mce.activateVisible($(this).closest('.carousel-details'));
		}).on('focus', 'textarea.mce', function() {
			if (!$(this).hasClass('mce-activated')) {
				ecb.mce.instantiate($(this));
			}
		}).on('change', '.toggleable_param', function(e) {
			let $parent = $(this).closest('.c-item'),
				params = {
					action: 'ToggleParam',
					param_name: $(this).attr('name'),
					param_value: $(this).prop('checked') ? 1 : 0,
					id_carousel: $parent.attr('data-id'),
					clear_cache_for_hook: $('.hookSelector').val(),
				},
				response = function(r) {
					if (r.success) {
						if (params.param_name == 'active') {
							$parent.find('.activateCarousel').toggleClass('action-enabled action-disabled');
						}
					} else {
						$.growl.error({title: '', message: failedTxt});
					}
				}
			if ($parent.hasClass('open')) {
				response({success:1}); // just toggle classes if required
			} else {
				ecb.ajaxRequest(params, response);
			}
		}).on('click', '[data-bulk-act]', function(e) {
			e.preventDefault();
			$('.bulk-actions-error').remove();
			let params = {
					action: 'BulkUpdate',
					bulk_action: $(this).data('bulk-act'),
					ids: ecb.getArrayOfInputValues('.carousel-box:checked'),
					clear_cache_for_hook: $('.hookSelector').val(),
				},
				response = function(r) {
					if (r.errors) {
						let err = '<div class="bulk-actions-error">'+r.errors+'</div>';
						$('.bulk-actions').removeClass('open').before(err);
					} else if (r.success) {
						blockAjax = true;
						switch (params.bulk_action) {
							case 'enable':
								for (let i in params.ids) {
									$('.c-item[data-id="'+params.ids[i]+'"] .activateCarousel').addClass('action-enabled')
									.removeClass('action-disabled').find('input').prop('checked', true);
								}
							break;
							case 'disable':
								for (let i in params.ids) {
									$('.c-item[data-id="'+params.ids[i]+'"] .activateCarousel').removeClass('action-enabled')
									.addClass('action-disabled').find('input').prop('checked', false);
								}
							break;
							case 'group_in_tabs':
							case 'ungroup':
								let checked = params.bulk_action == 'group_in_tabs';
								for (let i in params.ids) {
									$('.c-item[data-id="'+params.ids[i]+'"] [name="in_tabs"]').prop('checked', checked);
								}
							break;
							case 'delete':
								removeCarouselRows(params.ids);
							break;
						}
						blockAjax = false;
					}
				};
			if (params.bulk_action == 'delete' && params.ids.length && !confirm(areYouSureTxt)) {
				return;
			}
			ecb.ajaxRequest(params, response);
		});
		ecb.hookCaching.bindEvents();
	},
	hookCaching: {
		bindEvents: function() {
			$(document).on('click', '.clearHookCache', function() {
				let $parent = $(this).closest('.main-caching-option'),
					params = 'action=ClearHookCache&hook_name='+$('.hookSelector').val(),
					response = function(r) {
						if (r.successText) {
							$parent.find('.caching-info').addClass('hidden').find('.grey-note').html('');
						} else {
							$.growl.error({title: '', message: failedTxt});
						}
					};
				ecb.ajaxRequest(params, response);
			}).on('change', '.caching-options', function() {
				let $parent = $(this).closest('.main-caching-option'),
					noCaching = $(this).val() == 0;
				$parent.siblings('.related-caching-option').toggleClass('hidden', noCaching);
				$parent.find('.caching-info').toggleClass('hidden', noCaching || $parent.find('.grey-note').text() == '');
			});
		},
	},
	prepareCarouselForm: function($item) {
		$item.addClass('open').tooltip({selector: '.label-tooltip'})
		.find('.carousel-details').slideDown(function() {
			ecb.mce.activateVisible($(this));
		}).find('#carousel_type, select[name="settings[carousel][type]"]').change();
	},
	mce: {
		activateVisible: function($container) {
			$container.find('textarea.mce:visible').not('.mce-activated').each(function() {
				$el = $(this);
				if ($el.val()) {
					ecb.mce.instantiate($el);
				}
			});
		},
		instantiate: function($el) {
			// if there is # in URL, page scrolls to top after activating MCE
			if (window.location.href.indexOf('#') >= 0) {
				window.history.pushState(null, null, window.location.href.split('#')[0]);
			}
			if (!$el.attr('id')) {
				$el.attr('id', 'id-'+(new Date().getTime()));
			}
			tinySetup({
				selector: '#'+$el.attr('id'),
				setup: function(editor) {
					editor.on('LoadContent', function(e) {
						$el.addClass('mce-activated');
						editor.focus();
					});
				},
				content_css: mce_content_css,
			});
		},
		removeInstances: function($container) {
			$container.find('.mce-activated').removeClass('mce-activated').each(function() {
				// PS 1.7.8+: after 2nd remove() there is an error related to autoresize plugin
				// no matter if mce elements are present in DOM or not
				// tinyMCE.remove('#'+$(this).attr('id'));
			});
		},
	},
	libraryForm: function() {
		$('.library-form').on('change', 'select, input', function() {
			ecb.ajaxRequest($(this).closest('form').serialize()+'&action=UpdateSliderLibrary');
		});
	},
	getUrlParam: function(name) {
		return (location.search.split(name+'=')[1] || '').split('&')[0];
	},
	getArrayOfInputValues: function(inputSelector) {
		return $(inputSelector).map(function() {
			return $(this).val();
		}).get();
	},
	ajaxRequest: function(params, response) {
		if (blockAjax) {
			return;
		}
		$.ajax({
			type: 'POST',
			url: ajax_action_path,
			dataType : 'json',
			data: params,
			success: function(r) {
				if (r) {
					if (r.successText) {
						$.growl.notice({title: '', message: r.successText});
					} else if (r.success) {
						$.growl.notice({title: '', message: savedTxt});
					}
				}
				if (response) {
					response(r);
				}
			},
			error: function(r) {
				$.growl.error({title: '', message: 'Error. Check console log'});
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	},
	storage: {
		get: function(key) {
			return typeof localStorage != 'undefined' ? localStorage[key] : '';
		},
		save: function(key, value) {
			return typeof localStorage != 'undefined' ? localStorage[key] = value : '';
		},
	},
}

/* custom codes */
var customCode = {
	editors: {},
	activate: function() {
		$('.custom-code-content').each(function(){
			var type = $(this).data('type');
			customCode.editors[type] = ace.edit('code'+type);
			customCode.editors[type].setOptions({
				showPrintMargin: false,
				theme: 'ace/theme/monokai',
				mode: 'ace/mode/'+(type == 'js' ? 'javascript' : type),
				useSoftTabs: false,
				wrap: true,
				// indentedSoftWrap: false,
				// useWorker: false,
			});
			customCode.editors[type].commands.addCommand({
				name: 'saveCode',
				bindKey: {win: 'Ctrl-s', mac: 'Command-s'},
				exec: function(editor) {
					$('.processCustomCode[data-type="'+type+'"][data-action="Save"]').click();
				}
			});
		});
		customCode.bindActions();
	},
	bindActions: function() {
		let $themeSelector = $('.updateEditorTheme'),
			savedTheme = ecb.storage.get('ec_editor_theme');
		if (savedTheme) {
			$themeSelector.val(savedTheme);
		}
		$themeSelector.on('change', function() {
			var theme = $(this).val();
			$.each(customCode.editors, function(i, e) {
				e.setTheme('ace/theme/'+theme);
			});
			ecb.storage.save('ec_editor_theme', theme);
		}).change();
		$('.processCustomCode').on('click', function(e) {
			e.preventDefault();
			var action = $(this).data('action'),
				type = $(this).data('type'),
				editor = (type in customCode.editors) ? customCode.editors[type] : false;
			if (!editor) {
				return;
			}
			if (action == 'GetInitial') {
				editor.setValue($('.custom-code-backup.'+type).text());
				customCode.toggleResetNotification(type, true);
				return;
			}
			$.ajax({
				type: 'POST',
				url: ajax_action_path+'&action='+action+'CustomCode',
				dataType : 'json',
				data: {type: type, code: editor.getValue()},
				success: function(r){
					if ('successText' in r) {
						$.growl.notice({title: '', message: r.successText});
						customCode.toggleResetNotification(type, false);
					} else if ('original_code' in r) {
						editor.setValue(r.original_code);
						customCode.toggleResetNotification(type, true);
					}
				},
				error: function(r) {
					$.growl.error({title: '', message: 'Error'});
					console.warn($(r.responseText).text() || r.responseText);
				}
			});
		});
		$('.toggleResetOptions').on('click', function(e) {
			e.stopPropagation();
			$(this).next().click();
		});
		$('.saveCode').on('click', function() {
			$(this).closest('.custom-code').find('.processCustomCode[data-action="Save"]').click();
		});
		$('.undoCodeAction').on('click', function() {
			var type = $(this).closest('.custom-code').find('.custom-code-content').data('type');
			if (type in customCode.editors) {
				customCode.editors[type].undo();
				customCode.toggleResetNotification(type, false);
			}
		});
	},
	toggleResetNotification: function(type, visible) {
		$('.reset-note.for-'+type).toggleClass('hidden', !visible);
	}
}

function scrollUpAllCarousels() {
	$('.c-item').each(function() {
		var $el = $(this);
		$el.removeClass('open').find('.carousel-details').slideUp(function() {
			ecb.mce.removeInstances($(this));
			$(this).html('');
		});
	});
}

function removeCarouselRows(ids){
	if (!$.isArray(ids)) {
		ids = [ids];
	}
	var lastId = ids[ids.length - 1];
	for (var i in ids){
		$('.c-item[data-id="'+ids[i]+'"]').fadeOut(function(){
			var updateHookCount = $(this).attr('data-id') == lastId;
			$(this).remove();
			if (updateHookCount) {
				var hook_name = $('.hookSelector').val(),
					carousels_num = $('#'+hook_name).find('.c-item:visible').length;
				$('.hookSelector').find('option[value="'+hook_name+'"]').text(hook_name+' ('+carousels_num+')');
				markEmptyWrappers();
			}
		});
	}
}

function markEmptyWrappers() {
	$('.c-wrapper').each(function() {
		$(this).toggleClass('empty', !$(this).find('.c-item').length);
	});
}

function updateWrapperId($wrapper, new_id) {
	$wrapper.attr('data-id', new_id);
}

function activateSortable() {
	$('.carousel-list, .wrappers-container').each(function(){
		if ($(this).hasClass('ui-sortable')) {
			return;
		}
		var isCarouselList = $(this).hasClass('carousel-list') ? 1 : 0,
			params = {
				placeholder: 'new-position-placeholder',
				connectWith: isCarouselList ? '.carousel-list' : '',
				handle: '.dragger',
				start: function(e, ui) {
					var $item = ui.item,
						css = {
							'height': $item.innerHeight(),
							// 'width': $item.innerWidth(),
							// 'display': 'inline-block',
						};
					$('.new-position-placeholder').css(css);
				},
				update: function(event, ui) {
					var $item = ui.item,
						$parent = $item.parent();
					// update may be called twice if elements are moved among wrappers
					// the following condition makes sure positions are updated only once
					if (this === $parent[0]) {
						$.ajax({
							type: 'POST',
							url: ajax_action_path+'&action=UpdatePositionsInHook',
							dataType : 'json',
							data: {
								ordered_ids: getOrderedIds($item.closest('.hook-content')),
								moved_element_is_carousel: isCarouselList,
								moved_element_wrapper_id: $parent.closest('.c-wrapper').attr('data-id'),
								moved_element_id: $item.attr('data-id'),
								hook_name: $('.hookSelector').val(),
							},
							success: function(r){
								if ('successText' in r) {
									$.growl.notice({title: '', message: r.successText});
								}
								if (isCarouselList && r.id_wrapper_new) {
									updateWrapperId($parent.closest('.c-wrapper'), r.id_wrapper_new);
								}
								markEmptyWrappers();
							},
							error: function(r) {
								$.growl.error({title: '', message: 'Error'});
								console.warn(r.responseText);
							}
						});
					}
				}
			};
		$(this).sortable(params);
	});
}

function getOrderedIds($container) {
	var ordered_ids = [];
	$container.find('.c-item').each(function(){
		ordered_ids.push($(this).attr('data-id'));
	});
	return ordered_ids;
}
/* since 2.7.7 */
