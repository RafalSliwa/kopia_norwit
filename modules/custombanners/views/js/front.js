/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var cb = {
	init: function() {
		Object.keys(cb).forEach(function(key) {
			if (key.startsWith('init') && key != 'init') {
				cb[key]();
			}
		});
	},
	initAccordion: function() {
		$('.cb-wrapper.type-4').find('.cb-item-title').on('click', function() {
			$(this).siblings('.cb-item-content').slideToggle().parent().toggleClass('active')
			.siblings().removeClass('active').find('.cb-item-content').slideUp();
		});
	},
	initHoverImages: function() {
		$('.hover-src').on('mouseenter mouseleave', function() {
			let $img = $(this);
			if (!$img.data('toggle-src')) {
				$img.data('toggle-src', $img.attr('src').substring(0, $img.attr('src').lastIndexOf('/') + 1) + $img.data('hover-src'));
				if ($img.data('toggle-src').endsWith('.webp') && $img.attr('src').endsWith('.webp.jpg')) {
					$img.data('toggle-src', $img.data('toggle-src') + '.jpg');
				}
			}
			let newSrc = $img.data('toggle-src');
			$img.data('toggle-src', $img.attr('src')).attr('src', newSrc);
		});
	},
};
$(document).ready(function() {
	cb.init();
});
/* since 2.9.9 */
