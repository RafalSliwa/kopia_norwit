/* global $, prestashop, autocomplete_stock_ajax, autocomplete_stock_cfg */
(function ($) {

  var ASTOCK_DEBUG = false;
  function dlog(){ if(!ASTOCK_DEBUG) return; try{ console.log.apply(console, arguments);}catch(e){} }

  /* ===== Helpers ===== */
  function highlight(text, term) {
    if (!term) return String(text);
    try {
      var t = String(term).trim();
      if (!t) return String(text);
      var esc = t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      esc = esc.replace(/\s+/g, '[\\s\\-_.\\,\\/]*');
      esc = esc.replace(/\[(?:\\s|\\-|_|\\.|,|\\\\\/)\]\*/g, '[\\s\\-_.\\,\\/]*');
      var rx = new RegExp('(' + esc + ')', 'ig');
      return String(text).replace(rx, '<span class="highlight">$1</span>');
    } catch(e){ return String(text); }
  }

  function makeSearchUrl(q){
    var searchUrl = (prestashop && prestashop.urls && prestashop.urls.pages && prestashop.urls.pages.search)
      ? prestashop.urls.pages.search
      : ((prestashop && prestashop.urls && prestashop.urls.base_url)
          ? prestashop.urls.base_url + 'index.php?controller=search'
          : 'index.php?controller=search');
    return searchUrl + (searchUrl.indexOf('?') === -1 ? '?s=' : '&s=') + encodeURIComponent(q || '');
  }

  // get the <ul> autocomplete widget for the given input
  function getAutoMenu($input) {
    var inst = $input.data('prestashop-psBlockSearchAutocomplete')
           || $input.data('ui-autocomplete')
           || $input.data('autocomplete');
    if (!inst) return null;
    return (inst.menu && inst.menu.element)
      ? inst.menu.element
      : ($input.autocomplete ? $input.autocomplete('widget') : null);
  }

  // [overlay] helper — enable/disable active state of the list + optional body dimming (on desktop)
  function setMenuActive($input, on) {
    var $menu = getAutoMenu($input);
    if ($menu && $menu.length) {
      $menu.toggleClass('astock-active', !!on);
    }
    // dim only when the mobile overlay is NOT open
    var isMobileOverlayOpen = $('#astock-search-fs').hasClass('open');
    if (!isMobileOverlayOpen) {
      $('body').toggleClass('astock-overlay-dim', !!on);
    }
  }

  // hard hide/clear the list
  function hardHideMenu($input) {
    var $menu = getAutoMenu($input);
    if ($menu && $menu.length) {
      $menu.stop(true, true);
      $menu.empty();
      $menu
        .hide()
        .css({ display: 'none', top: '-9999px', left: '-9999px', width: '', height: '' })
        .attr('aria-hidden', 'true')
        .removeClass('astock-active');
    }
    $('body').removeClass('astock-overlay-dim');
  }

  /* === watchdog menu === */
  function bindMenuObserver($input) {
    var $menu = getAutoMenu($input);
    if (!$menu || !$menu.length) return;
    if ($menu.data('astockObserved')) return;
    $menu.data('astockObserved', true);

    function isVisible($m) {
      return $m.is(':visible') && $.contains(document, $m[0]) && $m.children().length > 0;
    }
    function refreshState() {
      if ($('#astock-search-fs').hasClass('open')) {
        $menu.removeClass('astock-active');
        $('body').removeClass('astock-overlay-dim');
        return;
      }
      setMenuActive($input, isVisible($menu));
    }

    var obs = new MutationObserver(function(){ refreshState(); });
    obs.observe($menu[0], { attributes:true, attributeFilter:['style','class','aria-hidden'], childList:true, subtree:false });

    var poll = setInterval(function(){
      if (!$.contains(document, $menu[0])) {
        clearInterval(poll);
        try{ obs.disconnect(); }catch(e){}
        return;
      }
      refreshState();
    }, 150);

    $(window).on('unload', function(){ try{obs.disconnect();}catch(e){} clearInterval(poll); });
    setTimeout(refreshState, 0);
  }

  /* ===== Banery ===== */
  function isGlobalEntry(entry){
    if (!entry) return false;
    var v = entry.id_category;
    if (v === null || v === undefined) return true;
    if (typeof v === 'number') return !isFinite(v) || v <= 0;
    if (typeof v === 'string') {
      var s = v.trim();
      if (s === '' || s === '0' || s === '-' || s === '—' || s.toLowerCase() === 'null' || s.toLowerCase() === 'none') return true;
      var n = parseInt(s, 10);
      return isNaN(n) || n <= 0;
    }
    return true;
  }
  function filterCustomGlobal(arr){ return Array.isArray(arr) ? arr.filter(isGlobalEntry) : []; }
  function filterCustomStrictByCategory(arr, catId){
    if (!Array.isArray(arr)) return [];
    var id = parseInt(catId, 10); if (isNaN(id) || id <= 0) return [];
    return arr.filter(function(c){
      var raw = c && c.id_category;
      var n = (typeof raw === 'string') ? parseInt(raw,10) : raw;
      return !isNaN(n) && n === id;
    });
  }
  function renderCustomTiles($tiles, list, term){
    $tiles.empty();
    (list||[]).forEach(function(c){
      var $a = $('<a class="custom-tile" tabindex="-1">').attr('href', c.link || '#').attr('aria-label', c.title || '');
      var glob = (typeof c._isGlobal !== 'undefined') ? !!c._isGlobal : isGlobalEntry(c);
      if (glob) $a.addClass('global-banner').attr('data-global','1');
      if (c.image) $a.append($('<img class="custom-image" alt="">').attr('src', c.image).attr('alt', c.alt || c.title || ''));
      if (c.title) $a.append($('<div class="custom-title">').html(highlight(c.title, term)));
      $tiles.append($a);
    });
  }

  /* ===== Building items ===== */
 function buildItems(resp, term){
  var items = [];
  var t = (typeof term === 'string') ? term.trim() : '';

  var i18n = (window.autocomplete_stock_cfg && autocomplete_stock_cfg.i18n) || {};
  var labelProducts = i18n.products || 'Produkty';
  var labelSearchIn = i18n.search_in || 'Szukaj w kategorii';
  var labelCustom   = i18n.custom || 'Polecane produkty';
  var labelSeeAll   = i18n.see_all || 'Zobacz wszystkie wyniki dla';
  var labelBrands   = i18n.manufacturers || 'Marki';

  // Flagi
  var hasProducts = !!(resp && Array.isArray(resp.products)      && resp.products.length);
  var hasCats     = !!(resp && Array.isArray(resp.categories)    && resp.categories.length);
  var hasManufs   = !!(resp && Array.isArray(resp.manufacturers) && resp.manufacturers.length);
  var hasCustom   = !!(resp && Array.isArray(resp.custom)        && resp.custom.length);

  // CORE = only products/categories/manufacturers (banners do not count as results)
  var hasCoreResults = hasProducts || hasCats || hasManufs;

  // 1) Products
  if (hasProducts) {
    items.push({
      type: 'products-block',
      label: labelProducts,
      products: resp.products.map(function(p){
        var imageType = (autocomplete_stock_cfg && autocomplete_stock_cfg.imageType) || 'small_default';
        var fallback = (prestashop && prestashop.urls && prestashop.urls.no_picture_image && prestashop.urls.no_picture_image.bySize && prestashop.urls.no_picture_image.bySize[imageType])
          ? prestashop.urls.no_picture_image.bySize[imageType].url : '';
        var img = (p.cover && p.cover.bySize && p.cover.bySize[imageType]) ? p.cover.bySize[imageType].url : fallback;
        return { type:'product', name:p.name, url:p.url, image:img };
      })
    });
  }

  // 2) Categories (chips)
  if (hasCats) {
    var catsSorted = resp.categories.slice().sort(function(a,b){
      var c = (b.product_count||0) - (a.product_count||0);
      if (c!==0) return c;
      return String(a.name).localeCompare(String(b.name), 'pl');
    });
    var maxChips = (autocomplete_stock_cfg && typeof autocomplete_stock_cfg.maxChips !== 'undefined')
      ? parseInt(autocomplete_stock_cfg.maxChips,10) : 0;
    var list = (maxChips>0) ? catsSorted.slice(0,maxChips) : catsSorted;

    items.push({
      type:'chips-block',
      label: labelSearchIn + ':',
      chips: list.map(function(c){
        return { type:'chip', id_category:c.id_category, label:c.name, url:c.url, badge:c.product_count||0 };
      })
    });
  }

  // 3) Manufacturers
  if (hasManufs) {
    items.push({
      type: 'manufacturers-block',
      label: labelBrands,
      manufacturers: resp.manufacturers.map(function (m) {
        return { type: 'manufacturer', name: m.name, product_count: m.product_count || 0, url: m.url, logo: m.logo || null };
      })
    });
  }

  // 4) No CORE results → show message (even if there are only banners)
  if (t.length && !hasCoreResults) {
    items.push({
      type: 'no-results',
      label: 'Ups! Nic nie znaleźliśmy 🤔 <br><a class="call-us" href="tel:+48573580785">Skontaktuj się z nami,</a> pomożemy!'
    });
  }

  // 5) Banners (CUSTOM) — can be displayed regardless of the no-results message
  if (hasCustom) {
    var all = resp.custom.slice();
    var init = filterCustomGlobal(all);
    items.push({
      type: 'custom-block',
      label: labelCustom,
      custom: init.map(function(c){ return {
        type:'custom', title:c.title||'', link:c.link||'#', alt:c.alt||'',
        image:c.image||null, id_category:(typeof c.id_category!=='undefined'?c.id_category:null),
        _isGlobal: isGlobalEntry(c)
      };}),
      _allCustom: all.map(function(c){ return {
        type:'custom', title:c.title||'', link:c.link||'#', alt:c.alt||'',
        image:c.image||null, id_category:(typeof c.id_category!=='undefined'?c.id_category:null),
        _isGlobal: isGlobalEntry(c)
      };})
    });
  }

  // 6) "See all" — only when there are CORE results
  if (t.length && hasCoreResults) {
    items.push({ type:'seeall', label: labelSeeAll + ' "' + t + '"', url: makeSearchUrl(t) });
  }

  // 7) Fallback – old format (array of products)
  if (!items.length && Array.isArray(resp)) {
    resp.forEach(function(p){ items.push({ type:'product', name:p.name, url:p.url, image:'' }); });
  }

  return items;
}


  /* ===== Renderery ===== */
  function attachRenderers(inst, getTerm){
    var origMenu = inst._renderMenu ? inst._renderMenu.bind(inst) : null;
    inst._renderMenu = function(ul, items){
      var $ul = $(ul);
      $ul.addClass('autocomplete-three-cols');
      $ul.removeData('astockAllCustom astockTerm').off('.astock-custom-hover');

      if (origMenu) { origMenu(ul, items); }
      else {
        var self = this;
        items.forEach(function (it) { self._renderItemData(ul, it); });
      }

      // hover chips → banery
      $ul.on('mouseenter.astock-custom-hover', '.chips-block a.chip[data-cat-id]', function(){
        var catId = parseInt($(this).attr('data-cat-id'), 10);
        var all   = $ul.data('astockAllCustom') || [];
        var term  = $ul.data('astockTerm') || '';
        var $tiles= $ul.find('.custom-block .astock-tiles').first();
        if (!$tiles.length) return;

        var list = filterCustomStrictByCategory(all, catId);
        if (!list.length) list = filterCustomGlobal(all);
        renderCustomTiles($tiles, list, term);
      });
    };

    var origItem = inst._renderItem ? inst._renderItem.bind(inst) : null;
    inst._renderItem = function(ul, it){
      var term = getTerm ? getTerm() : '';
      var $ul = $(ul);

      if (it.type === 'products-block') {
        var $div = $('<div class="products-block ui-menu-item only-products" role="presentation"></div>');
        $div.append($('<div class="ui-state-disabled ui-autocomplete-category autocomplete-section" aria-disabled="true">').text(it.label));
        it.products.forEach(function(prod){
          var $li = $('<li class="ui-menu-item col-left" role="presentation">');
          var $a  = $('<a class="ui-menu-item-wrapper">').attr('href', prod.url);
          if (prod.image) $a.append($('<img class="autocomplete-thumbnail" alt="">').attr('src', prod.image));
          $a.append($('<span class="product">').html(highlight(prod.name, term)));
          $li.append($a); $div.append($li);
        });
        $ul.append($div);
        return $div;
      }

      if (it.type === 'chips-block') {
        var $div2 = $('<div class="chips-block ui-menu-item only-cats" role="presentation"></div>');
        $div2.append($('<div class="ui-state-disabled ui-autocomplete-category autocomplete-section" aria-disabled="true">').text(it.label));
        var $row = $('<li class="ui-menu-item col-span-2" role="presentation">');
        var $wrap= $('<div class="chips" role="group" aria-label="Suggestions">');
        it.chips.forEach(function(chip){
          $wrap.append(
            $('<a class="chip" tabindex="-1">').attr('href', chip.url).attr('data-cat-id', String(chip.id_category||'')).text(chip.label)
          );
        });
        $row.append($wrap); $div2.append($row);
        $ul.append($div2);
        return $div2;
      }

      if (it.type === 'manufacturers-block') {
        var $div3 = $('<div class="manufacturers-block ui-menu-item hide-on-mobile" role="presentation"></div>');
        $div3.append($('<div class="ui-state-disabled ui-autocomplete-category autocomplete-section" aria-disabled="true">').text(it.label));
        it.manufacturers.forEach(function (m) {
          var $liM = $('<li class="ui-menu-item col-right" role="presentation">');
          var $aM  = $('<a class="ui-menu-item-wrapper">').attr('href', m.url);
          if (m.logo) $aM.append($('<img class="manufacturer-logo" alt="">').attr('src', m.logo));
          $aM.append($('<span class="manufacturer">').text(m.name));
          $liM.append($aM); $div3.append($liM);
        });
        $ul.append($div3);
        return $div3;
      }

      if (it.type === 'custom-block') {
        var $block = $('<div class="custom-block ui-menu-item only-products" role="presentation"></div>');
        $block.append($('<div class="ui-state-disabled ui-autocomplete-category autocomplete-section" aria-disabled="true">').text(it.label));
        var $tiles = $('<div class="recommended_products astock-tiles" role="group" aria-label="Featured"></div>');
        renderCustomTiles($tiles, it.custom || [], term);
        var $row = $('<li class="ui-menu-item col-span-2" role="presentation">').append($tiles);
        $block.append($row); $ul.append($block);
        var existing = $ul.data('astockAllCustom') || [];
        if (!existing.length && Array.isArray(it._allCustom)) {
          $ul.data('astockAllCustom', it._allCustom);
          $ul.data('astockTerm', term);
        }
        return $block;
      }

      if (it.type === 'product') {
        var $liP = $('<li class="ui-menu-item" role="presentation">');
        var $aP  = $('<a class="ui-menu-item-wrapper">').attr('href', it.url);
        if (it.image) $aP.append($('<img class="autocomplete-thumbnail" alt="">').attr('src', it.image));
        $aP.append($('<span class="product">').html(highlight(it.name, term)));
        $liP.append($aP); return $liP.appendTo($ul);
      }

      if (it.type === 'seeall') {
        return $('<li class="ui-menu-item see-all" role="presentation">')
          .append($('<a class="ui-menu-item-wrapper">').attr('href', it.url).text(it.label))
          .appendTo($ul);
      }

      // NEW: no results message
      if (it.type === 'no-results') {
        return $('<li class="ui-menu-item no-results" role="presentation">')
          .append($('<div class="ui-menu-item-wrapper">').html(it.label))
          .appendTo($ul);
      }

      return origItem ? origItem(ul, it) : this._renderItemData(ul, it);
    };
  }

  /* ===== Desktop/common init ===== */
  function enhanceExistingWidget($input, ajaxUrl, minLength, delayMs, $posRoot){
    var inst = $input.data('prestashop-psBlockSearchAutocomplete')
           || $input.data('ui-autocomplete')
           || $input.data('autocomplete');
    if (!inst) return false;

    var setSource = function(){
      return function(query, response){
        $.post(ajaxUrl, { s: query.term, resultsPerPage: autocomplete_stock_cfg ? autocomplete_stock_cfg.productsLimit : 10 }, null, 'json')
          .then(function(resp){ response(buildItems(resp, query.term)); })
          .fail(function(){ response([]); });
      };
    };

    if ($input.psBlockSearchAutocomplete) {
      $input.psBlockSearchAutocomplete('option', 'minLength', minLength);
      $input.psBlockSearchAutocomplete('option', 'delay', delayMs);
      $input.psBlockSearchAutocomplete('option', 'source', setSource());
      if ($posRoot) $input.psBlockSearchAutocomplete('option', 'position', { my:'right top', at:'right bottom', of:$posRoot });
    } else {
      $input.autocomplete('option', 'minLength', minLength);
      $input.autocomplete('option', 'delay', delayMs);
      $input.autocomplete('option', 'source', setSource());
      if ($posRoot) $input.autocomplete('option', 'position', { my:'right top', at:'right bottom', of:$posRoot });
    }

    attachRenderers(inst, function(){ return $input.val ? $input.val() : ''; });

    $input.off('.astock-ol')
      .on('autocompleteopen.astock-ol', function(){ setMenuActive($input, true); })
      .on('autocompleteclose.astock-ol', function(){ setMenuActive($input, false); });

    bindMenuObserver($input);

    return true;
  }

  function initOwnAutocomplete($input, ajaxUrl, minLength, delayMs, $posRoot){
    $input.autocomplete({
      minLength: minLength,
      delay: delayMs,
      position: { my:'right top', at:'right bottom', of:$posRoot || $input },
      source: function(req, res){
        $.post(ajaxUrl, { s: req.term, resultsPerPage: autocomplete_stock_cfg ? autocomplete_stock_cfg.productsLimit : 10 }, null, 'json')
          .then(function(resp){ res(buildItems(resp, req.term)); })
          .fail(function(){ res([]); });
      },
      focus: function(){ return false; },
      select: function(e, ui){ if (ui.item && ui.item.url) { window.location.href = ui.item.url; return false; } }
    });

    var tries=0, poll=setInterval(function(){
      var inst = $input.data('ui-autocomplete') || $input.data('autocomplete');
      if (!inst){ if(++tries>60) clearInterval(poll); return; }
      clearInterval(poll);
      attachRenderers(inst, function(){ return $input.val ? $input.val() : ''; });
      $input.off('.astock-ol')
        .on('autocompleteopen.astock-ol', function(){ setMenuActive($input, true); })
        .on('autocompleteclose.astock-ol', function(){ setMenuActive($input, false); });
      bindMenuObserver($input);
    },50);
  }

  function initOnRoot($root){
    var $input = $root.find('input[name="s"]');
    if (!$input.length) return;

    var ajaxUrl  = window.autocomplete_stock_ajax || $root.attr('data-search-controller-url');
    var minChars = (autocomplete_stock_cfg && autocomplete_stock_cfg.minChars) ? autocomplete_stock_cfg.minChars : 1;
    var delayMs  = (autocomplete_stock_cfg && autocomplete_stock_cfg.debounce) ? autocomplete_stock_cfg.debounce : 150;

    if (!ajaxUrl){ console.warn('[autocomplete_enhanced] Missing AJAX URL'); return; }
    $root.attr('data-search-controller-url', ajaxUrl);
    initMobileFullscreenSearch($root, ajaxUrl);

    var tries=0, timer=setInterval(function(){
      tries++;
      if (enhanceExistingWidget($input, ajaxUrl, minChars, delayMs, $root)) { clearInterval(timer); return; }
      if (tries>=40){ clearInterval(timer); initOwnAutocomplete($input, ajaxUrl, minChars, delayMs, $root); }
    },50);

    $input.on('keydown', function(e){
      if (e.key === 'Enter') {
        var val = ($input.val() || '').trim();
        if (val.length) {
          var inst = $input.data('ui-autocomplete') || $input.data('prestashop-psBlockSearchAutocomplete');
          var $menu = inst && inst.menu ? inst.menu.element : null;
          if (!$menu || !$menu.find('.ui-state-active').length) window.location.href = makeSearchUrl(val);
        }
      }
    });
  }

  $(function(){ $('#search_widget').each(function(){ initOnRoot($(this)); }); });

  /* ===== MOBILE FULLSCREEN ===== */
  function initMobileFullscreenSearch($root, ajaxUrl){
    var isMobile = function(){ return window.matchMedia('(max-width: 768px)').matches; };

    if (!$('#astock-search-fs').length) {
      var $ov = $(
        '<div id="astock-search-fs" class="search-fs-overlay" aria-hidden="true">' +
          '<div class="search-fs-header">' +
            '<div class="search-fs-input"></div>' +
            '<button class="search-fs-close" aria-label="Zamknij" type="button">✕</button>' +
          '</div>' +
          '<div class="search-fs-tabs">' +
            '<div class="search-fs-tab active" data-tab="products">Produkty</div>' +
            '<div class="search-fs-tab" data-tab="cats">Szukaj w kategorii</div>' +
          '</div>' +
          '<div class="search-fs-body"></div>' +
        '</div>'
      );
      $('body').append($ov);
    }

    var $overlay = $('#astock-search-fs');
    var $body    = $overlay.find('.search-fs-body');
    var $srcInput = $root.find('input[name="s"]');
    var $placeholder = $('<span id="astock-fs-ph" style="display:none;"></span>');

    function setAutocompleteToOverlay(){
      var opts = { appendTo: $body, position: { my:'left top', at:'left top', of:$body, collision:'none' } };
      if ($srcInput.psBlockSearchAutocomplete) {
        $srcInput.psBlockSearchAutocomplete('option', 'appendTo', opts.appendTo);
        $srcInput.psBlockSearchAutocomplete('option', 'position', opts.position);
      } else {
        $srcInput.autocomplete('option', 'appendTo', opts.appendTo);
        $srcInput.autocomplete('option', 'position', opts.position);
      }

      // po otwarciu menu → wklej do body overlayu i rozciągnij
      $srcInput.off('autocompleteopen.astock-fs-fix').on('autocompleteopen.astock-fs-fix', function(){
        if (!$overlay.hasClass('open')) return;
        var $menu = getAutoMenu($srcInput);
        if ($menu && $menu.length) {
          $menu.appendTo($body).css({
            left: 0, top: 0, width: '100%',
            maxHeight: '100%', overflow: 'auto',
            border: 0, boxShadow: 'none', background: '#fff',
            zIndex: 10001, position: 'absolute'
          });
          setMenuActive($srcInput, true);
        }
      });
    }

    function setAutocompleteToDesktop(){
      $srcInput.off('autocompleteopen.astock-fs-fix');

      var opts = { appendTo: document.body, position: { my:'right top', at:'right bottom', of:$root } };
      if ($srcInput.psBlockSearchAutocomplete) {
        $srcInput.psBlockSearchAutocomplete('option','appendTo', opts.appendTo);
        $srcInput.psBlockSearchAutocomplete('option','position', opts.position);
        try{ $srcInput.psBlockSearchAutocomplete('close'); }catch(e){}
      } else {
        $srcInput.autocomplete('option','appendTo', opts.appendTo);
        $srcInput.autocomplete('option','position', opts.position);
        try{ $srcInput.autocomplete('close'); }catch(e){}
      }

      hardHideMenu($srcInput);
      var $menu = getAutoMenu($srcInput);
      if ($menu && $menu.length) $menu.appendTo(document.body);
    }

    function activateTab(tab){
      $overlay.removeClass('tab-products tab-cats');
      $overlay.addClass(tab === 'cats' ? 'tab-cats' : 'tab-products');
      $overlay.find('.search-fs-tab').removeClass('active');
      $overlay.find('.search-fs-tab[data-tab="'+tab+'"]').addClass('active');

      var val = ($srcInput.val() || '').trim();
      var minChars = (window.autocomplete_stock_cfg && autocomplete_stock_cfg.minChars) || 1;
      if (val.length >= minChars) {
        try {
          if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('search', val);
          else $srcInput.autocomplete('search', val);
        } catch(e){}
      }
    }

    function openOverlay(){
      if (!isMobile()) return;

      hardHideMenu($srcInput);

      if (!$('#astock-fs-ph').length) $srcInput.after($placeholder);
      $overlay.find('.search-fs-input').append($srcInput);

      $('body').addClass('search-fs-lock');
      $overlay.addClass('open').attr('aria-hidden','false');
      activateTab('products');
      setAutocompleteToOverlay();

      setTimeout(function(){
        $srcInput.trigger('focus');
        var val = ($srcInput.val() || '').trim();
        var minChars = (window.autocomplete_stock_cfg && autocomplete_stock_cfg.minChars) || 1;
        try{
          if (val.length < minChars) {
            if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('close');
            else $srcInput.autocomplete('close');
            hardHideMenu($srcInput);
          } else {
            if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('search', val);
            else $srcInput.autocomplete('search', val);
          }
        }catch(e){}
      },0);

      /* === iOS fix: Enter/OK i blur === */

      // 1) Block form submission when the overlay is open
      var $form = $srcInput.closest('form');
      if ($form.length) {
        $form.off('submit.astock-ios').on('submit.astock-ios', function (e) {
          if ($overlay.hasClass('open')) { e.preventDefault(); e.stopPropagation(); }
        });
      }

      // 2) Enter/OK in the overlay does not navigate — only refreshes the list
      $srcInput.off('keydown.astock-ios-enter').on('keydown.astock-ios-enter', function (e) {
        if (!$overlay.hasClass('open')) return;
        if (e.key === 'Enter' || e.keyCode === 13) {
          e.preventDefault(); e.stopPropagation();
          var v = ($srcInput.val() || '').trim();
          var min = (window.autocomplete_stock_cfg && autocomplete_stock_cfg.minChars) || 1;
          if (v.length >= min) {
            try {
              if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('search', v);
              else $srcInput.autocomplete('search', v);
            } catch (err) {}
          }
        }
      });

      // 3) After blur (e.g. after OK on iOS) – keep/restore the list
      $srcInput.off('blur.astock-ios-keep').on('blur.astock-ios-keep', function () {
        if (!$overlay.hasClass('open')) return;
        var v = ($srcInput.val() || '').trim();
        var min = (window.autocomplete_stock_cfg && autocomplete_stock_cfg.minChars) || 1;
        setTimeout(function () {
          if (!$overlay.hasClass('open')) return;
          if (v.length >= min) {
            try {
              if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('search', v);
              else $srcInput.autocomplete('search', v);
            } catch (err) {}
          }
        }, 120);
      });
    }

    function closeOverlay(){
      try {
        if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('close');
        else $srcInput.autocomplete('close');
      } catch(e){}
      hardHideMenu($srcInput);
      setMenuActive($srcInput, false);

      $srcInput.off('.astock-fs-fix .astock-ios-enter .astock-ios-keep');

      $srcInput.blur();

      var $ph = $('#astock-fs-ph');
      if ($ph.length){ $ph.before($srcInput); $ph.remove(); }
      setAutocompleteToDesktop();

      $overlay.removeClass('open').attr('aria-hidden','true');
      $('body').removeClass('search-fs-lock');

      // detach submit blocking
      var $form = $srcInput.closest('form');
      if ($form.length) $form.off('submit.astock-ios');
    }

    // open on focus (mobile)
    $srcInput.on('focus.astock-fs', function(){
      if (isMobile() && !$overlay.hasClass('open')) {
        openOverlay();
        this.blur();
      }
    });

    // "✕" — clears the input and results, then closes the overlay
    $overlay.on('click', '.search-fs-close', function(e){
      e.preventDefault(); e.stopPropagation();
      $srcInput.val('');
      try {
        if ($srcInput.psBlockSearchAutocomplete) $srcInput.psBlockSearchAutocomplete('close');
        else $srcInput.autocomplete('close');
      } catch(e){}
      hardHideMenu($srcInput);
      setMenuActive($srcInput, false);
      closeOverlay();
    });

    // Esc closes the overlay
    $(document).on('keydown.astock-fs', function(e){
      if (e.key === 'Escape' && $overlay.hasClass('open')) closeOverlay();
    });

    // tabs
    $overlay.on('click', '.search-fs-tab', function(){ activateTab($(this).data('tab')); });

    // Enter outside the overlay => full results
    $srcInput.on('keydown.astock-fs-enter', function(e){
      if ($('#astock-search-fs').hasClass('open')) return; // overlay ma własną obsługę Enter
      if (e.key === 'Enter') {
        var val = ($srcInput.val() || '').trim();
        if (val.length) window.location.href = makeSearchUrl(val);
      }
    });
  }
})(jQuery);
