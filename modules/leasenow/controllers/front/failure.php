<?php

/**
 * Class LeaseNowFailureModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class LeaseNowFailureModuleFrontController extends ModuleFrontController
{

	/**
	 * Initialize controller.
	 *
	 * @throws PrestaShopException
	 * @see FrontController::init()
	 */
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		parent::init();
	}

	/**
	 * @throws PrestaShopException
	 */
	public function initContent()
	{
		parent::initContent();
		$this->context->smarty->assign('ga_key', Configuration::get('LEASENOW_GA_KEY'));
		$this->setTemplate(LeaseNow::buildTemplatePath('failure', 'front'));
	}
}
