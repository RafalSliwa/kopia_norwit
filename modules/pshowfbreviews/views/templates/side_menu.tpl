<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.7/angular.min.js"></script>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Pixel"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Pixel", true)}">
    {l s='Facebook Conversion API & Pixel' mod='pshowfbreviews'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Main"}active{/if}" 
    href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Main", true)}">
    {l s='Facebook Opinions' mod='pshowfbreviews'}
</a>

<a class="list-group-item {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}BHook"}active{/if}" 
    href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}BHook", true)}">
    {l s='Opinions Hooks' mod='pshowfbreviews'}
</a>

<a class="list-group-item visible {if $smarty.get.controller == "{$PSHOW_MODULE_CLASS_NAME_}Settings"}active{/if}"
   href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}">
    {l s='Settings' mod='pshowfbreviews'}
</a>

<a target="_blank" class="list-group-item" href="https://helpdesk.prestashow.pl/kb/faq.php?id=103">
    {l s='HelpDesk & FAQ' mod='pshowfbreviews'}
</a>

<style>
    a.list-group-item[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Hook", true)}"] { display: none; }
    a.list-group-item[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}"] { display: none; }
    a.list-group-item.visible[href="{$link->getAdminLink("{$PSHOW_MODULE_CLASS_NAME_}Settings", true)}"] { display: block; }
</style>
