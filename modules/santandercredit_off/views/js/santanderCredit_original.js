function jakKupicEhp() {
    window.open('https://www.santanderconsumer.pl/raty-jak-kupic', 'jakKupic', 'width=710,height=500,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no');
    return false;
}

function obliczRateEhp(nrSklepu, symUrl, qtySelector, basketSelector) {
	let koszyk, ilo, qty, basket, scbQty, scbPrice;
    scbQty = -1;
    scbPrice = -1;

    if ($("#scb_price") && typeof($("#scb_price")) == 'object') {
        scbPrice = $("#scb_price").text();
    }
    if ($("#scb_quantity") && typeof($("#scb_quantity")) == 'object') {
        scbQty = $("#scb_quantity").text();
    }
    
    if (scbQty > 0 && scbPrice > 0) {
        qty = $(qtySelector);
        basket = $(basketSelector);
        if(qty.length == 1 && basket.length == 1) {        
            scbQty = qty.val();
            scbPrice = basket.attr('content');
        }        
        scbPrice = scbPrice * scbQty;
        scbPrice = Math.round(scbPrice * 100, 2) / 100; //some stupid code just to solve strange js numeric results problem        
        if (scbPrice > 100) {
            window.open(symUrl + 'numerSklepu/' + nrSklepu + '/wariantSklepu/1/typProduktu/0/wartoscTowarow/' + scbPrice);            
        } else {
            alert("Kredytujemy zakupy w cenie powyżej 100zł");
        }
    } else {
        alert('Wrong parameters for calculate');
    }
}

function santanderCreditValidateForm() {
    if ($('#santanderAgreement').is(':checked')) {        
        $('#scbSubmitBtn').removeAttr('disabled');
    } else {
        $('#scbSubmitBtn').attr('disabled','disabled');
    }
}

function ehpStateRefresh() {
    $('#busy').css('visibility','visible');
    $('#refreshBtn').prop('disabled', true);                
    $.ajax({
        type: 'POST',
        url: $('#refreshCommand').val()+'?id_order=' + $('#ehp_id_order').val(),
        // data: 'id_order=' + $('#id_order').val(),
        // dataType: 'json',
        timeout: 30000
    }).done(function(json) {
        location.reload(true);
        // alert('done');
      }).fail(function(json){
        alert("Błąd przy wywołaniu serwisu bankowego.");
      });                         
};
