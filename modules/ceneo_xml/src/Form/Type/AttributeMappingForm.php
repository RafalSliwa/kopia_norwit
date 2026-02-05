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

namespace CeneoXml\Form\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AttributeMappingForm extends CommonAbstractType
{
    private $locales;
    private $sourceAttributesChoices;

    public function __construct(
        $locales,
        array $sourceAttributesChoices
    ) {
        $this->locales = $locales;
        $this->sourceAttributesChoices = $sourceAttributesChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $all_attributes = \AttributeGroup::getAttributesGroups(1);
        $shop_attributes = [];
        foreach ($all_attributes as $group) {
            foreach (\AttributeGroup::getAttributes(1, $group['id_attribute_group']) as $attr) {
                $shop_attributes[] = ['id_attribute' => $attr['id_attribute'], 'name' => $group['name'] . ': ' . $attr['name']];
            }
        }

        $all_features = \Feature::getFeatures(1);
        $shop_features = [];
        foreach ($all_features as $feature) {
            foreach (\FeatureValue::getFeatureValuesWithLang(1, $feature['id_feature']) as $fv) {
                $shop_features[] = ['id_feature_value' => $fv['id_feature_value'], 'name' => $feature['name'] . ': ' . $fv['value']];
            }
        }

        foreach ($shop_attributes as $i => $attribute) {
            if ($i > 5) {
                break;
            }
            $builder
                ->add('attribute_' . $attribute['id_attribute'], FormType\ChoiceType::class, [
                    'choices' => $this->sourceAttributesChoices,
                    'multiple' => false,
                    'attr' => [],
                    'label' => $attribute['name'] . ' (ID: ' . $attribute['id_attribute'] . ')',
                    'required' => true,
                ]);
        }

        foreach ($shop_features as $i => $feature) {
            if ($i > 5) {
                break;
            }
            $builder
                ->add('featurevalue_' . $feature['id_feature_value'], FormType\ChoiceType::class, [
                    'choices' => $this->sourceAttributesChoices,
                    'multiple' => false,
                    'attr' => [],
                    'label' => $feature['name'] . ' (ID: ' . $feature['id_feature_value'] . ')',
                    'required' => true,
                ]);
        }

        $builder
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ]);
    }
}
