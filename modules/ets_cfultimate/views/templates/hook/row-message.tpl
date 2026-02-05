{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<td class="message-more-action">
    <input type="checkbox" name="etsCfuMessageReaded[{$message.id_contact_message|intval}]" class="message_readed" value="1" data="{$message.readed|intval}"/>
    <div class="star {if $message.special} star-on{/if}" title="{if $message.special}{l s='Unstar this message' mod='ets_cfultimate'}{else}{l s='Star this message' mod='ets_cfultimate'}{/if}">
        <input type="checkbox" name="message_special[{$message.id_contact_message|intval}]" class="message_special" value="{$message.id_contact_message|intval}" data="{if $message.special|intval > 0}1{else}0{/if}"/>
        <svg class="read_mes" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>
        <svg class="no_read_mes" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>
    </div>
</td>
<td class="message-subject">
    <span data-toggle="tooltip" title="{l s='From' mod='ets_cfultimate'}: {$message.sender|escape:'html':'UTF-8'}">{$message.subject|escape:'html':'UTF-8'|truncate:100:'...'}</span>
</td>
<td class="message-message">
    {$message.body|strip_tags|nl2br|truncate:400:'...' nofilter}
    {if $message.attachments}
        <span class="message-attachements">
            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1596 1385q0 117-79 196t-196 79q-135 0-235-100l-777-776q-113-115-113-271 0-159 110-270t269-111q158 0 273 113l605 606q10 10 10 22 0 16-30.5 46.5t-46.5 30.5q-13 0-23-10l-606-607q-79-77-181-77-106 0-179 75t-73 181q0 105 76 181l776 777q63 63 145 63 64 0 106-42t42-106q0-82-63-145l-581-581q-26-24-60-24-29 0-48 19t-19 48q0 32 25 59l410 410q10 10 10 22 0 16-31 47t-47 31q-12 0-22-10l-410-410q-63-61-63-149 0-82 57-139t139-57q88 0 149 63l581 581q100 98 100 235z"/></svg>
        </span>
    {/if}
</td>
<td class="message-title">
    {$message.title|escape:'html':'UTF-8'|truncate:100:'...'}
</td>
<td class="replies text-center">
    {if $message.replies}
        <i class="action-enabled" title="{l s='Message has been replied' mod='ets_cfultimate'}">
            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
        </i>
    {else}
        <i class="svg-icon-clock-o" title="{l s='Pending' mod='ets_cfultimate'}" style="color: #fdc107"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>
    {/if}
</td>
<td class="text-center msg_date_form">
    <span class="msg_date">{$message.date_add|date_format:"%Y-%m-%d"|escape:'html':'UTF-8'}</span>
    <span class="msg_hour">{$message.date_add|date_format:"%H:%M:%S"|escape:'html':'UTF-8'}</span>
</td>
<td class="text-center">
    <div class="btn-group-action">
        <div class="btn-group">
            <a class="ctf_view_message" href="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}&etsCfuViewMessage&id_message={$message.id_contact_message|intval}" class="message-view">
                {l s='View' mod='ets_cfultimate'}
            </a>
            <a class="btn btn-link dropdown-toggle dropdown-toggle-split product-edit" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" >
                <svg width="12" height="12" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
            </a>
            <div x-placement="bottom-end" class="dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-164px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                <a href="{$link->getAdminLink('AdminContactFormUltimateMessage',true)|escape:'html':'UTF-8'}&etsCfuDeleteMessage&id_message={$message.id_contact_message|intval}" class="dropdown-item message-delete product-edit" title="{l s='Delete' mod='ets_cfultimate'}">
                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg> {l s='Delete' mod='ets_cfultimate'}
                </a>
            </div>
        </div>
    </div>
</td>