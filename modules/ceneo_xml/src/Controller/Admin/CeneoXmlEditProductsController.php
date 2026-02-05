<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace CeneoXml\Controller\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Form\Type\AvailabilityStatusForm;
use CeneoXml\Form\Type\VisibilityForm;
use CeneoXml\Grid\Definition\Factory\ProductGridDefinitionFactory;
use CeneoXml\Grid\Filters\ProductFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CeneoXmlEditProductsController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->module = \Module::getInstanceByName('ceneo_xml');
    }

    public function indexAction(ProductFilters $filters)
    {
        $quoteGridFactory = $this->get('ceneo_xml.grid.factory.products');
        $quoteGrid = $quoteGridFactory->getGrid($filters);
        $changeVisibilityForm = $this->createForm(VisibilityForm::class);
        $changeAvailabilityStatusForm = $this->createForm(AvailabilityStatusForm::class);

        return $this->render(
            '@Modules/ceneo_xml/views/templates/admin/index.html.twig',
            [
                'changeVisibilityForm' => $changeVisibilityForm->createView(),
                'changeAvailabilityStatusForm' => $changeAvailabilityStatusForm->createView(),
                'enableSidebar' => true,
                'layoutTitle' => $this->module->l('Product listing'),
                'quoteGrid' => $this->presentGrid($quoteGrid),
                'ps8' => version_compare(_PS_VERSION_, '8.0.0', '>='),
            ]
        );
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('ceneo_xml.grid.definition.factory.products'),
            $request,
            ProductGridDefinitionFactory::GRID_ID,
            'ceneo_xml_products_search'
        );
    }

    public function updateProductFeed($enable = true)
    {
        $productRepository = $this->get('ceneo_xml.repository.product');
        $status = $enable ? 0 : 1; // 0 for enable, 1 for disable

        foreach ($this->prepareBulk() as $id) {
            $productRepository->getByIdProduct($id, 0);
            $productRepository->setExcludeByIdProduct($id, null, $status);
        }

        $message = $enable ? 'Products have been enabled successfully.' : 'Products have been disabled successfully.';
        $this->addFlash('success', $this->module->l($message));

        return $this->redirectToRoute('ceneo_xml_products');
    }

    public function updateProductVisibility($elements, $status)
    {
        $productRepository = $this->get('ceneo_xml.repository.product');

        foreach ($this->getElementsArray($elements) as $id) {
            $productRepository->getByIdProduct($id, 0);
            $productRepository->setBasketByIdProduct($id, $status);
        }

        $message = $status ? 'Products have been visible successfully.' : 'Products have been hidden successfully.';
        $this->addFlash('success', $this->module->l($message));

        return $this->redirectToRoute('ceneo_xml_products');
    }

    public function updateProductAvailabilityStatus($elements, $status)
    {
        $productRepository = $this->get('ceneo_xml.repository.product');

        foreach ($this->getElementsArray($elements) as $id) {
            $productRepository->getByIdProduct($id, 0);
            $productRepository->setAvailByIdProduct($id, $status);
        }

        $message = 'Products have been availability status successfully.';
        $this->addFlash('success', $this->module->l($message));

        return $this->redirectToRoute('ceneo_xml_products');
    }

    public function getElementsArray($elements)
    {
        return explode(',', $elements);
    }

    public function enableProductFeed()
    {
        return $this->updateProductFeed(true);
    }

    public function disableProductFeed()
    {
        return $this->updateProductFeed(false);
    }

    public function changeProductStatus($statusKey)
    {
        if (\Tools::getValue('visibility_form')) {
            $value = \Tools::getValue('visibility_form');
        } elseif (\Tools::getValue('availability_status_form')) {
            $value = \Tools::getValue('availability_status_form');
        }

        if (!isset($value['products'])) {
            return null;
        }

        if ($statusKey === 'visibility') {
            return $this->updateProductVisibility($value['products'], $value['visibility']);
        } elseif ($statusKey === 'availability') {
            return $this->updateProductAvailabilityStatus($value['products'], $value['status']);
        }

        return null;
    }

    public function changeVisibilityProducts()
    {
        return $this->changeProductStatus('visibility');
    }

    public function changeAvailabilityStatusProducts()
    {
        return $this->changeProductStatus('availability');
    }

    public function prepareBulk()
    {
        return \Tools::getValue('product_bulk');
    }
}
