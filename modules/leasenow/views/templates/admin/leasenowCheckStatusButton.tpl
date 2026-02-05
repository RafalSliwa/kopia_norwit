{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    ING Lease Now
 *  @copyright 2022-now ING Lease Now
 *  @license   GNU General Public License
 */
*}

<style>
    .leasenow_check-leasing {
        text-align: center;
    }

    .leasenow_loading_gif-center {
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
        display: none;
    }
</style>

{if isset($ps_version) && $ps_version >= 1770}
    {capture assign=checkleasing_fieldset}

        <div class="well">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-header-title">
                        {l s='Check leasing status' mod='leasenow'}
                    </h3>
                </div>

                <div class="card-body">
                    <div class="leasenow_check-leasing">
                        <img class="leasenow_loading_gif-center" src="{$leasenow_loading_gif|escape:'htmlall':'UTF-8'}" alt="Loading..."/>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-6" id="leasenow_button_container">
                                <div class="text-right">
                                    <button class="btn btn-primary" id="button-check-leasing">
                                        {l s='Check' mod='leasenow'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/capture}
    <script>
        $(document).ready(function () {

            $("{$checkleasing_fieldset|escape:'javascript':'UTF-8'}").insertAfter($('#orderProductsPanel').first());

            var $leasenow_button = $('#button-check-leasing'),
                $leasenow_loading_gif = $('.leasenow_loading_gif-center'),
                $leasenow_check_leasing_div = $('.leasenow_check-leasing'),
                $leasenow_leasing_status = $('.leasenow_leasing_status');

            $leasenow_button.on('click', function () {

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{$url|escape:'htmlall':'UTF-8'}",
                    beforeSend: function () {
                        $leasenow_loading_gif.show();
                        $leasenow_button.hide();
                    },
                    data: {
                        reservationId: "{$reservationId|escape:'htmlall':'UTF-8'}",
                        token: "{$token|escape:'htmlall':'UTF-8'}"
                    }
                })
                    .done(function (data, textStatus, jqXHR) {


                        if (!data.success) {
                            leasenow_insert_error_status();
                            return;
                        }

                        $leasenow_check_leasing_div.text(data.status);
                    })
                    .fail(function () {
                        leasenow_insert_error_status();
                    })
                    .always(function () {
                        $leasenow_leasing_status.toggle();
                        $leasenow_loading_gif.toggle()
                    })

            });

            function leasenow_insert_error_status() {
                $leasenow_check_leasing_div.text('{l s='The status could not be retrieved. Please try again later' mod='leasenow'}')
            }
        });
    </script>
{else}
        {capture assign=checkleasing_fieldset}
        <div class="well">
                <h3>{l s='Check leasing' mod='leasenow'}</h3>
                <div class="row">
                    <div class="leasenow_check-leasing">
                        <img class="leasenow_loading_gif-center" src="{$leasenow_loading_gif|escape:'htmlall':'UTF-8'}" alt="Loading..."/>
                    </div>
                        <div class="col-lg-2">
                            <button class="btn btn-primary" id="button-check-leasing">
                                {l s='Check' mod='leasenow'}
                            </button>
                        </div>
                    </form>
                </div>
        </div>
    {/capture}
    <script>
        $(document).ready(function () {
            $("{$checkleasing_fieldset|escape:'javascript':'UTF-8'}").insertAfter($('.panel-heading').first());

            var $leasenow_button = $('#button-check-leasing'),
                $leasenow_loading_gif = $('.leasenow_loading_gif-center'),
                $leasenow_check_leasing_div = $('.leasenow_check-leasing'),
                $leasenow_leasing_status = $('.leasenow_leasing_status');

            $leasenow_button.on('click', function () {

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{$url|escape:'htmlall':'UTF-8'}",
                    beforeSend: function () {
                        $leasenow_loading_gif.show();
                        $leasenow_button.hide();
                    },
                    data: {
                        reservationId: "{$reservationId|escape:'htmlall':'UTF-8'}",
                        token: "{$token|escape:'htmlall':'UTF-8'}"
                    }
                })
                    .done(function (data, textStatus, jqXHR) {


                        if (!data.success) {
                            leasenow_insert_error_status();
                            return;
                        }

                        $leasenow_check_leasing_div.text(data.status);
                    })
                    .fail(function () {
                        leasenow_insert_error_status();
                    })
                    .always(function () {
                        $leasenow_leasing_status.toggle();
                        $leasenow_loading_gif.toggle()
                    })

            });

            function leasenow_insert_error_status() {
                $leasenow_check_leasing_div.text('{l s='The status could not be retrieved. Please try again later' mod='leasenow'}')
            }
        });
    </script>
{/if}
