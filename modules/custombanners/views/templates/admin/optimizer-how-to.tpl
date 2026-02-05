{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*  NOTE: texts are not translatable in order to prevent loading excessive data in global translation variables
*}

{if $iso_lang_current == 'some_other_lang'}
{else}
	{if $o->label == 'None'}
		Images will be saved as-is, without any compression/optimization.
		You can use this method if uploaded images are already optimized.
	{else if $o->label == 'Basic'}
		Images will be compressed using built-in PHP methods: imagejpeg an imagepng.
		<!-- <br>In some cases these methods can give pretty good results. -->
	{else if $o->label == 'TinyPNG'}
		<a href="https://tinypng.com/" target="_blank">TinyPNG</a> is an external service for image optimization.
		It uses smart compression techniques to reduce the file size of images.<br>
		<!-- The effect is nearly invisible but the difference in file size can be quite large.<br> -->
		It requires an API key, that can be obtained <a href="https://tinypng.com/developers" target="_blank">here</a>.
		Compresisng first 500 images per month is FREE. Additional pricing details can be found
		<a href="https://tinypng.com/developers#pricing" target="_blank">here</a>
	{else if $o->label == 'ReSmushit'}
		<a href="https://resmush.it/" target="_blank">ReSmushit</a> is an external service for image optimization that is FREE to use.<br>
		It is based on several well-known algorithms such as pngquant, jpegoptim, optipng.
		<!-- that allow reducing the file size of images without significant impact on visual appearance. -->
	{/if}
{/if}
{* since 2.9.7 *}
