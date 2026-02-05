{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
* @copyright 2010-2024 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{if isset($form_import)}
    <div class="panel">
        <div class="panel-heading"><i class="icon-wrench"></i> {l d='Modules.Seoredirect.Adminseoredirectimport' s='Import settings' mod='seoredirect'}</div>
        {$form_import nofilter}
    </div>
{/if}

{if Tools::isSubmit('submit_vouchers')}
    <div class="panel">
        <div class="panel-heading"><i class="icon-cloud"></i> {l d='Modules.Seoredirect.Adminseoredirectimport' s='Import settings' mod='seoredirect'}</div>
        <div class="alert alert-success">{l d='Modules.Seoredirect.Adminseoredirectimport' s='imported to database' mod='seoredirect'}</div>
    </div>
{/if}
<div class="col-lg-6 col-sm-12 col-xs-12">
    <div class="col-lg-12">
        <div class="panel clearfix">
            <div class="panel-heading"><i class="icon-wrench"></i>
                {l d='Modules.Seoredirect.Adminseoredirectimport' s='Import settings' mod='seoredirect'}
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-wrapper">
                    {$form_settings nofilter}
                </div>
                <div class="panel-footer">
                    <button type="submit" name="save_voucher_settings" class="button btn btn-default pull-right"/>
                    <i class="process-icon-save"></i>
                    {l d='Modules.Seoredirect.Adminseoredirectimport' s='save' mod='seoredirect'}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="col-lg-6 col-sm-12 col-xs-12">
    <div class="panel clearfix">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="panel-heading"><i class="icon-wrench"></i> {l d='Modules.Seoredirect.Adminseoredirectimport' s='Upload' mod='seoredirect'}</div>
                <div class="form-wrapper>
                    <div class="col-lg-12">
                        <input id="upload_csv" type="file" name="upload_csv" class="hide">
                        <div class="dummyfile input-group  clearfix">
                            <span class="input-group-addon"><i class="icon-file"></i></span>
                            <input id="upload_csv-name" type="text" name="filename" readonly="">
                            <span class="input-group-btn">
                                <button id="upload_csv-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                    <i class="icon-folder-open"></i> {l d='Modules.Seoredirect.Adminseoredirectimport' s='Add file' mod='seoredirect'}
                                </button>
                            </span>
                        </div>
                    </div>
                    {literal}
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#upload_csv-selectbutton').click(function (e) {
                                    $('#upload_csv').trigger('click');
                                });
                                $('#upload_csv-name').click(function (e) {
                                    $('#upload_csv').trigger('click');
                                });

                                $('#upload_csv').change(function (e) {
                                    if ($(this)[0].files !== undefined) {
                                        var files = $(this)[0].files;
                                        var name = '';
                                        $.each(files, function (index, value) {
                                            name += value.name + ', ';
                                        });
                                        $('#upload_csv-name').val(name.slice(0, -2));
                                    }
                                    else // Internet Explorer 9 Compatibility
                                    {
                                        var name = $(this).val().split(/[\\/]/);
                                            $('#upload_csv-name').val(name[name.length - 1]);
                                        }
                                    });
                            });
                        </script>
                    {/literal}
                    <div class="panel-footer">
                        <button type="submit" name="upload_csv" class="button btn btn-default pull-right"/>
                            <i class="process-icon-upload"></i>{l d='Modules.Seoredirect.Adminseoredirectimport' s='Upload File' mod='seoredirect'}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <div class="col-lg-12 col-sm-12 col-xs-12 card">
        <div class="panel clearfix">
            <div class="panel-heading"><i class="icon-wrench"></i> {l d='Modules.Seoredirect.Adminseoredirectimport' s='Uploaded CSV files' mod='seoredirect'}</div>
            <form action="" method="post">
                <fieldset class="col-lg-12">
                    {$csvfiles}
                </fieldset>
            </form>
        </div>
    </div>
</div>
</div>