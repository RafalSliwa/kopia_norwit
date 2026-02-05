<?php

include_once(_PS_MODULE_DIR_ . 'leasenow/libraries/Helper.php');

if(!defined('_PS_VERSION_')) {
	exit;
}

/**
 * Class LeaseNow
 *
 * @property string name
 * @property string tab
 * @property string version
 * @property string author
 * @property int    need_instance
 * @property bool   currencies
 * @property string currencies_mode
 * @property array  ps_versions_compliancy
 * @property int    is_eu_compatible
 * @property string displayName
 * @property string description
 * @property string confirm_uninstall
 */
class LeaseNow extends PaymentModule
{

	/**
	 * Sets the Information for the Module manager
	 * Also creates an instance of this class
	 */
	public function __construct()
	{
		$this->name = 'leasenow';
		$this->displayName = 'ING Lease Now';
		$this->tab = 'payments_gateways';
		$this->version = '1.1.0';
		$this->author = 'ING Lease Now';
		$this->need_instance = 1;
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';
		$this->ps_versions_compliancy = ['min' => '1.6'];
		$this->is_eu_compatible = 1;

		parent::__construct();

		$this->displayName = $this->l('ING Lease Now');
		$this->description = $this->l('Customer can calculate lease installments and sign a lease contract. An item will be purchased by ING.');
		$this->confirm_uninstall = $this->l('Are you sure you want to uninstall ING Lease Now module?');
		$this->module_key = 'c0fb5087be631a126d2a5a35ee84dd40';
	}

	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return string
	 */
	public static function buildTemplatePath($name, $type)
	{

		if(self::isPs17()) {
			return 'module:leasenow/views/templates/' . $type . '/' . $name . '.tpl';
		}

		return $name . '.tpl';
	}

	/**
	 * @return bool|int
	 */
	public static function isPs17()
	{
		return version_compare(_PS_VERSION_, '1.7.0.0', '>=');
	}

	/**
	 * This function installs the Lease Now Module
	 *
	 * @return boolean
	 * @throws PrestaShopDatabaseException
	 * @throws PrestaShopException
	 */
	public function install()
	{

		if(extension_loaded('curl') == false) {
			$this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');

			return false;
		}

		include(dirname(__FILE__) . '/sql/install.php');

		if(!(
			parent::install()
			&& $this->registerHook('payment')
			&& $this->registerHook('paymentOptions')
			&& $this->registerHook('orderConfirmation')
			&& $this->registerHook('paymentReturn')
			&& $this->registerHook('displayBackOfficeHeader')
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('displayReassurance')
			&& $this->registerHook('displayShoppingCartFooter') // display leasing button at product page, additional info

			&& Configuration::updateValue('LEASENOW_STORE_ID', '')
			&& Configuration::updateValue('LEASENOW_SECRET', '')

			&& Configuration::updateValue('LEASENOW_SANDBOX', 0)
			&& Configuration::updateValue('LEASENOW_SANDBOX_STORE_ID', '')
			&& Configuration::updateValue('LEASENOW_SANDBOX_SECRET', '')

			&& Configuration::updateValue('LEASENOW_BUTTON_CHECKOUT', 0)

			&& Configuration::updateValue('LEASENOW_BUTTON_PRODUCT', 0)
			&& Configuration::updateValue('LEASENOW_BUTTON_PRODUCT_SCALE', 100)

			&& Configuration::updateValue('LEASENOW_BUTTON_CART', 0)
			&& Configuration::updateValue('LEASENOW_BUTTON_CART_SCALE', 100)

			&& Configuration::updateValue('LEASENOW_REL_NO_FOLLOW', 0)
			&& Configuration::updateValue('LEASENOW_PAYMENT_TITLE', '')

			&& Configuration::updateValue('LEASENOW_GA_KEY', '')
		)) {

			return false;
		}

		if(version_compare(_PS_VERSION_, '1.7.0', '>=')) {

			if(!$this->registerHook('displayProductAdditionalInfo')) { // display leasing button at product page

				$this->_errors[] = $this->l('There was an Error installing the module. Cannot register hook displayProductAdditionalInfo.');

				return false;
			}
		} else {

			if(!$this->registerHook('displayProductButtons')) { // display leasing button at product page

				$this->_errors[] = $this->l('There was an Error installing the module. Cannot register hook displayProductButtons.');

				return false;
			}
		}

		if(Validate::isInt(Configuration::get('PAYMENT_LEASENOW_NEW_STATUS'))
			xor (Validate::isLoadedObject($orderStateNew = new OrderState(Configuration::get('PAYMENT_LEASENOW_NEW_STATUS'))))) {

			$orderStateNew = new OrderState();
			$missingLang = true;

			$langs = [
				'en' => 'Payment ING Lease Now: awaiting for leasing',
				'pl' => 'Płatność ING Lease Now: oczekuje na leasing',
			];

			foreach($langs as $lang => $message) {
				$langId = Language::getIdByIso($lang);
				if(isset($langId)) {
					$orderStateNew->name[$langId] = $message;
					$missingLang = false;
				}
			}

			if($missingLang) {
				$langId = (int) $this->context->language->id;
				$orderStateNew->name[$langId] = $langs['en'];
			}

			$orderStateNew->send_email = false;
			$orderStateNew->invoice = false;
			$orderStateNew->unremovable = false;
			$orderStateNew->color = "lightblue";

			if(!$orderStateNew->add()) {
				$this->_errors[] = $this->l('There was an Error installing the module. Cannot add new order state.');

				return false;
			}
			if(!Configuration::updateValue('PAYMENT_LEASENOW_NEW_STATUS', $orderStateNew->id)) {
				$this->_errors[] = $this->l('There was an Error installing the module. Cannot update new order state.');

				return false;
			}
		}

		return true;
	}

