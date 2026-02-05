<?php

/**
 * Class LeaseNowNotificationModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 * @property bool display_footer
 * @property bool display_header
 */
class LeaseNowAvailabilityModuleFrontController extends ModuleFrontController
{

	/**
	 * @var string
	 */
	const ACTION_ATTRIBUTE = 'attribute';

	/**
	 * @var string
	 */
	const ACTION_CART = 'cart';

	/**
	 * @var string
	 */
	const ACTION_SIMPLE = 'simple';

	/**
	 * Initialize controller.
	 *
	 * @see FrontController::init()
	 */
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		$this->display_footer = false;
		$this->display_header = false;

		parent::init();
	}

	/**
	 * @throws PrestaShopException
	 * @throws Exception
	 */
	public function process()
	{

		$action = Tools::getValue('action');

		$actions = [
			self::ACTION_ATTRIBUTE,
			self::ACTION_CART,
			self::ACTION_SIMPLE,
		];

		if(!in_array($action, $actions)) {
			$this->response();
		}

		switch($action) {
			case self::ACTION_ATTRIBUTE:

				$this->attribute();

				break;
			case self::ACTION_CART:

				$this->cart();
				break;

			case self::ACTION_SIMPLE:

				$this->simple();
				break;

			default:
				$this->response();
		}
	}

	/**
	 * @param bool  $success
	 * @param array $data
	 *
	 * @return void
	 */
	private function response($success = false, $status = '', $data = [])
	{

		header('Content-Type: application/json; charset=utf-8');
		$response = [
			'success' => $success,
		];

		if($status) {
			$response['status'] = $status;
		}

		if($data) {
			$response['data'] = $data;
		}

		echo json_encode($response);
		exit();
	}

	/**
	 * @return false|void
	 * @throws PrestaShopException
	 */
	private function attribute()
	{
		$idProduct = Tools::getValue('idProduct');
		$idAttribute = Tools::getValue('idAttribute');

		if(empty($idProduct) || empty($idAttribute)) {
			$this->response();
		}

		$product = new Product((int) $idProduct);

		if(!$product->id) {
			$this->response();
		}

		$id_lang = $this->context->language->id;
		$productCombinationList = $product->getAttributeCombinations((int) $id_lang, true);

		if(!$productCombinationList) {
			$this->response();
		}

		$productName = $this->getProductName($product);

		$combinationList = [];

		foreach($productCombinationList as $combination) {
			if($combination['id_product_attribute'] === $idAttribute) {
				$combinationList[] = $combination;
				$productName .= ' - ' . $combination['attribute_name'];
			}
		}

		if(!$combinationList) {
			$this->response();
		}

		$price = $product->price + $combinationList[0]['price'];

		$credentials = $this->module->getCredentials();

		if(!$credentials) {
			$this->response();
		}

		$category = new Category((int) $product->id_category_default, $id_lang);

		$quantity = 1;

		$ajaxQuantity = Tools::getValue('quantity');

		if($ajaxQuantity && is_int($ajaxQuantity)) {
			$quantity = $ajaxQuantity;
		}

		$leasing = $this->module->getLeasing(
			$this->module->prepareProductAvailabilityBody(
				$this->context->link->getProductLink(
					$product->id,
					null,
					null,
					null,
					$id_lang,
					null,
					$idAttribute,
					false,
					false,
					true
				),
				$productName,
				$price,
				$quantity,
				$product->id . ',' . $idAttribute,
				$category->name,
				Tax::getProductTaxRate($idProduct)
			)
		);

		if(!$leasing['success']) {
			return false;
		}

		$this->response(true, '', [
			'leasenow_redirect_url' => $leasing['body']['redirectUrl'],
			'leasenow_image_url'    => $leasing['body']['imageUrl'],
			'leasenow_button_scale' => Configuration::get('LEASENOW_BUTTON_PRODUCT_SCALE'),

		]);
	}

	/**
	 * @param object $product
	 *
	 * @return string
	 */
	private function getProductName($product)
	{

		return reset($product->name);
	}

	/**
	 * @return false|void
	 * @throws PrestaShopException
	 */
	private function simple()
	{
		$idProduct = Tools::getValue('idProduct');
		$quantity = Tools::getValue('quantity');

		if(empty($idProduct) || empty($quantity)) {
			$this->response();
		}

		$product = new Product((int) $idProduct);

		if(!$product->id) {
			$this->response();
		}

		$id_lang = $this->context->language->id;
		$category = new Category((int) $product->id_category_default, $id_lang);

		$leasing = $this->module->getLeasing(
			$this->module->prepareProductAvailabilityBody(
				$this->context->link->getProductLink($product->id),
				$this->getProductName($product),
				$product->price,
				$quantity,
				$product->id,
				$category->name,
				Tax::getProductTaxRate($idProduct)
			)
		);

		if(!$leasing['success']) {
			return false;
		}

		$this->response(true, '', [
			'leasenow_redirect_url' => $leasing['body']['redirectUrl'],
			'leasenow_image_url'    => $leasing['body']['imageUrl'],
			'leasenow_button_scale' => Configuration::get('LEASENOW_BUTTON_PRODUCT_SCALE'),

		]);
	}

	/**
	 * @return false|void
	 * @throws PrestaShopException
	 */
	private function cart()
	{
		$idCart = Tools::getValue('idCart');

		if(empty($idCart)) {
			$this->response();
		}

		$cart = new Cart($idCart);

		if(!$cart->id) {
			$this->response();
		}

		if(Order::getOrderByCartId($idCart)) {
			$this->response();
		}

		$products = $cart->getProducts(true);

		if(!$products) {
			return false;
		}

		$leasing = $this->module->getLeasing(
			$this->module->prepareCartAvailabilityBody($cart)
		);

		if(!$leasing['success']) {
			return false;
		}

		$leasing = $leasing['body'];

		$arr = [
			'leasenow_redirect_url' => isset($leasing['redirectUrl']) && $leasing['redirectUrl']
				? $leasing['redirectUrl']
				: '',
			'leasenow_image_url'    => $leasing['imageUrl'],
			'leasenow_button_scale' => Configuration::get('LEASENOW_BUTTON_CART_SCALE'),
		];

		if(Leasenow\Payment\Util::displayTooltip($leasing, Leasenow\Payment\Util::isEveryProductAvailable($leasing))
			&& ($leasing['missingNetAmount'] !== $leasing['minimalNetAmount'])) {

			$arr['leasenow_missing_amount'] = number_format($leasing['missingNetAmount'], 2) . ' ' . $leasing['currencyIsoName'];
		}

		$this->response(true, '', $arr);
	}
}
