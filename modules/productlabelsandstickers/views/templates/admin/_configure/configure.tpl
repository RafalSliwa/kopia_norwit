{*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   productlabelsandstickers
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="fmmstickergetcontent">
  <div class="panel">
    <ul class="nav nav-tabs" id="Tabmirakl" role="tablist">
      <li class="nav-item active">
        <a class="nav-link" id="cf_spain" data-toggle="tab" href="#cfspain" role="tab" aria-controls="cfspain" aria-selected="true">{l s='Configurations' mod='productlabelsandstickers'}</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="cf_france" data-toggle="tab" href="#cffrance" role="tab" aria-controls="cffrance" aria-selected="true">{l s='Carrefour Francia' mod='productlabelsandstickers'}</a>
      </li>
    </ul>
    <div class="tab-content" id="TabmiraklContent">
      <div class="tab-pane fade active in" id="cfspain" role="tabpanel" aria-labelledby="cfspain-tab">
        {$fmm_stickere_configuration}{* html content*}
      </div>
      <div class="tab-pane fade" id="cffrance" role="tabpanel" aria-labelledby="cffrance-tab">
        
        <h2>{l s='Text/Image Sticker' mod='productlabelsandstickers'}</h2>
      
      </div>
    </div>
  </div>
</div>

<style type="text/css">
  .fmmstickergetcontent {
    background-color: white;
  }

  .fmmstickergetcontent .tab-content {
    margin-top: 1rem;
  }

  .fmmstickergetcontent .nav-link {
    font-weight: 500 !important;
  }
</style>