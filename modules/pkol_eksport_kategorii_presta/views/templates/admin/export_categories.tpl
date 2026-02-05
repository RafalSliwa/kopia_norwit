<div class="panel">
    <h3>{$mod->displayName|escape:'html':'UTF-8'}</h3>
    {if isset($export_link)}
        <a href="{$export_link}" class="btn btn-primary">{l s='Eksportuj kategorie do CSV' mod=$mod->name}</a>
    {else}
        <p>Brak linku eksportu</p>
    {/if}
</div>
