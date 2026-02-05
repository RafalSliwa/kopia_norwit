<script type="text/javascript">
  
  {$quantityFn nofilter}

  {$priceFn nofilter}
  
</script>

<!-- The Modal -->
<div id="simInputModal" class="ehp-modal">

  <!-- Modal content -->
  <div class="ehp-modal-content">
  	<header style="vertical-align: top">
    	<div style="padding-bottom: 10px" class="ehp-close" onclick="document.getElementById('simInputModal').style.display = 'none';">&times;</div>
    	<div style="padding-bottom:10px">Dane wejściowe dla symulatora rat</div>
    </header>
    <div>
    	<div style="display:inline-block">
        	<div>cena jednostkowa</div>
    		<div><input type="number" value="1234" id="ehpModalPrice"></div>
 		</div>
        <div style="display:inline-block">
        	<div>ilość jednostek</div>
    		<div><input type="number" value="1234" id="ehpModalQuantity"></div>
 		</div>
    	<div style="display:inline-block">
        	<input type="button" value="Oblicz ratę" onclick="ehpSimRequest('{$shopId}', '{$symulatorURL}', ehpCalcOrderPrice($('#ehpModalPrice').val(), $('#ehpModalQuantity').val()))">
 		</div>
        
    </div>
  </div>

</div>

<!--<div style="text-align:right;">    -->
<p id="eraty" class="buttons_bottom_block" >
    <a onClick="simulationEhp({$displaySimInputModal},'{$shopId}', '{$symulatorURL}', ehpGetPquantity(), ehpGetPprice());" title="Kupuj na eRaty Santander Consumer Banku!" align="right" style="cursor: pointer;">
        <img src="{$module_dir}views/img/obliczRate.png" alt="Oblicz ratę!"/>
    </a>
    <div style="position:absolute;visibility:hidden" id="scb_quantity">{$scb_quantity}</div>
    <div style="position:absolute;visibility:hidden" id="scb_price">{$santanderCreditProductPrice}</div>
</p>
<!--</div>-->