	/**
	 * @return boolean
	 */
	public function uninstall()
	{
		return Configuration::deleteByName('LEASENOW_STORE_ID')
			&& Configuration::deleteByName('LEASENOW_SECRET')

			&& Configuration::deleteByName('LEASENOW_SANDBOX')
			&& Configuration::deleteByName('LEASENOW_SANDBOX_STORE_ID')
			&& Configuration::deleteByName('LEASENOW_SANDBOX_SECRET')

			&& Configuration::deleteByName('LEASENOW_BUTTON_PRODUCT')
			&& Configuration::deleteByName('LEASENOW_BUTTON_PRODUCT_SCALE')

			&& Configuration::deleteByName('LEASENOW_BUTTON_CHECKOUT')

			&& Configuration::deleteByName('LEASENOW_BUTTON_CART')
			&& Configuration::deleteByName('LEASENOW_BUTTON_CART_SCALE')

			&& Configuration::deleteByName('LEASENOW_REL_NO_FOLLOW')
			&& Configuration::deleteByName('LEASENOW_PAYMENT_TITLE')

			&& Configuration::deleteByName('LEASENOW_GA_KEY')

			&& parent::uninstall();
	}

	/**
	 * Display configuration form.
	 *
	 * @return mixed
	 */
	public function getContent()
	{

		$msg = '';

		if(Tools::isSubmit('submitLeaseNow')) {
			if($this->saveConfiguration()) {
				$msg = [
					'type'    => 'success',
					'message' => $this->l('Configuration updated successfully'),
				];
			} else {
				$msg = [
					'type'    => 'error',
					'message' => $this->l('There was an error saving your configuration'),
				];
			}
		}

		$this->context->smarty->assign([
			'leasenow_form' => './index.php?tab=AdminModules&configure=leasenow&token=' . Tools::getAdminTokenLite('AdminModules') . '&tab_module=' . $this->tab . '&module_name=leasenow',

			'leasenow_store_id' => Configuration::get('LEASENOW_STORE_ID'),
			'leasenow_secret'   => Configuration::get('LEASENOW_SECRET'),

			'leasenow_sandbox'          => Configuration::get('LEASENOW_SANDBOX'),
			'leasenow_sandbox_store_id' => Configuration::get('LEASENOW_SANDBOX_STORE_ID'),
			'leasenow_sandbox_secret'   => Configuration::get('LEASENOW_SANDBOX_SECRET'),

			'leasenow_button_product'       => Configuration::get('LEASENOW_BUTTON_PRODUCT'),
			'leasenow_button_product_scale' => Configuration::get('LEASENOW_BUTTON_PRODUCT_SCALE'),

			'leasenow_button_cart'       => Configuration::get('LEASENOW_BUTTON_CART'),
			'leasenow_button_cart_scale' => Configuration::get('LEASENOW_BUTTON_CART_SCALE'),

			'leasenow_button_checkout' => Configuration::get('LEASENOW_BUTTON_CHECKOUT'),

			'leasenow_msg' => $msg,

			'leasenow_ga_key'        => Configuration::get('LEASENOW_GA_KEY'),
			'leasenow_rel_no_follow' => Configuration::get('LEASENOW_REL_NO_FOLLOW'),

			'leasenow_payment_title'         => Configuration::get('LEASENOW_PAYMENT_TITLE'),
			'leasenow_payment_title_default' => $this->getPaymentTitleDefault(),

		]);

		return $this->fetchTemplate('admin.tpl');
	}

