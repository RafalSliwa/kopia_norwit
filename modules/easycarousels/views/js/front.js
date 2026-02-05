/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ec = {
	carousels: {},
	classes: { n: 'sw-nav', p: 'sw-pgn' },
	quickViewForced: false,
	documentReady: function () {
		ec.ww = window.innerWidth;
		ec.activateTabs();
		ec.prepareVisibleCarousels();
		ec.loadDynamicCarousels();
		if (!is_16) {
			if (prestashop.page.page_name == 'cart') {
				prestashop.blockcart.showModal = function () { }; // update summary without popup
			}
		} else {
			if (page_name == 'order' || page_name == 'order-opc') {
				$(document).off('click', '.ajax_add_to_cart_button'); // reload page
			};
			// if carousels are minimized in accordion and page is resized/orientationchanged,
			// they should be regenerated after clicking on block title
			$('.column').on('click', 'h3.carousel_title.active', function () {
				var $parent = $(this).parent();
				$parent.find('.c_container').removeClass('rendered');
				ec.prepareCarousel($parent.attr('id'));
			});
		}
	},
	prepareVisibleCarousels: function () {
		$('.c_container:visible').not('.rendered').each(function () {
			var $parent = $(this).closest('.easycarousel');
			ec.prepareCarousel($parent.attr('id'));
			if ($parent.closest('.column').length) {
				$parent.toggleClass('block', $parent.hasClass('carousel_block'));
			}
		});
		if ($('.accordion').find('.carousel_block').length) {
			try { accordion('disable'); accordion('enable'); } catch (e) { };
		}
		if (is_16 && $('.easycarousels .quick-view').length && !ec.quickViewForced) {
			try { quick_view(); ec.quickViewForced = true; } catch (e) { };
		}
	},
	loadDynamicCarousels: function () {
		$('.easycarousels.dynamic').each(function () { // generated in easycarousels.php->displayNativeHook
			var $el = $(this);
			if ($el.data('ajaxpath')) {
				$.ajax({
					type: 'POST',
					url: $el.data('ajaxpath'),
					dataType: 'json',
					success: function (r) {
						$el.replaceWith(r.carousels_html);
					},
					error: function (r) {
						console.warn($(r.responseText).text() || r.responseText);
					}
				});
			}
		});
	},
	activateTabs: function () {
		$('.ec-tabs').not('.activated').each(function () {
			$(this).addClass('activated').find('.ec-tab-link').on('click', function (e) {
				e.preventDefault();
				var $parent = $(this).parent(), txt = $(this).text(), id = $(this).attr('href').replace('#', '');
				if ($parent.hasClass('active') || !id) {
					return;
				}
				$parent.addClass('active').siblings().removeClass('active');
				$parent.closest('ul').addClass('closed').find('.responsive_tabs_selection').find('span').html(txt);
				$('#' + id).addClass('active').siblings('.ec-tab-pane').removeClass('active');
				ec.prepareCarousel(id);
			});
			$(this).find('.responsive_tabs_selection').on('click', function () {
				var $parent = $(this).parent();
				$parent.toggleClass('closed');
				if (!$parent.hasClass('closed')) {
					ec.onClickOutSide($(this), function () { $parent.addClass('closed') });
					$('.ec-tabs').not($parent).addClass('closed');
				}
			});
		});
		ec.compactTabs();
	},
	compactTabs: function (reset) {
		var $cTabs = $('.c-wrapper').find('.in_tabs.compact_on');
		if (reset) {
			$cTabs.removeClass('compact');
		}
		$cTabs.filter(':visible').not('.compact').each(function () {
			var $tabList = $(this).find('ul.ec-tabs'),
				$lastLi = $tabList.find('li.carousel_title').last(),
				$firstLi = $tabList.find('li.carousel_title').first();
			if ($lastLi.prev().hasClass('carousel_title') && $lastLi.offset().top != $firstLi.offset().top) {
				$tabList.closest('.in_tabs').addClass('compact');
			}
		});
	},
	onClickOutSide: function ($el, action) {
		$(document).off('click.outside').on('click.outside', function (e) {
			if (!$el.is(e.target) && $el.has(e.target).length === 0) {
				action();
				$(document).off('click.outside');
			}
		});
	},
	prepareCarousel: function (id) {
		var $container = $('#' + id).find('.c_container'),
			settings = ec.formatSettings($container.data('settings'));
		if (!$container.hasClass('rendered')) {
			if ($container.hasClass('ecarousel')) {
				ec.renderCarousel(id, $container, settings);
			} else {
				ec.markRendered($container);
			}
			ec.extraEvents($container);
		}
	},
	renderCarousel: function (id, $container, settings) {
		if (!$container.data('id')) {
			ec.carousels[id] = new Swiper($container[0], ec.prepareCarouselParams($container, settings));
			$container.data('id', id);
		} else if (id in ec.carousels) {
			$.extend(ec.carousels[id].params, ec.getResponsiveParams($container, settings, false));
			ec.carousels[id].update();
		}
	},
	extraEvents: function ($container) {
		if ($container.data('extra-events-ready')) {
			return;
		}
		if ($container.data('si')) {
			$container.find('img[data-toggle-src]').each(function () {
				let $img = $(this);
				$img.closest('.c_item').on('mouseenter mouseleave', function () {
					let newSrc = $img.data('toggle-src');
					$img.data('toggle-src', $img.attr('src')).attr('src', newSrc);
				});
			});
		}
		$container.find('.att-group').each(function () {
			$(this).data('value', $(this).val()).off('change').on('change', function () {
				var $select = $(this),
					$form = $select.closest('form'),
					id_product = $form.find('[name="id_product"]').val();
				$.ajax({
					type: 'POST',
					url: ec_ajax_path,
					data: 'action=updateCombination&id_product=' + id_product + '&' + $form.find('.att-group').serialize(),
					dataType: 'json',
					success: function (r) {
						if (r.id_comb) {
							$select.data('value', $select.val());
							$form.find('[name="id_product_attribute"]').val(r.id_comb);
						} else {
							$select.val($select.data('value')).addClass('shaking');
							setTimeout(function () { $select.removeClass('shaking'); }, 500);
						}
					},
					error: function (r) {
						console.warn($(r.responseText).text() || r.responseText);
					}
				});
			});
		});
		$container.data('extra-events-ready', 1);
	},
	prepareCarouselParams: function ($container, settings) {
		var params = {
			speed: settings.s,
			autoplay: settings.a ? {
				delay: settings.ps,
				pauseOnMouseEnter: !!settings.ah,
			} : false,
			updateOnImagesReady: false,
			on: {
				init: function () {
					ec.markRendered($container);
					ec.fixLazyDelay($container, settings);
				},
				update: function () {
					ec.markRendered($container);
				},
			},
			// effect: 'cube', //"slide", "fade", "cube", "coverflow", "flip"
		};
		if (settings.n) {
			$container.append('<div class="' + ec.classes.n + ' prev"></div><div class="' + ec.classes.n + ' next"></div>');
			params.navigation = {
				prevEl: $container.find('.' + ec.classes.n + '.prev')[0],
				nextEl: $container.find('.' + ec.classes.n + '.next')[0],
			};
		}
		if (settings.p) {
			params.pagination = {
				el: $container.parent().find('.' + ec.classes.p)[0], // .sw-pgn is added in tpl to avoid CLS issues
				bulletClass: ec.classes.p + '-bullet',
				modifierClass: ec.classes.p + '-',
				bulletActiveClass: 'active',
				clickable: true,
			};
		}
		$.extend(params, ec.getResponsiveParams($container, settings, true));
		return params;
	},
	getResponsiveParams: function ($container, settings, includeLoop) {
		var itemsData = ec.getItemsData($container, settings),
			params = {
				slidesPerView: itemsData.num,
				slidesPerGroup: settings.m && settings.m < itemsData.num ? settings.m : itemsData.num,
			};
		if (includeLoop) {
			params.loop = settings.l && itemsData.overflow;
		}
		$container.parent().find('.' + ec.classes.n + ', .' + ec.classes.p).toggleClass('hidden', !itemsData.overflow);
		return params;
	},
	formatSettings: function (settings) {
		$.each(settings, function (i, v) {
			settings[i] = parseInt(v);
		});
		return settings;
	},
	getItemsData: function ($container, settings) {
		var itemsNum = ec.getResponsiveItemsNum(settings),
			wrapperWidth = $container.closest('.c-wrapper').innerWidth(),
			itemWidth = parseInt(wrapperWidth / itemsNum),
			minWidth = settings.min_width < wrapperWidth ? settings.min_width : wrapperWidth;
		if (itemWidth < minWidth) {
			itemsNum = parseInt(wrapperWidth / minWidth);
			itemWidth = parseInt(wrapperWidth / itemsNum);
		}
		return { num: itemsNum, width: itemWidth, overflow: $container.find('.c_col').length > itemsNum };
	},
	getResponsiveItemsNum: function (settings) {
		var w = $(window).width(), itemsNum = settings.i;
		if (w < 480) { itemsNum = settings.i_480; }
		else if (w < 768) { itemsNum = settings.i_768; }
		else if (w < 992) { itemsNum = settings.i_992; }
		else if (w < 1200) { itemsNum = settings.i_1200; }
		return itemsNum;
	},
	markRendered: function ($container) {
		$container.addClass('rendered');
		if ($container.data('nh')) {
			ec.normalizeHeights($container);
		}
	},
	fixLazyDelay: function ($container, settings) {
		if (settings.a) {
			setTimeout(function () {
				ec.removeLazyAttr($container);
			}, Math.round(settings.ps / 2));
		} else {
			$container.one('mouseenter.nolazy', function () {
				ec.removeLazyAttr($container);
			});
		}
	},
	removeLazyAttr: function ($container) {
		$container.find('img[loading="lazy"]').removeAttr('loading');
	},
	onWindowResize: function () {
		if (ec.ww != window.innerWidth) {
			$('.c_container').removeClass('rendered').closest('.easycarousel').filter(':visible').each(function () {
				ec.prepareCarousel($(this).attr('id'));
			});
			ec.compactTabs(true);
			ec.ww = window.innerWidth;
		}
	},
	normalizeHeights: function ($container) {
		var hMax = { 'title': 0, 'reference': 0, 'category': 0, 'manufacturer': 0, 'description-short': 0, 'availability': 0 };
		$.each(hMax, function (el, max) {
			$container.find('.product-' + el).each(function () {
				var h = $(this).outerHeight();
				max = h > max ? h : max;
			});
			$container.find('.product-' + el).css('min-height', max + 'px');
		});
	},
};

$(document).ready(function () {
	ec.documentReady();
	$(window).on('resize.ec', function () {
		ec.onWindowResize();
	});
});
/* since 2.7.7 */
