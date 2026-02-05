{if Configuration::get('PSHOW_FBREVIEWS_FB_LIKEIT_BUTTON') && Configuration::get('PSHOW_FBREVIEWS_PAGE_ID')}
<div class="fb_for_body">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/pl_PL/sdk.js#xfbml=1&version=v3.1&appId=878746685652713';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
</div>
{/if}

{if Configuration::get('PSHOW_FBREVIEWS_RIGHT_PANEL')}
	{include file='../hook/getReviewsRightPanel.tpl'}
{/if}