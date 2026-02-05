/**
 * NR SKU Sync – PS 8 (delegation + queue + robust refs + current variant from form)
 */
(function () {
  'use strict';

  function $$(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }
  function txt(el){ return (el && el.textContent ? el.textContent : '').trim(); }
  function log(){ /* removed console.log */ }
  function err(){ try{ console.error.apply(console, ['[NR-SKU]'].concat([].slice.call(arguments))); }catch(e){} }

  // ==== SKU helpers ====
  function currentSkuNode(){ return document.querySelector('[data-product-refresh="reference"] [itemprop="sku"]'); }
  function currentSkuText(){ var n=currentSkuNode(); return n && n.textContent ? n.textContent.trim() : ''; }

  var lastRef = currentSkuText();

  function writeSku(value){
    if (!value) return;
    if (currentSkuText() === value) return;
    $$('[data-product-refresh="reference"] [itemprop="sku"]').forEach(function(el){
      el.textContent = value;
    });
    $$('[data-product-refresh="reference"]').forEach(function(wrap){
      wrap.classList.remove('d-none');
      wrap.style.display = '';
    });
  }

  function updateReferenceDom(ref, source){
    var value = (ref && ref.trim()) ? ref : (lastRef || '');
    if (value) lastRef = value;
    writeSku(value);
    setTimeout(function(){ writeSku(lastRef); }, 60);
    setTimeout(function(){ writeSku(lastRef); }, 180);
  }

  // ==== CURRENT VARIANT helpers ====
  function writeCurrentVariant(html){
    var nodes = $$('[data-product-refresh="current-variant"]');
    if (!nodes.length) return;
    nodes.forEach(function(el){ el.innerHTML = html || ''; });
  }

  function renderVariantHtmlFromAttributes(attrsObj){
    if (!attrsObj) return '';
    var parts = [];
    try {
      Object.keys(attrsObj).forEach(function(k){
        var a = attrsObj[k];
        if (!a) return;
        var group = a.group || a.public_group || '';
        var name  = a.name || '';
        if (!group && !name) return;
        parts.push(
          '<span class="control-label">'+(group||'')+': </span>' +
          '<span class="control-value">'+(name||'')+'</span>'
        );
      });
    } catch(e){}
    return parts.join(', ');
  }

  // === NEW: read selected variant from form (selects and radios) ===
  function readVariantFromForm(){
    var container = document.querySelector('.product-variants, .js-product-variants');
    if (!container) return '';

    var parts = [];

    // Each variant block
    $$('.product-variants-item', container).forEach(function(item){
      // Try group name from label on the left (as in TPL)
      var labelEl = item.querySelector('.control-label');
      var groupName = labelEl ? txt(labelEl).replace(/:\s*$/,'') : '';

      // SELECT
      var sel = item.querySelector('select[name^="group["]');
      if (sel) {
        var opt = sel.options[sel.selectedIndex];
        var val = opt ? (opt.getAttribute('title') || opt.text || opt.label || '').trim() : '';
        if (!groupName) groupName = sel.getAttribute('aria-label') || '';
        if (groupName && val) {
          parts.push(
            '<span class="control-label">'+groupName+': </span>' +
            '<span class="control-value">'+val+'</span>'
          );
        }
        return; // go to next item
      }

      // RADIO (including color swatch)
      var radios = $$('input[type="radio"][name^="group["]', item);
      var checked = radios.find(function(r){ return r.checked; });
      if (checked) {
        var val = checked.getAttribute('title') || '';
        if (!val) {
          var lab = checked.closest('label');
          var labSpan = lab && lab.querySelector('.radio-label');
          if (labSpan) val = txt(labSpan);
        }
        if (!groupName) groupName = checked.getAttribute('aria-label') || '';
        if (groupName && val) {
          parts.push(
            '<span class="control-label">'+groupName+': </span>' +
            '<span class="control-value">'+val+'</span>'
          );
        }
      }
    });

    return parts.join(', ');
  }

  function updateCurrentVariantFromProduct(product, tag){
    if (product && product.attributes) {
      var html = renderVariantHtmlFromAttributes(product.attributes);
      if (html) {
        writeCurrentVariant(html);
        return true;
      }
    }
    return false;
  }

  // ==== Sources of reference ====
  function pickReference(product){
    if (!product) return {ref:'', src:'none'};

    if (product.reference_to_display) return {ref:String(product.reference_to_display), src:'reference_to_display'};

    if (product.attributes) {
      try {
        var keys = Object.keys(product.attributes);
        for (var i=0;i<keys.length;i++){
          var a = product.attributes[keys[i]];
          if (a && a.reference) return {ref:String(a.reference), src:'attributes[*].reference'};
        }
      } catch(e){}
    }

    var ipa = product.id_product_attribute
           || (product.selected_combination && product.selected_combination.id_product_attribute)
           || product.id_product_attribute_default;
    if (ipa && product.combinations && product.combinations[ipa] && product.combinations[ipa].reference) {
      return {ref:String(product.combinations[ipa].reference), src:'combinations[id].reference'};
    }

    if (product.selected_combination && product.selected_combination.reference) {
      return {ref:String(product.selected_combination.reference), src:'selected_combination.reference'};
    }

    if (product.product_attribute_minimal && product.product_attribute_minimal.reference) {
      return {ref:String(product.product_attribute_minimal.reference), src:'product_attribute_minimal.reference'};
    }

    if (product.reference) return {ref:String(product.reference), src:'product.reference'};

    return {ref:'', src:'none'};
  }

  function getRefFromDom(){
    var n = document.querySelector('#product-details .product-reference span, .js-product-details .product-reference span');
    return n && n.textContent ? n.textContent.trim() : '';
  }

  // ==== Refresh endpoint ====
  function buildRefreshUrl(){
    var url = new URL(window.location.href);
    url.searchParams.set('controller','product');
    url.searchParams.set('ajax','1');
    url.searchParams.set('action','refresh');
    return url.toString();
  }

  function collectFormData(){
    var form = document.querySelector('#add-to-cart-or-refresh');
    if (!form) { err('No form #add-to-cart-or-refresh'); return null; }
    var fd = new FormData();

    var idp = form.querySelector('[name="id_product"]');
    if (idp) fd.set('id_product', idp.value);

    $$('select[name^="group["], input[name^="group["]').forEach(function(el){
      if ((el.type==='radio'||el.type==='checkbox') && !el.checked) return;
      if (el.name) fd.set(el.name, el.value);
    });

    var ipaNode = document.querySelector('[name="id_product_attribute"], #idCombination, input.js-id-product-attribute');
    if (ipaNode && ipaNode.value) fd.set('id_product_attribute', ipaNode.value);

    var qty = form.querySelector('[name="qty"], [name="quantity_wanted"]');
    if (qty && qty.value) fd.set(qty.name, qty.value);

    var cust = form.querySelector('[name="id_customization"]');
    if (cust) fd.set('id_customization', cust.value || '0');

    return fd;
  }

  // ==== Queueing requests ====
  var refreshing = false;
  var pending = false;
  var pendingReason = 'pending';
  var debounceTimer;

  function requestRefresh(reason){
    clearTimeout(debounceTimer);

    // NEW: instant update of "selected variant" from form (UX)
    var htmlInstant = readVariantFromForm();
    if (htmlInstant) writeCurrentVariant(htmlInstant);

    debounceTimer = setTimeout(function(){
      if (refreshing) {
        pending = true;
        pendingReason = reason || 'pending';
        return;
      }
      doRefresh(reason);
    }, 60);
  }

  function doRefresh(reason){
    var fd = collectFormData();
    if (!fd) return;

    refreshing = true;
    var url = buildRefreshUrl();

    fetch(url, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' },
      credentials: 'same-origin'
    })
    .then(function(r){ return r.text().then(function(t){ return {ok:r.ok,status:r.status,text:t}; }); })
    .then(function(res){
      if (!res.ok) throw new Error('HTTP '+res.status+' '+res.text.slice(0,200));
      var data = JSON.parse(res.text);
      var product = data.product || data;
      if (!product) throw new Error('No product in refresh response');

      // SKU
      var pick = pickReference(product);
      if (!pick.ref || pick.ref === product.reference) {
        setTimeout(function(){
          var domRef = getRefFromDom();
          updateReferenceDom(domRef || pick.ref || '', domRef ? 'dom' : pick.src);
        }, 60);
      } else {
        updateReferenceDom(pick.ref, pick.src);
      }

      // Variant: first try from JSON
      if (!updateCurrentVariantFromProduct(product, 'refresh')) {
        // Fallback: what is selected in the form
        var html = readVariantFromForm();
        if (html) {
          writeCurrentVariant(html);
        }
      }
    })
    .catch(function(e){ err('refresh error', e); })
    .finally(function(){
      refreshing = false;
      if (pending) {
        pending = false;
        doRefresh(pendingReason);
      }
    });
  }

  // ==== Events — delegation ====
  function armListeners(){
    document.addEventListener('change', function(e){
      var t = e.target;
      if (!t) return;
      if (t.matches && t.matches('select[name^="group["], input[name^="group["]')) {
        requestRefresh('change');
      }
    }, true);

    document.addEventListener('click', function(e){
      var el = e.target.closest && e.target.closest('.input-container, .color, .radio-label, [name^="group["]');
      if (el) requestRefresh('click');
    }, true);

    var ipaObserver;
    function bindIpaObserver(){
      try { if (ipaObserver) ipaObserver.disconnect(); } catch(e){}
      var node = document.querySelector('[name="id_product_attribute"], #idCombination, input.js-id-product-attribute');
      if (!node) return;
      var last = node.value;
      ipaObserver = new MutationObserver(function(){
        if (node.value !== last) { last = node.value; requestRefresh('observer'); }
      });
      ipaObserver.observe(node, { attributes:true, attributeFilter:['value'] });
    }
    bindIpaObserver();

    var bodyObserver = new MutationObserver(function(muts){
      var needRebind = false;
      for (var i=0;i<muts.length;i++){
        var m = muts[i];
        if (m.type === 'childList' && (m.addedNodes && m.addedNodes.length)) {
          needRebind = true; break;
        }
      }
      if (needRebind) setTimeout(bindIpaObserver, 50);
    });
    try { bodyObserver.observe(document.body, { childList:true, subtree:true }); } catch(e){}

    // Initial variant write (from form or data-product)
    setTimeout(function(){
      var html = readVariantFromForm();
      if (html) writeCurrentVariant(html);
    }, 0);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', armListeners);
  else armListeners();
})();

