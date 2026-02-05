{if isset($module.license)}
    {if isset($module.license.download)}
        {if $module.license.download.can_upgrade}
            <li style="list-style: none;margin: 0.5rem 0">
                {if $module.type === 'module'}
                    <img src="{$base_url|escape:'html':'UTF-8'}modules/{$module.name|escape:'html':'UTF-8'}/logo.png" width="20" height="20"
                         alt="{$module.displayName|escape:'html':'UTF-8'}" style="margin: 3px">
                {else}
                    <img src="{$base_url|escape:'html':'UTF-8'}themes/{$module.name|escape:'html':'UTF-8'}/assets/img/logo.png" width="20"
                         height="20" alt="{$module.displayName|escape:'html':'UTF-8'}" style="margin: 3px">
                {/if}
                <b>{$module.displayName|escape:'html':'UTF-8'}</b>
                v{$module.version|escape:'html':'UTF-8'}
                =>
                <b>v{$module.license.download.version|escape:'html':'UTF-8'}</b>
            </li>
        {/if}
    {/if}
{/if}
