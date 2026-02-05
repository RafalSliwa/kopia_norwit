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

<div class="row clearfix">
    <div class="col-lg-12 col-xs-12">
        <div class="tabs">
            <ul class="nav nav-tabs">
                {foreach $extratabs as $tab name='for'}
                    <li class="nav-item "><a class="nav-link {if $smarty.foreach.for.first}active{/if}" id="extratabpro{$tab->id_tab}link" href="#extratabpro{$tab->id_tab}" data-toggle="tab">{$tab->name[$id_lang]}</a></li>
                {/foreach}
            </ul>

            <div class="tab-content">
                {foreach $extratabs as $tab name='for'}
                    <section id="extratabpro{$tab->id_tab}" class="{if $smarty.foreach.for.first}active{/if} page-product-box tab-pane fade in">
                        {$tab->body[$id_lang] nofilter}
                        {if $tab->cms==1}
                            {$tab->cms_body_show[$id_lang] nofilter}
                        {/if}
                    </section>
                {/foreach}
            </div>
        </div>
    </div>
</div>