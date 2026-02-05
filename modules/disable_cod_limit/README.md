# Disable COD Limit for PrestaShop

**Disable COD Limit** is a lightweight and configurable PrestaShop 8 module that disables the *Cash on Delivery (COD)* payment option when the total value of the cart exceeds a specified threshold (default: **15,000 zł**).

---

## 💡 Features

- 🚫 Automatically disables the *Cash on Delivery* option for high-value orders
- 🧾 Frontend warning message shown when COD is disabled
- ⚙️ Customizable amount limit (hardcoded or via configuration panel – if extended)
- 🔐 No override or core modifications
- 🌐 Fully translatable using PrestaShop’s native translation system
- ✅ Compatible with PrestaShop 8.x

---

## ⚙️ Requirements

- PrestaShop **8.0.0+**
- PHP **7.4+** (recommended: **PHP 8.1** or higher)
- A theme that supports `displayPaymentTop` hook (most default themes do)

---

## 📦 Installation

### 1. Manual Installation

Download or clone the module into your `/modules` directory:


### 2. Install via Back Office

- Go to **Modules → Module Manager**
- Search for **Disable COD Limit**
- Click **Install**

---

## ⚠️ Usage

By default, the COD method will be disabled if the cart total exceeds **15,000 zł**.

If you want to change this threshold, you can:

- Modify the hardcoded value in `disable_cod_limit.php`:
  ```php
  $maxAmount = 15000;
