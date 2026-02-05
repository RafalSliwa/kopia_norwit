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
    <div class="row">
        <div class="col-md-4 left-column"><img src="../modules/santandercredit/views/img/logo_254x83.png" style="float:left; margin-right:15px;"></div>
        <div class="col-md-8 right-column" style="text-align:right;">

                <a id="docDnldBtn" href="{$scbEhpDocUrl}">
                    <button class="btn ">Dokumentacja dla Klienta</button>
                </a>
                <a id="docDnldBtn" href="{$scbEhpSlsDocUrl}">
                    <button class="btn ">Dokumentacja dla Sprzedawcy</button>
                </a>
                <a id="docDnldBtn" href="{$scbEhpAdmDocUrl}">
                    <button class="btn ">Dokumentacja dla Administratora</button>
                </a>
        </div>
    </div>
    <form>
        <input type="hidden" name="EHP_DEF_URL_SYMULATOR" id="EHP_DEF_URL_SYMULATOR" value="{$EHP_DEF_URL_SYMULATOR}" />
        <input type="hidden" name="EHP_DEF_URL_WNIOSEK" id="EHP_DEF_URL_WNIOSEK" value="{$EHP_DEF_URL_WNIOSEK}" />
        <input type="hidden" name="EHP_DEF_SVC_LOCATION" id="EHP_DEF_SVC_LOCATION" value="{$EHP_DEF_SVC_LOCATION}" />        
        <input type="hidden" name="EHP_DEF_QTY_QUERY" id="EHP_DEF_QTY_QUERY" value="{$EHP_DEF_QTY_QUERY}" />
        <input type="hidden" name="EHP_DEF_PRICE_QUERY" id="EHP_DEF_PRICE_QUERY" value="{$EHP_DEF_PRICE_QUERY}" />
        

        <input type="hidden" name="EHP_CURRENT_URL_SYMULATOR" id="EHP_CURRENT_URL_SYMULATOR" value="{$EHP_CURRENT_URL_SYMULATOR}" />
        <input type="hidden" name="EHP_CURRENT_URL_WNIOSEK" id="EHP_CURRENT_URL_WNIOSEK" value="{$EHP_CURRENT_URL_WNIOSEK}" />
        <input type="hidden" name="EHP_CURRENT_SVC_LOCATION" id="EHP_CURRENT_SVC_LOCATION" value="{$EHP_CURRENT_SVC_LOCATION}" />
        <input type="hidden" name="EHP_CURRENT_QTY_QUERY" id="EHP_CURRENT_QTY_QUERY" value="{$EHP_CURRENT_QTY_QUERY}" />
        <input type="hidden" name="EHP_CURRENT_PRICE_QUERY" id="EHP_CURRENT_PRICE_QUERY" value="{$EHP_CURRENT_PRICE_QUERY}" />                
    </form>
</div>

<!-- The Modal -->
<div id="pshPassModal" class="ehp-modal">

  <!-- Modal content -->
  <div class="ehp-modal-content">
  	<header style="vertical-align: top">
    	<div style="padding-bottom: 10px" class="ehp-close" onclick="document.getElementById('pshPassModal').style.display = 'none';">&times;</div>
    	<div style="padding-bottom:10px">Zmiana hasła do usługi ProposalServiceHybrid</div>
    </header>
    <div>
    	<div style="display:inline-block">
        	<div>Hasło</div>
    		<div><input type="password" value="" id="pshPass1"></div>
 		</div>
        <div style="display:inline-block">
        	<div>Powtórz Hasło</div>
    		<div><input type="password" value="" id="pshPass2"></div>
 		</div>
         <div style="display:inline-block">
             <input type="button" class="btn" value="Anuluj" onclick="document.getElementById('pshPassModal').style.display = 'none';">
        </div>        
    	<div style="display:inline-block">
        	<input type="button" class="btn" value="Zapisz" id='pshPassSaveBtn'>
 		</div>        
    </div>
  </div>

</div>

<!-- The Modal for PSH test-->
<div id="pshTestModal" class="ehp-modal">

  <!-- Modal content -->
  <div class="ehp-modal-content">
  	<header style="vertical-align: top">
    	<div style="padding-bottom: 10px" class="ehp-close" onclick="document.getElementById('pshTestModal').style.display = 'none';">&times;</div>
    	<div style="padding-bottom:10px">Test połączenia z usługą ProposalServiceHybrid</div>
    </header>
    <div>
        <p id="pshTestResult">Trwa weryfikacja połączenia...</p>
        <div style="display:inline-block">
             <input type="button" class="btn" value="Zamknij" onclick="document.getElementById('pshTestModal').style.display = 'none';">
        </div>        
    </div>
    <input type="hidden" id="pshLoginChckCommand" value="{$pshLoginChckCommand}" />
    <input type="hidden" id="pshIsActiveCommand" value="{$pshIsActiveCommand}" />
  </div>

</div>
