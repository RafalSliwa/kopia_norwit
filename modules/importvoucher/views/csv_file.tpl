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

<div class="col-lg-2 tag" style="text-align:center;">
    <a href="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/importvoucher/{$csv_name}" target="_blank" style="margin-bottom:10px; display:block; ">
        <strong>{$csv_name}</strong>
    </a>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="importfile" value="{$csv_name}">
        <input type="submit" value="{l s='import to databse' mod='importvoucher'}" name="importcsv" class="btn btn-default"/>
    </form>
    <form action="" style="margin-top:10px" method="post" enctype="multipart/form-data">
        <input type="hidden" name="fcsv" value="{$csv_name}">
        <input type="submit" value="{l s='delete' mod='importvoucher'}" name="delete_csv_file" class="btn btn-default"/>
    </form>
</div>