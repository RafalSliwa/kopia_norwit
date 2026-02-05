{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="text/javascript">
    $( document ).ready(function() {
        $('#btn-webp-faq-li').prependTo('.btn-toolbar ul.nav');
    });
</script>
<div style="font-size: 1rem; border: 1px solid #3ed2f0; padding: 0.5rem; background-color: #f4f9fb;">
    <i class="material-icons mi-help" style="vertical-align: text-top;">help</i>
    {l s='If some images are not compressed in WEBP, consult our FAQ which lists the known problems with their solutions' mod='jprestaspeedpack'}: <a href="{$faq_url|escape:'html':'UTF-8'}" target="_blank" class="btn btn-sm btn-success">{l s='FAQ Webp' mod='jprestaspeedpack'}</a>
</div>
<ul style="display:none">
    <li id="btn-webp-faq-li">
        <a id="webp-faq" class="toolbar_btn" href="{$faq_url|escape:'html':'UTF-8'}" target="_blank" style="color:white; background-color: #33bd25">
            <i class="process-icon-help" style="color:white;"></i>
            <div>{l s='FAQ Webp' mod='jprestaspeedpack'}</div>
        </a>
    </li>
</ul>
