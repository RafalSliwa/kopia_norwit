<div class="modal" id="product_gpsr_modal_{$index}" x-name="product_gpsr" x-index="{$index}" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header x13allegro-modal-header">
                <button type="button" class="close x13allegro-modal-close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="x13allegro-modal-title">{l s='Zgodność z GPSR' mod='x13allegro'}</h4>
                <h6 class="x13allegro-modal-title-small">dla produktu: <span>{$product.name}{if $product.name_attribute} - {$product.name_attribute}{/if}</span></h6>

                <span class="xproductization-product-label">
                    {if $productization_show_reference && !empty($product.reference)}<strong>Ref:</strong> {$product.reference}{/if}
                    {if $productization_show_gtin}
                        {if !empty({$product.ean13})}<strong>EAN13:</strong> {$product.ean13}{/if}
                        {if !empty({$product.isbn})}<strong>ISBN:</strong> {$product.isbn}{/if}
                        {if !empty({$product.upc})}<strong>UPC:</strong> {$product.upc}{/if}
                    {/if}
                    {if $productization_show_mpn && !empty($product.mpn)}<strong>MPN:</strong> {$product.mpn}{/if}
                </span>
            </div>
            <div class="modal-body x13allegro-modal-body">
                {if !$x13gpsrInstalled && !$x13gpsrInfoHide}
                    <div class="row x13gpsr-info-allegro">
                        <div class="col-md-12">
                            <a href="#" class="x13gpsr-info-allegro-close"><i class="icon-times"></i></a>

                            <div class="row">
                                <div class="col-xs-3">
                                    <div class="x13gpsr-info-allegro-img">
                                        <a href="https://x13.pl/moduly-prestashop/gpsr-rozporzadzenie-o-ogolnym-bezpieczenstwie-produktow.html?&utm_campaign=x13allegro_gpsr" target="_blank">
                                            <img alt="" src="../modules/x13allegro/img/x13gpsr.jpg"><br/>
                                            Sprawdź moduł
                                        </a>
                                    </div>
                                </div>
                                <div class="col-xs-9">
                                    <h4>Chcesz wystawiać oferty jeszcze szybciej? </h4>
                                    <p>
                                        Nie trać czasu na ręczne wybieranie danych!<br/>
                                        Skorzystaj z naszego modułu <a href="https://x13.pl/moduly-prestashop/gpsr-rozporzadzenie-o-ogolnym-bezpieczenstwie-produktow.html?&utm_campaign=x13allegro_gpsr" target="_blank"><strong>GPSR - Rozporządzenie o Ogólnym Bezpieczeństwie Produktów</strong></a> i usprawnij cały proces: <br/>
                                    </p>
                                    <ul>
                                        <li>Błyskawicznie przypisz <strong>osoby odpowiedzialne</strong> i <strong>producentów</strong> do swoich produktów w sklepie.</li>
                                        <li>Bezpośrednio w module <strong>utworzysz i zsynchronizujesz</strong> dane z Allegro.</li>
                                        <li>Podczas wystawiania ofert – dane GPSR <strong>wczytają się automatycznie</strong>!</li>
                                    </ul>
                                    <p>Zwiększ efektywność, oszczędzaj czas i spełniaj wymogi prawne bez zbędnych formalności.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="row">
                    <div class="col-lg-offset-2 col-md-8">
                        <div class="form-group">
                            <h4 style="font-size: 18px; font-weight: 600; margin: 20px 0 10px 0;">Dane producenta</h4>
                            <div class="alert alert-info">
                                Jeśli producent jest <b>spoza Unii Europejskiej</b>, musisz też wskazać osobę odpowiedzialną.
                            </div>
                            <label for="item[{$index}][responsible_producer]" class="control-label required xproductization-gpsr-required">Dane producenta - GPSR</label>
                            <select id="item[{$index}][responsible_producer]" name="item[{$index}][responsible_producer]" x-name="responsible_producer" data-default-value="{$product.responsible_producer}">
                                <option value="">-- Wybierz --</option>
                                {foreach $responsibleProducers as $responsibleProducer}
                                    <option value="{$responsibleProducer->id}" {if $responsibleProducer->id == $product.responsible_producer}selected="selected"{/if}>{$responsibleProducer->name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-offset-2 col-md-8">
                        <div class="form-group">
                            <h4 style="font-size: 18px; font-weight: 600; margin: 20px 0 0 0;">Osoba odpowiedzialna</h4>
                            <label for="item[{$index}][responsible_person]" class="control-label">Osoba odpowiedzialna za zgodność produktu - GPSR (opcjonalnie)</label>
                            <select id="item[{$index}][responsible_person]" name="item[{$index}][responsible_person]" x-name="responsible_person">
                                <option value="">-- Wybierz --</option>
                                {foreach $responsiblePersons as $responsiblePerson}
                                    <option value="{$responsiblePerson->id}" {if $responsiblePerson->id == $product.responsible_person}selected="selected"{/if}>{$responsiblePerson->name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-offset-2 col-md-8">
                        <div class="form-group">
                            <h4 style="font-size: 18px; font-weight: 600; margin: 20px 0 10px 0;">Bezpieczeństwo produktu</h4>
                            <div class="alert alert-info">
                                Jeśli produkt zawiera informacje o bezpieczeństwie, muszą być one dostępne <b>w&nbsp;językach wszystkich rynków</b>, na których oferujesz produkt.
                            </div>
                            <label for="item[{$index}][safety_information_type]" class="control-label required xproductization-gpsr-required">Informacje o bezpieczeństwie produktu</label>
                            <select id="item[{$index}][safety_information_type]" name="item[{$index}][safety_information_type]" x-name="safety_information_type" data-default-value="{$product.safety_information_type}">
                                <option value="">-- Wybierz --</option>
                                {foreach $safetyInformationTypes as $safetyInformationType}
                                    <option value="{$safetyInformationType.id}" {if $product.safety_information_type == $safetyInformationType.id}selected="selected"{/if}>{$safetyInformationType.name}</option>
                                {/foreach}
                            </select>

                            <div class="gpsr-safety-information-text-wrapper" style="display: none;">
                                <textarea name="item[{$index}][safety_information_text]" x-name="safety_information_text" data-default-value="{$product.safety_information_text}">{$product.safety_information_text}</textarea>
                                <p class="help-block counter-wrapper" data-max="{$safetyInformationTextMax}">
                                    <span class="counter-error" style="display: none;">Tekst jest za długi!</span>
                                    <span class="counter"><span class="count">0</span>/{$safetyInformationTextMax}</span>
                                </p>
                            </div>

                            {include file="./gpsr-safety-information-attachment-wrapper.tpl" productAttachments=$product.attachments x13gpsrAttachments=$product.safety_information_attachment_x13gpsr index=$index}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer x13allegro-modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">{l s='Zapisz' mod='x13allegro'}</button>
            </div>
        </div>
    </div>
</div>
