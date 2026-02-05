<tr id="row_{$module.name|escape:'html':'UTF-8'}">
    <td>
        {if $module.type === 'module'}
            <img src="../modules/{$module.name|escape:'html':'UTF-8'}/logo.png" width="64" height="64" alt="{$module.displayName|escape:'html':'UTF-8'}">
        {else}
            <img src="../themes/{$module.name|escape:'html':'UTF-8'}/assets/img/logo.png" width="64" height="64" alt="{$module.displayName|escape:'html':'UTF-8'}">
        {/if}
    </td>
    <td>
        <div>
            <span style="font-size: 1.1rem; font-weight: bold;">{$module.displayName|escape:'html':'UTF-8'}</span>
            <small>v{$module.version|escape:'html':'UTF-8'} - by <b>{$module.author|escape:'html':'UTF-8'}</b></small>
            {if isset($module.license)}
                {$module.license.status|default:'' nofilter}
            {else}
                <span class="badge badge-danger">{l s='No license found!' mod='jprestaupgrade'}</span>
            {/if}
        </div>
        <div class="notifications"></div>
        <p>{$module.description|escape:'html':'UTF-8'}</p>
        {if isset($module.license) && isset($module.license.latest)}
            <p style="font-weight: bold"><span class="badge">v{$module.license.latest.version|escape:'html':'UTF-8'}</span> {l s='is available if you extend your license!' mod='jprestaupgrade'}</p>
        {/if}

        {if isset($module.license) && isset($module.license.download)}
            <a data-toggle="collapse" href="#changelogs{$module.name|escape:'html':'UTF-8'}" role="button" aria-expanded="false" aria-controls="changelogs{$module.name|escape:'html':'UTF-8'}" class="btn btn-default">{l s='See changelogs of' mod='jprestaupgrade'} v{$module.license.download.version|escape:'html':'UTF-8'}</a>
        {/if}
        {if isset($module.license) && isset($module.license.latest)}
            <a data-toggle="collapse" href="#changelogslatest{$module.name|escape:'html':'UTF-8'}" role="button" aria-expanded="false" aria-controls="changelogslatest{$module.name|escape:'html':'UTF-8'}" href="#" class="btn btn-default">{l s='See changelogs of' mod='jprestaupgrade'} v{$module.license.latest.version|escape:'html':'UTF-8'}</a>
        {/if}
        {if isset($module.configure_link) && $module.name != 'jprestaupgrade'}
            <a href="{$module.configure_link|escape:'html':'UTF-8'}" style="border: 1px solid #25b9d7" class="btn btn-secondary"><i class="icon-wrench"></i>&nbsp;{l s='Configure' d='Admin.Actions'}</a>
        {/if}
        {if isset($module.license) && isset($module.license.download)}
            <div id="changelogs{$module.name|escape:'html':'UTF-8'}" class="collapse changelogs">
                {foreach from=$module.license.download.changelogs key=versionLogs item=logs}
                    {$versionLogs|escape:'html':'UTF-8'}
                    <ul>
                        {foreach from=$logs item=log}
                            <li>{$log|escape:'html':'UTF-8'}</li>
                        {/foreach}
                    </ul>
                {/foreach}
            </div>
        {/if}
        {if isset($module.license) && isset($module.license.latest)}
            <div id="changelogslatest{$module.name|escape:'html':'UTF-8'}" class="collapse changelogs">
                {foreach from=$module.license.latest.changelogs key=versionLogs item=logs}
                    {$versionLogs|escape:'html':'UTF-8'}
                    <ul>
                        {foreach from=$logs item=log}
                            <li>{$log|escape:'html':'UTF-8'}</li>
                        {/foreach}
                    </ul>
                {/foreach}
            </div>
        {/if}
    </td>
    <td>
        {if isset($module.license)}
            {if isset($module.license.download)}
                {if !$module.license.download.can_upgrade}
                    <span class="btn btn-default label-tooltip" style="cursor: not-allowed !important;" data-html="true" data-toggle="tooltip" data-original-title="{l s='You need to upgrade the following modules first: ' mod='jprestaupgrade'} {$module.license.download.message|escape:'html':'UTF-8'}" data-placement="top">
                        <i class="icon-cloud-download"></i>
                        {l s='Upgrade' mod='jprestaupgrade'} v{$module.version|escape:'html':'UTF-8'} => v{$module.license.download.version|escape:'html':'UTF-8'}
                    </span>
                {else}
                    {if $module.name == 'pagecache' && $module.license.is_migration_pcu2sp}
                        {if $can_migrate}
                            <a class="btn btn-success upgrade" href="{$migration_pcu2sp_link|escape:'html':'UTF-8'}">
                                <img src="../modules/jprestaupgrade/views/img/migration-pcu2sp.png" width="107" height="32"/>
                                {l s='Launch migration to the Speed Pack module' mod='jprestaupgrade'} v{$module.license.download.version|escape:'html':'UTF-8'}
                            </a>
                        {else}
                            <div class="alert alert-danger">
                                {l s='You cannot migrate to Speed Pack because your Prestashop version is too old, please contact the support.' mod='jprestaupgrade'}
                            </div>
                        {/if}
                    {else}
                        <form action="#" method="post" class="upgrade">
                            <input type="hidden" name="submitModuleUpgrade" value="{$module.name|escape:'html':'UTF-8'}">
                            <input type="hidden" name="displayName" value="{$module.displayName|escape:'html':'UTF-8'}">
                            <input type="hidden" name="currentVersion" value="{$module.version|escape:'html':'UTF-8'}">
                            <input type="hidden" name="newVersion" value="{$module.license.download.version|escape:'html':'UTF-8'}">
                            <button class="btn btn-success upgrade">
                                <i class="icon-cloud-download"></i>
                                {l s='Upgrade' mod='jprestaupgrade'} v{$module.version|escape:'html':'UTF-8'} => v{$module.license.download.version|escape:'html':'UTF-8'}
                            </button>
                        </form>
                    {/if}
                {/if}
            {elseif $module.name == 'pagecache' && $module.license.is_migration_pcu2sp}
                {if $can_migrate}
                    <a class="btn btn-success upgrade" href="{$migration_pcu2sp_link|escape:'html':'UTF-8'}">
                        <img src="../modules/jprestaupgrade/views/img/migration-pcu2sp.png" width="107" height="32"/>
                        {l s='Launch migration to the Speed Pack module' mod='jprestaupgrade'}
                    </a>
                {else}
                    <div class="alert alert-danger">
                        {l s='You cannot migrate to Speed Pack because your Prestashop version is too old, please contact the support.' mod='jprestaupgrade'}
                    </div>
                {/if}
            {elseif !isset($module.license.latest)}
                <button class="btn btn-success" disabled>
                    <i class="icon-check"></i>
                    {l s='Up-to-date' mod='jprestaupgrade'}
                </button>
            {/if}
            {if isset($module.license.link_renew)}
                <a class="btn btn-info" href="{$module.license.link_renew|escape:'html':'UTF-8'}" target="_blank"><i class="icon-external-link-sign"></i>
                    {if isset($module.license.latest)}
                        {l s='Extend your license to get' mod='jprestaupgrade'} v{$module.license.latest.version|escape:'html':'UTF-8'}
                    {else}
                        {l s='Extend your license to stay up-to-date and receive support.' mod='jprestaupgrade'}
                    {/if}
                </a>
            {/if}
            {if $can_migrate && isset($module.license.can_migrate_pcu2sp_link)}
                <div class="migrate_sp">
                    <img src="../modules/jprestaupgrade/views/img/migration-pcu2sp.png" width="107" height="32"/>
                    <b>{l s='Migrate this license to our all-in-one Speed Pack module for the price of a license extension! Get more features to speed up your shop at a great price!' mod='jprestaupgrade'}</b>
                    <a class="" href="{$module.license.can_migrate_pcu2sp_link|escape:'html':'UTF-8'}" target="_blank">{l s='Click here for more infos!' mod='jprestaupgrade'}&nbsp;<i class="icon-external-link-sign"></i></a>
                </div>
            {/if}
        {else}
            <a class="btn btn-info" href="https://jpresta.com/{$lang_iso|escape:'html':'UTF-8'}/my-licenses" target="_blank"><i class="icon-external-link-sign"></i> {l s='Find your license in your JPresta account' mod='jprestaupgrade'}</a>
        {/if}
    </td>
</tr>
