{if $LEASELINK_KLIENTID != 'null' || $LEASELINK_CALC_SWITCH eq 1}
	<div class="buttonKalkulatorLeasingowego" style="margin:15px 0px;">
		<a target="_blank" rep="nofollow"
			href="https://online.leaselink.pl/RateCalculator/calculate?rate=999&externalId={$LEASELINK_KLIENTID|escape:'htmlall':'UTF-8'}&categoryLevel={$LEASELINK_CAT1|escape:'htmlall':'UTF-8'}&categoryLevel2={$LEASELINK_CAT2|escape:'htmlall':'UTF-8'}&tax={$PRODUCT_VAT|escape:'htmlall':'UTF-8'}&value={$item["GrossValue"]|escape:'htmlall':'UTF-8'}&isNet=false&productName={$item["Name"]|escape:'htmlall':'UTF-8'}">
			<img src="{$module_dir}views/img/leaselink_logo.png" title="weź leasing">
		</a>
	</div>
	{if $LEASELINK_INFO_DETAILS != 'null' || $LEASELINK_INFO_DETAILS eq 1}
		<div class="opisPodButtonLeaselink" style="font-size:12px;text-align:center;">{$LEASELINK_INFO_DETAILS}</div>
	{/if}
{/if}