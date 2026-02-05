{*
* auction Products
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
*  @author    FME Modules
*  @copyright 2018 fmemodules All right reserved
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if $data}
    {if $data['type'] == 'success'}
        <div class="conf alert alert-success">
            {l s='Settings saved successful' mod='productlabelsandstickers'}
        </div>
    {/if}

    {if $data['type'] == 'warning'}
        <div class="conf alert alert-warning">
            {l s='New settings not saved (visible items must be positive integer)' mod='productlabelsandstickers'}
        </div>
    {/if}

    {if $data['type'] == 'warning'}
        <div class="conf alert alert-warning">
            {l s='New settings not saved (visible items must be positive integer)' mod='productlabelsandstickers'}
        </div>
    {/if}

    {if $data['type'] == 'config_form'}
        <p>
            In case you are using CSS Based, please add this hook <b>{hook h='displayProductPageCss' product=$product}</b> in
            two files<br />
            <b>1:</b> file {$data['path_tpl']}{* html contents *} like shown in image: <img src="{$data['path_img']}{* html contents *}" /><br /><b>2:</b> In file: {$data['path_tpl_ii']}{* html contents *}
            <br />like shown in image: <img src="{$data['path_img_ii']}{* html contents *}"/>
        </p>
    {/if}
{/if}