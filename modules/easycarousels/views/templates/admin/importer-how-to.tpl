{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{* texts are not translatable in order to not load excessive data in global translation variables *}
{if $iso_lang_current == 'some_other_lang'}
{else}
	<p>In order to export all data, click "Export carousels" and save file. This file contains all multilingual data for carousels, hook positions, page exceptions and custom code.</p>
	<p>In order to import data, upload the file using "Import carousels" button. You can use this file on the same store as a backup, or you can upload it to any other store. When you upload the file, data is processed in a smart way to synchronize with installed languages/shops.</p>
	<p>Note: If you are using multistore, data is imported only to shops that are currently selected. It may be a single shop, a group of shops, or all shops. </p>
	<h4>Advanced use:</h4>
	<p>If you want, you can change pre-installed demo content, that is used on module installation/reset. Here is how to do that:</p>
	<ol>
		<li>Make a regular export and save the file</li>
		<li>Rename file to "carousels-custom.txt"</li>
		<li>Move file to "/modules/easycaroulels/democontent/carousels-custom.txt"</li>
	</ol>
{/if}
{* since 2.7.0 *}
