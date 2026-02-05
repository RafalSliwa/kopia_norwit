<?php
/**
 * 2007-2018 PrestaShop.
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace CeneoXml\Controller\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Form\Type\MappingForm;
use CeneoXml\Model\AttributeMapping;
use CeneoXml\Model\Mapping;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

class CeneoXmlCategoryMappingController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->module = \Module::getInstanceByName('ceneo_xml');
    }

    public function updateAction(Request $request)
    {
        $errors = [];

        $form = $this->createForm(MappingForm::class);
        $form->handleRequest($request);
        $repository = $this->get('mapping');
        $id_shop = \Context::getContext()->shop->id ? \Context::getContext()->shop->id : 1;
        $mapping = new Mapping($id_shop);

        $formData = [
            'id_mapping' => $mapping->id_mapping,
        ];

        $categories = json_decode($mapping->categories);
        if ($categories) {
            foreach ($categories as $key => $value) {
                $formData['category_' . $key] = (int) $value;
            }
        }

        $form = $this->createForm(MappingForm::class, $formData);

        return $this->render('@Modules/ceneo_xml/templates/admin/mapping.html.twig', [
            'layoutTitle' => $this->module->l('Category Mapping'),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtonsAdd($mapping->id_mapping),
            'help_link' => false,
            'mappingForm' => $form->createView(),
        ]);
    }

    public function updateActionSave(Request $request)
    {
        $errors = [];

        $form = $this->createForm(MappingForm::class);
        $form->handleRequest($request);

        $formData = \Tools::getValue('mapping_form');

        $repository = $this->get('mapping');
        $id_shop = \Context::getContext()->shop->id ? \Context::getContext()->shop->id : 1;
        $mapping = new Mapping($id_shop);
        if (empty($mapping->id_mapping)) {
            \Db::getInstance()->execute('insert into ' . _DB_PREFIX_ . 'ceneo_xml_category_mapping values (' . $id_shop . ', "[]")');
            \Db::getInstance()->execute('insert into ' . _DB_PREFIX_ . 'ceneo_xml_attribute_mapping values (' . $id_shop . ', "[]")');
            $mapping = new Mapping($id_shop);
        }

        if (!empty($mapping->id_mapping)) {
            $categories = [];
            foreach ($formData as $key => $value) {
                if (strpos($key, '_') === false) {
                    continue;
                }
                $key = (int) explode('_', $key)[1];
                $categories[$key] = (int) $value;
            }

            $formData['categories'] = $categories;
            $mapping->categories = json_encode($categories);

            if (!$mapping->update()) {
                $this->addFlash(
                    'error',
                    $this->module->l('Mapping could not be edited.')
                );

                return $this->redirectToRoute('ceneo_xml_category_mapping');
            }

            $attributes = [];
            $fields = $_POST;

            $id_shop = \Context::getContext()->shop->id ? \Context::getContext()->shop->id : 1;
            $mapping_attributes = new AttributeMapping($id_shop);
            foreach ($fields as $key => $value) {
                if (strpos($key, 'attribute-') !== false) {
                    $shop_cat = explode('-', $key)[1];
                    $category = explode('-', $key)[2];
                    $name = str_replace('attribute-' . $shop_cat . '-' . $category . '-', '', $key);
                    $attributes[$category][$shop_cat][] = ['ceneo' => $name, 'shop' => $value];
                }
            }
            $mapping_attributes->attributes = json_encode($attributes);
            $mapping_attributes->save();

            $this->addFlash('success', $this->module->l('Mapping saved.'));

            return $this->redirectToRoute('ceneo_xml_category_mapping');
        }
    }

    private function getToolbarButtonsAdd()
    {
        return [];
    }
}
