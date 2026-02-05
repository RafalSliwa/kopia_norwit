{extends file="helpers/options/options.tpl"}

{block name="input" append}
    {if $field['type'] == 'html'}
        {if isset($field.html_content)}
            {$field.html_content}
        {else}
            {$field.name}
        {/if}
    {/if}
{/block}