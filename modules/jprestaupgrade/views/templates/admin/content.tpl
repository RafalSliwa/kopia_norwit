{*
* Upgrade module powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
*               is permitted for one Prestashop instance only but you can install it on your test instances.
*}
<script type="application/javascript">
    {if !$jpresta_account_key}
    function refreshSubmitModuleJakStatus() {
        if ($('#jprestaAccountKey').val().length >= 20 && $('input[name=prestashopType]:checked').length > 0) {
            $('#submitModuleJak').removeAttr('disabled');
            $('#cannotValidate').hide();
        } else {
            $('#submitModuleJak').attr('disabled', '1');
            $('#cannotValidate').show();
        }
    }

    $(document).ready(function () {
        refreshSubmitModuleJakStatus();
        $('input').on('keyup keypress blur change', refreshSubmitModuleJakStatus);
    });
    {else}
    function upgradeModule(module_name, retryCount, displayName, currentVersion, newVersion) {
        if (typeof retryCount === 'undefined') { retryCount = 0; }
        startUpgrading(module_name, displayName, currentVersion, newVersion);
        if (retryCount === 0) {
            // Clear notifications
            $('#row_' + module_name + ' .notifications').html('');
        }
        $.ajax({
            type: "POST",
            url: '{$request_uri|escape:'javascript':'UTF-8'}',
            data: { action: 'upgradeModule', submitModuleUpgrade: module_name, ajax: true },
            dataType: 'json'
        })
            .done(function(data, textStatus, jqXHR) {
                treatUpgradeModule(data, module_name, retryCount, displayName, currentVersion, newVersion);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                if (typeof jqXHR.responseJSON != 'undefined' && typeof jqXHR.responseJSON['status'] != 'undefined') {
                    console.log('Ignoring the error code since the response body is correct');
                    // With PS8.1.7 the response is good but response code is 500, cannot find why :'(
                    treatUpgradeModule(jqXHR.responseJSON, module_name, retryCount, displayName, currentVersion, newVersion);
                }
                else {
                    // Technical/network error
                    console.error(textStatus, errorThrown);
                    endUpgrading(module_name);
                }
            });
    }
    function treatUpgradeModule(jsonResponse, module_name, retryCount, displayName, currentVersion, newVersion) {
        if (jsonResponse['status'] === 'ok') {
            $('#row_' + module_name).replaceWith(jsonResponse['content']);
            endUpgrading(module_name);
        }
        else if (jsonResponse['status'] === 'restart') {
            if (retryCount < 1) {
                upgradeModule(module_name, retryCount + 1, displayName, currentVersion, newVersion);
            }
            else {
                alert("{l s='Not sure if the upgrade has correctly finished, please reload the page to be sure.' mod='jprestaupgrade'}");
                endUpgrading(module_name);
            }
        }
        else if (jsonResponse['status'] === 'error') {
            // Fonctional error
            endUpgrading(module_name);
        }
        for (let msgIndex in jsonResponse['confirmations']) {
            $('#row_' + module_name + ' .notifications').append('<div class="alert alert-success">' + jsonResponse['confirmations'][msgIndex] + '</div>');
        }
        for (let msgIndex in jsonResponse['informations']) {
            $('#row_' + module_name + ' .notifications').append('<div class="alert alert-info">' + jsonResponse['informations'][msgIndex] + '</div>');
        }
        for (let msgIndex in jsonResponse['warnings']) {
            $('#row_' + module_name + ' .notifications').append('<div class="alert alert-warning">' + jsonResponse['warnings'][msgIndex] + '</div>');
        }
        for (let msgIndex in jsonResponse['errors']) {
            $('#row_' + module_name + ' .notifications').append('<div class="alert alert-danger">' + jsonResponse['errors'][msgIndex] + '</div>');
        }
        for (let msgIndex in jsonResponse['error']) {
            $('#row_' + module_name + ' .notifications').append('<div class="alert alert-danger">' + jsonResponse['error'][msgIndex] + '</div>');
        }
    }
    function startUpgrading(module_name, displayName, currentVersion, newVersion) {
        console.log('JPresta Easy Upgrade - Start upgrading ' + module_name + '...');
        $('#loading').show();
        // Disable all buttons
        $('#jprestaeasyupgrade button.upgrade').attr('disabled', 'true');
    }
    function endUpgrading(module_name) {
        console.log('JPresta Easy Upgrade - End upgrading ' + module_name + '.');
        $('#loading').hide();
        // Enable back all buttons
        $('#jprestaeasyupgrade button.upgrade').removeAttr('disabled');
    }
    {/if}
    $(document).ready(function () {
        $('#jprestaeasyupgrade form.upgrade').on('submit', function(event) {
            // Do not send the form, we will do it with ajax because we may need 2 requests to uprgade modules
            event.preventDefault();
            // Confirmation message
            if (!confirm("{l s='Please, confirm the upgrade of ' mod='jprestaupgrade'} " + $(this).find('input[name=displayName]').val())) {
                return;
            }
            // Execute the upgrade function
            upgradeModule(
                $(this).find('input[name=submitModuleUpgrade]').val(),
                0,
                $(this).find('input[name=displayName]').val(),
                $(this).find('input[name=currentVersion]').val(),
                $(this).find('input[name=newVersion]').val()
            );
        });
    });
