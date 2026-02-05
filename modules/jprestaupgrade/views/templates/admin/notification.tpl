{*
* Upgrade module powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
*               is permitted for one Prestashop instance only but you can install it on your test instances.
*}
<div style="border: 1px solid #25b9d7; background-color: #beeaf3; padding: 1rem; margin-bottom: 1rem; border-radius: 8px;">
    <i class="icon-info-circle"></i>
    {l s='New versions are available for your JPresta modules and/or theme' mod='jprestaupgrade'}&nbsp;:
    <ul>
    {foreach from=$jpresta_modules item=module}
        {include file="./_notification_row.tpl" module=$module}
    {/foreach}
    </ul>
    <div><a href="{$jpresta_upgrade_link|escape:'html':'UTF-8'}" class="btn btn-primary" style="margin-left: 2rem;">{l s='See changlelogs and upgrade' mod='jprestaupgrade'}...</a></div>
</div>

