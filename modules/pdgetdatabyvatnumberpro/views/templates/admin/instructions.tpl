{**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - Pd Get data by vat number Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek - PrestaDev.pl
* @link      http://prestadev.pl
* @package   Pd Get data by vat number Pro for - PrestaShop 1.5.x and 1.6.x and 1.7.x
* @version   1.0.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date      7-06-2018
*}

<!-- Pd Regon Api Pro customHook -->

<fieldset class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='Instalation instructions how to add collect data by nip to store forms' mod='pdgetdatabyvatnumberpro'}
    </div>
    <div class="form-wrapper">
        <ul>
            <li>{l s='First go to site: http://bip.stat.gov.pl/dzialalnosc-statystyki-publicznej/rejestr-regon/interfejsyapi/ and register your company to get user key to comunicate with GUS API' mod='pdgetdatabyvatnumberpro'}</li>
            <li>{l s='When GUS send you user key please enter it in module settings abowe' mod='pdgetdatabyvatnumberpro'}</li>
            <li>{l s='Last step is to add to choosen places in our store form to get data by NIP, belowe are instructions how to do it:' mod='pdgetdatabyvatnumberpro'}</li>
        </ul>
            {l s='Please open file: (to add form to coustomer address add section in my account)' mod='pdgetdatabyvatnumberpro'}
            <b>themes/{$theme_name}/address.tpl</b>
            {l s='for editing and add before opening html form tag add belowe line of code:' mod='pdgetdatabyvatnumberpro'}

            <textarea name="code" cols="100" rows="1">{literal}{hook h='displaySearchByNip'}{/literal}</textarea><br />
            {l s='Please open file: (to add form to 5 step checkout new account page)' mod='pdgetdatabyvatnumberpro'}
            <b>themes/{$theme_name}/authentication.tpl</b>
            {l s='for editing and add after that line: {if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE} line of code:' mod='pdgetdatabyvatnumberpro'}
            <textarea name="code" cols="100" rows="1">{literal}{hook h='displaySearchByNip'}{/literal}</textarea><br />
            {l s='Please open file: (to add form to OPC new account page)' mod='pdgetdatabyvatnumberpro'}
            <b>themes/{$theme_name}/order-opc-new-account.tpl</b>
            {l s='for editing and add before html H3 tag containing text "Delivery address" add line of code:' mod='pdgetdatabyvatnumberpro'}
            <textarea name="code" cols="100" rows="1">{literal}{hook h='displaySearchByNip'}{/literal}</textarea><br />
            {l s='Please open file: (to add form to admin panel add address page)' mod='pdgetdatabyvatnumberpro'}

            <b>{l s='(your admin folder name)' mod='pdgetdatabyvatnumberpro'}/themes/defaul/template/controller/addresses/helpers/form/form.tpl</b>
            {l s='for editing and add before ' mod='pdgetdatabyvatnumberpro'}
            {$admin_address_line_of_code|escape:'htmlall':'UTF-8'}
            {l s=' add line of code:' mod='pdgetdatabyvatnumberpro'}
            <textarea name="code" cols="100" rows="1">{literal}{hook h='displaySearchByNipAdmin'}{/literal}</textarea>

    </div>
</fieldset>