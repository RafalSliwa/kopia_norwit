<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryTreeGenerator
{
    private $htmlCategory = '';
    private $recurseDone = [];
    private $isGoogle = false;
    private $isGender = false;
    private $genderValues = [];

    protected $context;
    protected $shotLang = 0;
    protected $moduleImgPath = '';
    protected $googleCategoriesMap = [];
    protected $feedId = 0;
    protected $list = [];

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @param int $shotLang
     */
    public function setShotLang($shotLang)
    {
        $this->shotLang = $shotLang;
    }

    /**
     * @param string $moduleImgPath
     */
    public function setModuleImgPath($moduleImgPath)
    {
        $this->moduleImgPath = $moduleImgPath;
    }

    /**
     * @param array $googleCategoriesMap
     */
    public function setGoogleCategoriesMap($googleCategoriesMap)
    {
        $this->googleCategoriesMap = $googleCategoriesMap;
    }

    /**
     * @param int $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    public function save($feedId, $genderCategories)
    {
        Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_xml_gender_map WHERE feed_id = '.(int)$feedId);

        if (empty($genderCategories)) {
            return true;
        }

        foreach ($genderCategories as $cateogyrId => $name) {
            if (empty($name)) {
                continue;
            }

            Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blmod_xml_gender_map
                (`feed_id`, `category_id`, `name`)
                VALUE
                ("'.(int)$feedId.'", "'.(int)$cateogyrId.'", "'.pSQL($name).'")');
        }

        return true;
    }

    public function get($feedId)
    {
        $categories = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'blmod_xml_gender_map WHERE feed_id = '.(int)$feedId);
        $values = [];

        foreach ($categories as $c) {
            $values[$c['category_id']] = $c['name'];
        }

        return $values;
    }

    public function categoriesTree($selected = [], $shopLang = 0, $isGender = false, $isGoogleCat = false)
    {
        $this->list = [];
        $this->recurseDone = [];
        $langId = !empty($shopLang) ? $shopLang : $this->context->language->id;
        $this->htmlCategory = '';
        $selected = !empty($selected) ? explode(',', $selected) : [];
        $categories = Category::getCategories($langId, false);
        $this->isGender = $isGender;
        $this->isGoogle = $isGoogleCat;

        if ($this->isGender) {
            $this->genderValues = $this->get($this->feedId);
        }

        if (!empty($categories)) {
            $categories[0][1] = isset($categories[0][1]) ? $categories[0][1] : false;
            $this->recurseCategoryForIncludePref2(null, $categories, $categories[0][1], 1, null, $selected);
        }

        return $this->list;
    }

    protected function recurseCategoryForIncludePref2($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = null, $selected = [])
    {
        if (!isset($this->recurseDone[$current['infos']['id_parent']])) {
            $this->recurseDone[$current['infos']['id_parent']] = 0;
        }

        $this->recurseDone[$current['infos']['id_parent']] += 1;

        $categories[$current['infos']['id_parent']] = isset($categories[$current['infos']['id_parent']]) ? $categories[$current['infos']['id_parent']] : false;

        $todo = count($categories[$current['infos']['id_parent']]);
        $doneC = $this->recurseDone[$current['infos']['id_parent']];

        $level = $current['infos']['level_depth'] + 1;
        $img = $level == 1 ? 'lv1.png' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.png';
        $levelImagePath = $this->moduleImgPath.$img;
        $levelDivClass1 = '';
        $levelDivClass2 = '';

        if ($level > 5) {
            $levelDivClass1 = (($level - 2) * 24) - 12;
            $levelDivClass2 = $todo == $doneC ? 'f' : 'b';
        }

        $this->list[] = [
            'is_checked' => in_array($id_category, $selected),
            'id' => $id_category,
            'level' => $level,
            'levelImagePath' => $levelImagePath,
            'name' => stripslashes($current['infos']['name']),
            'levelDivClass1' => $levelDivClass1,
            'levelDivClass2' => $levelDivClass2,
            'genderValue' => !empty($this->genderValues[$id_category]) ? htmlspecialchars($this->genderValues[$id_category], ENT_QUOTES) : '',
            'googleCatValue' =>!empty($this->googleCategoriesMap[$id_category]) ? $this->googleCategoriesMap[$id_category]['name'] : '',
        ];

        if (isset($categories[$id_category])) {
            foreach ($categories[$id_category] as $key => $row) {
                if ($key != 'infos') {
                    $this->recurseCategoryForIncludePref2($indexedCategories, $categories, $categories[$id_category][$key], $key, null, $selected);
                }
            }
        }
    }

    protected function l($string)
    {
        return $string;
    }
}
