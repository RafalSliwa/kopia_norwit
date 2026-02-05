{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2019 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<script type="text/javascript">
    $(document).ready(function () {
        displayCartRuleTab('csv');
    });
    function displayCartRuleTab(tab) {
        $('.importvoucher_tab').hide();
        $('.importvoucher_tab_page').removeClass('selected');
        $('#importvoucher_' + tab).show();
        $('#importvoucher_link_' + tab).addClass('selected');
        $('#currentFormTab').val(tab);
    }
</script>

<script type="text/javascript">
    {literal}
    $(document).ready(function(){
        window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set._.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");$.src="//v2.zopim.com/?zNxjFI8jYsyzdr7F0iqmE3Y3VfR2PErH";z.t=+new Date;$.type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
        $zopim(function() {
            window.$zopim.livechat.window.hide();
        });
    });
    {/literal}
</script>

{if $output != false}
    <div class="col-lg-12 panel">
        <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/views/img/config.png"/> {l s='Import settings' mod='importvoucher'}</h3>
        {$output nofilter}
    </div>
{/if}

{if $confirm != false}
    <div class="alert alert-success">
        {l s='Vouchers added to database properly!' mod='importvoucher'}
    </div>
{/if}

<div class="importvoucher_container">
    <div class="col-lg-2 " id="importvoucher">
        <div class="productTabs">
            <ul class="tab">
                <li class="tab-row">
                    <a class="importvoucher_tab_page selected" id="importvoucher_link_csv" href="javascript:displayCartRuleTab('csv');">1. {l s='Upload CSV files' mod='importvoucher'}</a>
                </li>
                <li class="tab-row">
                    <a class="importvoucher_tab_page" id="importvoucher_link_fset" href="javascript:displayCartRuleTab('fset');">2. {l s='File settings' mod='importvoucher'}</a>
                </li>
                <li class="tab-row">
                    <a class="importvoucher_tab_page" id="importvoucher_link_vsettings" href="javascript:displayCartRuleTab('vsettings');">3. {l s='Voucher settings' mod='importvoucher'} ({l s='Pattern' mod='importvoucher'})</a>
                </li>
                <li class="tab-row">
                    <a class="importvoucher_tab_page" id="importvoucher_link_imp" href="javascript:displayCartRuleTab('imp');">4. {l s='Import selected file' mod='importvoucher'}</a>
                </li>
            </ul>
        </div>
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="general"/>
    </div>

    <div class="col-lg-10 panel">
        <div id="importvoucher_csv" class="importvoucher_tab tab-pane">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/views/img/config.png"/> {l s='Upload CSV files' mod='importvoucher'}</h3>
            {include file="../views/csv.tpl"}
        </div>
        <div id="importvoucher_vsettings" class="importvoucher_tab tab-pane">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/views/img/config.png"/> {l s='Voucher Configuration' mod='importvoucher'}</h3>
            <form action="" method="post">
                <p class="alert alert-info">
                <strong>{l s='How to import codes?' mod='importvoucher'}</strong><br/>
                3. {l s='Now it is time to define pattern of voucher code. If your CSV file will not have all required informations about voucher code - module will use settings from pattern of voucher code you can define below.' mod='importvoucher'}<br/><br/>
                </p>

                {$voucher_conf}
                <div class="separation"></div>
                <div class="clearfix"></div>
                <div class="panel-footer clearfix">
                    <button class="btn btn-default pull-right" name="save_voucher_settings" type="submit">
                        <i class="process-icon-save"></i>
                        {l s='Save settings' mod='importvoucher'}
                    </button>
                </div>
            </form>
        </div>
        <div id="importvoucher_fset" class="importvoucher_tab tab-pane">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/views/img/config.png"/> {l s='File settings' mod='importvoucher'}</h3>
            {include file="../views/file_settings.tpl"}
        </div>
        <div id="importvoucher_imp" class="importvoucher_tab tab-pane">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/views/img/config.png"/> {l s='Import selected file' mod='importvoucher'}</h3>
            <p class="alert alert-info">
                <strong>{l s='How to import codes?' mod='importvoucher'}</strong><br/>
                4. {l s='Now select file that you want to import. Hit "import to database" button to start import process' mod='importvoucher'}<br/><br/>
            </p>
            {$csvfiles}
        </div>
    </div>
    <div class="clearfix"></div>
</div>


{literal}
    <style type="text/css">
        /*== PS 1.6 ==*/
        #importvoucher ul.tab {
            list-style: none;
            padding: 0;
            margin: 0
        }

        #importvoucher ul.tab li a {
            background-color: white;
            border: 1px solid #DDDDDD;
            display: block;
            margin-bottom: -1px;
            padding: 10px 15px;
        }

        #importvoucher ul.tab li a {
            display: block;
            color: #555555;
            text-decoration: none
        }

        #importvoucher ul.tab li a.selected {
            color: #fff;
            background: #00AFF0
        }

        #importvoucher_form .language_flags {
            display: none
        }

        form#importvoucher_form {
            background-color: #ebedf4;
            border: 1px solid #ccced7;
            /*min-height: 404px;*/
            padding: 5px 10px 10px;
        }
        .importvoucher_tab h3 {
            border-top-right-radius: 5px;
            border-top-left-radius: 5px;
            padding: 5px 10px;
        }
    </style>
{/literal}