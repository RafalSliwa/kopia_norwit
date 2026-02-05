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

namespace CeneoXml\Form\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Model\Mapping;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class MappingForm extends CommonAbstractType
{
    private $locales;
    private $sourceCategoriesChoices;

    public function __construct(
        $locales,
        array $sourceCategoriesChoices
    ) {
        $this->locales = $locales;
        $this->sourceCategoriesChoices = $sourceCategoriesChoices;
    }

    public static function getChildren(&$tree, $id_parent, $name)
    {
        $children = \Category::getCategories(\Context::getContext()->language->id, true, false, ' and id_parent = ' . (int) $id_parent);
        foreach ($children as &$child) {
            $child['name'] = $name . ' > ' . $child['name'];
            $tree[] = $child;
            self::getChildren($tree, $child['id_category'], $child['name']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $module = \Module::getInstanceByName('ceneo_xml');
        $shop_categories = \Category::getCategories(\Context::getContext()->language->id, true, false, ' and level_depth < 3 and is_root_category = 0 and id_parent > 0 ');
        $tree = [];
        foreach ($shop_categories as $category) {
            $tree[] = $category;
            $id = $category['id_category'];
            $children = \Category::getCategories(\Context::getContext()->language->id, true, false, ' and id_parent = ' . $id);
            foreach ($children as &$child) {
                $child['name'] = $category['name'] . ' > ' . $child['name'];
                $tree[] = $child;
                self::getChildren($tree, $child['id_category'], $child['name']);
            }
        }

        $mapping = new Mapping(\Context::getContext()->shop->id);
        $preselect = json_decode($mapping->categories, true);

        $ceneo_categories = \Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'ceneo_xml_category');
        $ceneo_names = [];
        if ($ceneo_categories) {
            foreach ($ceneo_categories as $row) {
                $ceneo_names[$row['id_ceneo_category']] = $row['path'];
            }
        }

        foreach ($tree as $i => $category) {
            if (isset($preselect[$category['id_category']]) && $preselect[$category['id_category']]) {
                $values = [$ceneo_names[$preselect[$category['id_category']]] => $preselect[$category['id_category']]];
            } else {
                $values = [$module->l('-- Choose --') => 0];
            }

            $builder
                ->add('category_' . $category['id_category'], FormType\ChoiceType::class, [
                    'choices' => $values,
                    'multiple' => false,
                    'attr' => [],
                    'label' => $category['name'] . ' (ID: ' . $category['id_category'] . ')',
                    'required' => true,
                ]);
        }
        $builder
            ->add('save', SubmitType::class, [
                'label' => \Context::getContext()->getTranslator()->trans('Save', [], 'Admin.Actions'),
            ]);
    }
}
