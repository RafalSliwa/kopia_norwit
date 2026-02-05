{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2025 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 *}

<section class="page-product-box">
    {foreach $extratabs as $tab}
        <h3 id="extratabpro{$tab->id_tab}link" class="idTabHrefShort page-product-heading">{$tab->name[$id_lang]}</h3>
        <div class="rte" id="extratabpro{$tab->id_tab}">
            {$tab->body[$id_lang]}
            {if $tab->cms==1}
                {$tab->cms_body_show[$id_lang] nofilter}
            {/if}
        </div>
    {/foreach}
</section>