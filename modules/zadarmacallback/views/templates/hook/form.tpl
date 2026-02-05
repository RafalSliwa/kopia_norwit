<script>
window.zadarmaTranslations = {
  {if $language.iso_code == 'pl'}
    phoneError: '{l s="Wprowadź poprawny numer telefonu (9 cyfr)." mod="zadarmacallback" js=1}',
    generalError: '{l s="Wystąpił błąd." mod="zadarmacallback" js=1}',
    connectionError: '{l s="Błąd połączenia z serwerem." mod="zadarmacallback" js=1}'
  {else}
    phoneError: '{l s="Enter a valid phone number with country code." mod="zadarmacallback" js=1}',
    generalError: '{l s="An error occurred." mod="zadarmacallback" js=1}',
    connectionError: '{l s="Server connection error." mod="zadarmacallback" js=1}'
  {/if}
};

// Przekaż informację o języku do JavaScript  
window.prestashopLanguage = '{$language.iso_code}';
</script>

<div id="zadarma-modal" class="zadarma-modal">
  <div class="zadarma-modal-content">
    <span class="zadarma-close">&times;</span>

    <div class="zadarma-header">
      <div class="zadarma-header-text">
        <h3 id="zadarma-header-title">{l s='Talk to a specialist. We will call back in 20 seconds.' mod='zadarmacallback'}</h3>
        <p id="zadarma-description">{l s='Leave your contact details.' mod='zadarmacallback'}</p>
      </div>
      <div class="zadarma-header-icon">
        <img src="{$module_dir}views/img/norwitek_with_phoneme.png" alt="Norwitek" class="zadarma-icon">
      </div>
    </div>

    <form method="post" id="zadarma-form" action="{$link->getModuleLink('zadarmacallback', 'callback')}">
      <input type="hidden" name="token" value="{$token}">
      <input type="hidden" name="from" id="zadarma-from-number" value=""> {* ← dynamiczne FROM *}

      <label for="zadarma-name">{l s='Name' mod='zadarmacallback'}</label>
      <input type="text" id="zadarma-name" name="name" required placeholder="{l s='Name' mod='zadarmacallback'}">

      <label for="zadarma-phone">{l s='Phone number' mod='zadarmacallback'}</label>
      <input type="tel" id="zadarma-phone" name="phone" required 
             {if $language.iso_code == 'pl'}
               placeholder="+48 700 111 222" 
               minlength="15" maxlength="15"
               title="{l s='Enter full phone number (9 digits)' mod='zadarmacallback'}"
             {else}
                 placeholder="+XX XXXXXXXXX country code required" 
               minlength="8" maxlength="20"
               title="{l s='Enter your phone number with country code' mod='zadarmacallback'}"
             {/if}>

       <button type="submit" id="zadarma-submit"><i class="fa fa-phone"></i> {l s='CALL ME' mod='zadarmacallback'}</button>
    </form>

    <div id="zadarma-success" style="display:none;">
      ✅ {l s='Thank you! Our specialist will contact you shortly.' mod='zadarmacallback'}
    </div>

    <div id="zadarma-error" style="display:none;"></div>
  </div>
</div>
