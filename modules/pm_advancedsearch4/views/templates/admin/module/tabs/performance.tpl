{as4_startForm id="performanceOptionsTab" iframetarget=false target='_self'}

{module->displaySubTitle text="{l s='Performance' mod='pm_advancedsearch4'}"}

{as4_inputActive obj=$config key_active='moduleCache' key_db='moduleCache' label={l s='Enable cache' mod='pm_advancedsearch4'} defaultvalue=$default_config.moduleCache}
{as4_inputActive obj=$config key_active='autoReindex' key_db='autoReindex' label={l s='Activate automatic indexing when adding/editing/deleting items (may slow down your back-office)' mod='pm_advancedsearch4'} defaultvalue=$default_config.autoReindex tips={l s='If you disable this option, you will have to manually reindex the search engine or use cron URL' mod='pm_advancedsearch4'}}
{as4_inputActive obj=$config key_active='joinOptimization' key_db='joinOptimization' label={l s='Enable straight join optimization' mod='pm_advancedsearch4'} defaultvalue=$default_config.joinOptimization}
{as4_inputActive obj=$config key_active='sqlCheckCustomersGroups' key_db='sqlCheckCustomersGroups' label={l s='Enable check of customers groups and categories associations' mod='pm_advancedsearch4'} defaultvalue=$default_config.sqlCheckCustomersGroups}
{if $canShowPageBuilderOption}
    {as4_inputActive obj=$config key_active='pageBuilderCompatibilityMode' key_db='pageBuilderCompatibilityMode' label={l s='Enable compatibility mode with page builders' mod='pm_advancedsearch4'} defaultvalue=$default_config.pageBuilderCompatibilityMode tips={l s='Enable this option only if you use a Page Builder and are facing issues with unresponsive search engines on page loads' mod='pm_advancedsearch4'}}
{/if}

{module->displaySubmit text="{l s='Save' d='Admin.Actions'}" name='submitPerformanceConfiguration'}

{as4_endForm id="performanceOptionsTab" includehtmlatend=true}
