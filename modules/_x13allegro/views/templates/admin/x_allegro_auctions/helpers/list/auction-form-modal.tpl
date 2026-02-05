<div class="modal-dialog {if $formAction == 'update'}modal-lg{/if}" role="document">
    <form action="#" method="post">
        <input type="hidden" name="action" value="auction{$formAction|ucfirst}">
        <input type="hidden" name="id_xallegro_account" value="{$allegroAccountId}">

        <div class="modal-content">
            <div class="modal-header x13allegro-modal-header">
                <button type="button" class="close x13allegro-modal-close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="x13allegro-modal-title">
                    {if $formAction == 'finish'}
                        {l s='Zakończ oferty' mod='x13allegro'}
                    {elseif $formAction == 'redo'}
                        {l s='Wznów oferty' mod='x13allegro'}
                    {elseif $formAction == 'auto_renew'}
                        {l s='Ustaw opcje auto wznawiania' mod='x13allegro'}
                    {elseif $formAction == 'update'}
                        {l s='Masowa aktualizacja ofert' mod='x13allegro'}
                    {/if}
                </h4>

                {if $formAction == 'update'}
                    <h6 class="x13allegro-modal-title-small">{l s='Wybranych ofert do aktualizacji' mod='x13allegro'}: <span class="badge">{$auctions|count}</span></h6>
                {/if}
            </div>

            <div class="modal-body x13allegro-modal-body">
                <table class="table x-auction-form-list{if $formAction != 'redo'} x-auction-form-list-hidden{/if}" {if $formAction == 'update' && $auctions|count > 5}style="display: none;"{/if}>
                    <colgroup>
                        <col>
                        {if $formAction == 'redo'}
                            <col width="120px">
                            <col width="140px">
                        {/if}
                        <col width="25px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            {if $formAction == 'redo'}
                                <th>{l s='Wznawianie' mod='x13allegro'}</th>
                                <th>{l s='Ilość po wznowieniu' mod='x13allegro'}</th>
                            {/if}
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $auctions as $auction}
                            <tr data-id="{$auction.id_auction}" {if $formAction == 'redo' && isset($auction.redoData.auctionDisabled) && $auction.redoData.auctionDisabled} class="x-auction-form-list-disabled"{/if}>
                                <td>
                                    <input type="hidden" name="xallegro_auction_id[{$auction.id_auction}]" data-name="xAllegroAuctionId" value="1" {if $formAction == 'redo' && isset($auction.redoData.auctionDisabled) && $auction.redoData.auctionDisabled}disabled="disabled"{/if}>

                                    {if $formAction == 'redo' && isset($auction.redoData.auctionDisabled) && $auction.redoData.auctionDisabled}
                                        <span class="icon-warning text-danger label-tooltip" data-toggle="tooltip" data-original-title="{l s='Brak odpowiedniej ilości produktu w sklepie, lub produkt jest nieaktywny' mod='x13allegro'}"></span>
                                    {/if}

                                    <strong>{$auction.title}</strong>&nbsp;
                                    <small><i><a href="{$auction.href}" target="_blank" rel="nofollow">{$auction.id_auction}</a></i></small>

                                    {if isset($auction.redoData.status) && $auction.redoData.status}
                                        {if $auction.redoData.status == 1}
                                            {$activeOffersTxt = 'aktywną ofertę'}
                                        {elseif $auction.redoData.status < 5}
                                            {$activeOffersTxt = 'aktywne oferty'}
                                        {else}
                                            {$activeOffersTxt = 'aktywnych ofert'}
                                        {/if}

                                        <span class="badge badge-warning label-tooltip" data-toggle="tooltip" data-original-title="Powiązany produkt/kombinacja ma już {$auction.redoData.status} {$activeOffersTxt}">
                                            <span style="cursor: default"><i class="icon-warning"></i> {$auction.redoData.status}</span>
                                        </span>
                                    {/if}
                                </td>
                                {if $formAction == 'redo' && isset($auction.redoData)}
                                    <td>
                                        <select name="xallegro_auction_auto_renew[{$auction.id_auction}]" data-name="xAllegroAuctionAutoRenew" {if $auction.redoData.auctionDisabled}disabled="disabled"{/if}>
                                            <option value="">{{l s='domyślnie' mod='x13allegro'}}</option>
                                            <option value="1">{{l s='tak' mod='x13allegro'}}</option>
                                            <option value="0">{{l s='nie' mod='x13allegro'}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="xallegro_auction_quantity[{$auction.id_auction}]" data-name="xAllegroAuctionQuantity" value="{$auction.redoData.auctionQuantity}" data-max="{$auction.redoData.auctionQuantityMax}" data-oos="{$auction.redoData.productOOS}" data-cast="integer" {if $auction.redoData.auctionDisabled}disabled="disabled"{/if}>
                                        <small>/ {$auction.redoData.productQuantity}</small>
                                    </td>
                                {/if}
                                <td style="text-align: right;">
                                    {if $formAction != 'update'}<a class="x-auction-form-list-delete"><i class="icon-times"></i></a>{/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>

                <div class="x-updater-progress">
                    <div class="clearfix">
                        <h4 class="x-updater-start-title pull-left">{l s='Trwa aktualizacja' mod='x13allegro'}...</h4>
                        <h4 class="x-updater-end-title pull-left">{l s='Aktualizacja zakończona' mod='x13allegro'}</h4>
                        <a class="btn btn-success pull-right x-updater-redo-btn" href="#">{l s='Aktualizuj inną opcję' mod='x13allegro'}</a>
                    </div>

                    <div class="x-updater-progress-bar">
                        <span class="x-updater-progress-bar-fill"></span>
                    </div>

                    <p class="x-updater-progress-bar-data">
                        {l s='Zaktualizowano' mod='x13allegro'}
                        <span class="x-updater-progress-from">0</span> z <span class="x-updater-progress-to">0</span>
                    </p>
                </div>

                <div class="x-updater-finish-message alert alert-success"></div>
                <div class="x-updater-error-message alert alert-danger"></div>

                <div class="x-updater-logger">
                    <hr>
                    <div class="clearfix">
                        <h4 class="pull-left">{l s='Dziennik zdarzeń' mod='x13allegro'}</h4>
                        <a class="btn btn-danger pull-right x-updater-logger-with-errors" href="#">{l s='Pokaż błędy' mod='x13allegro'}</a>
                        <a class="btn btn-warning pull-right x-updater-logger-with-warnings" href="#">{l s='Pokaż ostrzeżenia' mod='x13allegro'}</a>
                        <a class="btn btn-default pull-right x-updater-logger-all" href="#">{l s='Pokaż wszystko' mod='x13allegro'}</a>
                    </div>
                    <ul class="x-updater-logger-content"></ul>
                </div>

                {if $formAction == 'auto_renew'}
                    <div class="form-group row">
                        <label class="control-label col-lg-3">
                            {l s='Auto wznawianie' mod='x13allegro'}
                        </label>
                        <div class="col-lg-9">
                            <select name="allegro_auto_renew">
                                <option value="">{l s='domyślnie' mod='x13allegro'}</option>
                                <option value="1">{l s='tak' mod='x13allegro'}</option>
                                <option value="0">{l s='nie' mod='x13allegro'}</option>
                            </select>
                        </div>
                    </div>
                {elseif $formAction == 'update'}
                    <div class="x-updater-methods">
                        <h4>{l s='Wybierz akcje' mod='x13allegro'}</h4>

                        <div class="form-group">
                            <select x-name="update-auction-entity" class="form-control">
                                <option value="0"> -- wybierz --</option>
                                {foreach $availableUpdateEntities as $entity}
                                    <option value="{$entity.name}">{$entity.desc}</option>
                                {/foreach}
                            </select>
                        </div>

                        <div class="x-updater-extra-settings">
                            {foreach $availableUpdateEntities as $entity}
                                <div id="updater_entity_{$entity.name}" class="x-updater-entity">
                                    {$entity.additional_settings}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {/if}
            </div>

            <div class="modal-footer x13allegro-modal-footer">
                {if $formAction == 'update'}
                <p class="x13allegro-modal-footer-left text-muted">
                    Jak korzystac z aktualizacji ofert? <a href="https://x13.pl/doc/dokumentacja-integracja-allegro-z-prestashop#aktualizacja-aukcji" target="_blank">Zobacz tutaj.</a><br/>
                    Pierwszą aktualizację prosimy przeprowadzić na mniejszej ilości ofert.
                </p>
                {/if}
                <button type="button" class="btn btn-primary x-auction-form-submit">
                    {if $formAction == 'finish'}
                        {l s='Zakończ wybrane oferty' mod='x13allegro'}
                    {elseif $formAction == 'redo'}
                        {l s='Wznów wybrane oferty' mod='x13allegro'}
                    {elseif $formAction == 'auto_renew'}
                        {l s='Ustaw' mod='x13allegro'}
                    {elseif $formAction == 'update'}
                        {l s='Aktualizuj oferty' mod='x13allegro'}
                    {/if}
                </button>
                <button class="btn btn-default x-updater-action-close-popup" type="button">{l s='Zamknij' mod='x13allegro'}</button>
            </div>
        </div>
    </form>
</div>
