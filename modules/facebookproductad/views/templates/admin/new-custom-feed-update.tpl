{*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
<div class="bootstrap" style="min-width: 350px;">
	{if !empty($bUpdate)}
		<div class="col-xs-12 alert alert-success">{l s='Your new feed is added' mod='facebookproductad'}</div>
	{else}
		<div class="col-xs-12 alert alert-danger">{l s='The combination already exists' mod='facebookproductad'}</div>
	{/if}
</div>


{literal}
	<script type="text/javascript">
		setTimeout('parent.$.fancybox.close();', 2000);
	</script>
{/literal}