{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    SeoSA    <885588@bk.ru>
* @copyright 2012-2024 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<thead>
<tr>
    <th class="name">Nome</th>
    <th class="priority">Priorità</th>
    <th class="changefreq">Cambia freq</th>
    <th class="export">Esportare?</th>
</tr>
</thead>
<tbody>
{foreach $data as $cms}
<tr class="object_item">
    <td class="object_item_name">
        <input type="hidden" name="sitemap[cms{$cms['id_cms']|escape:'htmlall':'UTF-8'}][type_object]" value="cms">
        <input type="hidden" name="sitemap[cms{$cms['id_cms']|escape:'htmlall':'UTF-8'}][id_object]" value="{$cms['id_cms']|escape:'htmlall':'UTF-8'}">
        {$cms['meta_title']|escape:'htmlall':'UTF-8'}
    </td>
    <td class="object_item_priority">
        <select class=" priority-block_select" name="sitemap[cms{$cms['id_cms']|escape:'htmlall':'UTF-8'}][priority]">
            {foreach $priorities as $prioritet}
            <option value="{$prioritet['id']|escape:'htmlall':'UTF-8'}" {if $prioritet['id'] == $cms['priority']} selected{/if}>{$prioritet['id']|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
    </td>
    <td class="object_item_changefreq">
        <select class="changefreq-block_select " name="sitemap[cms{$cms['id_cms']|escape:'htmlall':'UTF-8'}][changefreq]">
            {foreach $changefreqs as $changefreq}
                <option type='checkbox'
                        value="{$changefreq['id']|escape:'htmlall':'UTF-8'}" {if $changefreq['id'] == $cms['changefreq']} selected{/if}>{$changefreq['name']|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
    </td>
    <td class="object_item_action">
        <div class="wrapp_checkbox">
            <input type="checkbox"  value="1" {if $cms['is_export'] == 1}checked{/if} name="sitemap[cms{$cms['id_cms']|escape:'htmlall':'UTF-8'}][is_export]"/>
            <span class="icon_checkbox"></span>
        </div>
    </td>
</tr>
{/foreach}
</tbody>