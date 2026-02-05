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
    <div id="extratabpro{$tab->id_tab}" class="idTabHrefShort page-product-heading">{$tab->body[$id_lang]}</div>
{/foreach}