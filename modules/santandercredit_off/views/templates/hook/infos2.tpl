{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>
        Uwaga:
    </div>
        <p id="qvalid_msg">
            <span style="color:red; font-weight: bold;">Wykryto problem w parametrach QTY_QUERY i/lub PRICE_QUERY! 
            Zapoznaj się z dokumentacją i <a href="#" style="color:red;font-size:large">obejrzyj film instruktażowy</a></SPAN>
            <br/>
            <span style="color:red;">
                Do czasu rozwiązania problemu zalecamy przywrócenie wartości domyślnych. Można też ustawić "Potwierdzaj dane do symulacji" na TAK.
            </span>
        </p>
        <p style="text-align: center;">Konfigurację osadzania widgetu wywołującego symulator rat na stronie sklepu należy wykonywać bardzo ostrożnie. W szczególności chodzi o parametry QTY_QUERY oraz 
        PRICE_QUERY. W założeniu są to funkcje jQuery pozwalające na odczyt aktualnej wartości ceny jednostkowej oraz ilości towaru z aktualnie wyświetlanej strony
        produktu. Jeśli są skonfigurowane poprawnie - mamy gwarancję że do symulatora wysłana zostanie kwota uwzględnijąca aktualnie ustawioną przez klienta ilość towaru.
        Jeśli moduł stwierdzi że nie jest w stanie odnaleźć ceny i ilości na stronie - wyśle do symulatora kwotę wynikającą z ceny jednostkowej i minimalnej
        ilości towaru określonej dla danego produktu w parametrach sklepu. Domyślnie, parametry QTY_QUERY i PRICE_QUERY są zgodne z defaultowym templatem PrestaShop. 
        Jeśli Twój template zachowuje zgodnosć z defaultowym - nie trzeba nic zmieniać. Wskazane jest by zmian tych parametrów dokonywała osoba znająca bibliotekę jQuery. 
        Jeśli nie jesteś w stanie poprawnie skonfigurować QTY_QUERY i PRICE_QUERY, możesz 
        użyć opcji "Potwierdzaj dane do symulacji". Zostanie wtedy wyświetlony dodatkowy dialog który pozwoli Klientowi ustawić właściwą ilość towaru.
        Parametry te należy weryfikować po każdej zmianie szablonu wykorzystywanego w Sklepie. Jest to zadanie Webmastera/Administratora instancji PrestaShop.
        Zanim zaczniesz modyfikować QTY_QUERY i PRICE_QUERY zapoznaj się z <a id="docDnldBtn" href="{$scbEhpAdmDocUrl}">dokumentacją.</a>
        </p>
    </div>
</div>

<script type="text/javascript">
  
  {$quantityFn nofilter}

  {$priceFn nofilter}
  
  function testQueries(quantity, price) {
    let pmessage = document.getElementById('qvalid_msg');  
    let qq = $('#EHP_CURRENT_QTY_QUERY').val();
    let qp = $('#EHP_CURRENT_PRICE_QUERY').val();
    let isOk = 1;    
    //start z dolara
    if(!(qq.charAt(0) == '$')) {
        isOk = isOk - 1;    
    };
    if(!(qp.charAt(0) == '$')) {
        isOk = isOk - 1;    
    };
    
    //liczba nawiasow lewych == liczba nawiasow prawych
    if(!(qq.split('(').length == qq.split(')').length)) {
        isOk = isOk - 1;
    };
    if(!(qp.split('(').length == qp.split(')').length)) {
        isOk = isOk - 1;
    };
  
    //parzysta ilość apostrofów
    if((qq.split("'").length % 2) == 0) {
        isOk = isOk - 1;
    };
    if((qp.split("'").length % 2) == 0) {
        isOk = isOk - 1;
    };

    //parzysta ilość cudzysłowów
    if((qq.split('"').length % 2) == 0) {
        isOk = isOk - 1;
    };    
    if((qp.split('"').length % 2) == 0) {
        isOk = isOk - 1;
    };    

    // max liczba średników = 1 i średnik na ostatnim miejscu
    if(qq.indexOf(';') >= 0) {
        if(!(qq.indexOf(';') == qq.lastIndexOf(';'))) {
            isOk = isOk -1;
        };
    }  
    if(qp.indexOf(';') >= 0) {
        if(!(qp.indexOf(';') == qp.lastIndexOf(';'))) {
            isOk = isOk -1;
        };
    }  
    
    if(isOk == 1)  {
        pmessage.style.display = "none";
    }    
  }

  testQueries(ehpGetPquantity(), ehpGetPprice());

</script>
