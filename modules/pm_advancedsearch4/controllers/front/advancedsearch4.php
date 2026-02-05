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

if (!defined('_PS_VERSION_')) {
    exit;
}
use AdvancedSearch\Core;
use AdvancedSearch\Models\Search;
use AdvancedSearch\SearchEngineUtils;
use AdvancedSearch\Traits\SupportsStepSearch;
use AdvancedSearch\AdvancedSearchProductListingFrontController;
if (!defined('_PS_VERSION_')) {
    exit;
}
class pm_advancedsearch4advancedsearch4ModuleFrontController extends AdvancedSearchProductListingFrontController
{
    use SupportsStepSearch;
    use \AdvancedSearch\Traits\StepSearchTrait;
    public $display_column_left = true;
    public $display_column_right = true;
    protected $display_header = true;
    protected $display_footer = true;
    protected $next_id_criterion_group = false;
    protected $reset = false;
    protected $reset_group;
    public function __construct()
    {
        parent::__construct();
        if (Tools::getValue('ajaxMode')) {
            $this->ajax = true;
            $this->display_column_left = false;
            $this->display_column_right = false;
            $this->display_header = false;
            $this->display_footer = false;
        }
    }
    public function init()
    {
        $this->idSearch = (int)Tools::getValue('id_search');
        $this->searchInstance = new Search((int)$this->idSearch, (int)$this->context->language->id);
        if (!Validate::isLoadedObject($this->searchInstance)) {
            Tools::redirect('404');
        }
        if (!$this->searchInstance->isAssociatedToShop()) {
            Tools::redirect('404');
        }
        if (!$this->searchInstance->active) {
            if (!headers_sent()) {
                header('Status: 307 Temporary Redirect', false, 307);
            }
            Tools::redirect('index');
        }
        parent::init();
        $this->setSEOTags();
        $this->setCriterions();
        $this->setProductFilterList();
        $this->processActions();
        if (empty($this->ajax)) {
            Tools::redirect($this->getCanonicalURL());
            die;
        } else {
            if (!headers_sent()) {
                header('Link: <' . $this->getCanonicalURL() . '>; rel="canonical"', true);
            }
            $this->doProductSearch('');
        }
    }
    protected function setSEOTags()
    {
        $this->idSeo = Tools::getValue('id_seo', false);
        if (Tools::getValue('ajaxMode')) {
            if (!headers_sent()) {
                header('X-Robots-Tag: noindex, nofollow', true);
            }
            $this->context->smarty->assign([
                'nofollow' => true,
                'nobots' => true,
            ]);
        } elseif (Tools::getValue('only_products')) {
            if ($this->idSeo && (Tools::getValue('p') || Tools::getValue('n'))) {
                if (!headers_sent()) {
                    header('X-Robots-Tag: noindex, follow', true);
                }
            } else {
                if (!headers_sent()) {
                    header('X-Robots-Tag: noindex, nofollow', true);
                }
            }
            $this->context->smarty->assign([
                'nofollow' => true,
                'nobots' => true,
            ]);
        }
    }
    protected function setCriterions()
    {
        if (empty($this->getSearchEngine())) {
            return;
        }
        $currentSearchInstance = $this->getSearchEngine();
        $this->criterionsList = Tools::getValue('as4c', []);
        if (is_array($this->criterionsList)) {
            $this->criterionsList = SearchEngineUtils::cleanArrayCriterion($this->criterionsList);
        } else {
            $this->criterionsList = [];
        }
        $this->criterionsListHidden = Tools::getValue('as4c_hidden', []);
        if (is_array($this->criterionsListHidden)) {
            $this->criterionsListHidden = SearchEngineUtils::cleanArrayCriterion($this->criterionsListHidden);
        } else {
            $this->criterionsListHidden = [];
        }
        $this->reset = (bool)Tools::getValue('reset', false);
        $this->reset_group = (int)Tools::getValue('reset_group', false);
        if (!empty($this->reset)) {
            $this->criterionsList = [];
        }
        if ($this->reset_group && isset($this->criterionsList[$this->reset_group])) {
            unset($this->criterionsList[$this->reset_group]);
            if (self::supportsStepSearch() && $currentSearchInstance->step_search) {
                $this->resetNextCriterionGroups();
            }
        }
        if ($currentSearchInstance->filter_by_emplacement) {
            $criterionsFromEmplacement = SearchEngineUtils::getCriteriaFromEmplacement($currentSearchInstance->id, $currentSearchInstance->id_category_root);
            foreach ($criterionsFromEmplacement as $idCriterionGroup => $idCriterionList) {
                if (!isset($this->criterionsList[$idCriterionGroup])) {
                    $this->criterionsList[$idCriterionGroup] = $idCriterionList;
                } elseif (is_array($this->criterionsList[$idCriterionGroup]) && !count($this->criterionsList[$idCriterionGroup])) {
                    $this->criterionsList[$idCriterionGroup] = $idCriterionList;
                }
            }
        }
        $this->next_id_criterion_group = (int)Tools::getValue('next_id_criterion_group', false);
        $this->context->cookie->{'next_id_criterion_group_' . (int)$this->idSearch} = $this->next_id_criterion_group;
    }
    public function processActions()
    {
        if (Tools::getValue('setHideCriterionStatus')) {
            ob_end_clean();
            $this->idSearch = (int)Tools::getValue('id_search');
            $state = (int)Tools::getValue('state') > 0;
            if (isset($this->context->cookie->hidden_criteria_state)) {
                $hidden_criteria_state = Core::decodeCriteria($this->context->cookie->hidden_criteria_state);
                if (is_array($hidden_criteria_state)) {
                    $hidden_criteria_state[$this->idSearch] = $state;
                } else {
                    $hidden_criteria_state = [];
                }
                $this->context->cookie->hidden_criteria_state = Core::encodeCriteria($hidden_criteria_state);
            } else {
                $this->context->cookie->hidden_criteria_state = Core::encodeCriteria([$this->idSearch => $state]);
            }
            die;
        }
    }
}
