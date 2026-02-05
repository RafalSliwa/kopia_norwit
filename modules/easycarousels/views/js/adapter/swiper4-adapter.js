/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/
ec.prepareCarouselParamsOrig = ec.prepareCarouselParams;
ec.prepareCarouselParams = function($container, settings) {
	var params = ec.prepareCarouselParamsOrig($container, settings);
	if (params.autoplay && params.autoplay.pauseOnMouseEnter) {
		$container.on('mouseenter', function() {
			ec.carousels[$container.data('id')].autoplay.stop();
		}).on('mouseleave', function() {
			ec.carousels[$container.data('id')].autoplay.start();
		});
	}
	return params;
};
/* since 2.7.6 */
