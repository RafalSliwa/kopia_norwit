{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
	{$smarty.block.parent}
{/block}

{block name="field"}
    {if $input.type == 'settings'}
    <div class="row">
    	<div class="col-lg-12">
    		<div class="row">
    			{if Configuration::get("PSHOWFBREVIEWS_CRON_INFO_DATE")}
					<div class="alert alert-success" id="tip_combine">
				        <p>
					       	{l s='Date when data was last sent via cron' mod='pshowfbreviews'}: {Configuration::get("PSHOWFBREVIEWS_CRON_INFO_DATE")}<br/>
					       	{l s='Number of sent orders' mod='pshowfbreviews'}:<br/>
					       	{l s='Successfully' mod='pshowfbreviews'}: {Configuration::get("PSHOWFBREVIEWS_CRON_INFO_ORDERS_SUCCESS")}<br/>
					       	{l s='Failed' mod='pshowfbreviews'}: {Configuration::get("PSHOWFBREVIEWS_CRON_INFO_ORDERS_FAILED")}<br/>
					       	{l s='Number of sent events' mod='pshowfbreviews'}:<br/>
					       	{l s='Successfully' mod='pshowfbreviews'}: {Configuration::get("PSHOWFBREVIEWS_CRON_INFO_EVENTS_SUCCESS")}<br/>
					       	{l s='Failed' mod='pshowfbreviews'}: {Configuration::get("PSHOWFBREVIEWS_CRON_INFO_EVENTS_FAILED")}
				       	</p>
				    </div>
			    {else}
					<div class="alert alert-danger" id="tip_combine">
				        <p>{l s='The cron job has not started yet.' mod='pshowfbreviews'}</p>
				    </div>
			    {/if}
				<div class="alert alert-info" id="tip_combine">
			        <p>{l s='Please add the following address to the CRON' mod='pshowfbreviews'}:<br/>
			        	<a href="{$link_to_page}" target="_blank">{$link_to_page}</a>
			        </p>
			    </div>
    		</div>
    	</div>
    </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name=script}
{literal}
$(document).ready(function() {
	$('input[type=radio][name=pshowfbpixel]').change(function() {
	    enableOrDisableFields();
	});
	function enableOrDisableFields() {
		if($("input[name='pshowfbpixel']:checked").val() == 1) {
    		enableFields();
    	}
	    else {
    		disableFields();
    	}
	}
	
	function disableFields() {
		$("#pshowfbpixel_send_method").prop( "disabled", true );
		$("#pshowfbpixel_token").prop( "disabled", true );
		$("#pshowfbpixel_id").prop( "disabled", true );
		$("input[name='pshowfbpixel_send_personal_data']").prop( "disabled", true );
		$("input[name='pshowfbpixel_alternate_hook']").prop( "disabled", true );
		$("input[name='pshowfbpixel_send_by_cron']").prop( "disabled", true );
		$("#tip_combine").parent().parent().parent().parent().hide();
	}
	
	function enableFields() {
		$("#pshowfbpixel_send_method").prop( "disabled", false );
		$("#pshowfbpixel_token").prop( "disabled", false );
		$("#pshowfbpixel_id").prop( "disabled", false );
		$("input[name='pshowfbpixel_send_personal_data']").prop( "disabled", false );
		$("input[name='pshowfbpixel_alternate_hook']").prop( "disabled", false );
		$("input[name='pshowfbpixel_send_by_cron']").prop( "disabled", false );
		$("#tip_combine").parent().parent().parent().parent().show();
	}
	
	$('input[type=radio][name=pshowfbpixel_send_by_cron]').change(function() {
	    showOrHideTipAboutCron();
	});
	function showOrHideTipAboutCron() {
		if($("input[name='pshowfbpixel_send_by_cron']:checked").val() == 1) {
			$("#tip_combine").parent().parent().parent().parent().show();
    	}
	    else {
			$("#tip_combine").parent().parent().parent().parent().show();
    	}
	}
	
	$('input[type=radio][name=pshowfbpixel_send_method]').change(function() {
	    showOrHideCronSettings();
	});
	function showOrHideCronSettings() {
		if($("input[type=radio][name=pshowfbpixel_send_method]:checked").val() == 1) {
			$("input[name='pshowfbpixel_send_by_cron']").parent().parent().parent().hide();
			$("#tip_combine").parent().parent().parent().parent().hide();
    	}
	    else {
			$("input[name='pshowfbpixel_send_by_cron']").parent().parent().parent().show();
			$("#tip_combine").parent().parent().parent().parent().show();
    	}
	}
});
{/literal}
{/block}