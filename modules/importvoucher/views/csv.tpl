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

<p class="alert alert-info">
    <strong>{l s='How to import codes?' mod='importvoucher'}</strong><br/>
    1. {l s='Firstly upload a file from your hard disc drive that cotains list of codes you want to import. Then go to step 2 where you can specify settings of the file' mod='importvoucher'}
</p>
<div class="panel clearfix">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s='Select CSV file' mod='importvoucher'}
            </label>
            <div class="col-lg-9">
                <div class="form-group">
                    <div class="col-sm-6">
                        <input id="upload_csv" type="file" name="upload_csv" class="hide">
                        <div class="dummyfile input-group  clearfix">
                            <span class="input-group-addon"><i class="icon-file"></i></span>
                            <input id="upload_csv-name" type="text" name="filename" readonly="">
                            <span class="input-group-btn">
                        <button id="upload_csv-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                            <i class="icon-folder-open"></i> {l s='Add file' mod='beforeafter'}
                        </button>
                    </span>
                        </div>
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
            </div>
            <div class="separation"></div>
        </div>
        <div class="separation"></div>
        <div class="clearfix"></div>
        <div class="panel-footer clearfix">
            <button class="btn btn-default pull-right" name="upload_csv" type="submit">
                <i class="process-icon-save"></i>
                {l s='Upload!' mod='importvoucher'}
            </button>
        </div>
    </form>
</div>

<div class="panel clearfix">
    <h3>{l s='Uploaded files' mod='importvoucher'}</h3>
    {$csvfiles}
</div>
