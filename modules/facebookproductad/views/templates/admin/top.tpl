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
<div id="header_bar">
    <div class="container">
        <div class="logos-container" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="module-logo">
                <img src="{$imagePath|escape:'htmlall':'UTF-8'}admin/logo.png" alt="Module logo" />
            </div>
            <div class="logo-bt">
                <img src="{$imagePath|escape:'htmlall':'UTF-8'}admin/bt_white.png" alt="Business Tech logo" />
            </div>
        </div>
        <div class="content-center">
            <h2 style="color: white;">{$moduleName|escape:'htmlall':'UTF-8'} ({$version|escape:'htmlall':'UTF-8'})</h2>
        </div>
        <div class="bt-links" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <a class="btn btn-info" target="_blank" href="{$faqLink|escape:'htmlall':'UTF-8'}/product/71">
                <span class="fa fa-question-circle"></span>&nbsp;{l s='Online FAQ' mod='facebookproductad'}
            </a>
            {if !empty($sBusinessId)}
                <a class="btn btn-info-fb" target="_blank" href="https://business.facebook.com/home/accounts?business_id={$sBusinessId|escape:'htmlall':'UTF-8'}">
                    <span class="fa fa-facebook-official"></span>&nbsp;{l s='Business Manager' mod='facebookproductad'}
                </a>
            {else}
                <a class="btn btn-info-fb" target="_blank" href="https://business.facebook.com">
                    <span class="fa fa-facebook-official"></span>&nbsp;{l s='Business Manager' mod='facebookproductad'}
                </a>
            {/if}
        </div>
    </div>
    <div class="color-bt"></div>
</div>

<script type="text/javascript">
    $("a.bt_add-feed").fancybox({
        'hideOnContentClick' : false
    });
</script>
