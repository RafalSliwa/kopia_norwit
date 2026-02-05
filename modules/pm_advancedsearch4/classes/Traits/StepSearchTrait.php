<?php
/**
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

namespace AdvancedSearch\Traits;
if (!defined('_PS_VERSION_')) {
    exit;
}
use Validate;
use AdvancedSearch\Core;
use AdvancedSearch\Models\CriterionGroup;
trait StepSearchTrait
{
    protected function isFirstStep($row, $row2, $selected_criterion_groups, $prev_id_criterion_group, $result, $key, $key2)
    {
        return !(!$row['step_search'] || ($row['step_search'] && $row['step_search_next_in_disabled']) || (
            $row['step_search'] && (
                $key2 == 0 || (
                    is_array($selected_criterion_groups) && Core::isFilledArray($selected_criterion_groups) && (
                        in_array($row2['id_criterion_group'], $selected_criterion_groups)
                        || ($prev_id_criterion_group && in_array($prev_id_criterion_group, $selected_criterion_groups))
                        || !count($result[$key]['criterions'][$prev_id_criterion_group])
                    )
                )
            )
        ));
    }
    protected function isStepSearchSliderUnavailable($row, $result, $key, $row2)
    {
        return $row['step_search']
            && $result[$key]['criterions'][$row2['id_criterion_group']][0]['min'] == 0
            && $result[$key]['criterions'][$row2['id_criterion_group']][0]['max'] == 0;
    }
    protected function setStepSearchType(&$params)
    {
        $params['obj']->search_type = 2;
    }
    protected function resetNextCriterionGroups()
    {
        $criterionsGroups = CriterionGroup::getCriterionsGroupsFromIdSearch((int)$this->idSearch, (int)$this->context->language->id, false);
        if (Core::isFilledArray($criterionsGroups)) {
            $deleteAfter = false;
            foreach ($criterionsGroups as $criterionGroup) {
                if ((int)$criterionGroup['id_criterion_group'] == $this->reset_group) {
                    $deleteAfter = true;
                }
                if ($deleteAfter && isset($this->criterionsList[(int)$criterionGroup['id_criterion_group']])) {
                    unset($this->criterionsList[(int)$criterionGroup['id_criterion_group']]);
                }
            }
        }
    }
    protected static function prepareNewCriterionGroupForStepSearchIndexation(&$objAdvancedSearchCriterionGroupClass)
    {
        if ($objAdvancedSearchCriterionGroupClass->criterion_group_type == 'category' && !empty($objAdvancedSearchCriterionGroupClass->id_criterion_group_linked) && !Validate::isLoadedObject($objAdvancedSearchCriterionGroupClass)) {
            $objAdvancedSearchCriterionGroupClass->only_children = 1;
        }
    }
}
