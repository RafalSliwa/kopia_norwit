{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*  NOTE: texts are not translatable in order to prevent loading excessive data in global translation variables
*}

{if $iso_lang_current == 'some_other_lang'}
{else}
	<p>In order to export banners, click "Export all banners" and save the archive. This archive contains all current banner data including images, custom css/js files, hook positions and page exceptions.</p>
	</p>In order to import data, upload this archive using "Import banners" button. You can use this archive on the same store as a backup, or you can upload it to any other store. When you upload the archive, data is processed in a smart way to synchronize with installed languages/shops.</p>
	<h4>Advanced use:</h4>
	<p>You can change pre-installed demo content, that is used on module installation/reset. Here is how to do that:</p>
	<ol>
		<li>Make a regular export and save the archive</li>
		<li>Rename the archive to "data-custom.zip"</li>
		<li>Move the archive to "/custombanners/defaults/data-custom.zip"</li>
	</ol>
{/if}
{* since 2.9.5 *}
