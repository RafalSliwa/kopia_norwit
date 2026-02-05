{as4_startForm id='searchForm' obj=$searchEngine}
{module->displayTitle text="{l s='Duplicate search engine “%s” (ID: %d)' mod='pm_advancedsearch4' sprintf=[$searchEngine->internal_name, $searchEngine->id]}"}
{module->showWarning text="{l s='Be aware that if the catalog is not shared between the current and target shops, you will have to adapt the search engine settings and criterion groups.' mod='pm_advancedsearch4'}"}
{as4_select options=$shopsList label={l s='Target shop' mod='pm_advancedsearch4'} key='id_shop_destination' size='550px'}
{module->displaySubmit text="{l s='Duplicate' d='Admin.Actions'}" name='submitSearchDuplicate'}
{as4_endForm id='searchForm'}
