/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
cb.prepareCarouselParamsOrig = cb.prepareCarouselParams;
cb.prepareCarouselParams = function($container, settings) {
	$container.data('id', $container.closest('.cb-wrapper').data('wrapper'));
	let params = cb.prepareCarouselParamsOrig($container, settings);
	params.onInit = function(swpr) {
		params.on.init();
	}
	if (params.autoplay) {
		if (params.autoplay.pauseOnMouseEnter) {
			$container.on('mouseenter', function() {
				cb.carousels[$container.data('id')].stopAutoplay();
			}).on('mouseleave', function() {
				cb.carousels[$container.data('id')].startAutoplay();
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
/* since 3.0.0 */