</script>
<style type="text/css">
    button:disabled {
        cursor: not-allowed;
        pointer-events: all !important;
    }
    #jprestaeasyupgrade .table tbody>tr>td {
        vertical-align: top;
        padding: 0.5rem 0.5rem 1rem 0.5rem;
    }
    #jprestaeasyupgrade .changelogs {
        max-height: 30rem;
        overflow-y: scroll;
        border: 1px solid #bbcdd2;
        border-radius: 4px;
        margin: 0.5rem 0 1rem 0;
        padding: 0.5rem;
    }
    #jprestaeasyupgrade form {
        display: inline-block;
    }
    #jprestaeasyupgrade #loading {
        display: none;
        position: fixed;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 9998;
        background-color: #ffffffdd;
    }
    #jprestaeasyupgrade #loading .loadingmsg {
        margin: 150px auto auto;
        width: 500px;
        padding: 1rem;
        font-size: 1.5rem;
        font-weight: bold;
        border: 2px solid #25b9d7;
        border-radius: 8px;
    }
    #jprestaeasyupgrade .notifications {
        margin-top: 1rem;
    }
    #jprestaeasyupgrade .migrate_sp {
        background-color: #ffffb2;
        border: 2px solid #d3cd22;
        margin: 1rem 0;
        padding: 0.5rem;
        font-size: 0.9rem;
    }
