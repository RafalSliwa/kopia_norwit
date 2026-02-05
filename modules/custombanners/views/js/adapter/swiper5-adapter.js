/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
cb.prepareCarouselParamsOrig = cb.prepareCarouselParams;
cb.prepareCarouselParams = function($container, settings) {
	$container.data('id', $container.closest('.cb-wrapper').data('wrapper'));
	let params = cb.prepareCarouselParamsOrig($container, settings);
	if (params.autoplay && params.autoplay.pauseOnMouseEnter) {
		$container.on('mouseenter', function() {
			cb.carousels[$container.data('id')].autoplay.stop();
		}).on('mouseleave', function(){
			cb.carousels[$container.data('id')].autoplay.start();
		});
	}
	return params;
};
/* since 3.0.0 */
