(function () {
  // ❗️ Skip these keys to avoid adding them to cookies or links
  const COOKIE_EXCLUDE = ['_x13eucookie', '_x13eucookie_consent_hash'];

  function getAllUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = {};
    for (const [key, value] of urlParams.entries()) {
      params[key] = value;
    }
    return params;
  }

  function setCookie(name, value, days) {
    const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + expires.toUTCString() + ';path=/';
  }

  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
  }

  // 📥 Save all GET parameters to cookies (excluding system ones)
  const foundParams = getAllUrlParams();
  Object.entries(foundParams).forEach(([key, value]) => {
    if (!COOKIE_EXCLUDE.includes(key)) {
      setCookie(key, value, 30);
    }
  });

  function pushToDataLayer() {
    const data = {};
    const allCookies = document.cookie.split('; ');
    allCookies.forEach(cookie => {
      const [key, val] = cookie.split('=');
      if (key && val && !COOKIE_EXCLUDE.includes(key)) {
        try {
          data[key] = decodeURIComponent(val);
        } catch (e) {
        }
      }
    });

    if (Object.keys(data).length > 0) {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        ...data,
        event: 'tracking_data_ready'
      });
    }
  }

  // 🔗 Append parameters to internal links
  function appendTrackingParamsToLinks() {
    const trackingParams = getAllUrlParams(); // Tylko z URL
    const cookies = {};
    Object.keys(trackingParams).forEach((key) => {
      const cookieVal = getCookie(key);
      if (cookieVal && !COOKIE_EXCLUDE.includes(key)) {
        cookies[key] = cookieVal;
      }
    });

    if (Object.keys(cookies).length === 0) return;

    const links = document.querySelectorAll('a[href]');
    links.forEach(link => {
      try {
        const url = new URL(link.href, window.location.origin);
        if (url.hostname !== window.location.hostname) return;

        let modified = false;
        Object.entries(cookies).forEach(([key, value]) => {
          if (!url.searchParams.has(key)) {
            url.searchParams.set(key, value);
            modified = true;
          }
        });

        if (modified) {
          link.href = url.toString();
        }
      } catch (e) {
      }
    });
  }

  // ⏳ Wait for marketing consent (x13eucookies)
  document.addEventListener('x13eucookies_consent_accepted_marketing', function () {
    pushToDataLayer();
    appendTrackingParamsToLinks();
  });

  // If consent was previously given – act immediately
  try {
    const raw = getCookie('_x13eucookie');
    const x13cookie = raw ? JSON.parse(raw) : null;
    if (x13cookie && x13cookie['3'] === true) {
      pushToDataLayer();
      document.addEventListener('DOMContentLoaded', appendTrackingParamsToLinks);
    }
  } catch (e) {
    // Cookie nieczytelne lub brak zgody
  }
})();
