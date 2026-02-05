window.onload = function () {
    (function () {
        // will be executed after page loading

        $("#refreshBtn").click(function(){
            $('#busy').css('visibility','visible');
            $('#refreshBtn').prop('disabled', true);                
            $.ajax({
                type: 'POST',
                url: $('#refreshCommand').val()+'?id_order=' + $('#id_order').val(),
                // data: 'id_order=' + $('#id_order').val(),
                // dataType: 'json',
                timeout: 30000
            }).done(function(json) {
                location.reload(true);
                // alert('done');
              }).fail(function(json){
                alert("Błąd przy wywołaniu serwisu bankowego.");
              });                         
        });

        $("#OrderMapBtn").click(function(){
            alert($('#OrderMapCmd').val());
        }); 

        $("[name='SCB_EHP_defaultQUeries']").on('change', function(){                                    
            if(this.value == 1){
                try {
                    $("[name='SANTANDERCREDIT_QTY_QUERY']")[0].value = $('#EHP_CURRENT_QTY_QUERY').val();
                    $("[name='SANTANDERCREDIT_PRICE_QUERY']")[0].value = $('#EHP_CURRENT_PRICE_QUERY').val();                        
                } catch (error) {
                    
                }
            } else {
                try {
                    $("[name='SANTANDERCREDIT_QTY_QUERY']")[0].value = $('#EHP_DEF_QTY_QUERY').val();
                    $("[name='SANTANDERCREDIT_PRICE_QUERY']")[0].value = $('#EHP_DEF_PRICE_QUERY').val();                        
                } catch (error) {
                    
                }
            }            
        });

        $("#SCB_EHP_defaultUrls").on('change', function(){
            if(this.value == 1){
                try {
                    $("[name='SANTANDERCREDIT_URL_SYMULATOR']")[0].value = $('#EHP_CURRENT_URL_SYMULATOR').val();
                    $("[name='SANTANDERCREDIT_URL_WNIOSEK']")[0].value = $('#EHP_CURRENT_URL_WNIOSEK').val();                        
                    $("[name='SANTANDERCREDIT_SVC_LOCATION']")[0].value = $('#EHP_CURRENT_SVC_LOCATION').val(); 
                } catch (error) {
                    
                }
            } else {
                try {
                    $("[name='SANTANDERCREDIT_URL_SYMULATOR']")[0].value = $('#EHP_DEF_URL_SYMULATOR').val();
                    $("[name='SANTANDERCREDIT_URL_WNIOSEK']")[0].value = $('#EHP_DEF_URL_WNIOSEK').val();     
                    $("[name='SANTANDERCREDIT_SVC_LOCATION']")[0].value = $('#EHP_DEF_SVC_LOCATION').val();                    
                } catch (error) {
                    
                }
            }            
        });

        $('#configuration_form').on('submit', function(){

            scbQueryEncode($('#EHP_CURRENT_QTY_QUERY'));
            scbQueryEncode($('#EHP_CURRENT_PRICE_QUERY'));
            scbQueryEncode($('#EHP_DEF_QTY_QUERY'));
            scbQueryEncode($('#EHP_DEF_PRICE_QUERY'));
            scbQueryEncode($('#SANTANDERCREDIT_QTY_QUERY'));
            scbQueryEncode($('#SANTANDERCREDIT_PRICE_QUERY'));
           
        });

        function scbQueryEncode(jqObj){
            let tmpVal = jqObj.val();            
            tmpVal = tmpVal.replaceAll(')', '_nawiasP_',tmpVal).replaceAll('(','_nawiasL_',tmpVal);
            // tmpVal = window.btoa(tmpVal);            
            jqObj.val(tmpVal);
        };

        $('#pshPassButton').on('click',function pshPassChange(){
            let currentPass = $('#SANTANDERCREDIT_PSH_PASS').val();
            let modal = document.getElementById('pshPassModal');
            $('#pshPass1').val(currentPass);
            $('#pshPass2').val(currentPass);
            modal.style.display = "block";
        });
        
        $('#pshPassSaveBtn').on('click',function pshPassSave(){
            let modal = document.getElementById('pshPassModal');
            let pass1 = $('#pshPass1').val();
            let pass2 = $('#pshPass2').val();
            if(pass1 == pass2){                
                modal.style.display = "none";
                $('#SANTANDERCREDIT_PSH_PASS').val(pass1);
                $('#configuration_form_submit_btn').click();
            } else {
                alert('Różne hasła w obu polach!');
            }
        });

        $('#pshTestButton').on('click',function pshTestConn(){
            let modal = document.getElementById('pshTestModal');
            let msgText = "Test 1 - Łączność z serwisem: ";
            $('#pshTestResult').text('Trwa weryfikacja połączenia...');
            modal.style.display = "block";
            $.ajax({
                type: 'POST',
                url: $('#pshIsActiveCommand').val(),
                timeout: 30000
            }).done(function(json) {
                msgText = msgText + json;
                msgText = msgText + ', Test 2 - Logowanie do usługi: ';
                $.ajax({
                    type: 'POST',
                    url: $('#pshLoginChckCommand').val()+'?id_order=nosuchorder',
                    timeout: 30000
                }).done(function(json) {
                    msgText = msgText + ' ' + json;
                    $('#pshTestResult').text(msgText);
                  }).fail(function(json){                    
                    msgText = msgText + " Błąd przy wywołaniu serwisu bankowego.";
                    $('#pshTestResult').text(msgText);
                  });                             
              }).fail(function(json){                
                msgText = msgText + " Błąd przy wywołaniu serwisu bankowego.";
                $('#pshTestResult').text(msgText);
              });                                     
        });

    })();
};
