# Availability Sort

Lightweight PrestaShop module that introduces an "Availability" sort option based on product stock quantity and makes it the default choice for search listings.

## Features

- Injects the availability sort option through core hooks (no overrides).
- Sets the sort order as the default unless the customer picks a specific option.
- Logs hook registration issues to the PrestaShop back-office log.
- Translation-ready with sample `pl` catalog.

## Installation

1. Copy the `availabilitysort` folder into your PrestaShop `modules` directory.
2. Install the module from **Modules > Module Manager** or run:
   ```bash
   ./bin/console prestashop:module install availabilitysort
   ```
3. Clear the cache if enabled.

## Upgrading

Update the module files, then run:
```bash
./bin/console prestashop:module upgrade availabilitysort
```
Upgrade scripts ensure required hooks stay registered.

## Compatibility

Tested with PrestaShop 8.x.
