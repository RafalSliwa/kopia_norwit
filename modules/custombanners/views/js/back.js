/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var ajax_action_path,
	blockAjax = false,
	cb = {
		init: function() {
			ajax_action_path = window.location.href.split('#')[0]+'&ajax=1';
			activateSortable();
			cb.processConfirmations();
			cb.bindEvents();
			cb.customCode.activate();
			cb.quickSearch.init();
		},
		bindEvents: function() {
			$(document).on('click', 'a[href="#"]', function(e) {
				e.preventDefault();
			}).on('change', '.switch-select', function() {
				$(this).toggleClass('yes', $(this).val() > 0);
			});
			cb.horizontalTabs();
			cb.bannerFormEvents();
			cb.bulkActionEvents();
			cb.img.bindEvents();
			$('.library-form').on('change', 'select, input', function() {
				cb.ajaxRequest($(this).closest('form').serialize()+'&action=UpdateSliderLibrary');
			});
		},
		horizontalTabs: function() {
			let $t = $('.cb.horizontal-tabs').on('click', '.nav-tab-name', function(e) {
				e.preventDefault();
				$(this).addClass('active').siblings().removeClass('active');
				$($(this).attr('href')).addClass('active').siblings().removeClass('active');
			});
			$('.page-head').last().append($t);
			if ($t.hasClass('ps-16')) {
				cb.retroAdjustHorizontalTabs($t);
			}
			$('#content').css('padding-top', ($t.offset().top+30)+'px');
			let customTab = cb.getUrlParam('tab');
			if (customTab) {
				$('.nav-tab-name[href="#'+customTab).click();
			}
		},
		retroAdjustHorizontalTabs: function($t) {
			$t.css({'margin': ($('.page-head').outerHeight()-10)+'px 0 0 '+$('#nav-sidebar').outerWidth()+'px'});
			if (!cb.retroSidebarEventsReady) {
				$('#nav-sidebar').find('.menu-collapse').on('click', function() {
					setTimeout(function() {
						cb.retroAdjustHorizontalTabs($t);
					}, 0);
				});
				cb.retroSidebarEventsReady = 1;
			}
		},
		bannerFormEvents: function() {
			$(document).on('change', '.toggleable_param', function(e) {
				let $el = $(this),
					$parent = $el.closest('.cb-item'),
					data = {
						action: 'ToggleParam',
						id_banner: $parent.attr('data-id'),
						param_name: $el.data('param'),
						param_value: $el.prop('checked') ? 1 : 0,
					},
					response = function(r) {
						if (r.success) {
							if (data.param_name.startsWith('active')) {
								$el.closest('.status').toggleClass('active');
							}
						} else {
							$.growl.error({title: '', message: cb_txt.failed});
						}
					}
				if ($parent.hasClass('open')) {
					response({success:1}); // just toggle classes if required
				} else {
					cb.ajaxRequest(data, response);
				}
			});
		},
		openForEditing: function($banner) {
			cb.slideUpAllBanners();
			$banner.addClass('open').find('.cb-details').slideDown(function() {
				$banner.find('textarea.mce:visible').not('.mce-activated').each(function() {
					prepareVisibleTextarea($(this));
				});
				$banner.tooltip({selector: '.label-tooltip'});
				cb.activateDatePicker($banner);
			});
		},
		activateDatePicker: function($banner) {
			$banner.find('.datepicker').datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:00',
				onClose: function(current_time, $el) {
					setTimeout(function() { // minimal timeout for clicking "clear"
						$el.input.closest('.form-group').find('.clear-date').addClass('hidden');
					}, 100);
				},
			});
			$banner.find('.hasDatepicker').on('focusin', function() {
				$(this).closest('.form-group').find('.clear-date').removeClass('hidden');
			}).end().find('.clear-date').on('click', function() {
				$(this).closest('.form-group').find('.hasDatepicker').val('');
			});
		},
		slideUpAllBanners: function() {
			$('.cb-item').removeClass('open').find('.cb-details').slideUp();
		},
		bulkActionEvents: function() {
			$(document).on('click', '.bulk-select, .bulk-unselect', function(e) {
				e.preventDefault();
				let checked = $(this).hasClass('bulk-select');
				$('.cb-item').find('.cb-box').prop('checked', false).filter(':visible').prop('checked', checked);
			}).on('click', '[data-bulk-act]', function(e) {
				e.preventDefault();
				if ($(this).hasClass('conf-required') && !confirm(cb_txt.areYouSure)) {
					return;
				}
				$('.bulk-actions-error').remove();
				let data = {
						action: 'BulkUpdate',
						bulk_action: $(this).data('bulk-act'),
						bulk_value: $(this).data('bulk-value'),
						ids: cb.getArrayOfInputValues('.cb-box:checked'),
						no_selection_required: $(this).hasClass('no-selection-required') ? 1 : 0,
					},
					response = function(r) {
						if (r.errors) {
							let err = '<div class="bulk-actions-error">'+r.errors+'</div>';
							$('.bulk-actions').removeClass('open').before(err);
						} else if (r.success) {
							blockAjax = true;
							if (data.bulk_action.startsWith('active')) {
								for (let i in data.ids) {
									$('.cb-item[data-id="'+data.ids[i]+'"]').
									find('[data-param="'+data.bulk_action+'"]').prop('checked', !!data.bulk_value).
									closest('.status').toggleClass('active', !!data.bulk_value);
								}
							} else if (data.bulk_action == 'delete') {
								removeBannerRows(data.ids);
							} else if (data.bulk_action == 'deleteAll') {
								window.location.reload();
							} else if (data.bulk_action == 'copy' || data.bulk_action == 'move' ) {
								if (r.append_to_wrapper_id) {
									var idWrapper = r.append_to_wrapper_id,
										wrapperHTML = r.new_wrapper_form,
										bannersHTML = r.responseHTML;
									// make sure that required wrapper is present on page
									if (!$('.cb-wrapper[data-id="'+idWrapper+'"]').length) {
										$('.hook-content#'+data.to_hook).find('.wrappers-container').append(wrapperHTML);
									}
									addToWrapper(idWrapper, data.to_hook, bannersHTML, false);
									if (data.bulk_action == 'move') {
										removeBannerRows(data.ids);
									}
								}
								$('.bulk-actions').removeClass('open');
							}
							blockAjax = false;
						}
					};
				if (data.bulk_action == 'move' || data.bulk_action == 'copy') {
					data.to_hook = $(this).siblings('select').val();
				}
				cb.ajaxRequest(data, response);
			});
		},
		customCode: {
			editors: {},
			activate: function() {
				$('.custom-code-content').each(function() {
					var type = $(this).data('type');
					cb.customCode.editors[type] = ace.edit('code'+type);
					cb.customCode.editors[type].setOptions({
						showPrintMargin: false,
						theme: 'ace/theme/monokai',
						mode: 'ace/mode/'+(type == 'js' ? 'javascript' : type),
						useSoftTabs: false,
						wrap: true,
						// indentedSoftWrap: false,
						// useWorker: false,
					});
					cb.customCode.editors[type].commands.addCommand({
						name: 'saveCode',
						bindKey: {win: 'Ctrl-s', mac: 'Command-s'},
						exec: function(editor) {
							$('.processCustomCode[data-type="'+type+'"][data-action="Save"]').click();
						}
					});
				});
				cb.customCode.bindActions();
			},
			bindActions: function() {
				$('.processCustomCode').on('click', function(e) {
					e.preventDefault();
					var $btn = $(this),
						action = $btn.data('action'),
						type = $btn.data('type'),
						editor = (type in cb.customCode.editors) ? cb.customCode.editors[type] : false;
					if (!editor) {
						return;
					}
					if (action == 'GetInitial') {
						editor.setValue($('.custom-code-backup.'+type).text());
						cb.customCode.toggleResetNotification(type, true);
						return;
					}
					var data = {action: action+'CustomCode', type: type, code: editor.getValue()},
						response = function(r) {
							if (r.errors) {
								$btn.parent().prepend('<div class="thrown-errors">'+r.errors+'</div>');
							} else {
								if ('successText' in r) {
									cb.customCode.toggleResetNotification(type, false);
								} else if ('original_code' in r) {
									editor.setValue(r.original_code);
									cb.customCode.toggleResetNotification(type, true);
								}
							}
						};
					if (cb_is_16) {
						data.code = data.code.replace(/\\/g, '\\\\');
					}
					$btn.parent().find('.thrown-errors').remove();
					cb.ajaxRequest(data, response);
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
					if (type in cb.customCode.editors) {
						cb.customCode.editors[type].undo();
						cb.customCode.toggleResetNotification(type, false);
					}
				});
			},
			toggleResetNotification: function(type, visible) {
				$('.reset-note.for-'+type).toggleClass('hidden', !visible);
			}
		},
		img: {
			bindEvents: function() {
				$(document).on('click', '.img-browse', function(e) {
					$(this).siblings('.img-file').removeProp('dropped_files').click();
				}).on('change', '.img-file', function() {
					this.file_to_submit = false;
					if ('dropped_files' in this && /^image/.test(this.dropped_files[0].type)) {
						this.file_to_submit = this.dropped_files[0];
					} else if (!!this.files && this.files.length && /^image/.test(this.files[0].type)) {
						this.file_to_submit = this.files[0];
					}
					if (!this.file_to_submit || !window.FileReader) {
						return;
					}
					let $parent = $(this.closest('.img-holder')),
						reader = new FileReader();
					reader.readAsDataURL(this.file_to_submit);
					reader.onloadend = function() {
						$parent.addClass('has-img').find('.img-preview').find('img').attr('src', this.result);
						let autoCopy = !$parent.closest('.multilang').siblings().find('.img-holder.has-img').length,
							fieldName = $parent.data('field');
						$parent.closest('.cb-item').find('.lang-source[name="img_lang_source['+fieldName+']"]').prop('checked', autoCopy);
						if ($parent.data('field') == 'img') {
							$parent.closest('.cb-details').addClass('show-img-fields');
						}
						cb.img.submitCurrentImgForDeletion($parent);
					}
				}).on('dragover', '.img-holder:not(.has-img)', function(e) {
					e.preventDefault();
					e.stopPropagation();
					$(this).addClass('ondragover');
				}).on('dragend dragleave', '.img-holder', function(e) {
					$(this).removeClass('ondragover');
				}).on('drop', '.img-holder', function(e) {
					e.preventDefault();
					$(this).removeClass('ondragover');
					// most browsers dont support modyfing prop('files'), so we create an additional prop 'dropped_files'
					$(this).find('.img-file').prop('dropped_files', e.originalEvent.dataTransfer.files).change();
				}).on('click', '.img-action-btn', function() {
					switch ($(this).data('action')) {
						case 'toggleSettings':
							$(this).closest('.form-group').toggleClass('show-img-settings');
							break;
						case 'delete':
							if (confirm(cb_txt.areYouSure)) {
								let $parent = $(this).closest('.img-holder');
								$parent.removeClass('has-img').find('.img-file').prop('file_to_submit', '');
								cb.img.submitCurrentImgForDeletion($parent);
								if ($parent.data('field') == 'img') {
									$parent.closest('.cb-details').removeClass('show-img-fields');
								}
							}
							break;
					}
				}).on('click', '.toggleLockedField', function(e) {
					e.preventDefault();
					$(this).toggleClass('icon-lock icon-unlock-alt').closest('.lockable-field').toggleClass('locked')
					.find('input').val('');
				});
				cb.img.optm.bindEvents();
			},
			submitCurrentImgForDeletion: function($parent) {
				let $hiddenImgValues = $parent.find('.hidden-img-value'),
					imgName = $hiddenImgValues.filter('[data-key="name"]').val();
				$hiddenImgValues.remove();
				if (imgName) {
					$parent.append('<input type="hidden" name="imgs_to_delete[]" value="'+imgName+'">');
				}
				$parent.find('.visible-img-value').val('');
			},
			optm: {
				bindEvents: function() {
					this.$regenBtn = $('.optimizer-actions').find('.regenerateThumbs');
					this.$saveBtn = $('.optimizer-actions').find('.saveOptimizer');
					$('.selectOptimizer').on('change', function() {
						$('.optimizer-form').removeClass('active').
						filter('[data-optimizer="'+$(this).val()+'"]').addClass('active');
						cb.img.optm.blockRegenBtn();
					});
					$('.o-field-value').on('keyup', function() {
						clearTimeout(cb.img.optm.keyupTimer);
						cb.img.optm.keyupTimer = setTimeout(function() {
							cb.img.optm.blockRegenBtn();
						}, 200);
					});
					$('.saveOptimizer').on('click', function() {
						$('.o-field.wrong-value').removeClass('wrong-value');
						cb.img.optm.save();
					});
					this.$regenBtn.on('click', function() {
						if ($(this).hasClass('blocked')) {
							return;
						}
						$(this).toggleClass('running');
						$(this).closest('.tab-panel').toggleClass('block-o-fields', $(this).hasClass('running'));
						$('.o-size-stats').addClass('hidden').find('.dynamic-value').html('');
						cb.img.optm.removeErrors();
						cb.img.optm.regenerateThumbs($(this));
					});
				},
				blockRegenBtn: function() {
					if (!cb.img.optm.$regenBtn.hasClass('blocked')) {
						cb.img.optm.$regenBtn.addClass('blocked');
						setTimeout(function() {
							cb.img.optm.$saveBtn.addClass('btn-pulsate');
							setTimeout(function() {
								cb.img.optm.$saveBtn.removeClass('btn-pulsate');
							}, 500);
						}, 300);
					}
				},
				save: function() {
					let data = 'action=saveOptimizer&'+$('.optimizer-form.active').serialize(),
						response = function(r) {
							if (r.saved) {
								$.growl.notice({title: '', message: cb_txt.saved});
								$('.o-size-stats').addClass('hidden').find('.dynamic-value').html('');
								cb.img.optm.$regenBtn.removeClass('blocked');
							} else {
								let err = '';
								if (r.errors) {
									$.each(r.errors, function(name, error_txt) {
										let $possibleField = $('.o-field[data-name="'+name+'"]');
										if ($possibleField.length) {
											$possibleField.addClass('wrong-value');
										} else {
											err += name+': '+error_txt+'; ';
										}
									});
								} else {
									err = cb_txt.failed;
								}
								if (err) {
									cb.img.optm.displayError(err, true);
								}
								cb.img.optm.$regenBtn.addClass('blocked');
							}
						};
					cb.img.optm.removeErrors();
					cb.ajaxRequest(data, response);
				},
				regenerateThumbs: function($btn, data) {
					// TODO: block other actions like selecting different settings
					data = data ? data : {};
					data.action = 'regenerateThumbs';
					if ($btn.hasClass('running')) {
						let response = function(r) {
							if (r.errors) {
								$btn.removeClass('running').closest('.tab-panel').removeClass('block-o-fields');
								cb.img.optm.displayError(r.errors);
							} else {
								if (r.complete || !$btn.hasClass('running')) {
									$btn.removeClass('running').find('.processed-num').html('')
									.closest('.tab-panel').removeClass('block-o-fields');
								} else {
									let stats = r.params.processed.length+'/'+(r.params.processed.length + r.params.to_process.length);
									$btn.find('.processed-num').html(stats);
									$('.o-size-stats').removeClass('hidden').find('.dynamic-value').html(r.diff_formatted);
									cb.img.optm.regenerateThumbs($btn, r);
								}
							}
						},
						onError = function() {
							$btn.removeClass('running').closest('.tab-panel').removeClass('block-o-fields');
						}
						cb.ajaxRequest(data, response, onError);
					}
				},
				updateDisplayedData: function(imgData) {
					$('.all-img-data').find('.total-num').html(imgData.num);
					$('.all-img-data').find('.avg-compression').html(imgData.compression+'%');
				},
				displayError: function(err, wrap) {
					err = err ? err : cb_txt.failed;
					if (wrap) {
						err = '<div class="module_error alert alert-danger">'+err;
						err += '<button type="button" class="close" data-dismiss="alert">×</button></div>';
					}
					$('.optimizer-actions').prepend('<div class="regen-thumbs-error">'+err+'</div>');
				},
				removeErrors: function() {
					$('.regen-thumbs-error').remove();
					$('.optimizer-form').find('.has-error').removeClass('has-error');
				},
			},
			prepareFormData: function($cbItem) {
				var fd = new FormData();
				$cbItem.find('.img-file').each(function() {
					var $nameInput = $(this).siblings('.hidden-img-value[data-key="name"]');
					if ($nameInput.length && $nameInput.val() && $(this).closest('.form-group').hasClass('empty')) {
						fd.append('imgs_to_delete[]', $nameInput.val());
					} else if (this.file_to_submit) {
						fd.append($(this).attr('name'), this.file_to_submit);
					}
				});
				return fd;
			},
		},
		processConfirmations: function() {
			if (importConfirmationHTML) {
				window.history.pushState(null, null, window.location.href.replace('&'+importSuccessParam+'=1', ''));
				$(importConfirmationHTML).addClass('cb-import-confirmation').insertBefore('.cb.horizontal-tabs-content');
			}
		},
		quickSearch: {
			init: function() {
				let absolute = $('.quick-search').removeClass('absolute').offset().top != $('.addWrapper').offset().top;
				$('.quick-search').removeClass('transparent').toggleClass('absolute', absolute);
				if (absolute) {
					$('.toggleSearch').on('click', function() {
						var $searchBlock = $(this).closest('.quick-search');
						$searchBlock.toggleClass('active');
						if (!$searchBlock.hasClass('active')) {
							$('.searchBy').val('banner_name').change();
						}
					});
				}
				$('.searchBy').on('change', function() {
					$('.searchByValue').val('').trigger('keyup');
				});
				$('.searchByValue').on('keyup', function() {
					cb.quickSearch.find({by: $('.searchBy').val(), val: $(this).val().toLowerCase()});
				});
			},
			find: function(q) {
				let $hookContent = $('.hook-content.active'),
					matchesNum = 0;
				if (q.by == 'banner_name') {
					if (!q.val) {
						$hookContent.removeClass('hidden').find('.cb-wrapper').removeClass('hidden').
						find('.cb-item').removeClass('hidden');
						$('.no-matches').addClass('hidden');
						return;
					}
				} else {
					q.val = cb.formatIDs(q.val);
				}
				$hookContent.find('.cb-item').each(function() {
					var hidden = false;
					switch (q.by) {
						case 'banner_name':
							hidden = $(this).find('.cb-label input').val().toLowerCase().indexOf(q.val) < 0;
							break;
						case 'product':
						case 'category':
						case 'manufacturer':
						case 'supplier':
						case 'cms':
							hidden = $(this).find('.qs-exc-type').val() != q.by;
							if (!hidden && q.val.length > 0) {
								let savedIDs = cb.formatIDs($(this).find('.qs-exc-ids').val());
								hidden = $(q.val).filter(savedIDs).length < 1;
							}
							break;
					}
					$(this).toggleClass('hidden', !!hidden);
					if (!$(this).next().length) {
						var $wrapper = $(this).closest('.cb-wrapper');
						$wrapper.toggleClass('hidden', !$wrapper.find('.cb-item').not('.hidden').length);
					}
					if (!hidden) {
						matchesNum++;
					}
				});
				$('.no-matches').toggleClass('hidden', matchesNum > 0);
				$hookContent.toggleClass('hidden', matchesNum < 1);
			},
			updateOptions: function() {
				let $exc = $('.hook-content.active').find('.qs-exc-type');
				$('.searchBy').find('option').each(function() {
					if ($(this).val() != 'banner_name') {
						$(this).toggleClass('hidden', !$exc.filter('[value="'+$(this).val()+'"]').length);
					}
				});
			}
		},
		getUrlParam: function(name) {
			return (location.search.split(name+'=')[1] || '').split('&')[0];
		},
		getArrayOfInputValues: function(inputSelector) {
			return $(inputSelector).map(function() {
				return $(this).val();
			}).get();
		},
		formatIDs: function(ids) {
			return $.map(ids.split(','), function(val) {
				return parseInt(val) || null;
			});
		},
		ajaxRequest: function(params, response, onError) {
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
							$.growl.notice({title: '', message: cb_txt.saved});
						}
						if (r.upd_img_data) {
							cb.img.optm.updateDisplayedData(r.upd_img_data);
						}
					}
					if (response) {
						response(r);
					}
				},
				error: function(r) {
					onError ? onError() : '';
					$.growl.error({title: '', message: 'Error. Check console log'});
					console.warn($(r.responseText).text() || r.responseText);
				},
			});
		},
	};

