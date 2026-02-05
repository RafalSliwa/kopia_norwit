document.addEventListener('DOMContentLoaded', function () {
  const input = document.querySelector('input[name="product_redirect_search"]');
  const hidden = document.querySelector('input[name="id_product_redirect"]');

  if (!input || !hidden) return;

  input.addEventListener('input', function () {
    let baseUrl = retiredproducts_ajax_url.replace(/[?&]$/, '');
    let sep = baseUrl.includes('?') ? '&' : '?';
    fetch(baseUrl + sep + 'query=' + encodeURIComponent(input.value))
      .then(res => res.json())
      .then(products => {
        const list = document.getElementById('autocomplete-results');
        list.innerHTML = '';
        if (!Array.isArray(products) || products.length === 0) {
          const li = document.createElement('li');
          li.textContent = 'Brak wyników';
          list.appendChild(li);
          return;
        }
        products.forEach(product => {
          const li = document.createElement('li');
          li.textContent = `#${product.id_product} – ${product.name} (Indeks: ${product.reference || '-'}, Marka: ${product.manufacturer_name || '-'})`;
          li.dataset.id = product.id_product;
          li.addEventListener('click', () => {
            input.value = product.name;
            hidden.value = product.id_product;
            list.innerHTML = '';
          });
          list.appendChild(li);
        });
      });
  });
});

