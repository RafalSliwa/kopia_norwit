{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2019 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

<div class="alert alert-info">
    <strong>{l s='How to import codes?' mod='importvoucher'}</strong><br/>
    2. {l s='Now it is time to configure settings of CSV file you uploaded previously' mod='importvoucher'}<br/><br/>
    {l s='CSV file formats that use delimiter-separated values store two-dimensional arrays of data by separating the values in each row and column with specific delimiter characters.' mod='importvoucher'}<br/>
    <strong>{l s='Row delimiter' mod='importvoucher'}</strong> {l s='Any character may be used to separate the lines in CSV file, but the most common delimiters are new line charcode (\\\n) or (\\\r)' mod='importvoucher'}<br/>
    <strong>{l s='Column delimiter' mod='importvoucher'}</strong> {l s='Any character may be used to separate values, but the most common delimiters are comma (,) or semicolon (;)' mod='importvoucher'}<br/>
</div>
<form action="" method="post" class="form-horizontal">
    <div class="form-wrapper">
        <div class="form-group">
            <div id="conf_id_iv_row_delimiter">
                <label class="control-label col-lg-3 required">
                    <span>
                        {l s='Row delimiter' mod='importvoucher'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <input class="form-control" type="text" name="iv_row_delimiter" value="{Configuration::get('IV_ROW_DELIMITER')}">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div id="conf_id_iv_col_delimiter">
                <label class="control-label col-lg-3 required">
					    <span>
                            {l s='Column delimiter' mod='importvoucher'}
                        </span>
                </label>
                <div class="col-lg-9">
                    <input class="form-control" type="text" name="iv_col_delimiter" value="{Configuration::get('IV_COL_DELIMITER')}">
                </div>
            </div>
        </div>
    </div>
    <div class="separation"></div>
    <div class="clearfix"></div>
    <div class="panel-footer clearfix">
        <button class="btn btn-default pull-right" name="delimiters_submit" type="submit">
            <i class="process-icon-save"></i>
            {l s='Save!' mod='importvoucher'}
        </button>
    </div>
    <div class="separation"></div>
    <div class="clearfix"></div>
</form>
