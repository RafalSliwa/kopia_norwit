<div class="panel card" style="padding-left:10px;padding-right:10px;">
    <div class="row">        
                        <div class="col-xxl-4 text-xxl-left">
                            <input type="text" name="refreshCommand" id="refreshCommand" style="display:none;" value="{$refreshCommand}">
                            <input type="text" name="id_order" id="id_order" style="display:none;" value="{$id_order}">
                            <div style="padding-top:10px;padding-bottom:10px;"><button class="btn btn-primary" id="refreshBtn">Pobierz informacje z Banku</button></div>
                            <img src="{$module_dir|escape:'html':'UTF-8'}views/img/busy.gif" id="busy" style="visibility:hidden;"/>
                        </div>
                        <div class="col-xxl-4 text-xxl-left">&nbsp;</div>
                        <div class="col-xxl-4 text-xxl-left">
                        <div style="padding-top:10px;padding-bottom:10px;"><a href="{$instrukcja}">Instrukcja obsługi eHP</a></div>
                        </div>
    </div>
    <div class="row">

                    <div class="col-md-4 left-column">
                        <p class="mb-1"><strong>POSApplicationNumber:</strong></p>
                        <p class="mb-1">{$fullAppInfo->ShopApplicationNumber}</p>
                        <p class="mb-1"><strong>Numer wniosku:</strong></p>
                        <p class="mb-1">{$fullAppInfo->ApplicationNumber}</p>            
                        <p class="mb-1"><strong>Status wniosku:</strong></p>
                        <p class="mb-1">{$fullAppInfo->CreditState}</p>
                        <p class="mb-1"><strong>Data statusu:</strong></p>
                        <p class="mb-1">{$fullAppInfo->ChangeDate}</p>                         
                    </div>
                    <div class="col-md-4 right-column">
                            <p class="mb-1"><strong>Numer umowy:</strong></p>
                            <p class="mb-1">{$fullAppInfo->AgreementNumber}</p>
                            <p class="mb-1"><strong>Data umowy:</strong></p>
                            <p class="mb-1">{$fullAppInfo->AgreementDate}</p>                
                    </div>
                    <div class="col-md-4 right-column">
                        <p class="mb-1"><strong>Wplata własna:</strong></p>
                        <p class="mb-1">{$fullAppInfo->Downpayment}</p>
                        <p class="mb-1"><strong>Cena całkowita:</strong></p>
                        <p class="mb-1">{$fullAppInfo->TotalPrice}</p>            
                        <p class="mb-1"><strong>Numer Sklepu:</strong></p>
                        <p class="mb-1">{$fullAppInfo->ShopNumber}</p>            
                    </div>

    </div>
</div>