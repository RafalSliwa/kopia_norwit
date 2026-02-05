
# Autocomplete Stock

> PrestaShop 8 module that enhances the search bar autocomplete with **products sorted by stock quantity** and **category suggestions**.

---

## ✨ Features

- Autocomplete search with:
  - ✅ Products (sorted by available stock, tie-breakers by name & ID)
  - ✅ Categories (based on name and products matching the search)
- Configurable in Back Office:
  - Min chars to trigger autocomplete
  - Debounce time (ms)
  - Products limit
  - Categories limit
  - Show only products in stock (optional)
  - Image type for product thumbnails
- Multilingual support (`$this->l()` and PrestaShop translations)
- Uses PrestaShop hooks and JS injection — **no override of `ps_searchbar` template**
- Clean JSON API endpoint:  
---

## ⚙️ Configuration

After installation, go to  
**Back Office → Modules → Autocomplete by stock → Configure**

Available options:

| Setting            | Description                                                | Default |
|--------------------|------------------------------------------------------------|---------|
| Show categories    | Show matching categories in suggestions                    | Yes     |
| Products limit     | Max number of products to show                             | 10      |
| Categories limit   | Max number of categories to show                           | 5       |
| Only available     | Show only products with stock > 0                          | No      |
| Min chars          | Minimum characters required to trigger autocomplete        | 1       |
| Debounce (ms)      | Delay after typing before sending AJAX request             | 150     |
| Image type         | PrestaShop image type (`small_default`, `home_default`...) | small_default |

---

## 🖼️ Example

Typing **"shoes"**:

- **Categories**
  - Shoes (120 products)
  - Sport shoes (45 products)
- **Products**
  - Running shoes (stock: 54)
  - Trekking shoes (stock: 32)
  - ...

---

## 🛠️ Installation

1. Copy the module folder `autocomplete_stock/` into your PrestaShop `modules/` directory.
2. Install from Back Office → Modules.
3. Configure settings as needed.
4. The module automatically hooks into the existing `ps_searchbar` (no template override).

---

## 📜 License

MIT © 2025 [norwit](mailto:your@email.com)

See [LICENSE.txt](LICENSE.txt) for details.
