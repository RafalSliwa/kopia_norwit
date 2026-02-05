/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/
$.extend(ec, {
	renderCarousel: function(id, $container, settings) {
		var params = ec.prepareCarouselParams($container, settings);
		if ($container.data('initial-classes')) {
			ec.carousels[id].reloadSlider(params);
		} else {
			ec.removeLazyAttr($container); // onSliderLoad may not be called if some visible images were not lazy-loaded
			ec.carousels[id] = $container.data('initial-classes', $container.attr('class')).bxSlider(params);
		}
	},
	prepareCarouselParams: function($container, settings) {
		var itemsData = ec.getItemsData($container, settings),
			params = {
				pager: itemsData.overflow && settings.p,
				controls: itemsData.overflow && settings.n,
				infiniteLoop: itemsData.overflow && settings.l,
				auto: settings.a,
				autoHover: settings.ah,
				pause: settings.ps,
				moveSlides: settings.m,
				speed: settings.s,
				maxSlides: itemsData.num,
				minSlides: itemsData.num,
				slideWidth: itemsData.width,
				responsive: false,
				swipeThreshold: 50,
				useCSS: true,
				oneToOneTouch: false,
				prevText: '',
				nextText: '',
				onSliderLoad: function(){
					ec.adjutNavStyling($container, settings);
					$container.attr('class', $container.data('initial-classes')+' items-num-'+itemsData.num)
						.closest('.bx-wrapper').css({'max-width': '100%'}).find('.bx-viewport').css({'height': ''});
					ec.markRendered($container, settings);
				},
				onSlideAfter: function ($slideElement) {
					$slideElement.addClass('current').siblings('.current').removeClass('current');
				},
				pagerSelector: '.'+ec.classes.p,
			};
		return params;
	},
	adjutNavStyling: function($container, settings) {
		var $parent = $container.closest('.easycarousel');
		if (settings.p) {
			$parent.find('.bx-pager').removeClass('bx-pager')
				.find('.bx-pager-link').attr('class', ec.classes.p+'-bullet').html('');
		}
		if (settings.n) {
			$parent.find('.sw-controls').remove();
			$parent.find('.bx-controls-direction').removeClass('bx-controls-direction')
				.addClass('sw-controls').appendTo($container.closest('.bx-viewport'))
				.find('.bx-prev').removeClass('bx-prev').addClass(ec.classes.n+' prev')
				.siblings('.bx-next').removeClass('bx-next').addClass(ec.classes.n+' next');
		}
	},
});
/* since 2.7.6 */