	/**
	 * @return bool
	 */
	private function saveConfiguration()
	{

		Configuration::updateValue('LEASENOW_PAYMENT_TITLE', pSQL(Tools::getValue('leasenow_payment_title')));

		Configuration::updateValue('LEASENOW_STORE_ID', pSQL(Tools::getValue('leasenow_store_id')));
		Configuration::updateValue('LEASENOW_SECRET', pSQL(Tools::getValue('leasenow_secret')));

		Configuration::updateValue('LEASENOW_SANDBOX', pSQL(Tools::getValue('leasenow_sandbox')));
		Configuration::updateValue('LEASENOW_SANDBOX_STORE_ID', pSQL(Tools::getValue('leasenow_sandbox_store_id')));
		Configuration::updateValue('LEASENOW_SANDBOX_SECRET', pSQL(Tools::getValue('leasenow_sandbox_secret')));

		Configuration::updateValue('LEASENOW_BUTTON_PRODUCT', pSQL(Tools::getValue('leasenow_button_product')));
		Configuration::updateValue('LEASENOW_BUTTON_PRODUCT_SCALE', pSQL(Tools::getValue('leasenow_button_product_scale')));

		Configuration::updateValue('LEASENOW_BUTTON_CART', pSQL(Tools::getValue('leasenow_button_cart')));
		Configuration::updateValue('LEASENOW_BUTTON_CART_SCALE', pSQL(Tools::getValue('leasenow_button_cart_scale')));

		Configuration::updateValue('LEASENOW_BUTTON_CHECKOUT', pSQL(Tools::getValue('leasenow_button_checkout')));

		Configuration::updateValue('LEASENOW_REL_NO_FOLLOW', pSQL(Tools::getValue('leasenow_rel_no_follow')));
		Configuration::updateValue('LEASENOW_GA_KEY', pSQL(Tools::getValue('leasenow_ga_key')));

		return true;
	}

	/**
	 * @return string
	 */
	private function getPaymentTitleDefault()
	{

		return $this->l('Leasing (for business)');
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function fetchTemplate($name)
	{
		return $this->display(__FILE__, $name);
	}

	/**
	 * @return mixed|string
	 */
	public function hookBackOfficeHeader()
	{
		$this->context->controller->addJquery();
		$this->context->controller->addCSS($this->_path . 'views/css/leasenow-admin.css');
		$this->context->controller->addCSS($this->_path . 'views/css/font-awesome.css');

		$credentials = $this->getCredentials();

		if(!$credentials) {
			return '';
		}

		$orderId = Tools::getValue('id_order');

		if($orderId === false) {
			return '';
		}

		$order = new Order($orderId);

		if($order->module !== 'leasenow') {
			return '';
		}

		$tableName = _DB_PREFIX_ . 'leasenow';
		$reservationId = Db::getInstance()->getValue("SELECT `" . $tableName . "`.`id_leasing` FROM `" . $tableName . "` WHERE  `" . $tableName . "`.`id_order` = '" . pSQL($orderId) . "';");

		if(!$reservationId) {
			return '';
		}

		$this->context->smarty->assign('ps_version', (int) str_replace('.', '', _PS_VERSION_));
		$this->context->smarty->assign('leasenow_loading_gif', $this->getImage('loading.gif'));
		$this->context->smarty->assign('order_id', $orderId);
		$this->context->smarty->assign('token', Tools::getAdminToken($reservationId));
		$this->context->smarty->assign('url', $this->context->link->getModuleLink('leasenow', 'checkleasing'));
		$this->context->smarty->assign('reservationId', $reservationId);

		return $this->fetchTemplate('/views/templates/admin/leasenowCheckStatusButton.tpl');
	}

	/**
	 * @return array
	 */
	public function getCredentials()
	{

		$array = [
			'storeId'     => Configuration::get('LEASENOW_STORE_ID'),
			'secret'      => Configuration::get('LEASENOW_SECRET'),
			'environment' => \Leasenow\Payment\Util::ENVIRONMENT_PRODUCTION,
		];

		if(Configuration::get('LEASENOW_SANDBOX')) {
			$array['storeId'] = Configuration::get('LEASENOW_SANDBOX_STORE_ID');
			$array['secret'] = Configuration::get('LEASENOW_SANDBOX_SECRET');
			$array['environment'] = \Leasenow\Payment\Util::ENVIRONMENT_SANDBOX;
		}

		if(!$array['storeId'] || !$array['secret']) {
			return [];
		}

		return $array;
	}

	/**
	 * @param string $file
	 *
	 * @return array|bool|mixed|string
	 */
	public function getImage($file)
	{

		return Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $file);
	}