</style>
<div id="jprestaeasyupgrade">
    <div id="loading">
        <div class="loadingmsg">
            <i class="icon-refresh icon-spin" style="font-size: 1.8rem;"></i> {l s='Upgrading, please wait...' mod='jprestaupgrade'}
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="panel">
                <h3><img height="22" src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/logo-jpresta.png"
                         alt=""/>&nbsp;{l s='JPresta account' mod='jprestaupgrade'}</h3>

                {if !$jpresta_account_key}
                    <p>{l s="To be able to download and install latest versions of your modules and theme, you must create an account on jpresta.com and attach your JPresta Account Key to this Prestashop instance." mod='jprestaupgrade'}</p>
                    <form method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                        <input type="hidden" name="submitModuleJak" value="true"/>
                        <input type="hidden" name="pctab" value="license"/>
                        <div style="margin: 10px 0">
                            {l s='This Prestashop instance is: ' mod='jprestaupgrade'}
                            <label class="radio-inline" style="margin-left: 10px">
                                <input type="radio" name="prestashopType" id="prestashopType1"
                                       value="prod"> {l s='a live site with real customers' mod='jprestaupgrade'}
                            </label>
                            <label class="radio-inline" style="margin-left: 10px">
                                <input type="radio" name="prestashopType" id="prestashopType2"
                                       value="test"> {l s='for test only' mod='jprestaupgrade'}
                            </label>
                        </div>
                        <div class="form-group">
                            <input type="text" style="width:20rem" class="form-control" id="jprestaAccountKey"
                                   name="jprestaAccountKey"
                                   placeholder="{l s='Example: JPRESTA-AB12YZ89XX00' mod='jprestaupgrade'}">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-sign-in"></i>&nbsp;{l s='Attach my JPresta Account Key' mod='jprestaupgrade'}
                        </button>
                        <div class="alert alert-warning" style="margin-top: 10px" id="cannotValidate">
                            {l s='In order to validate, select the type of Prestashop instance and fill in the JPresta Account Key' mod='jprestaupgrade'}
                        </div>
                    </form>
                {else}
                    {if $jpresta_clone_detected}
                        <div class="alert alert-danger">
                            <strong>{l s='This Prestashop instance seems to be a clone of an other Prestahop.' mod='jprestaupgrade'}</strong>
                            <p>
                                {l s='Clones are allowed but can messes up your licenses or your JPresta-Cache-Warmer subscription so please, just tell us if it is a clone or not.' mod='jprestaupgrade'}
                            </p>
                            <p>
                                {l s='This message can be displayed if you modified your database connexion. If so then just click on "No, it is the same Prestashop".' mod='jprestaupgrade'}
                            </p>

                            <form id="confirmClone" method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                                <input type="hidden" name="submitModule" value="true"/>
                                <input type="hidden" name="pctab" value="license"/>
                                <div style="text-align: left; margin: 10px 0 0 0;">
                                    <button type="submit" id="submitModuleConfirmClone" name="submitModuleConfirmClone" class="btn btn-secondary">{l s='Yes, it is a clone' mod='jprestaupgrade'}</button>
                                    <button type="submit" id="submitModuleNotAClone" name="submitModuleNotAClone" class="btn btn-secondary">{l s='No, it is the same Prestashop' mod='jprestaupgrade'}</button>
                                </div>
                            </form>
                        </div>
                    {/if}

                    <p>
                        <input type="text" style="width:20rem;display: inline-block" class="form-control"
                               name="jprestaAccountKey" readonly disabled
                               value="{$jpresta_account_key|escape:'url':'UTF-8'}">
                        <i class="icon-check" style="font-size: 1.5rem; margin: 0 6px; color: green;"></i>
                        <a href="#" onclick="$('#confirmDetach').toggle()">{l s='detach' mod='jprestaupgrade'}</a>
                    </p>
                    <p>{l s="Congratulation, the module is attached to your JPresta Account." mod='jprestaupgrade'}</p>
                    <form id="confirmDetach" style="display: none" method="post"
                          action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                        <input type="hidden" name="submitModuleJakDetach" value="true"/>
                        <input type="hidden" name="pctab" value="license"/>
                        {l s="If you detach your JPresta Account you will not be able to download and install upgrades with this module anymore" mod='jprestaupgrade'}
                        <div style="text-align: center; margin: 10px 0 0 0;">
                            <button type="submit" class="btn btn-danger">
                                <i class="icon-sign-out"></i>&nbsp;{l s='I confirm, detach my JPresta Account' mod='jprestaupgrade'}
                            </button>
                        </div>
                    </form>
                {/if}
            </div>
        </div>
    </div>
    {if $jpresta_account_key && !$jpresta_clone_detected}
        <div class="row">
            <div class="col">
                <div class="panel">
                    <h3><i class="icon-cogs"></i>&nbsp;{l s='JPresta themes' mod='jprestaupgrade'}</h3>
                    <div>
                        <div class="alert alert-info">
                            {l s='Here you can only upgrade theme, to get more informations or to manage your licenses you must go in your JPresta account. To refresh informations about your licenses just reload this page.' mod='jprestaupgrade'}
                        </div>
                        <table class="table">
                            <colgroup>
                                <col style="width: 60px; text-align: center">
                                <col>
                                <col style="width: 50%;">
                            </colgroup>
                            {foreach from=$jpresta_modules item=module}
                                {if $module.type==='theme'}
                                    {include file="./_row.tpl" module=$module}
                                {/if}
                            {/foreach}
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="panel">
                    <h3><i class="icon-cogs"></i>&nbsp;{l s='JPresta modules' mod='jprestaupgrade'}</h3>
                    <div>
                        <div class="alert alert-info">
                            {l s='Here you can only upgrade modules, to get more informations or to manage your licenses you must go in your JPresta account. To refresh informations about your licenses just reload this page.' mod='jprestaupgrade'}
                        </div>
                        <table class="table">
                            <colgroup>
                                <col style="width: 60px; text-align: center">
                                <col>
                                <col style="width: 50%;">
                            </colgroup>
                            {foreach from=$jpresta_modules item=module}
                                {if $module.type==='module'}
                                    {include file="./_row.tpl" module=$module}
                                {/if}
                            {/foreach}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>
