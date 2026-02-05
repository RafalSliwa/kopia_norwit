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
{hook h='contactFormUltimateTopBlock'}
<div class="cfu-content-block">
	<div class="panel ctf7_backend_help">
		<div class="panel-heading">
			<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg> {l s='Help' mod='ets_cfultimate'}
		</div>
		<p>{l s='Click on the following link to download the documentation of this module' mod='ets_cfultimate'}: <a target="_blank" href="{$link_doc|escape:'html':'UTF-8'}">{l s='Download documentation' mod='ets_cfultimate'}</a></p>
		<p>{l s='Below are some notes you should pay attention to while using [1]Contact Form Ultimate:[/1]' tags = ['<strong>'] mod='ets_cfultimate'}</p>
		<h4>{l s='Contact Forms' mod='ets_cfultimate'}</h4>

		<p>{l s='When creating a new contact form, if you click on [1]"Add new input field/row to create your contact form"[/1] link and select an input field, by default this input field will be put on a full-width row (1 column)' tags=['<strong>'] mod='ets_cfultimate'}.</p>
		<p>{l s='To add reCAPTCHA input field, you need to enable reCAPTCHA first. Navigate to [1]"Settings > Integration > reCAPTCHA"[/1], enable reCAPTCHA option and enter your key pair.' tags=['<strong>'] mod='ets_cfultimate'}</p>
		<p>{l s='For how to get reCAPTCHA site key and secret key, please read our module documentation' mod='ets_cfultimate'}</p>
		<p>{l s='To be able to reply customer messages directly on back office, you need your customer email address. When building a contact form, make sure to add an email input field and mark it as required field.' mod='ets_cfultimate'}</p>

		<h4>{l s='Email configurations' mod='ets_cfultimate'}</h4>

		<p>{l s='To get the info from your contact form to the email send to admin or auto responder email, please copy and paste the respective mail-tags into [1]"Message body"[/1] field in [1]"Contact Forms > Mail"[/1] subtab' tags=['<strong>'] mod='ets_cfultimate'}</p>
		<p>{l s='To receive attachment file from your customer via email, please navigate to [1]"Contact Forms > Mail"[/1] and check the [1]"File attachment"[/1] box.' tags=['<strong>'] mod='ets_cfultimate'}</p>
		<p>{l s='To receive attachment file from your customer via “Messages” tab, please navigate to [1]"Contact Forms > Settings"[/1] and turn on the [1]"Save attachments"[/1] option.' tags=['<strong>'] mod='ets_cfultimate'}</p>
	</div>
</div>