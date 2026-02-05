{if !$resultTotalProducts}
    $("input[name=submitSeoSearchForm]").hide();
    $("#errorCombinationSeoSearchForm").show();
    $("#nbProductsCombinationSeoSearchForm").html('<p class="alert alert-danger"><b>{l s='no result found' mod='pm_advancedsearch4'}</b></p>');
{else}
    $("input[name=submitSeoSearchForm]").show();
    $("#errorCombinationSeoSearchForm").hide();
    $("#nbProductsCombinationSeoSearchForm").html('<p class="alert alert-info"><b>{l s='%d result(s) found(s)' mod='pm_advancedsearch4' sprintf=[$resultTotalProducts]}</b></p>');
{/if}
