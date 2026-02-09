---
agent: Programista
---
# Prompt for AI Assistance

## Project Context
This repository contains the source code for a PrestaShop-based e-commerce project. The main goals of the project are:
- Customizing PrestaShop modules and themes to meet specific client requirements.
- Integrating external APIs (e.g., shipping providers, payment gateways).
- Optimizing the performance and user experience of the online store.

## Key Technologies
- **PrestaShop**: Version 8.0.x.
- **Languages**: PHP, Smarty, JavaScript, CSS.
- **Frameworks/Libraries**: Symfony components, jQuery, Bootstrap.

## Guidelines for AI Assistance
- Follow PrestaShop coding standards and best practices.
- Ensure compatibility with PrestaShop 1.7.x.
- Use clear and concise comments to explain the generated code.
- Prioritize performance and security in all solutions.
- When working with templates, use Smarty syntax and follow PrestaShop's theme structure.
- For API integrations, ensure proper error handling and logging.

## Example Tasks

### Module Development & Integration
1. Generate a PrestaShop module to integrate with a shipping provider's API.
2. Create custom hooks and implement hook handlers (e.g., `hookActionProductUpdate`, `hookDisplayProductExtraContent`).
3. Develop cross-module integrations (e.g., `customcarrier` + `relatedproducts` for dynamic shipping calculations).

### Template Customization
4. Customize Smarty templates (`.tpl` files) such as `modal.tpl`, `product.tpl`, `category.tpl`, or module templates to add new functionality.
5. Modify template hooks to display dynamic content based on product settings or cart data.
6. Implement responsive UI components with Bootstrap styling in theme and module templates.

### Performance Optimization
7. Optimize database queries in the `Product.php` class to improve performance.
8. Implement JavaScript throttling/debouncing for DOM updates to prevent visual glitches.
9. Reduce redundant API calls and cache frequently accessed data.

### Bug Fixing & Debugging
10. Debug and fix field reset issues in PrestaShop admin panels (e.g., missing fields in save hooks).
11. Resolve PHP type coercion problems (e.g., `!empty("0.000000")` returning incorrect boolean values).
12. Fix visual glitches like flash effects in modal cart updates.
13. Troubleshoot PrestaShop cache issues affecting template rendering.

### Versioning & Deployment
14. Version modules using semantic versioning (major.minor.patch).
15. Create and maintain CHANGELOG.md files documenting all changes.
16. Package modules as ZIP files for production deployment.

### Data Validation & Security
17. Implement proper input validation using PrestaShop `Tools::getValue()` with type casting.
18. Add XSS prevention through proper output escaping in Smarty templates.
19. Ensure SQL queries use PrestaShop ORM/Db class to prevent injection attacks.

### Feature Implementation
20. Add a new feature to display estimated delivery times based on the current day and time.
21. Implement dynamic shipping cost calculations based on product weight, quantity, and cart thresholds.
22. Create admin interfaces for bulk product configuration with conflict validation.

## Expected Output
- Clean, well-documented code.
- Solutions that align with the project's goals and technologies.
- Suggestions for improving code quality and performance.