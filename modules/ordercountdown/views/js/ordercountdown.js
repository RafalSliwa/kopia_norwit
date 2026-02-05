(function () {
  'use strict';

  function $(sel, root){ return (root||document).querySelector(sel); }
  function $all(sel, root){ return [].slice.call((root||document).querySelectorAll(sel)); }
  function txt(el){ return el ? (el.textContent || '').trim() : ''; }
  function setText(el, v){ if (el) el.textContent = v; }
  function show(el, yes){ if (!el) return; el.style.display = yes ? '' : 'none'; }
  function toggleClass(el, cls, on){ if (!el) return; el.classList[on?'add':'remove'](cls); }
  function log(){}   // disabled
  function err(){}   // disabled

  // Messages from PHP (fallback if missing)
  var M = (typeof window.orderCountdownMessages === 'object' && window.orderCountdownMessages) || {
    fridayBeforeCutoff: 'Arrives on Monday',
    fridayAfterCutoff: 'Arrives on Tuesday',
    thursdayBeforeCutoff: 'Arrives tomorrow',
    thursdayAfterCutoff: 'Arrives on Monday',
    weekend: 'Arrives on Tuesday',
    weekdayBeforeCutoff: 'Arrives tomorrow',
    mondayAfterCutoff: 'Arrives on Wednesday',
    tuesdayAfterCutoff: 'Arrives on Thursday',
    wednesdayAfterCutoff: 'Arrives on Friday',
  };

  var tickTimer = null;

  function getNow(){ return new Date(); }
  function computeCutoff(now){
    var cutoff = new Date(now); cutoff.setHours(13,0,0,0);
    var d = now.getDay(); // 0 Sun, 6 Sat
    if (d === 6 || d === 0) {
      var toMon = (8 - (d || 7));
      cutoff = new Date(now); cutoff.setDate(now.getDate()+toMon); cutoff.setHours(13,0,0,0);
    } else if (now > cutoff) {
      cutoff = new Date(now); cutoff.setDate(now.getDate()+1); cutoff.setHours(13,0,0,0);
    }
    return cutoff;
  }
  function formatTimer(now, cutoff){
    var ms = cutoff - now; if (ms <= 0) return null;
    var totalHours = Math.floor(ms / 3600000);
    var minutes = Math.floor((ms % 3600000) / 60000);
    var seconds = Math.floor((ms % 60000) / 1000);
    return totalHours+'h '+minutes+'m '+seconds+'s';
  }
  function computeMessage(now){
    var d = now.getDay(), h = now.getHours(), cut = 13;
    if (d === 5 && h >= cut) return M.fridayAfterCutoff;
    if (d === 5) return M.fridayBeforeCutoff;
    if (d === 6 || d === 0) return M.weekend;
    if (d === 4 && h >= cut) return M.thursdayAfterCutoff;
    if (d === 4) return M.thursdayBeforeCutoff;
    if (h >= cut) {
      if (d === 1) return M.mondayAfterCutoff;
      if (d === 2) return M.tuesdayAfterCutoff;
      if (d === 3) return M.wednesdayAfterCutoff;
    }
    return M.weekdayBeforeCutoff;
  }

  function updateUi(quantity){
    var rootD = $('#product_delivery');
    var rootM = $('#product_delivery_mobile');

    // --- Desktop references
    var qD = rootD && rootD.querySelector('.product-quantities .quantity-desktop');
    var lD = rootD && rootD.querySelector('.product-quantities .label');
    var availWrapD = rootD && rootD.querySelector('.delivery_wraper'); // first wrapper (availability)

    var cdBlockD = rootD && rootD.querySelector('.delivery_countdown_block');   // countdown block
    var cdWrapD  = cdBlockD ? cdBlockD.closest('.delivery_wraper') : null;      // "time to ship" wrapper
    var msgD     = rootD && rootD.querySelector('.delivery_message');           // "delivery date" text
    var msgWrapD = rootD && rootD.querySelector('.delivery_wraper.prduct_delivery');

    // --- Mobile references
    var qM = rootM && rootM.querySelector('.product-quantities .quantity-desktop');
    var lM = rootM && rootM.querySelector('.product-quantities .label');
    var availWrapM = rootM && rootM.querySelector('.delivery_wraper'); // first wrapper (availability)

    var cdBlockM = rootM && rootM.querySelector('.delivery_countdown_block');
    var cdWrapM  = cdBlockM ? cdBlockM.closest('.delivery_wraper') : null;
    var msgM     = (rootM && (rootM.querySelector('#delivery-message-mobile') || rootM.querySelector('.delivery_message'))) || null;
    var msgWrapM = rootM && rootM.querySelector('.delivery_wraper.prduct_delivery');

    var qty = parseInt(quantity, 10); if (isNaN(qty)) qty = 0;

    // Quantity and labels
    // --- CHANGE: hide "0" instead of showing it
    if (qD) { qD.style.display = qty > 0 ? '' : 'none'; if (qty > 0) setText(qD, String(qty)); }
    if (qM) { qM.style.display = qty > 0 ? '' : 'none'; if (qty > 0) setText(qM, String(qty)); }

    if (lD) setText(lD, qty > 0 ? (lD.getAttribute('data-instock-label') || 'pcs. in stock')
                                : (lD.getAttribute('data-supplier-label') || lD.getAttribute('data-supplier-fallback') || ''));
    if (lM) setText(lM, qty > 0 ? (lM.getAttribute('data-instock-label') || 'pcs. in stock')
                                : (lM.getAttribute('data-supplier-label') || lM.getAttribute('data-supplier-fallback') || ''));

    // Availability CSS class
    toggleClass(availWrapD, 'supplier-shipping', qty <= 0);
    toggleClass(availWrapM, 'supplier-shipping', qty <= 0);

    // Show/hide WHOLE WRAPPERS for countdown & message (desktop + mobile)
    show(cdWrapD, qty > 0);  show(cdBlockD, qty > 0);
    show(msgWrapD, qty > 0);

    show(cdWrapM, qty > 0);  show(cdBlockM, qty > 0);
    show(msgWrapM, qty > 0);

    // Set message only when qty > 0 (clear otherwise)
    if (qty > 0) {
      var now = getNow();
      var msg = computeMessage(now);
      if (msgD) setText(msgD, msg);
      if (msgM) setText(msgM, msg);
    } else {
      if (msgD) setText(msgD, '');
      if (msgM) setText(msgM, '');
    }

    // Timer
    if (tickTimer) { clearInterval(tickTimer); tickTimer = null; }
    if (qty > 0) {
      tickTimer = setInterval(function(){
        var now = getNow();
        var cutoff = computeCutoff(now);
        var s = formatTimer(now, cutoff);
        var tD = $('#product_delivery #countdown-timer-desktop');
        var tM = $('#product_delivery_mobile #countdown-timer-mobile');
        if (s) {
          if (tD) setText(tD, s);
          if (tM) setText(tM, s);
        } else {
          if (tD) { setText(tD, ''); tD.parentElement && (tD.parentElement.style.display='none'); }
          if (tM) { setText(tM, ''); tM.parentElement && (tM.parentElement.style.display='none'); }
          clearInterval(tickTimer); tickTimer = null;
        }
      }, 1000);
    }
  }

  function quantityFromPayload(product){
    if (product && typeof product.quantity !== 'undefined') return product.quantity;
    if (product && typeof product.quantity_all_versions !== 'undefined') return product.quantity_all_versions;
    return null;
  }

  // Fallback: read JSON from .js-product-details[data-product]
  function getProductFromDataAttr(){
    var el = document.querySelector('.js-product-details[data-product]');
    if (!el) return null;
    try { return JSON.parse(el.getAttribute('data-product')); }
    catch(e){ return null; }
  }
  function refreshFromDataAttr(tag){
    var p = getProductFromDataAttr();
    if (!p) return false;
    var qty = quantityFromPayload(p);
    if (qty === null) qty = 0;
    updateUi(qty);
    return true;
  }

  // Main listeners
  function arm(){
    // Prestashop event bus
    if (window.prestashop && typeof window.prestashop.on === 'function') {
      prestashop.on('updatedProduct', function(payload){
        var p = payload && (payload.product || payload);
        var qty = quantityFromPayload(p);
        if (qty === null) {
          if (!refreshFromDataAttr('bus-fallback')) updateUi(0);
        } else {
          updateUi(qty);
        }
      });
    }

    // Custom "product-updated" event
    document.addEventListener('product-updated', function(e){
      var p = e && e.detail ? (e.detail.product || e.detail) : null;
      var qty = quantityFromPayload(p);
      if (qty === null) {
        if (!refreshFromDataAttr('doc-fallback')) updateUi(0);
      } else {
        updateUi(qty);
      }
    });

    // Observe data-product attribute
    var dp = document.querySelector('.js-product-details');
    if (dp) {
      try {
        new MutationObserver(function(muts){
          for (var i=0;i<muts.length;i++){
            if (muts[i].type === 'attributes' && muts[i].attributeName === 'data-product') {
              refreshFromDataAttr('mutation');
              break;
            }
          }
        }).observe(dp, { attributes:true, attributeFilter:['data-product'] });
      } catch(e){}
    }

    // Observe hidden id_product_attribute
    var ipaNode = document.querySelector('[name="id_product_attribute"], #idCombination, input.js-id-product-attribute');
    if (ipaNode) {
      try {
        var last = ipaNode.value;
        new MutationObserver(function(){
          if (ipaNode.value !== last) {
            last = ipaNode.value;
            if (!refreshFromDataAttr('ipa-change')) {
              // nothing to do if no product JSON
            }
          }
        }).observe(ipaNode, { attributes:true, attributeFilter:['value'] });
      } catch(e){}
    }

    // Initial state from DOM
    var initialQtyNode = $('#product_delivery .product-quantities .quantity-desktop') || $('#product_delivery_mobile .product-quantities .quantity-desktop');
    var initialQty = initialQtyNode ? parseInt(txt(initialQtyNode), 10) : 0;
    updateUi(isNaN(initialQty) ? 0 : initialQty);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', arm);
  else arm();
})();
