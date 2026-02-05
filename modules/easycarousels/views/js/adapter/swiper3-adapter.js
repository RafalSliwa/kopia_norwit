/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/
ec.prepareCarouselParamsOrig = ec.prepareCarouselParams;
ec.prepareCarouselParams = function($container, settings) {
	var params = ec.prepareCarouselParamsOrig($container, settings);
	params.onInit = function(swpr) {
		params.on.init();
		var updateOrig = swpr.update;
		swpr.update = function() {
			updateOrig(true); // force update(updateTranslate)
			params.on.update(); // native onUpdate is not available in swiper 3.x
		}
	}
	if (params.autoplay) {
		if (params.autoplay.pauseOnMouseEnter) {
			$container.on('mouseenter', function() {
				ec.carousels[$container.data('id')].stopAutoplay();
			}).on('mouseleave', function() {
				ec.carousels[$container.data('id')].startAutoplay();
			});
		}
		params.autoplay = params.autoplay.delay;
	}
	if (params.navigation) {
		params.prevButton = params.navigation.prevEl;
		params.nextButton = params.navigation.nextEl;
	}
	if (params.pagination) {
		params.bulletClass = params.pagination.bulletClass;
		params.bulletActiveClass = params.pagination.bulletActiveClass;
		params.paginationModifierClass = params.pagination.modifierClass;
		params.paginationClickable = params.pagination.clickable;
		params.pagination = params.pagination.el;
	}
	return params;
};
/* since 2.7.6 */
