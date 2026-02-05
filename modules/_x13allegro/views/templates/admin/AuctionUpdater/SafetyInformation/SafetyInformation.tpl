<div class="form-group">
    <div class="alert alert-info">
        Jeśli produkt zawiera informacje o bezpieczeństwie, muszą być one dostępne <b>w językach wszystkich rynków</b>,
        na których oferujesz produkt.
    </div>
    <label for="allegro_safety_information_mode" class="control-label col-lg-4">
        {l s='Metoda aktualizacji' mod='x13allegro'}
    </label>
    <div class="col-lg-8">
        <select id="allegro_safety_information_mode" name="allegro_safety_information_mode">
            <option value="0" selected="selected">{l s='aktualizuj informacje o bezpieczeństwie w postaci opisu tekstowego' mod='x13allegro'}</option>
            <option value="1">{l s='aktualizuj informacje o bezpieczeństwie z katalogu Allegro' mod='x13allegro'}</option>
            {if $data.x13gpsr}<option value="2">{l s='aktualizuj informacje o bezpieczeństwie według powiązań z modułu X13 GPSR' mod='x13allegro'}</option>{/if}
        </select>
    </div>
    <div class="gpsr-safety-information-text-wrapper col-lg-offset-4 col-lg-8">
        <textarea name="allegro_safety_information_text"></textarea>
        <p class="help-block counter-wrapper" data-max="{$data.safetyInformationTextMax}">
            <span class="counter-error" style="display: none;">Tekst jest za długi!</span>
            <span class="counter"><span class="count">0</span>/{$data.safetyInformationTextMax}</span>
        </p>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-12">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="allegro_fill_responsible_producer" value="1">
                Uzupełnij producenta odpowiedzialnego, dla ofert które go nie posiadają
            </label>
        </div>
    </div>
</div>

<div class="form-group hidden" id="gpsr-responsible-producer-wrapper">
    {include file="../ResponsibleProducer/ResponsibleProducer.tpl"}
</div>

<script>
    $(document).on('change', '[name="allegro_safety_information_mode"]', function () {
        if (parseInt($(this).val()) !== 0) {
            $('.gpsr-safety-information-text-wrapper').hide();
        } else {
            $('.gpsr-safety-information-text-wrapper').show();
        }
    });

    $(document).on('input', '[name="allegro_safety_information_text"]', function () {
        var $counter = $(this).parent().find('.counter-wrapper');
        var count = $(this).val().length;

        if (count > parseInt($counter.data('max'))) {
            $counter.find('.counter-error').show();
        } else {
            $counter.find('.counter-error').hide();
        }

        $counter.find('.count').text(count);
    });

    $(document).on('change', '[name="allegro_fill_responsible_producer"]', function () {
        if ($(this).is(':checked')) {
            $('#gpsr-responsible-producer-wrapper').removeClass('hidden');
        } else {
            $('#gpsr-responsible-producer-wrapper').addClass('hidden');
        }
    });    
</script>
