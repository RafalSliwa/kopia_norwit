{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
	<div class="alert alert-info hidden tip col-lg-offset-3" id="tip_combine" style="margin-top: 17px;width: 56%;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p>{l s='Please add the following address to the cron' mod='pshowfbreviews'}:<br/>
        	<a href="{$link_to_page}/modules/pshowfbreviews/sdk/cron.php" target="_blank">{$link_to_page}/modules/pshowfbreviews/sdk/cron.php</a>
        </p>
    </div>
	{$smarty.block.parent}
{/block}