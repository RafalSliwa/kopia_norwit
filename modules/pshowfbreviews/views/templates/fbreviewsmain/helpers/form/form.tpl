{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
	<div class="alert alert-info hidden tip col-lg-offset-3" id="tip_combine" style="margin-top: 17px;width: 56%;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p>{l s='To update the list of reviews, please visit' mod='pshowfbreviews'}:<br/>
        	<a href="{$link_to_page}/modules/pshowfbreviews/update_reviews.php" target="_blank">{$link_to_page}/modules/pshowfbreviews/update_reviews.php</a>
        </p>
        <p>{l s='You can add the above link to the cron for regular updates.' mod='pshowfbreviews'}</p>
    </div>
	{$smarty.block.parent}
{/block}