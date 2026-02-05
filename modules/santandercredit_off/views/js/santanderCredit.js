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


function simulationEhp(displayModal, shopNumber, symUrl, quantity, price) {
	let scbQty, scbPrice;
    scbQty = -1;
    scbPrice = -1;    

    if ($("#scb_price") && typeof($("#scb_price")) == 'object') {
        scbPrice = $("#scb_price").text();
    }
    if ($("#scb_quantity") && typeof($("#scb_quantity")) == 'object') {
        scbQty = $("#scb_quantity").text();
    }
    
    if (scbQty > 0 && scbPrice > 0) {        
        /**
         * if quantity an price are ok then use it for simulation. Otherwise - use defaults(
         * scbQty and scbPrice) set on server side as smarty value
         */
        if(quantity >= scbQty) {        
            scbQty = quantity;
            // scbPrice = price;
        } 
        if(price > 0){
            scbPrice = price;
        }      
        eHpSimRequestPrepare(displayModal, shopNumber, symUrl, scbPrice, scbQty);
    } else {
        alert('Wrong parameters for calculate');
    }
}

function eHpSimRequestPrepare(displayModal, shopNumber, symUrl, unitPrice, orderQuantity){
    let modal = document.getElementById('simInputModal');
    /**
     * tu - dialog z edycją unitPrice i orderQuantity. Pokazywany w zależności od parametru w konfiguracji wtyczki.
     * 
     */
    if(displayModal == 1){
        $('#ehpModalPrice').val(unitPrice);
        $('#ehpModalQuantity').val(orderQuantity);
        //display modal
        modal.style.display = "block";
    } else {
        orderPrice = ehpCalcOrderPrice(unitPrice, orderQuantity);
        /*
            Jeśli w konfiguracji określono że dialog nie ma być pokazywany - wywołanie ehpSimRequest będzie robione stąd a nie z onclick
        */          
        ehpSimRequest(shopNumber, symUrl, orderPrice);
    }
}

function ehpSimRequest(shopNumber, symUrl, orderPrice){
    let modal = document.getElementById('simInputModal');
    modal.style.display = "none";
    if(!isNaN(orderPrice)){
        if (orderPrice <= 100) {
            alert("Uwaga, niewielka wartość towarów. Bank może nie udzielić tak niskiego kredytu.");
        }     
        window.open(symUrl + 'numerSklepu/' + shopNumber + '/wariantSklepu/1/typProduktu/0/wartoscTowarow/' + orderPrice);
    } else {
        alert("Pola cena i ilość muszą zawierać liczby.");
    }

}

function ehpCalcOrderPrice(unitPrice, qty){
    let op = NaN;
    unitPrice = parseFloat(unitPrice);
    qty = parseFloat(qty);
    if(!isNaN(qty)  && !isNaN(unitPrice)){
        op = unitPrice * qty;
        op = Math.round(op * 100, 2) / 100; 
    }
    return op;
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
