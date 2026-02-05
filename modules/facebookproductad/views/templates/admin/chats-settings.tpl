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
<div class="bootstrap">
    <h3 class="breadcrumb"><i class="fab fa-facebook-messenger"></i>&nbsp;<i class="fab fa-whatsapp"></i>&nbsp;{l s='Discover Chats Network for Facebook Messenger & WhatsApp' mod='facebookproductad'}</h3>

    <div class="col-xs-12">
        <div id="onboarding" class="panel discover">

            <div class="col-xs-12">
            <p>{l s='Install "Messenger" and "WhatsApp" intant messengers on your shop with' mod='facebookproductad'}&nbsp;<b>{l s='"Chats Network for Facebook Messenger & WhatsApp"' mod='facebookproductad'}</b>&nbsp;{l s='and stand out from the competition by offering a modern customer service that will allow you to build a real relationship of trust with your customers.' mod='facebookproductad'}</p>
            </div>

            <div class="clr_20"></div>

            <div class="col-xs-12 col-lg-3">
                <div class="col-xs-12 text-center">
                    <i class="far fa-grin-stars fcn-icon-market" aria-hidden="true"></i>
                </div>
                <div class="mt-3"></div>
                <p class="advantages-label text-center">{l s='Improve your customer satisfaction' mod='facebookproductad'}</p>

                <ul>
                    <li>{l s='Be close to your customers and inform them directly as you would in a physical store' mod='facebookproductad'}<br /><br /></li>
                    <li>{l s='Offer innovative and free customer service through chat channels that your customers use every day' mod='facebookproductad'}</li>
                </ul>
            </div>

            <div class="col-xs-12 col-lg-3">
                <div class="col-xs-12 text-center">
				    <i class="fa fa-line-chart fcn-icon-market" aria-hidden="true"></i>
                </div>
				<div class="mt-3"></div>
				<p class="advantages-label text-center">{l s='Increase your sales' mod='facebookproductad'}</p>
				<ul>
					<li>{l s='Be available to answer your customers\' questions when they are ready to buy and avoid cart abandonment' mod='facebookproductad'}<br /><br /></li>
					<li>{l s='Reply and continue the discussion even if your customers have left your site to make them come back to order' mod='facebookproductad'}</li>
                </ul>
			</div>

            <div class="col-xs-12 col-lg-3">
                <div class="col-xs-12 text-center">
                    <i class="fa fa-tags fcn-icon-market" aria-hidden="true"></i>
                </div>
                <div class="mt-3"></div>
                <p class="advantages-label text-center">{l s='Tag your products within chats' mod='facebookproductad'}</p>
                <ul>
                    <li>{l s='Use the catalog exported to Facebook with your "Facebook Dynamic Ads + Pixel & Shops" module to tag products directly within the Messenger chat' mod='facebookproductad'}<br /><br /></li>
                    <li>{l s='Create your catalog on WhatsApp Business(*) and also identify your products in the WhatsApp chat plugin' mod='facebookproductad'}<br /><br /><h6>{l s='(*)Only manual creation and management for the moment' mod='facebookproductad'}</h6></li>
                </ul>
            </div>



            <div class="col-xs-12 col-lg-3">
                <div class="col-xs-12 text-center">
                    <i class="fa fa-calendar fcn-icon-market" aria-hidden="true"></i>
                </div>
                <div class="mt-3"></div>
                <p class="advantages-label text-center">{l s='Schedule your chat activation' mod='facebookproductad'}</p>
                <ul>
                    <li>{l s='Define a chat activation schedule on your site to display instant messengers plugins only when you are available to reply' mod='facebookproductad'}<br /><br /></li>
                    <li>{l s='Activate/deactivate in one click all chats or only one of them, without having to modify your schedule' mod='facebookproductad'}</li>
                </ul>
            </div>

            <div class="clr_20"></div>
            <div class="row">
                <p id="discoverContainer" class="text-center mt-5">
                    {if !empty($bChatsIsInstalled)}
                        <div class="col-xs-12">
                            <a class="btn new-bg btn-lg col-xs-6 btn-center" href="{$sModuleChatsUrl|escape:'htmlall':'UTF-8'}" target="blank"><i class="fab fa-facebook-messenger fa-lg"></i>&nbsp;<i class="fab fa-whatsapp fa-lg"></i>&nbsp;&nbsp;{l s='Configure Chats Network for Facebook Messenger & WhatsApp' mod='facebookproductad'}</a>
                        </div>
                    {else}
                        <a class="btn new-bg btn-lg col-xs-6 btn-center" href="{$sWebsiteDiscover|escape:'htmlall':'UTF-8'}" target="blank"><i class="fab fa-facebook-messenger fa-lg"></i>&nbsp;<i class="fab fa-whatsapp fa-lg"></i>&nbsp;&nbsp;{l s='Discover Chats Network for Facebook Messenger & WhatsApp' mod='facebookproductad'}</a>
                    {/if}
                </p>
            </div>
        </div>
    </div>
</div>