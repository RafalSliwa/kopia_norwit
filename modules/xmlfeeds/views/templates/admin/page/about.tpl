{*
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
*}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-question-circle"></i> {l s='About' mod='xmlfeeds'}
    </div>
    <div class="row">
        <div class="blmod_about">
            <div style="float: right;">
                <div style="float: right;">
                    <a href="https://addons.prestashop.com/en/data-import-export/5732-xml-feeds-pro.html" target="_blank">
                        <img style="border-radius: 5px; padding: 0;" alt="Bl Modules" title="Bl Modules home page" src="../modules/{$name|escape:'htmlall':'UTF-8'}/views/img/blmod-logo-text.png" />
                    </a>
                </div>
            </div>
            <div style="float: left; width: 350px;">
                {l s='Module description at' mod='xmlfeeds'} <a href="https://addons.prestashop.com/en/data-import-export/5732-xml-feeds-pro.html" target="_blank">addons.prestashop.com</a><br/>
                {l s='You can contact us via' mod='xmlfeeds'} <a href="{$contactUsUrl|escape:'htmlall':'UTF-8'}" target="_blank">Prestashop messenger</a><br/>
                <a href="{$manualPdfUrl|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Download the module user manual' mod='xmlfeeds'}</a><br/><br>
                <a class="bl_comments" style="color: #7F7F7F; text-decoration: underline;" href="{$databaseUpgradeUrl|escape:'htmlall':'UTF-8'}">{l s='Run module database upgrade' mod='xmlfeeds'}</a><br/>
                <a target="_blank" class="bl_comments" style="color: #7F7F7F; text-decoration: underline;" href="{$exportSettingsUrl|escape:'htmlall':'UTF-8'}">{l s='Export module settings' mod='xmlfeeds'}</a><br/>
            </div>
            <div class="clear_block"></div>
            <hr>
            {l s='Access with password only' mod='xmlfeeds'}
            <form action="{$fullAdminUrl|escape:'htmlall':'UTF-8'}" method="post">
                <input type="password" name="admin_password" style="width: 200px; display: inline-block; margin-right: 10px;" value="{if !empty($currentPassword)}*******{/if}" />
                <input style="display: inline-block;" type="submit" name="update_admin_password" value="{l s='Save password' mod='xmlfeeds'}" class="btn btn-secondary">
                <div class="bl_comments">[{l s='You can restrict access to the module settings page to other administrators. If you dont want to, leave it blank.' mod='xmlfeeds'}]</div>
            </form>
        </div>
    </div>
</div>