<section id="ehp" class="box">
    <h2>Płatność w systemie eRaty - status wniosku kredytowego</h2>
    <div id="applicationNextTry" style="margin:auto;width:100%;text-align:center;">
        <p>{$ehpMessage}</p>
        <p><a href="{$userDoc}">Finansowanie zakupu kredytem ratalnym - instrukcja</a></p>
    </div>
    
    <div class="row" style="margin-bottom:10px;">
        <div class="col-md-6 left-column" style="padding-bottom:2px;">
            <input type="text" name="refreshCommand" id="refreshCommand" style="display:none;" value="{$refreshCommand}">
            <input type="text" name="ehp_id_order" id="ehp_id_order" style="display:none;" value="{$id_order}">
            <button id="refreshBtn" class="btn btn-primary" onclick="ehpStateRefresh();">Odśwież informację z Banku</button>
            <img src="{$module_dir|escape:'html':'UTF-8'}views/img/busy.gif" id="busy" style="visibility:hidden;"/>
        </div>
        <div class="col-md-6 right-column" style="text-align:right;padding-bottom:2px;">
            <form name="ehpCreditForm" id="ehpCreditForm" action="{$applicationURL}" method="post">

                {assign var='nr' value='0'}
                {foreach from=$products item=product}
                    {$nr = $nr + 1}
                    <input name="idTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['product_id']}" />
                    <input name="nazwaTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['product_name']}" />
                    <input name="wartoscTowaru{$nr}" readonly="readonly" type="hidden" value="{round($product['unit_price_tax_incl'], 2)}" />
                    <input name="liczbaSztukTowaru{$nr}" readonly="readonly" type="hidden" value="{$product['product_quantity']}" />
                    <input name="jednostkaTowaru{$nr}" readonly="readonly" type="hidden" value="szt" />        
                {/foreach}
            
                {if $shipping gt 0}
                    {$nr = $nr + 1}
                    <input type="hidden" name="idTowaru{$nr}" readonly="readonly" value="KosztPrzesylki" />
                    <input type="hidden" name="nazwaTowaru{$nr}" readonly="readonly" value="Koszt przesyłki" />
                    <input type="hidden" name="wartoscTowaru{$nr}" readonly="readonly" value="{$shipping}" />
                    <input type="hidden" name="liczbaSztukTowaru{$nr}" readonly="readonly" value="1" />
                    <input type="hidden" name="jednostkaTowaru{$nr}" readonly="readonly" value="szt" />'
                {/if}
            
                <input type="hidden" name="liczbaSztukTowarow" value="{$nr}" />
            
                <input type="hidden"  name="typProduktu" value="0" />
                <input type="hidden"  name="wariantSklepu" value="1" />
                <input type="hidden"  name="nrZamowieniaSklep" value="{$orderId}" id="orderId"/>
                <input type="hidden"  name="wartoscTowarow" value="{$totalOrder}" />
                <input type="hidden"  name="pesel" value="" />
                <input type="hidden"  name="imie" value="{$imie}" />
                <input type="hidden"  name="nazwisko" value="{$nazwisko}" />
                <input type="hidden"  name="email" value="{$email}" />
                <input type="hidden"  name="telKontakt" value="{$telKontakt}" />
                <input type="hidden"  name="ulica" value="{$ulica}" />
                <input type="hidden"  name="nrDomu" value="{$ulica2}" />
                <input type="hidden"  name="nrMieszkania" value="" />
                <input type="hidden"  name="miasto" value="{$miasto}" />
                <input type="hidden"  name="kodPocz" value="{$kodPocz}" />
                <input type="hidden"  name="char" value="UTF" />
                <input type="hidden"  name="numerSklepu" value="{$shopId}" />
                <input type="hidden"  name="shopName" value="{$shopName}" />
                <input type="hidden"  name="shopHttp" value="{$shopHttp}" />
                <input type="hidden"  name="wniosekZapisany" value="{$returnTrue}" />
                <input type="hidden"  name="wniosekAnulowany" value="{$returnFalse}" />	
                <input type="hidden"  name="shopMailAdress" value="{$shopMailAdress}" />
                <input type="hidden"  name="shopPhone" value="{$shopPhone}" />
                
                <button type="submit" class="btn btn-primary" {$sendApplicationDisabled}>{$backToApp_sendApp}</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 left-column">
            <p class="mb-1"><strong>POSApplicationNumber:</strong></p>
            <p class="mb-1">{$application->ShopApplicationNumber}</p>
            <p class="mb-1"><strong>Numer wniosku:</strong></p>
            <p class="mb-1">{$application->ApplicationNumber}</p>            
            <p class="mb-1"><strong>Status wniosku:</strong></p>
            <p class="mb-1">{$application->CreditState}</p>
            <p class="mb-1"><strong>Data statusu:</strong></p>
            <p class="mb-1">{$application->ChangeDate}</p>                         
        </div>
        <div class="col-md-4 right-column">
                <p class="mb-1"><strong>Numer umowy:</strong></p>
                <p class="mb-1">{$application->AgreementNumber}</p>
                <p class="mb-1"><strong>Data umowy:</strong></p>
                <p class="mb-1">{$application->AgreementDate}</p>                
        </div>
        <div class="col-md-4 right-column">
            <p class="mb-1"><strong>Wplata własna:</strong></p>
            <p class="mb-1">{$application->Downpayment}</p>
            <p class="mb-1"><strong>Cena całokwita:</strong></p>
            <p class="mb-1">{$application->TotalPrice}</p>            
            <p class="mb-1"><strong>Numer Sklepu:</strong></p>
            <p class="mb-1">{$application->ShopNumber}</p>            
        </div>
    </div>
</section>