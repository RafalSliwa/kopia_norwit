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
 * Ten plik zawiera szablony Smarty dla modułu PKO Leasing Integration.
 *}
{if $data.status eq false }
    <div class="pko_container">
        <img src="{{$data.response}}"/>
    </div>
    <input type="hidden" id="endpointurl" name="endpointurl" value="{{$link->getModuleLink('pkol', 'ajax', array())}}"/>
    <form id="pkol_form" action="{{$form_data}}" id="leasing" method="POST">
    </form>
{/if}
<style>
    .pko_container {
        margin: 15px 0;
    }

    .modal-body .pkol_widget, .modal-body .pko_container, .modal-footer .pkol_widget, .modal-footer .pko_container {
        display: none !important;
    }

</style>
