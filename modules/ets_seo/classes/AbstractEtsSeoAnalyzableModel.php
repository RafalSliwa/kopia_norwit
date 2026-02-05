<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/traits/EtsSeoTranslationTrait.php';
require_once __DIR__ . '/traits/EtsSeoGetModuleTrait.php';
require_once __DIR__ . '/interfaces/EtsSeoAnalyzeModelInterface.php';

/**
 * Class AbstractEtsSeoAnalyzableModel
 *
 * @mixin \ObjectModelCore
 *
 * @since 2.6.4
 */
abstract class AbstractEtsSeoAnalyzableModel extends ObjectModel implements EtsSeoAnalyzeModelInterface
{
    use EtsSeoTranslationTrait;
    use EtsSeoGetModuleTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        if (!$this->id) {
            if ($id_lang) {
                $this->id_lang = $id_lang;
            }
            if ($id_shop) {
                $this->id_shop = $id_shop;
            }
        }
    }

    /**
     * @param int $id
     * @param int|null $idLang
     * @param int|null $idShop
     *
     * @return static|null
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function findOneByRelationId($id, $idLang = null, $idShop = null)
    {
        $query = (new DbQuery())->select(static::$definition['primary'])->from(static::$definition['table']);
        $query->where(sprintf('%s = %d', bqSQL(static::getRelationIdColumnName()), $id));
        if ($idLang) {
            $query->where(sprintf('id_lang = %d', (int) $idLang));
        }
        if ($idShop) {
            $query->where(sprintf('id_shop = %d', (int) $idShop));
        }
        $id = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if ($id) {
            return new static($id);
        }

        return null;
    }

    /**
     * Reformat invalid properties
     *
     * @param array $contentAnalysis
     *
     * @return array
     */
    private function parseContentAnalysis($contentAnalysis)
    {
        foreach ($contentAnalysis as $idLang => $rules) {
            if ($idLang != $this->id_lang) {
                unset($contentAnalysis[$idLang]);
                continue;
            }
            foreach ($rules as $keyRule => &$rule) {
                if (false === strpos($keyRule, '_problem')) {
                    if (isset($rule['text']) && !Validate::isCleanHtml($rule['text'])) {
                        $rule['text'] = '';
                    }
                } else {
                    if (!empty($rule)) {
                        foreach ($rule as $idx => $item) {
                            if (!Validate::isCleanHtml($item)) {
                                $rule[$idx] = '';
                            }
                        }
                    }
                }
            }
            unset($rule);
        }

        return $contentAnalysis;
    }

    /**
     * @param array $seo_scores
     * @param array $readability_scores
     * @param array $content_analysis
     *
     * @return self
     *
     * @throws \PrestaShopException
     */
    public function setSeoScore($seo_scores = [], $readability_scores = [], array $content_analysis = [])
    {
        if (!$this->id_lang) {
            throw new PrestaShopException($this->l('Property "id_lang" is invalid. Can not set analysis score.'), __FILE__);
        }
        $seo_score = 0;
        $readability_score = 0;
        $scoreAnalysis = [
            'seo_score' => [],
            'readability_score' => [],
        ];
        if (is_array($seo_scores)) {
            foreach ($seo_scores as $keyRule => $rule) {
                if (isset($rule[$this->id_lang])) {
                    $scoreAnalysis['seo_score'][$keyRule] = $rule[$this->id_lang];
                    $seo_score += (int) $rule[$this->id_lang];
                }
            }
        }
        if (is_array($readability_scores)) {
            foreach ($readability_scores as $keyRule => $rule) {
                if (isset($rule[$this->id_lang])) {
                    $scoreAnalysis['readability_score'][$keyRule] = $rule[$this->id_lang];
                    $readability_score += (int) $rule[$this->id_lang];
                }
            }
        }
        $this->seo_score = $seo_score;
        $this->readability_score = $readability_score;
        $this->score_analysis = json_encode($scoreAnalysis);
        $this->content_analysis = json_encode($content_analysis[$this->id_lang]);

        return $this;
    }
}