	/**
	 * @param array $params
	 *
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function hookPayment($params)
	{

		if(!$this->checkAndPrepareForCheckout($params['cart'])) {
			return false;
		}

		$this->smarty->assign([
			'leasenow_ps_version' => _PS_VERSION_,

			'leasenow_img' => $this->getLogoPath(),

			'leasenow_sandbox' => Configuration::get('LEASENOW_SANDBOX'),

			'leasenow_payment_url' => $this->context->link->getModuleLink('leasenow', 'payment'),

			'leasenow_payment_title' => Configuration::get('LEASENOW_PAYMENT_TITLE')
				?: $this->getPaymentTitleDefault(),

			'leasenow_redirect_hint' => $this->l('Please wait, in the next step you will choose a payment method.'),
			'leasenow_loading_gif'   => $this->getImage('loading.gif'),
		]);

		return $this->fetchTemplate('payment.tpl');
	}

	/**
	 * @param Cart $cart
	 *
	 * @return bool
	 */
	private function checkAndPrepareForCheckout($cart)
	{

		$this->setLeasenowLeasingCookie();

		if(!$this->checkIsActiveCheckout()) {
			return false;
		}

		$leasing = $this->getLeasing(
			$this->prepareCartAvailabilityBody($cart, true)
		);

		if(!$leasing['success'] || !$leasing['body']['availability']) {
			return false;
		}

		$this->setLeasenowLeasingCookie(json_encode([
			'redirectUrl'   => $leasing['body']['redirectUrl'],
			'reservationId' => $leasing['body']['reservationId'],
		]));

		return true;
	}

	/**
	 * @param string $value
	 */
	private function setLeasenowLeasingCookie($value = '')
	{

		$this->context->cookie->__set('leasenowLeasing', $value);
	}

	/**
	 * @return bool
	 */
	private function checkIsActiveCheckout()
	{

		return $this->active && Configuration::get('LEASENOW_BUTTON_CHECKOUT');
	}

	/**
	 * @param string $body
	 * @param bool   $check
	 *
	 * @return array|false
	 */
	public function getLeasing($body, $check = false)
	{

		$credentials = $this->getCredentials();

		if(!$credentials) {
			return false;
		}

		$api = new \Leasenow\Payment\Api(
			$credentials['storeId'],
			$credentials['secret'],
			$credentials['environment']
		);

		if($check) {
			// body is leasing_id
			$leasing = $api->getStatus('', $body);

			// region check api response
			if(!$leasing['success']) {
				return [
					'success' => false,
					'body'    => [
						'error' =>
							[
								'code' => \Leasenow\Payment\Util::EC_S,
							],
					],
				];
			}
			// endregion
		} else {
			$leasing = $api->getAvailability($body);

			// region check api response
			if(!$leasing['success']) {
				return [
					'success' => false,
					'body'    => [
						'error' =>
							[
								'code' => \Leasenow\Payment\Util::EC_S,
							],
					],
				];
			}
			// endregion
		}

		return [
			'success' => true,
			'body'    => $leasing['body'],
		];
	}

	/**
	 * @param Cart $cart
	 * @param bool $ga
	 *
	 * @return string
	 * @throws PrestaShopException
	 */
	public function prepareCartAvailabilityBody($cart, $ga = false)
	{
		$availability = new \Leasenow\Payment\Availability();

		foreach($cart->getProducts(true) as $product) {

			$name = $product['name'];

			if(isset($product['attributes_small']) && $product['attributes_small']) {
				$name .= ' - ' . $product['attributes_small'];
			}

			$category = new Category($product['id_category_default'], Context::getContext()->language->id);

			$availability->addItem(
				$this->context->link->getProductLink($product),
				$name,
				$product['price'],
				$product['quantity'],
				$product['id_product_attribute']
					? $product['id_product'] . ',' . $product['id_product_attribute']
					: $product['id_product'],
				$category->name
					?: '',
				$product['rate']
			);
		}

		return $this->prepareAvailabilityBody($availability, $ga
			? $cart
			: false);
	}

