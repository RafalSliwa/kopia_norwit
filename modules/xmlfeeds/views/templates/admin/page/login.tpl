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
<div class="blmod_body">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-list-alt"></i> {l s='Access denied' mod='xmlfeeds'}
        </div>
        <div class="clear_block"></div>
        <div>
            {if !empty($invalidPassword)}
                <div class="alert alert-danger blmod_mt10">
                    {l s='Incorrect password, please try again' mod='xmlfeeds'}
                </div>
            {/if}
            {l s='Password' mod='xmlfeeds'}
            <form action="{$fullAdminUrl|escape:'htmlall':'UTF-8'}" method="post">
                <input type="password" name="admin_password" style="width: 200px; display: inline-block; margin-right: 10px;" value="" />
                <input style="display: inline-block;" type="submit" name="login_admin_action" value="{l s='Login' mod='xmlfeeds'}" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>
