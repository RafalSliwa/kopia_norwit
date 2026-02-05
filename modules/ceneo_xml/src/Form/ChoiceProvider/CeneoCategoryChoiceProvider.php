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

namespace CeneoXml\Form\ChoiceProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\LinkList\Form\ChoiceProvider\AbstractDatabaseChoiceProvider;

/**
 * Class CeneoCategoryChoiceProvider.
 */
final class CeneoCategoryChoiceProvider extends AbstractDatabaseChoiceProvider
{
    /**
     * @return mixed
     */
    public function getChoices()
    {
        $module = \Module::getInstanceByName('ceneo_xml');
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('c.id_ceneo_category, c.path')
            ->from($this->dbPrefix . 'ceneo_xml_category', 'c');

        $formats = $qb->execute()->fetchAll();
        $choices = [];
        $choices[$module->l('-- Choose --')] = 0;
        foreach ($formats as $format) {
            $choices[$format['path']] = $format['id_ceneo_category'];
        }

        return $choices;
    }
}
