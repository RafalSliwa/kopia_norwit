{capture name=path}{$title_of_reviews_page}{/capture}

<h1 class="reviews_list_box_title">{$title_of_reviews_page}</h1>
{if $display_reviews}
	<div class="star_content reviews_page" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
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
<div class="row">
    <div class="col-md-9">
        <h3 class="reviews_list_box_subtitle">
			{if $display_reviews}{l s='Average rating:' mod='pshowfbreviews'} {$main_review_text} ({$main_review}/5){/if}
        </h3>
    </div>
    <div class="col-md-3">

        <div class="form-group">
            <label for="sel1">{l s='Sorting:' mod='pshowfbreviews'}</label>
            <select class="form-control" id="sel1">
                <option>{l s='by date' mod='pshowfbreviews'}</option>
                <option>{l s='by rating' mod='pshowfbreviews'}</option>
            </select>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12"><h4 class="reviews_list_box_subtitle">{l s='Meet the reviews from our Facebook profile:' mod='pshowfbreviews'} <a href="https://facebook.com/{$profile_id}" target="_blank">https://facebook.com/{$profile_id}</a></h4></div>
</div>
<div class="reviews_list_box col-md-12">
    {foreach $array_of_reviews as $review name=foo}
        <div class="row reviews_list_box_row {if $smarty.foreach.foo.index % 2 != 0}reviews_backgorund{/if}">
            <div class="col-md-12">
                <div class="review_date">{$review["date"]|date_format:"%d.%m.%Y, %T"}</div>
            </div>
            <div class="col-md-12">
                {if !$only_positive_reviews}<div class="review_text">{if $review["recommendation_type"] eq 'positive'}{l s='Positive' mod='pshowfbreviews'}{else}{l s='Negative' mod='pshowfbreviews'}{/if}</div>{/if}
                {$review["review_text"]}
            </div>
        </div>
    {/foreach}
</div>
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
</style>