$(document).ready(function() {

	cb.init();

	$(document).on('change', '.hookSelector', function() {
		var hook_name = $(this).val();
		$('.hook-content#'+hook_name).addClass('active').siblings().removeClass('active');
		$('.callSettings.active').trigger('click');
		cb.quickSearch.updateOptions();
	});
	$('.hookSelector').change();

	var timerC;
	$(document).on('mouseenter mouseleave', '.show-classes, .predefined-classes', function(e) {
		let $parent = $(this).closest('.col-lg-10');
		if (e.type == 'mouseenter') {
			if (!$parent.find('.predefined-classes').length) {
				$parent.append($('.classes-wrapper').html()).tooltip({selector: '.label-tooltip'});
				var right = $parent.find('.show-classes').outerWidth() / 2;
				$parent.find('.caret-t').css({'right' : right+'px', 'left' : 'auto'});
			}
			$parent.find('.predefined-classes').show();
			clearTimeout(timerC);
		} else {
			timerC = setTimeout(function() {
				$parent.find('.predefined-classes').hide();
			}, 200);
		}
	});

	var timerW;
	$(document).on('mouseenter', '.multiclass', function() {
		var $classes = $(this).find('.list');

		// prestashop BO layout
		var hiddenTop = 100;
		var hiddenBottom = $('#footer').is(':visible') ? 50 : 0;

		var h = $(this).find('i').innerHeight();
		var top = Math.round($classes.innerHeight() / 2 - h / 2);
		var viewPortPosition = this.getBoundingClientRect();
		var overTop = viewPortPosition.top - hiddenTop - top;
		if (overTop < 0)
			top += overTop;
		var overBottom = $(window).height() - viewPortPosition.bottom - hiddenBottom - top;
		if (overBottom < 0)
			top -= overBottom;
		$classes.css('top', '-'+top+'px');
		$classes.find('.caret-l').css('top', (top + h / 2)+'px');

		$('.multiclass .list').hide();
		$classes.show();
		clearTimeout(timerW);

	}).on('mouseleave', '.multiclass', function() {
		$classes = $(this).find('.list');
		timerW = setTimeout(function() {
			$classes.hide();
		}, 200);
	});

	$(document).on('click', '.predefined-classes [data-class]', function() {
		var $input = $(this).closest('.css_class').find('input[type="text"]');
		var currentClasses = $.trim($input.val()).split(' ');
		var classToAdd = $(this).data('class');
		var fragment = classToAdd;
		if ($(this).find('.fragment').length > 0) {
			var fragment = $.trim($(this).find('.fragment').text());
			$(this).closest('.list').hide();
			setTimeout(function() {
				clearTimeout(timerC);
			}, 0);
		}
		var newClasses = [];
		for (var i in currentClasses)
			if (currentClasses[i].indexOf(fragment) == -1)
				newClasses.push(currentClasses[i]);
			else
				newClasses.push(classToAdd);
		if ($.inArray(classToAdd, newClasses) == -1)
			newClasses.push(classToAdd);
		$input.val(newClasses.join(' ')).trigger('keyup');
	});

	$(document).on('change', 'select.exc', function() {
		var value = $(this).val(),
			showExcludeTxt = value.lastIndexOf('_all') > -1;
		$(this).closest('.exceptions-block').toggleClass('has-ids', value != 0)
		.find('.exclude-ids-txt').toggleClass('hidden', !showExcludeTxt).prev().toggleClass('hidden', showExcludeTxt);
	});

	$(document).on('click', '.show-field', function() {
		let $group = $(this).closest('.form-group').removeClass('empty');
		$group.find('textarea.mce:visible').not('.mce-activated').each(function() {
			prepareVisibleTextarea($(this));
		});
		if ($group.hasClass('img') && $group.find('.img-holder').hasClass('has-img')) {
			$group.closest('.cb-details').addClass('show-img-fields');
		}
	});

	$(document).on('click', '.hide-field', function() {
		let $group = $(this).closest('.form-group').addClass('empty');
		if ($group.hasClass('img')) {
			$group.closest('.cb-details').removeClass('show-img-fields');
		}
	});

	$(document).on('change', '.linkTypeSelector', function() {
		$(this).next().attr('data-type', $(this).val()).find('input[type="text"]').val('');
	});

	$(document).on('click', '.scrollUp', function() {
		cb.slideUpAllBanners();
	});

	$(document).on('click', '.addBanner', function(e) {
		e.preventDefault();
		let hook_name = $('.hookSelector').val(),
			id_wrapper = $(this).closest('.cb-wrapper').attr('data-id'),
			params = 'action=callBannerForm&id_banner=0&hook_name='+hook_name+'&id_wrapper='+id_wrapper+'&full=1',
			response = function(r) {
				if ('banner_form_html' in r) {
					addToWrapper(id_wrapper, hook_name, r.banner_form_html, true);
					cb.openForEditing($('.cb-wrapper[data-id='+id_wrapper+']').find('.cb-item').first());
				}
			};
		cb.ajaxRequest(params, response);
	}).on('click', '.editBanner', function() {
		let $parent = $(this).closest('.cb-item'),
			params = 'action=callBannerForm&id_banner='+$parent.data('id')+'&full=1';
			response = function(r) {
				if ('banner_form_html' in r) {
					cb.openForEditing($(r.banner_form_html).replaceAll($parent));
				}
			};
		cb.ajaxRequest(params, response);
	}).on('click', '.saveBanner', function() {
		var $parent = $(this).closest('.cb-item'),
			formData = cb.img.prepareFormData($parent);
		$parent.find('.ajax-errors').slideUp().html('');
		$parent.find('textarea.mce-activated').each(function() {
			var html_content = tinyMCE.get($(this).attr('id')).getContent();
			$(this).val(html_content);
		});
		$.each($parent.find('form').serializeArray(), function (i, val) {
			if (!$parent.find('[name="'+val.name+'"]').closest('.form-group').hasClass('empty')) {
				formData.append(val.name, val.value);
			}
		});
		$('.error-on-save').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveBannerData',
			dataType : 'json',
			processData: false,
			contentType: false,
			data: formData,
			success: function(r) {
				if ('errors' in r) {
					$parent.prepend('<div class="error-on-save">'+r.errors+'</div>');
					$('html, body').animate({
						scrollTop: $parent.offset().top - 180
					}, 500);
					return;
				}
				else if ('banner_form_html' in r) {
					$parent.removeClass('open').find('.cb-details').slideUp(function() {
						$.growl.notice({title: '', message: cb_txt.saved});
						$parent.replaceWith(r.banner_form_html);
					});
					if (r.upd_img_data) {
						cb.img.optm.updateDisplayedData(r.upd_img_data);
					}
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
				$parent.find('.ajax-errors').slideDown().html('Error. Check console log');
			}
		});
	});

	$(document).on('click', '.addWrapper', function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=AddWrapper',
			dataType : 'json',
			success: function(r) {
				if ('wrapper_html' in r) {
					$('.hook-content.active').find('.wrappers-container').prepend(r.wrapper_html);
					activateSortable();
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	}).on('click', '.deleteWrapper', function(e) {
		var $wrapperContainer = $(this).closest('.cb-wrapper')
			id_wrapper = $wrapperContainer.attr('data-id');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=DeleteWrapper&id_wrapper='+id_wrapper,
			dataType : 'json',
			success: function(r) {
				if (r.deleted) {
					$wrapperContainer.remove();
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$(document).on('submit', '.w-settings-form', function(e) {
		e.preventDefault();
	}).on('focusin', '.save-on-the-fly', function(e) {
		$(this).data('initial-value', $(this).val());
	}).on('keyup', '.save-on-the-fly', function(e) {
		let $el = $(this);
		if (e.which == 13 && $el.val() != $el.data('initial-value')) {
			$el.trigger('blur'); // not 'change', because it would be triggered again after user clicks outside
		}
	}).on('change', '.save-on-the-fly', function() {
		let $el = $(this),
			$form = $el.closest('form'),
			successResponse = function(r) {
				$el.addClass('just-saved');
				setTimeout(function() {
					$el.removeClass('just-saved');
				}, 1000);
				if ($el.hasClass('display-type')) {
					$el.closest('.cb-wrapper').removeClass('w-'+$el.data('initial-value')).addClass('w-'+$el.val());
					$el.data('initial-value', $el.val());
				}
			};
		saveWrapperSettings($form, 'general', successResponse);
	});

	$(document).on('click', '.saveCarouselSettings', function(e) {
		e.preventDefault();
		var $form = $(this).closest('form'),
			successResponse = function(r) {
				$form.slideUp(function() {
					$('.callSettings.active').removeClass('active');
				});
			};
		saveWrapperSettings($form, 'carousel', successResponse);
	});

	function saveWrapperSettings($form, settingsType, successResponse) {
		$('.wrapper-settings-error').remove();
		var data = $form.serialize()+'&settings_type='+settingsType;
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveWrapperSettings',
			data: data,
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if ('errors' in r) {
					$form.prepend('<div class="wrapper-settings-error">'+r.errors+'</div>');
				} else if ('saved' in r) {
					$.growl.notice({title: '', message: cb_txt.saved});
					if (typeof successResponse === 'function') {
						successResponse(r);
					}
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	}

	$(document).on('click', 'li.dont-hide', function(e) {
		e.preventDefault();
		$el = $(e.target);
		// tweak to leave .btn-group open. Otherwise it is closed on any click on dropdown list element
		$el.closest('.btn-group').addClass('force-open');
		setTimeout(function() {
			$el.closest('.btn-group').removeClass('force-open').addClass('open');
		}, 50);
		if ($el.hasClass('toggle-hook-list')) {
			$el.closest('li').siblings().find('.dynamic-hook-list').hide();
			if (!$el.siblings('.dynamic-hook-list').find('select').length) {
				var selectHTML = '<select>';
				$.each(hooks_by_name, function(i, hook_name) {
					selectHTML += '<option value="'+hook_name+'">'+hook_name+'</option>';
				});
				selectHTML += '</select>';
				$el.siblings('.dynamic-hook-list').prepend(selectHTML);
			}
		}
		$el.siblings('.dynamic-hook-list').toggle();
	});

	$(document).on('click', '.copyToAnotherHook, .moveToAnotherHook', function() {
		let params = {
				action: 'CopyToAnotherHook',
				id_banner: $(this).closest('.cb-item').attr('data-id'),
				to_hook: $(this).siblings('select').val(),
				delete_original: $(this).hasClass('moveToAnotherHook') ? 1 : 0,
			},
			response = function(r) {
				if (r.new_banner_form) {
					$('[data-id="'+params.id_banner+'"]').find('.actions .dropdown-toggle').click();
					if (!$('.cb-wrapper[data-id="'+r.append_to_wrapper_id+'"]').length) {
						$('.hook-content#'+params.to_hook).find('.wrappers-container').append(r.new_wrapper_form);
					}
					addToWrapper(r.append_to_wrapper_id, params.to_hook, r.new_banner_form, false);
					if (params.delete_original) {
						removeBannerRows(params.id_banner);
					}
					$.growl.notice({title: '', message: r.reponseText});
				} else {
					$.growl.error({title: '', message: r.reponseText});
				}
			};
		cb.ajaxRequest(params, response);
	}).on('click', '.deleteBanner', function() {
		if (confirm(cb_txt.areYouSure)) {
			let params = {
					action: 'deleteBanner',
					id_banner: $(this).closest('.cb-item').attr('data-id'),
				},
				response = function(r) {
					if (r.deleted) {
						removeBannerRows(params.id_banner);
					}
				};
			cb.ajaxRequest(params, response);
		}
	});

	$(document).on('click', '.callSettings', function(e) {
		e.preventDefault();
		let $el = $(this),
			settings_type = $el.data('settings');
			id_wrapper = $el.closest('cb-wrapper').attr('data-id'),
			hook_name = $(this).closest('form').find('.hookSelector').val();
		$('#settings-content').hide().html('');
		$('.carousel-settings-form').hide();
		$('.callSettings').not(this).removeClass('active');
		if ($el.hasClass('active')) {
			$el.removeClass('active');
		} else if (settings_type == 'carousel') {
			$el.addClass('active').closest('.cb-wrapper').find('.carousel-settings-form').slideDown();
		} else {
			$.ajax({
				type: 'POST',
				url: ajax_action_path+'&action=CallSettingsForm&settings_type='+settings_type+'&hook_name='+hook_name,
				dataType : 'json',
				success: function(r) {
					if ('form_html' in r) {
						$('#settings-content').html(r.form_html).slideDown().tooltip({selector: '.label-tooltip'});
						$el.addClass('active');
					}
				},
				error: function(r) {
					console.warn($(r.responseText).text() || r.responseText);
				}
			});
		}
	});

	$(document).on('click', '.hide-settings', function() {
		$('.callSettings.active').click();
	});

	$(document).on('click', '.chk-action', function(e) {
		e.preventDefault();
		var $checkboxes = $(this).closest('#settings-content').find('input[type="checkbox"]');
		if ($(this).hasClass('checkall')) {
			$checkboxes.each(function() {
				$(this).prop('checked', true);
			});
		}
		else if ($(this).hasClass('uncheckall')) {
			$checkboxes.each(function() {
				$(this).prop('checked', false);
			});
		}
		else if ($(this).hasClass('invert')) {
			$checkboxes.each(function() {
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	});

	$(document).on('click', '.saveHookSettings', function(e) {
		e.preventDefault();
		let params = 'action=SaveHookSettings&'+$(this).closest('form').serialize(),
			response = function(r) {
				if (r.saved) {
					$('#settings-content').slideUp(function() {
						$('.callSettings').removeClass('active');
						$(this).html('');
						$.growl.notice({title: '', message: cb_txt.saved});
					});
				}
			};
		cb.ajaxRequest(params, response);
	});

	$(document).on('click', '.importBannersData', function() {
		$('input[name="zipped_banners_data"]').click();
	}).on('change', 'input[name="zipped_banners_data"]', function() {
		if (!this.files) {
			return;
		}
		let $i = $('.importBannersData').find('i'),
			fd = new FormData(),
			toggleClasses = 'icon-download loading-indicator';
		$i.toggleClass(toggleClasses);
		fd.append($(this).attr('name'), $(this).prop('files')[0]);
		$('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=importBannersData',
			dataType : 'json',
			processData: false,
			contentType: false,
			data: fd,
			success: function(r) {
				$i.toggleClass(toggleClasses);
				if ('errors' in r) {
					var errorsHTML = '<div class="thrown-errors">'+r.errors+'</div>';
					$('.importBannersData').closest('.panel').before(errorsHTML);
				} else {
					window.location.href = window.location.href.split('#')[0]+'&'+importSuccessParam+'=1';
				}
			},
			error: function(r) {
				$i.toggleClass(toggleClasses);
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$('.cb').on('change', '.check-all-data', function() {
		var checked = $(this).prop('checked');
		$(this).parent().siblings().find('.lang-source').prop('checked', checked);
	});

	// check for multiple ids
	// $('[id]').each(function() {
	// 	var ids = $('[id="'+this.id+'"]');
	// 	if(ids.length>1 && ids[0]==this)
	// 	console.warn('Multiple IDs #'+this.id);
	// });
})

function activateSortable() {
	$('.cb-list, .wrappers-container').each(function() {
		if ($(this).hasClass('ui-sortable')) {
			return;
		}
		var isBannerList = $(this).hasClass('cb-list') ? 1 : 0,
			params = {
				connectWith: isBannerList ? '.cb-list' : '',
				handle: '.dragger',
				update: function(event, ui) {
					var $item = ui.item,
						$parent = $item.parent(),
						id_wrapper = isBannerList ? $parent.closest('.cb-wrapper').attr('data-id') : false;
					// update may be called twice if elements are moved among wrappers
					// the following condition makes sure positions are updated only once
					if (this === $parent[0]) {
						$.ajax({
							type: 'POST',
							url: ajax_action_path+'&action=UpdatePositionsInHook',
							dataType : 'json',
							data: {
								ordered_ids: getOrderedIds($item.closest('.hook-content')),
								moved_element_is_banner: isBannerList,
								moved_element_wrapper_id: id_wrapper,
								moved_element_id: $item.attr('data-id'),
							},
							success: function(r) {
								if('successText' in r) {
									$.growl.notice({title: '', message: r.successText});
									if (isBannerList) {
										$item.find('input[name="id_wrapper"]').val(id_wrapper);
									}
								}
								markEmptyWrappers();
							},
							error: function(r) {
								$.growl.error({title: '', message: 'Error'});
								console.warn($(r.responseText).text() || r.responseText);
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
	$container.find('.cb-item').each(function() {
		ordered_ids.push($(this).attr('data-id'));
	});
	return ordered_ids;
}

function markEmptyWrappers() {
	$('.cb-wrapper').each(function() {
		$(this).toggleClass('empty', !$(this).find('.cb-item').length);
	});
}

function selectLanguage($el, id) {
	var $bannerItem = $el.closest('.cb-item');
	$bannerItem.find('.multilang').hide().filter('.lang-'+id).show().
	find('textarea.mce:visible').not('.mce-activated').each(function() {
		prepareVisibleTextarea($(this));
	});
	$bannerItem.find('input.lang-source').val(id);
}

function prepareVisibleTextarea($el) {
	setTimeout(function() { // minimal timeout for smooth sliding
		if (!$el.attr('id')) {
			$el.attr('id', 'mce_'+(new Date().getTime()));
		}
		tinySetup({
			selector: '#'+$el.attr('id'),
			onloadContent: $el.addClass('mce-activated'),
			content_css: mce_content_css,
		});
	}, 100);
}

function addToWrapper(id_wrapper, hook_name, html, prepend) {
	var $wrapper = $('.cb-wrapper[data-id="'+id_wrapper+'"]');
	if (prepend) {
		$wrapper.removeClass('empty').find('.cb-list').prepend(html);
	} else {
		$wrapper.removeClass('empty').find('.cb-list').append(html);
	}
	var banners_num = $('#'+hook_name).find('.cb-item').length;
	$('.hookSelector').find('option[value="'+hook_name+'"]').text(hook_name+' ('+banners_num+')');
}

function removeBannerRows(ids) {
	if (!$.isArray(ids))
		ids = [ids];
	var lastId = ids[ids.length - 1];
	for (var i in ids) {
		$('.cb-item[data-id="'+ids[i]+'"]').fadeOut(function() {
			if ($(this).data('id') == lastId) {
				var hook_name = $('.hookSelector').val();
				var banners_num = $('#'+hook_name+' .cb-item').length - 1;
				$('.hookSelector').find('option:selected').text(hook_name+' ('+banners_num+')');
				if (!$(this).siblings().length) {
					$(this).closest('.cb-wrapper').addClass('empty');
				}
			}
			$(this).remove();
		});
	}
}
/* since 3.0.1 */
