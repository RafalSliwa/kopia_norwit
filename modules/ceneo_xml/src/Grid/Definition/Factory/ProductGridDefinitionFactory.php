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

namespace CeneoXml\Grid\Definition\Factory;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Grid\Column\Type\HtmlColumnCeneo;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ModalFormSubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'product';

    public function __construct()
    {
        parent::__construct();
        $this->module = \Module::getInstanceByName('ceneo_xml');
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->module->l('Products');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_product',
                    ])
            )
            ->add(
                (new DataColumn('id_product'))
                    ->setName($this->module->l('ID'))
                    ->setOptions([
                        'field' => 'id_product',
                    ])
            )
            ->add(
                (new LinkColumn('name'))
                    ->setName($this->module->l('Name'))
                    ->setOptions([
                        'field' => 'name',
                        'route' => 'admin_product_form',
                        'route_param_name' => 'id',
                        'route_param_field' => 'id_product',
                    ])
            )
            ->add(
                (new DataColumn('reference'))
                    ->setName($this->module->l('Reference'))
                    ->setOptions([
                        'field' => 'reference',
                    ])
            )
            ->add(
                (new DataColumn('category'))
                    ->setName($this->module->l('Category'))
                    ->setOptions([
                        'field' => 'category',
                    ])
            )
            ->add(
                (new LinkColumn('price_tax_excluded'))
                    ->setName($this->module->l('Price'))
                    ->setOptions([
                        'field' => 'price_tax_excluded',
                        'route' => 'admin_product_form',
                        'route_param_name' => 'id',
                        'route_param_field' => 'id_product',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new HtmlColumnCeneo('active'))
                    ->setName($this->module->l('Visibility in the shop'))
                    ->setOptions([
                        'field' => 'active',
                    ])
            )
            ->add((new ActionColumn('actions'))
            ->setName('')
            );
    }

    public function getBadgeType($value)
    {
        return $value == 1 ? 'success' : 'danger';
    }

    public function getBadgeText($value)
    {
        return $value == 1 ? $this->module->l('Yes') : $this->module->l('No');
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_product', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->module->l('ID'),
                        ],
                    ])
                    ->setAssociatedColumn('id_product')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->module->l('Name'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('price_tax_excluded', NumberMinMaxFilterType::class, [
                    'min_field_options' => [
                        'attr' => [
                            'placeholder' => $this->module->l('Min'),
                        ],
                    ],
                    'max_field_options' => [
                        'attr' => [
                            'placeholder' => $this->module->l('Max'),
                        ],
                    ],
                ]))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->module->l('Price'),
                        ],
                    ])
                    ->setAssociatedColumn('price_tax_excluded')
            )
            ->add(
                (new Filter('reference', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->module->l('Reference'),
                        ],
                    ])
                    ->setAssociatedColumn('reference')
            )
            ->add(
                (new Filter('active', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->module->l('Yes') => 1,
                            $this->module->l('No') => 0,
                        ],
                        'attr' => [
                            'placeholder' => $this->module->l('Active'),
                        ],
                    ])
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'ceneo_xml_products',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('ceneo_xml_products_enable_feed'))
                    ->setName($this->module->l('Enable in feed Ceneo'))
                    ->setOptions([
                        'submit_route' => 'ceneo_xml_products_enable_feed',
                    ])
            )
            ->add(
                (new SubmitBulkAction('ceneo_xml_products_disable_feed'))
                    ->setName($this->module->l('Disable in feed Ceneo'))
                    ->setOptions([
                        'submit_route' => 'ceneo_xml_products_disable_feed',
                    ])
            )
            ->add(
                (new ModalFormSubmitBulkAction('ceneo_xml_products_visibility_on_buy_now'))
                    ->setName($this->module->l('Change the visibility on Ceneo Buy Now'))
                    ->setOptions([
                        'submit_route' => 'ceneo_xml_products_visibility_on_buy_now',
                        'modal_id' => 'changeVisibilityBuyNowModal',
                    ])
            )
            ->add(
                (new ModalFormSubmitBulkAction('ceneo_xml_products_availability_status'))
                    ->setName($this->module->l('Change product availability on Ceneo'))
                    ->setOptions([
                        'submit_route' => 'ceneo_xml_products_availability_status',
                        'modal_id' => 'changeAvailabilityStatusModal',
                    ])
            );
    }
}