	/**
	 * @param Availability $availability
	 * @param bool         $cart
	 *
	 * @return string
	 */
	public function prepareAvailabilityBody($availability, $cart = false)
	{

		$paramsUrl = [];

		if($cart && Configuration::get('LEASENOW_GA_KEY')) {
			$paramsUrl = [
				'ga_cart_id' => $cart->id,
				'ga_hash'    => hash('sha256', $cart->id . $cart->secure_key),
			];
		}

		$availability->setCurrencyIsoName($this->context->currency->iso_code);
		$availability->setRedirectUrl($this->context->link->getModuleLink('leasenow', 'success', $paramsUrl));
		$availability->setNotificationUrl($this->context->link->getModuleLink('leasenow', 'notification'));

		return $availability->prepareData();
	}

	/**
	 * @return array|bool|mixed|string
	 */
	public function getLogoPath($small = true)
	{

		if($small) {
			return $this->getImage('leasenow-small.png');
		}

		return $this->getImage('leasenow.png');
	}

	/**
	 * @param array $params
	 *
	 * @return array|false
	 * @throws Exception
	 */
	public function hookPaymentOptions($params)
	{

		if(!$this->checkAndPrepareForCheckout($params['cart'])) {
			return false;
		}

		$paymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
		$paymentOptions[] = $paymentOption->setCallToActionText(
			(
			Configuration::get('LEASENOW_PAYMENT_TITLE')
				?: $this->getPaymentTitleDefault())
		)
			->setLogo($this->getLogoPath(true))
			->setModuleName($this->name)
			->setAction($this->context->link->getModuleLink('leasenow', 'payment'));

		return $paymentOptions;
	}

	/**
	 * @param array $params
	 */
	public function hookDisplayProductAdditionalInfo($params)
	{
		return $this->leasingProductPage($params);
	}

	/**
	 * @param array $params
	 *
	 * @return false|mixed
	 */
	public function leasingProductPage($params)
	{

		if(!Configuration::get('LEASENOW_BUTTON_PRODUCT')) {
			return false;
		}

		$qty = empty($_REQUEST['qty'])
			? 1
			: $_REQUEST['qty'];

		/** @var $product ProductLazyArray */
		$product = $this->getProductDataForProductPage($params['product'], $qty);

		if(!$product) {
			return false;
		}

		$leasing = $this->getLeasing(
			$this->prepareProductAvailabilityBody(
				$product['link'],
				$product['name'],
				$product['total'],
				$qty,
				$product['id'],
				$product['categoryName'],
				$product['rate']
			)
		);

		if(!$leasing['success']) {
			return false;
		}

		return $this->renderButton($leasing, Configuration::get('LEASENOW_BUTTON_PRODUCT_SCALE'));
	}

	/**
	 * @param object $product
	 * @param int    $qty
	 *
	 * @return array|false
	 */
	private function getProductDataForProductPage($product, $qty = 0)
	{

		if(self::isPs17()) {

			$productDetailsIncludingAttributes = $product->getEmbeddedAttributes();

			if($productDetailsIncludingAttributes['quantity'] <= 0 || $productDetailsIncludingAttributes['quantity'] < $qty) {
				return false;
			}

			$name = $productDetailsIncludingAttributes['name'];

			if(isset($productDetailsIncludingAttributes['attributes']) && $productDetailsIncludingAttributes['attributes']) {

				foreach($productDetailsIncludingAttributes['attributes'] as $attribute) {

					$name .= ' - ' . $attribute['name'];
				}
			}

			return [
				'link'         => $productDetailsIncludingAttributes['link'],
				'name'         => $name,
				'total'        => $productDetailsIncludingAttributes['price_tax_exc'],
				'id'           => $productDetailsIncludingAttributes['id_product_attribute']
					? $productDetailsIncludingAttributes['id_product'] . ',' . $productDetailsIncludingAttributes['id_product_attribute']
					: $productDetailsIncludingAttributes['id_product'],
				'categoryName' => $productDetailsIncludingAttributes['category_name'],
				'rate'         => $productDetailsIncludingAttributes['rate'],
			];
		}

		if($product->quantity <= 0) {
			return false;
		}

		$name = $product->name;

		if(isset($product->attributes) && $product->attributes) {

			foreach($product->attributes as $attribute) {

				$name .= ' - ' . $attribute['name'];
			}
		}

		$category = new Category((int) $product->id_category_default, $this->context->language->id);

		return [
			'link'         => $this->context->link->getProductLink($product->id),
			'name'         => $name,
			'total'        => $product->price,
			'id'           => isset($product->id_product_attribute) && $product->id_product_attribute
				? $product->id_product . ',' . $product->id_product_attribute
				: $product->id,
			'categoryName' => $category->name,
			'rate'         => $product->tax_rate,
		];
	}

