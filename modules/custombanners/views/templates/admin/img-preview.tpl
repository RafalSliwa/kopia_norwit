{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="img-preview">
	<img src="{if !empty($src)}{$src|escape:'html':'UTF-8'}{else}data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7{/if}">
	<div class="img-actions">
		{if !empty($configurable)}
			<button type="button" class="img-action-btn" data-action="toggleSettings" title="{l s='Configure' mod='custombanners'}"><i class="icon-wrench"></i></button>
		{/if}
		<button type="button" class="img-action-btn" data-action="delete" title="{l s='Delete' mod='custombanners'}"><i class="icon-trash"></i></button>
	</div>
</div>
{* since 2.9.9 *}
