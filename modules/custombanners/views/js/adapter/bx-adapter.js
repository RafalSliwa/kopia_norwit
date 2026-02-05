/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
$.extend(cb, {
	renderCarousel: function($container) {
		var id = $container.closest('.cb-wrapper').data('wrapper'),
			settings = cb.formatSettings($container.data('settings')),
			params = cb.prepareCarouselParams($container, settings);
		if (!$container.data('initial-classes')) {
			cb.addHoverClassesIfRequired($container, settings);
			cb.removeLazyAttr($container); // onSliderLoad may not be called if some visible images were not lazy-loaded
			cb.carousels[id] = $container.data('initial-classes', $container.attr('class')).bxSlider(params);
		} else if (id in cb.carousels) {
			cb.carousels[id].reloadSlider(params);
		}
	},
	prepareCarouselParams: function($container, settings) {
		var itemsData = cb.getItemsData($container, settings),
			moveSlides = settings.m && settings.m < itemsData.num ? settings.m : itemsData.num,
			params = {
				pager: itemsData.overflow && settings.p,
				controls: itemsData.overflow && settings.n,
				infiniteLoop: itemsData.overflow && settings.l,
				auto: settings.a && !settings.t,
				autoHover: settings.ah,
				pause: settings.ps,
				ticker: settings.t,
				moveSlides: moveSlides,
				speed: settings.s,
				maxSlides: itemsData.num,
				minSlides: itemsData.num,
				slideWidth: itemsData.width,
				slideMargin: settings.sb,
				responsive: false,
				swipeThreshold: 50,
				useCSS: true,
				oneToOneTouch: false,
				prevText: '',
				nextText: '',
				onSliderLoad: function() {
					cb.adjutNavStyling($container, settings);
					$container.attr('class', $container.data('initial-classes')+' items-num-'+itemsData.num)
						.removeClass('pre-bx').closest('.bx-wrapper').css({'max-width': '100%'});
					cb.addItemClasses($container.find('.cb-item').not('.bx-clone').first(), itemsData.num);
				},
				onSlideAfter: function ($slideElement, prevIndex, newIndex) {
					cb.addItemClasses($container.find('.cb-item').not('.bx-clone').eq(newIndex * moveSlides), itemsData.num);
				},
			};
		return params;
	},
	adjutNavStyling: function($container, settings) {
		var $parent = $container.closest('.cb-wrapper');
		if (settings.p) {
			$parent.find('.bx-pager-link').removeClass('bx-pager-link').attr('class', cb.classes.p+'-bullet').html('');
			$parent.find('.bx-default-pager').removeClass('bx-default-pager bx-pager').addClass(cb.classes.p);
		}
		if (settings.n) {
			$parent.find('.sw-controls').remove();
			$parent.find('.bx-controls-direction').removeClass('bx-controls-direction')
				.addClass('sw-controls').appendTo($container.closest('.bx-viewport'))
				.find('.bx-prev').removeClass('bx-prev').addClass(cb.classes.n+' prev')
				.siblings('.bx-next').removeClass('bx-next').addClass(cb.classes.n+' next');
		}
	},
	addItemClasses: function($firstItem, itemsNum) {
		$firstItem.siblings().andSelf().removeClass('first last middle');
		$firstItem.addClass('first');
		if (itemsNum > 1) {
			$firstItem.nextAll().andSelf().eq(itemsNum - 1).addClass('last');
			if (itemsNum > 2) {
				var middleEq = parseInt(itemsNum/2) - 1;
				$firstItem.nextAll().eq(middleEq).addClass('middle');
				if (itemsNum % 2 == 0) { // 2 items in the middle for even itemsNum
					$firstItem.nextAll().eq(middleEq - 1).addClass('middle');
				}
			}
		}
	}
});
/* since 3.0.0 */
