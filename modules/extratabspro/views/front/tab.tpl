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
    <li class="nav-item"><a id="extratabpro{$tab->id_tab}link" class="nav-link" href="#extratabpro{$tab->id_tab}" data-toggle="tab">{$tab->name[$id_lang]}</a></li>
{/foreach}