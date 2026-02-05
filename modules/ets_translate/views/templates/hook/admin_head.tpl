{*
* Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<script type="text/javascript">
    const ETS_TRANS_ALL_LANGUAGES = {if isset($languages) && $languages}{$languages|@json_encode nofilter}{else}[]{/if} ;
    const ETS_TRANS_CURRENT_LANGUAGE = {if isset($current_language) && $current_language}{$current_language|@json_encode nofilter}{else}null{/if};
    const ETS_TRANS_IS_1780 = {if isset($is1780) && $is1780}1{else}0{/if};
    const ETS_TRANS_IS_801 = {if isset($is801) && $is801}1{else}0{/if};
    const ETS_TRANS_IS_810 = {if isset($is810) && $is810}1{else}0{/if};
    const ETS_TRANS_IS_812 = {if isset($is812) && $is812}1{else}0{/if};
    const ETS_TRANS_IS_813 = {if isset($is813) && $is813}1{else}0{/if};
    const ETS_TRANS_IS_GTE_814 = {if isset($isGte814) && $isGte814}1{else}0{/if};
    const ETS_TRANS_GTE_810 = {if isset($gte810) && $gte810}1{else}0{/if};
    const USE_PRODUCT_PAGE_V2 = {if isset($use_product_page_v2) && $use_product_page_v2}1{else}0{/if};
    const ETS_ADMIN_FD = "{$linkPsAdmin|escape:'quotes':'UTF-8'}";
    var ETS_TRANS_ENABLE_ANALYSIS = {if isset($enableAnalysis) && $enableAnalysis}1{else}0{/if};
    var etsTransLangSourceDefault = 1;
    {if isset($langSourceDefault)}
        etsTransLangSourceDefault = "{$langSourceDefault|escape:'html':'UTF-8'}";
    {/if}
    {if isset($langTargetInterTrans)}
        var etsTransLangTargetInterTrans = "{$langTargetInterTrans|escape:'html':'UTF-8'}";
    {/if}
    {if isset($transJs)}
        var etsTransText = {$transJs|@json_encode nofilter};
    {/if}
        var etsTransPageType = "{if isset($pageType) && $pageType}{$pageType|escape:'html':'UTF-8'}{/if}";
        var etsTransPageId = "{if isset($pageId) && $pageId}{$pageId|escape:'html':'UTF-8'}{/if}";
        var etsTransIsDetailPage = {if isset($isDetailPage) && $isDetailPage}1{else}0{/if};
        const ETS_TRANS_LINK_AJAX = "{if isset($linkAjaxBo)}{$linkAjaxBo|escape:'quotes':'UTF-8'}{/if}";
    {if isset($linkAjaxModule)}
        const ETS_TRANS_LINK_AJAX_MODULE = "{$linkAjaxModule|escape:'quotes':'UTF-8'}";
    {/if}
    {if isset($hasModuleSeo)}
        const ETS_TRANS_HAS_MODULE_SEO = "{if $hasModuleSeo}1{else}0{/if}";
    {/if}
    var ETS_TRANS_IS_AUTO_CONFIG = {if isset($isAutoConfigEnabled) && $isAutoConfigEnabled}1{else}0{/if};
    var ETS_TRANS_RATE = "{$rateVal|escape:'html':'UTF-8'}";
    {if isset($defaultTransConfig)}
    var ETS_TRANS_DEFAULT_CONFIG = {$defaultTransConfig|@json_encode nofilter};
    {/if}
    {if isset($rateSuffix)}
    var ETS_TRANS_RATE_SUFFIX = "{$rateSuffix|escape:'html':'UTF-8'}";
    {/if}
    const ETS_TRANS_IS_NEW_TEMPLATE = {if isset($isNewTemplate) && $isNewTemplate}1{else}0{/if};
    const ETS_TRANS_AUTO_DETECT_LANG = {if isset($autoDetectLanguage) && $autoDetectLanguage}1{else}0{/if};
    const ETS_TRANS_AUTO_GENERATE_LINK_REWRITE = {if isset($enableAutoGenerateLinkRewrite) && $enableAutoGenerateLinkRewrite}1{else}0{/if};
    const ETS_TRANS_ENABLE_TRANS_FIELD = {if isset($ETS_TRANS_ENABLE_TRANS_FIELD) && $ETS_TRANS_ENABLE_TRANS_FIELD}1{else}0{/if};
    const ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME = {if isset($ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME) && $ETS_TRANS_IGNORE_TRANS_PRODUCT_NAME}1{else}0{/if};
    const ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME = {if isset($ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME) && $ETS_TRANS_IGNORE_TRANS_CONTENT_HAS_PRODUCT_NAME}1{else}0{/if};
    const ETS_TRANS_ENABLE_TRANSLATE_TICKET = {if isset($ETS_TRANS_ENABLE_TRANSLATE_TICKET) && $ETS_TRANS_ENABLE_TRANSLATE_TICKET}1{else}0{/if};
    var etsAllowAccessChar = {if isset($PS_ALLOW_ACCENTED_CHARS_URL) && $PS_ALLOW_ACCENTED_CHARS_URL}1{else}0{/if};
    var ETS_TRANS_ENABLE_CHATGPT = {if isset($ETS_TRANS_ENABLE_CHATGPT) && $ETS_TRANS_ENABLE_CHATGPT}1{else}0{/if};
    {literal}
        if(typeof PS_ALLOW_ACCENTED_CHARS_URL == 'undefined')
            var PS_ALLOW_ACCENTED_CHARS_URL = etsAllowAccessChar;
    {/literal}
    var ETS_TRANS_BLOG_TYPE = {if isset($blogType)}"{$blogType|escape:'quotes':'UTF-8'}"{else}''{/if};
    var ETS_TRANS_IS_BLOG_LIST_POST = {if isset($is_list_post)}1{else}0{/if};
    var ETS_TRANS_IS_BLOG_LIST_CATEGORY = {if isset($is_list_category)}1{else}0{/if};
</script>
{if isset($linkJsSimulate) && $linkJsSimulate}
    <script type="text/javascript" src="{$linkJsSimulate|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsChatGPT) && $linkJsChatGPT}
    <script type="text/javascript" src="{$linkJsChatGPT|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsCommon) && $linkJsCommon}
    <script type="text/javascript" src="{$linkJsCommon|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsConfig) && $linkJsConfig}
    <script type="text/javascript" src="{$linkJsConfig|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsPages) && $linkJsPages}
    <script type="text/javascript" src="{$linkJsPages|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsBo) && $linkJsBo}
    <script type="text/javascript" src="{$linkJsBo|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsInterTrans) && $linkJsInterTrans}
    <script type="text/javascript" src="{$linkJsInterTrans|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($jsTransMegamenu) && $jsTransMegamenu}
    <script type="text/javascript" src="{$jsTransMegamenu|escape:'quotes':'UTF-8'}" defer="defer"></script>
{/if}
{if isset($linkJsLivechat) && $linkJsLivechat}
    <script type="text/javascript" src="{$linkJsLivechat|escape:'quotes':'UTF-8'}" defer="defer"></script>
    <script type="text/javascript">
        var lcLangSource = "{if isset($lcLangSource) }{$lcLangSource|escape:'quotes':'UTF-8'}{/if}";
        var lcLangTarget = "{if isset($lcLangTarget) }{$lcLangTarget|escape:'quotes':'UTF-8'}{/if}";
    </script>
{/if}
{if isset($linkJsHelpdesk) && $linkJsHelpdesk}
    <script type="text/javascript" src="{$linkJsHelpdesk|escape:'quotes':'UTF-8'}" defer="defer"></script>
    <script type="text/javascript">
        var hdLangSource = "{if isset($hdLangSource) }{$hdLangSource|escape:'quotes':'UTF-8'}{/if}";
        var hdLangTarget = "{if isset($hdLangTarget) }{$hdLangTarget|escape:'quotes':'UTF-8'}{/if}";
    </script>
{/if}
{if isset($allLangWithFlag) && $allLangWithFlag}
<script type="text/javascript">
    const ETS_TRANS_LANG_WITH_FLAG = {$allLangWithFlag|@json_encode nofilter};
</script>
{/if}
