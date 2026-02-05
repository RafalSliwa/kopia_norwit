{*
 * PKO Leasing Integration Module
 *
 * @package    PrestaShop Modules
 * @subpackage PKO Leasing Integration
 * @author     PKO Leasing
 * @license    MIT License
 * @copyright  2024 PKO Leasing
 * @link       https://www.pkoleasing.pl/
 *
 *}
{if $status}
    <div class="pkol_widget">
        <div data-baseurl="" data-pid="{$id}" id="lease_click" style="display:inline-block;position:relative;">
            {if $textVisible}
                <span class="pko_rate" style="{$styles|escape:'html':'UTF-8'}">Rata od<br> {$price|number_format:2:'.':''} zł</span>
            {/if}
            <img class="{$class|escape:'html':'UTF-8'}" src="{$imgPath|escape:'html':'UTF-8'}" alt="PKO Leasing Widget" />
        </div>
    </div>
    <style>
        .modal-body .pkol_widget,
        .modal-body .pko_container,
        .modal-footer .pkol_widget,
        .modal-footer .pko_container {
            display: none !important;
        }
        #lease_click:hover {
            cursor: pointer;
        }
    </style>
{else}
    <img src="{$imgPath|escape:'html':'UTF-8'}" alt="PKO Leasing Disabled" />
{/if}
