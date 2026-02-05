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
            {l s='Please open file:' mod='pdgetdatabyvatnumberpro'}
            <br><code>themes/{$theme_name}/templates/customer/_partials/address-form.tpl</code><br />
            {l s='for editing and add before block address_form_fields belowe line of code:' mod='pdgetdatabyvatnumberpro'}
            <textarea name="code" cols="100" rows="1">{literal}{hook h='displaySearchByNip'}{/literal}</textarea>
            <br />
            {l s='Please open file: (to add form to admin panel add address page)' mod='pdgetdatabyvatnumberpro'}
            <br><code>/src/PrestaShopBundle/Resources/views/Admin/Sell/Address/add.html.twig</code><br />
            {l s='for editing and add before ' mod='pdgetdatabyvatnumberpro'}
            <br /><code>{$admin_address_line_of_code_17|escape:'htmlall':'UTF-8'}</code><br />
            {l s='line of code:' mod='pdgetdatabyvatnumberpro'}
            <textarea name="code" cols="100" rows="1">{literal}{{renderhook('displaySearchByNipAdmin')}}{/literal}</textarea>
    </div>
</fieldset>