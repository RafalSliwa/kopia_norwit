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
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}admin.css">
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}all.css">
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}top.css">
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}cross-selling.css">
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}font-awesome.css">
<link rel="stylesheet" type="text/css" href="{$autocmp_css|escape:'htmlall':'UTF-8'}" />
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}toastr.min.css">
<link rel="stylesheet" type="text/css" href="{$moduleCssPath|escape:'htmlall':'UTF-8'}bootstrap4.css">

<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}jquery-ui-1.11.4.min.js"></script>
<script type="text/javascript" src="{$autocmp_js|escape:'htmlall':'UTF-8'}"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}module.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}feedList.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}custom_label.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}top.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}feature_by_cat.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}jquery.tablesorter.js"></script>
<script type="text/javascript" src="{$moduleJsPath|escape:'htmlall':'UTF-8'}toastr.js"></script>

<script type="text/javascript">
	// instantiate object
	var oFpa = oFpa || new Fpa('{$sModuleName|escape:'htmlall':'UTF-8'}');
	var oFpaFeatureByCat = oFpaFeatureByCat || new FpaFeatureByCat('{$sModuleName|escape:'htmlall':'UTF-8'}');
	var oFpaFeedList = oFpaFeedList || new FpaFeedList('{$sModuleName|escape:'htmlall':'UTF-8'}');
	var oFpaLabel = oFpaLabel || new FpaCustomLabel('{$sModuleName|escape:'htmlall':'UTF-8'}');
	var oBtUpdateStep = oBtUpdateStep || new btHeaderBar('{$sModuleName|escape:'htmlall':'UTF-8'}');

	// set URL of admin img
	oFpa.sImgUrl = '{$imagePath|escape:'htmlall':'UTF-8'}';

	{if !empty($sModuleURI)}
	// set URL of module's web service
	oFpa.sWebService = '{$sModuleURI|escape:'htmlall':'UTF-8'}';
	{/if}
</script>