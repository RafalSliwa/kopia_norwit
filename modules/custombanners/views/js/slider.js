/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$.extend(cb, {
	carousels: {},
	classes: {n: 'sw-nav', p: 'sw-pgn'},
	resizeTimer: null,
	initCarousels: function() {
		cb.prepareAllCarousels();
		$(window).on('resize.cb', function() {
			cb.onResize();
		});
	},
	prepareAllCarousels: function() {
		$('.cb-wrapper').find('.cb-carousel').each(function() {
			cb.renderCarousel($(this));
		});
	},
	renderCarousel: function($container) {
		var id = $container.closest('.cb-wrapper').data('wrapper'),
			settings = cb.formatSettings($container.data('settings'));
		if (!$container.data('ready')) {
			cb.addHoverClassesIfRequired($container, settings);
			cb.carousels[id] = new Swiper($container[0], cb.prepareCarouselParams($container, settings));
			$container.data('ready', 1);
		} else if (id in cb.carousels) {
			$.extend(cb.carousels[id].params, cb.getResponsiveParams($container, settings, false));
			cb.carousels[id].update();
		}
	},
	formatSettings: function(settings) {
		$.each(settings, function(i, v){
			settings[i] = parseInt(v);
		});
		return settings;
	},
	addHoverClassesIfRequired: function($container, settings) {
		if (cb_isDesktop) {
			['n', 'p'].forEach(function(type) {
				if (settings[type] && settings[type] == 2) {
					$container.closest('.cb-wrapper').addClass(type+'-hover');
				}
			});
		}
	},
	prepareCarouselParams: function($container, settings) {
		var params = {
				speed: settings.s,
				autoplay: settings.a ? {
					delay: settings.ps,
					pauseOnMouseEnter: !!settings.ah,
				} : false,
				updateOnImagesReady: false,
				spaceBetween: settings.sb,
				on: {
					init: function () {
						cb.fixLazyDelay($container, settings);
					},
				},
				// effect: 'cube', //"slide", "fade", "cube", "coverflow", "flip"
			};
		if (settings.n) {
			$container.append('<div class="'+cb.classes.n+' prev"></div><div class="'+cb.classes.n+' next"></div>');
			params.navigation = {
				prevEl: $container.find('.'+cb.classes.n+'.prev')[0],
				nextEl: $container.find('.'+cb.classes.n+'.next')[0],
			};
		}
		if (settings.p) {
			// pagination is added after container in order to not affect position of nav buttons
			$container.after('<div class="'+cb.classes.p+'"></div>');
			params.pagination = {
				el: $container.parent().find('.'+cb.classes.p)[0],
				bulletClass: cb.classes.p+'-bullet',
				modifierClass: cb.classes.p+'-',
				bulletActiveClass: 'active',
				clickable: true,
			};
		}
		$.extend(params, cb.getResponsiveParams($container, settings, true));
		return params;
	},
	getResponsiveParams: function($container, settings, includeLoop) {
		var itemsData = cb.getItemsData($container, settings),
			params = {
				slidesPerView: itemsData.num,
				slidesPerGroup: settings.m && settings.m < itemsData.num ? settings.m : itemsData.num,
			};
		if (includeLoop) {
			params.loop = settings.l && itemsData.overflow;
		}
		$container.parent().find('.'+cb.classes.n+', .'+cb.classes.p).toggleClass('hidden', !itemsData.overflow);
		return params;
	},
	getItemsData: function($container, settings) {
		var itemsNum = cb.getResponsiveItemsNum(settings),
			wrapperWidth = $container.closest('.cb-wrapper').innerWidth(),
			itemWidth = parseInt(wrapperWidth / itemsNum);
		return {num: itemsNum, width: itemWidth, overflow: $container.find('.cb-item').length > itemsNum};
	},
	getResponsiveItemsNum: function(settings) {
		var w = $(window).width(), itemsNum = settings.i;
		if (w < 480) {itemsNum = settings.i_480;}
		else if (w < 768) {itemsNum = settings.i_768;}
		else if (w < 992) {itemsNum = settings.i_992;}
		else if (w < 1200) {itemsNum = settings.i_1200;}
		return itemsNum;
	},
	fixLazyDelay: function($container, settings) {
		if (settings.a) {
			setTimeout(function() {
				cb.removeLazyAttr($container);
			}, Math.round(settings.ps / 2));
		} else {
			$container.one('mouseenter.nolazy', function() {
				cb.removeLazyAttr($container);
			});
		}
	},
	removeLazyAttr: function($container) {
		$container.find('img[loading="lazy"]').removeAttr('loading');
	},
	onResize: function() {
		clearTimeout(cb.resizeTimer);
		cb.resizeTimer = setTimeout(function() {
			cb.prepareAllCarousels();
		}, 200);
	},
});
/* since 3.0.0 */
