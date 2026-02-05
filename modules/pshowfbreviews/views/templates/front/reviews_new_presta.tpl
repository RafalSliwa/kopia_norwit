{extends file='page.tpl'}
{block name='content'}
<h1 class="reviews_list_box_title">{$title_of_reviews_page}</h1>
<div class="reviews_list_box col-xs-12">
	{foreach $array_of_reviews as $review}
		<div class="row reviews_list_box_row">
			{if Configuration::get('PSHOW_FBREVIEWS_STARS_REVIEWS')}
				<div class="col-xs-6">
					<div class="star_content clearfix" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
						{section name="i" start=0 loop=5 step=1}
							{if $review["rating"] le $smarty.section.i.index}
								<div class="material-icons">star</div>
							{else}
								<div class="material-icons star_on">star</div>
							{/if}
						{/section}
					</div>
				</div>
			{else}
				<div class="col-xs-6">
					<div class="review_text">{$review["rating"]}/5</div>
				</div>
			{/if}
			<div class="col-xs-6">
				<div class="review_date">{$review["date"]}</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="review_content">{$review["review_text"]}</div>
		</div>
	{/foreach}
</div>
{/block}
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
</style>