	/**
	 * @param string $link
	 * @param string $name
	 * @param float  $total
	 * @param string $quantity
	 * @param string $id
	 * @param string $categoryId
	 * @param string $rate
	 *
	 * @return string
	 */
	public function prepareProductAvailabilityBody($link, $name, $total, $quantity, $id, $categoryId, $rate)
	{

		$availability = new \Leasenow\Payment\Availability();

		$availability->addItem(
			$link,
			$name,
			$total,
			$quantity,
			$id,
			$categoryId,
			$rate
		);

		return $this->prepareAvailabilityBody($availability);
	}

	/**
	 * @param array  $leasing
	 * @param string $scale
	 * @param bool   $cart
	 *
	 * @return mixed
	 */
	private function renderButton($leasing, $scale, $cart = false)
	{

		$leasing = $leasing['body'];

		$isEveryProductAvailable = \Leasenow\Payment\Util::isEveryProductAvailable($leasing);

		$tooltipDisplay = Leasenow\Payment\Util::displayTooltip($leasing, $isEveryProductAvailable);

		$array = [
			'leasenow_nofollow'                => Configuration::get('LEASENOW_REL_NO_FOLLOW'),
			'leasenow_loading_gif'             => $this->getImage('loading.gif'),
			'is_ps_17'                         => self::isPs17(),
			'leasenow_cart_id'                 => $this->context->cart->id,
			'leasenow_availability_controller' => $this->context->link->getModuleLink('leasenow', 'availability'),
			'leasenow_button_scale'            => $scale,
			'leasenow_availability'            => $leasing['availability'],
			'leasenow_missing_amount'          => 0,
			'leasenow_code'                    => '',
			'leasenow_redirect_url'            => isset($leasing['redirectUrl']) && $leasing['redirectUrl']
				? $leasing['redirectUrl']
				: '',
			'leasenow_image_url'               => $leasing['imageUrl'],
			'leasenow_display'                 => $leasing['availability'] || $tooltipDisplay,
			'leasenow_tooltip_display'         => $tooltipDisplay,
		];

		$this->context->smarty->assign($array);

		if(!$leasing['availability'] && !$isEveryProductAvailable) {

			return $this->fetchTemplate('leasenowButton.tpl');
		}

		if($tooltipDisplay) {

			if($leasing['missingNetAmount'] === $leasing['minimalNetAmount']) {
				return $this->fetchTemplate('leasenowButton.tpl');
			}

			if((isset($leasing['missingNetAmount']) && $leasing['missingNetAmount']) && $leasing['missingNetAmount'] > 0) {
				$array['leasenow_missing_amount'] = number_format($leasing['missingNetAmount'], 2) . ' ' . $leasing['currencyIsoName'];
			}
		}

		$this->context->smarty->assign($array);
		$this->fetchTemplate('leasenowButton.tpl');

		return $this->fetchTemplate('leasenowButton.tpl');
	}

	/**
	 * @param array $params
	 */
	public function hookDisplayProductButtons($params)
	{
		return $this->leasingProductPage($params);
	}

	/**
	 * @param array $params
	 *
	 * @throws Exception
	 */
	public function hookDisplayShoppingCartFooter($params)
	{

		if(!Configuration::get('LEASENOW_BUTTON_CART')) {
			return false;
		}

		$products = $params['cart']->getProducts(true);

		if(!$products) {
			return false;
		}

		$leasing = $this->getLeasing(
			$this->prepareCartAvailabilityBody($params['cart'])
		);

		if(!$leasing['success']) {
			return $this->renderButton([], Configuration::get('LEASENOW_BUTTON_CART_SCALE'), true);
		}

		return $this->renderButton($leasing, Configuration::get('LEASENOW_BUTTON_CART_SCALE'));
	}

	/**
	 * @return null
	 */
	public function hookPaymentReturn()
	{

		return null;
	}

}
