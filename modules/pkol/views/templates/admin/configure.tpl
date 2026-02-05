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
{if $message}
    <div class="alert alert-info">{$message}</div>
{/if}

{if $disable_fields}
    <input type="hidden" name="disable_fields" id="disable_fields" value="{$disable_fields}" />
{/if}

{$form}
