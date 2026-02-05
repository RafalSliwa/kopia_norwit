{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<div class="form-horizontal well">
    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Name' mod='roja45quotationspro'}
        </label>
        <div class="col-lg-9">
            <input class="form-control" type="text" name="discount_name" id="discount_name" value="" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Type' mod='roja45quotationspro'}
        </label>
        <div class="col-lg-9">
            <select class="form-control" name="discount_type" id="discount_type">
                <option value="1">{l s='Percent' mod='roja45quotationspro'}</option>
                <option value="2">{l s='Amount' mod='roja45quotationspro'}</option>
            </select>
        </div>
    </div>
    <div id="discount_value_field" class="form-group">
        <label class="control-label col-lg-3">
            {l s='Discount' mod='roja45quotationspro'}
        </label>
        <div class="col-lg-9">
            <div class="input-group">
                <div class="input-group-addon">
                    <span id="discount_currency_sign" style="display: none;">{$currency->sign|escape:"html":"UTF-8"}</span>
                    <span id="discount_percent_symbol">%</span>
                </div>
                <input id="discount_value" class="form-control" type="text" name="discount_value"/>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Type' mod='roja45quotationspro'}
        </label>
        <div class="col-lg-9">
            <select class="form-control" name="discount_tax" id="discount_tax">
                <option value="1">{l s='Tax Included' mod='roja45quotationspro'}</option>
                <option value="2">{l s='Tax Excluded' mod='roja45quotationspro'}</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9 col-lg-offset-3">
            <button class="btn btn-secondary" type="button" id="cancel_add_voucher">
                <i class="icon-remove text-danger"></i>
                {l s='Cancel' mod='roja45quotationspro'}
            </button>
            <button class="btn btn-secondary submitNewVoucher" type="submit" name="submitNewVoucher">
                <i class="icon-ok text-success"></i>
                {l s='Add' mod='roja45quotationspro'}
            </button>
        </div>
    </div>
</div>
