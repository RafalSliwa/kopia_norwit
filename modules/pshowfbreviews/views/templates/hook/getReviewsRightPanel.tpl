<div class="slideout-widget">
 <div class="slideout-widget-handler">
 {if !$isNewPresta}
 	<i class="icon-star"></i>
 {else}
 	<i class="material-icons">star</i>
 {/if}
 </div>
 <div class="slideout-widget-content">
	{hook h='pShowFbReviewsGetReviews'}
 </div>
</div>
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