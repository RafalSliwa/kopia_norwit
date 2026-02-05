{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<div id="toole_module_header">
    <div class="logo-block">
        <h1><img height="50" src="/modules/roja45quotationspro/views/img/local-wide.png"/></h1>
    </div>
    <div class="header-item rate-block prestashop">@RATE_MODULE@</div>
    <div class="header-item support-block">
        <div class="support-link">@SUPPORT_BLOCK@<i class="icon-chevron-right right"></i></div>
        {if isset($roja45_auth_key)}<div class="auth-key"><small>{$roja45_auth_key}</small></div>{/if}
    </div>
</div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7&appId=273443963047825";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<style type="text/css" style="display: none">
    #toole_module_header {
        background-color: white;
        padding: 5px;
        margin-bottom: 20px;
        border: solid 1px #8f44da;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    #toole_module_header .header-item {
        display: inline-block;
        vertical-align: middle;
        text-align: right;
    }

    #toole_module_header .logo-block .logo-inline {
        display: inline-block;
        vertical-align: middle;
    }

    #toole_module_header .dontate-button .dontate-block {
        display: inline-block;
        height: 50px;
        top: 5px;
        vertical-align: middle;
    }
    #toole_module_header .dontate-button .dontate-block h2 {
        font-size: 20px;
        margin-top: 14px;
        margin-right: 5px;
    }

    #toole_module_header .dontate-button .dontate-block table {
        margin-top: 10px;
    }
    #toole_module_header .dontate-button .dontate-block.image-block {
        margin-left: 10px;
    }
    #toole_module_header a {
        text-decoration: none;
        margin-right: 20px;
        margin-left: 10px;
    }
    #toole_module_header .logo-block h1 {
        font-size: 40px;
        margin-top: 5px;
        margin-bottom: 5px;
        font-weight: 700;
    }
    #toole_module_header .logo-block h1 .r1 {
        color: black;
    }

    #toole_module_header .logo-block h1 .r2 {
        color: red;
    }

    #toole_module_header .support-block a {
        font-size: 17px;
        margin-top: 5px;
        margin-right: 5px;
        margin-bottom: 5px;
        font-weight: 700;
        color: red;
        text-transform: uppercase;
    }

    #toole_module_header .support-block a:hover {
        text-decoration: underline;
    }
</style>
