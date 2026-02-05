{if isset($errorsfb) && count($errorsfb) > 0}
	<div class="alert alert-error">
        {foreach $errorsfb as $errorfb}
			<p>{$errorfb}</p>
        {/foreach}
	</div>
{else}
	<div class="reviews_main_box col-xs-12 col-md-12">
		<div class="row">
			<div class=" col-xs-12 col-md-12">
				<div class="reviews_main_title">{l s='Opinions about' mod='pshowfbreviews'}&nbsp;{$name_of_shop}</div>
				{if $display_reviews}
	                {if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS')}
						<div class="star_content clearfix" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
	                        {section name="i" start=0 loop=5 step=1}
	                            {if $main_review le $smarty.section.i.index}
	                                {if !$isNewPresta}
										<div class="star"></div>
	                                {else}
										<i class="material-icons">star</i>
	                                {/if}
	                            {else}
	                                {if !$isNewPresta}
										<div class="star star_on"></div>
	                                {else}
										<i class="material-icons star_on">star</i>
	                                {/if}
	                            {/if}
	                        {/section}
						</div>
	                {/if}
					<div class="review_name">{$main_review_text} ({$main_review}/5)</div>
				{/if}
			</div>
			<div class="col-xs-12 col-md-12">
				<div class="opinions-header">{l s='Opinions:' mod='pshowfbreviews'}</div>
            	<div class="progress_label_start col-xs-6">{l s='%d positive' mod='pshowfbreviews' sprintf=[$positive_comments]}&nbsp;</div>
				<div class="progress col-xs-6">
					<div class="progress-bar" role="progressbar" aria-valuenow="{$positive_comments}"
						 aria-valuemin="0" aria-valuemax="{$count_reviews}" style="width: {100*($positive_comments/$count_reviews)}%"></div>
				</div>
				{if !$only_positive_reviews}
	            	<div class="progress_label_start col-xs-6">{l s='%d negative' mod='pshowfbreviews' sprintf=[$negative_comments]}&nbsp;</div>
					<div class="progress col-xs-6">
						<div class="progress-bar" role="progressbar" aria-valuenow="{$negative_comments}"
							 aria-valuemin="0" aria-valuemax="{$count_reviews}" style="width: {100*($negative_comments/$count_reviews)}%"></div>
					</div>
				{/if}
			</div>
		</div>
		<hr />
		<div class="col-xs-12 review_description">{l s='The total rating was calculated on the basis of' mod='pshowfbreviews'}&nbsp;
            {$count_reviews}&nbsp;{l s='reviews collected through Facebook over the last 12 months' mod='pshowfbreviews'}</div>
        {if Configuration::get('PSHOW_FBREVIEWS_PAGE_REVIEWS')}
			<a class="btn btn-default" href="{$link->getModuleLink('pshowfbreviews', 'reviews_list')}">{l s='See comments' mod='pshowfbreviews'}</a>
        {/if}
        {if Configuration::get('PSHOW_FBREVIEWS_FB_LIKEIT_BUTTON') && Configuration::get('PSHOW_FBREVIEWS_PAGE_ID')}
			<div class="fb-like" data-href="https://www.facebook.com/{Configuration::get('PSHOW_FBREVIEWS_PAGE_ID')}" data-width="150" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>
        {/if}
	</div>
{/if}
{if !$isNewPresta}
	<style>
		.reviews_main_box .star_content .star:after,
		.reviews_list_box .star_content .star:after {
			color: {if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS') && Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_DISABLED')}{Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_DISABLED')};{else}#777676;{/if}
		}

		.reviews_main_box .star_content .star.star_on:after,
		.reviews_list_box .star_content .star.star_on:after,
		.reviews_main_box .star_content .star.star_hover:after,
		.reviews_list_box .star_content .star.star_hover:after {
			color: {if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS') && Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_ENABLED')}{Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_ENABLED')};{else}#ef8743;{/if}
		}

		.reviews_main_box .progress {
			{if Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_DISABLED')}background-color: {Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_DISABLED')};{else}background-color: #f5f5f5;{/if}
		}

		.reviews_main_box .progress .progress-bar {
			{if Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_ENABLED')}background-color: {Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_ENABLED')};{else}background-color: #428bca;{/if}
		}
	</style>
{else}
	<style>
		.reviews_main_box .star_content .material-icons,
		.reviews_list_box .star_content .material-icons {
			color: {if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS') && Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_DISABLED')}{Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_DISABLED')};{else}#777676;{/if}
		}

		.reviews_main_box .star_content .material-icons.star_on,
		.reviews_list_box .star_content .material-icons.star_on,
		.reviews_main_box .star_content .material-icons.star_hover,
		.reviews_list_box .star_content .material-icons.star_hover {
			color: {if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS') && Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_ENABLED')}{Configuration::get('PSHOW_FBREVIEWS_STAR_COLOR_ENABLED')};{else}#ef8743;{/if}
		}

		.reviews_main_box .progress {
			{if Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_DISABLED')}background-color: {Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_DISABLED')};{else}background-color: #f5f5f5;{/if}
		}

		.reviews_main_box .progress .progress-bar {
			{if Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_ENABLED')}background-color: {Configuration::get('PSHOW_FBREVIEWS_PROGRESSBAR_COLOR_ENABLED')};{else}background-color: #428bca;{/if}
		}
	</style>
{/if}