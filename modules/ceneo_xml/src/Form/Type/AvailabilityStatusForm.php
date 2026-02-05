<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace CeneoXml\Form\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Utils\Helper;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;

class AvailabilityStatusForm extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $module = \Module::getInstanceByName('ceneo_xml');
        $labels = Helper::getSingleAvailabilitiesLabels((int) \Configuration::get('PS_STOCK_MANAGEMENT'), $module);
        $choices = Helper::transformArray($labels);

        $builder
            ->add('products', FormType\HiddenType::class)
            ->add('status', FormType\ChoiceType::class, [
                'label' => $module->l('Status'),
                'choices' => $choices,
                'attr' => [
                    'class' => 'c-select',
                ],
            ]);
    }
}
