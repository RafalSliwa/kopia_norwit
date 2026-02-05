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

{foreach $extratabs as $tab}
    <section id="extratabpro{$tab->id_tab}" class="page-product-box tab-pane">
        {$tab->body[$id_lang] nofilter}
        {if $tab->cms==1}
            {$tab->cms_body_show[$id_lang] nofilter}
        {/if}
    </section>
{/foreach}