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
<div class="panel view-message" id="message">
    <div class="panel-heading">
        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg>
        [#{$message.id_contact_message|intval}]
        {$message.subject|escape:'html':'UTF-8'}
        {if $message.reply_to_check}
            <span class="panel-heading-action">
                <span class="{if $message.reply_to_check}action-reply-message{else}action-reply-message-disable{/if}">
                    <i class="process-icon-reply"></i>{l s='Reply' mod='ets_cfultimate'}
                </span>
            </span>
        {/if}
    </div>
    <div class="message-from">
        <ul class="heade_if_form">
            <li><strong>{l s='Sent by:' mod='ets_cfultimate'}</strong>{$message.sender|escape:'html':'UTF-8'}</li>
            <li><strong>{l s='Date:' mod='ets_cfultimate'}</strong>{$message.date_add|escape:'html':'UTF-8'}</li>
            <li>
                <strong>{l s='Ip:' mod='ets_cfultimate'}</strong>
                <a class="link_ip"
                   href="https://www.infobyip.com/ip-{$message.ip|escape:'html':'UTF-8'}.html"
                   target="_blank">{$message.ip|escape:'html':'UTF-8'}</a>
            </li>
        </ul>
        {if $message.id_customer}
            <span class="customer_message">
                {l s='Identified customer:' mod='ets_cfultimate'}
                {if isset($is170) && $is170}
                    {assign var="linkCustomer" value="{$link->getAdminLink('AdminCustomers',true,['id_customer'=>{$message.id_customer|intval},'updatecustomer'=>1])|escape:'html':'UTF-8'}"}
                {else}
                    {assign var="linkCustomer" value="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&viewcustomer&id_customer={$message.id_customer|intval}"}
                {/if}

                <a href="{$linkCustomer|escape:'html':'UTF-8'}">
                    {$message.customer_name|escape:'html':'UTF-8'}
                </a>
            </span>
        {/if}
    </div>
    <div id="message-content">
        {$message.body nofilter}
    </div>
    {if $message.attachments}
        {if $message.save_attachments}
            <div class="ctf7_attachments">
                <div><strong>{l s='Attachments: ' mod='ets_cfultimate'}</strong></div>
                <ul id="list-attachments">
                    {assign var='index' value=1}
                    {foreach from =$message.attachments item='attachment'}
                        {if trim($attachment)}
                            {assign var='atts' value=explode('-',$attachment)}
                            {if count($atts)>1 && array_shift($atts)}
                                {assign var='attachment2' value=implode('-',$atts)}
                            {else}
                                {assign var='attachment2' value=$attachment}
                            {/if}
                            <li>
                                <a href="{$download_url nofilter}&file={$attachment|escape:'html':'UTF-8'}"
                                   target="_blank">{$attachment2|escape:'html':'UTF-8'}</a>
                            </li>
                        {/if}
                        {assign var='index' value=$index+1}
                    {/foreach}
                </ul>
            </div>
        {else}
            <p class="alert alert-warning">{l s='Attachments were sent via email' mod='ets_cfultimate'}</p>
        {/if}
    {/if}
    <ul id="list-replies">
        {if $replies}
            {foreach from=$replies key='key' item='reply'}
                <li>
                    <span class="content-reply">
                        <b>{l s='Reply' mod='ets_cfultimate'}&nbsp;{$key|intval+1}:&nbsp;</b>{$reply.content|strip_tags:'UTF-8'|truncate:150:'...'}
                    </span>
                    <span class="content-reply-full">
                        <p>
                            <b>{l s='Reply to:' mod='ets_cfultimate'}</b>&nbsp;{$reply.reply_to|escape:'html':'UTF-8'} {$reply.date_add|escape:'html':'UTF-8'}
                        </p>
                        <p>
                            <b>{l s='Subject:' mod='ets_cfultimate'}</b>&nbsp;{$reply.subject|escape:'html':'UTF-8'}
                        </p>
                        <p class="content-message">
                            <b>{l s='Content:' mod='ets_cfultimate'}</b>&nbsp;{$reply.content nofilter}
                        </p>
                       {if isset($reply.attachment_file) && $reply.attachment_file}
                        <p class="content-message">
                            <b>{l s='Attachment:' mod='ets_cfultimate'}</b> <a href="{$reply.attachment_file nofilter}">{$reply.attachment nofilter}</a>
                        </p>
                       {/if}
                    </span>
                </li>
            {/foreach}
        {/if}
    </ul>
</div>
<form id="module_form_reply-message" style="display:none;" class="defaultForm form-horizontal" novalidate=""
      enctype="multipart/form-data" method="post" action="">
    <div class="panel" id="replay-message-form">
        <div class="panel-heading">
            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg>
            {l s='Reply message:' mod='ets_cfultimate'}&nbsp;[#{$message.id_contact_message|intval}
            ]&nbsp;{$message.subject|escape:'html':'UTF-8'}
        </div>
        <div class="form-wrapper">
            <input type="hidden" value="{$message.id_contact_message|intval}" name="id_message"/>
            {if isset($message.product) && $message.product}
                <div class="form-group">
                    <label class="control-label col-lg-2">&nbsp;</label>
                    <div class="col-lg-10">
                        <a class="ets-cfu-product" href="{$message.product.link nofilter}">
                            {if isset($message.product.image) && $message.product.image|trim !== ''}
                                <img src="{$message.product.image nofilter}" style="width: 45px;height:auto;vertical-align: middle;">
                            {/if}
                            {$message.product.name|escape:'html':'UTF-8'}
                        </a>
                    </div>
                </div>
            {/if}
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='From:' mod='ets_cfultimate'} </label>
                <div class="col-lg-10">
                    <input name="from_reply" value="{$message.from_reply|escape:'html':'UTF-8'}" type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='To:' mod='ets_cfultimate'} </label>
                <div class="col-lg-10">
                    <input name="reply_to" value="{$message.reply_to|escape:'html':'UTF-8'}" type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Subject' mod='ets_cfultimate'}</label>
                <div class="col-lg-10">
                    <input name="reply_subject"
                           value="{l s='Reply' mod='ets_cfultimate'}: {$message.subject|escape:'html':'UTF-8'}"
                           type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Reply to' mod='ets_cfultimate'}</label>
                <div class="col-lg-10">
                    <input name="reply_to_reply" value="{$message.email_to|escape:'html':'UTF-8'}" type="text"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2 required">{l s='Message' mod='ets_cfultimate'} </label>
                <div class="col-lg-10">
                    <textarea name="message_reply" placeholder="{l s='Message' mod='ets_cfultimate'}"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-lg-2">{l s='Attachment' mod='ets_cfultimate'}</label>
                <div class="col-lg-10">
                    <input id="attachment" type="file" name="attachment" class="attachment">
                    <p class="help-block">
                        {l s='Accepted format: jpg, jpeg, png, gif, zip, pdf. Limit: %s' sprintf=[$ETS_CFU_POST_MAX_SIZE] mod='ets_cfultimate'}
                    </p>
                </div>
            </div>
            <div class="panel-footer">
                <button id="module_form_submit_btn_reply" class="btn btn-default pull-right"
                        name="etsCfuSubmitReplyMessage"
                        value="1" type="submit">
                    <i class="icon process-icon-reply"></i>
                    {l s='Send' mod='ets_cfultimate'}
                </button>
                <button id="module_form_submit_btn_back" class="btn btn-default pull-left" name="backReplyMessage"
                        value="1" type="button">
                    <i class="icon process-icon-back"></i>
                    {l s='Back' mod='ets_cfultimate'}
                </button>
            </div>
        </div>
    </div>
</form>