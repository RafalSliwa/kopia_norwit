<?php

/**
 * File from http://PrestaShow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @authors     PrestaShow.pl <kontakt@prestashow.pl>
 * @copyright   2016 PrestaShow.pl
 * @license     http://PrestaShow.pl/license
 */

require_once dirname(__FILE__) . "/../../config.php";

class PShowFbReviewsReviews_ListModuleFrontController extends ModuleFrontController
{

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(__PS_BASE_URI__ . 'modules/pshowfbreviews/views/css/pshowfbreviews.css');
    }

    public function initContent()
    {
        parent::initContent();

        $reviews_from_fb = PShowFbReviews::getReviewsFromFb();
        $list_of_reviews = $reviews_from_fb['list_of_reviews'];

        $array_of_reviews = array();
        $only_positive_reviews = Configuration::get('PSHOW_FBREVIEWS_ONLY_POSITIVE_REVIEWS');
        $display_reviews = Configuration::get('PSHOW_FBREVIEWS_DISPLAY_REVIEWS');

        $positive_comments = 0;
        $negative_comments = 0;
        $i = 0;
        if ($list_of_reviews && isset($list_of_reviews["data"])) {
            foreach ($list_of_reviews["data"] as $review_obj) {
                if ((!$only_positive_reviews || ($only_positive_reviews && !strcasecmp($review_obj["recommendation_type"], "positive"))) && isset($review_obj["review_text"])) {
                    $date = $review_obj["created_time"];
                    $time = strtotime($date);
                    $newformat = date('m-d-Y H:i:s', $time);
                    $array_of_reviews[$i]["date"] = $newformat;
                    $array_of_reviews[$i]["recommendation_type"] = $review_obj["recommendation_type"];
                    $array_of_reviews[$i]["review_text"] = $review_obj["review_text"];
                    $positive_comments += (!strcasecmp($review_obj["recommendation_type"], "positive") ? 1 : 0);
                    $negative_comments += (!strcasecmp($review_obj["recommendation_type"], "positive") ? 0 : 1);
                    $i++;
                }
            }

            if (!$only_positive_reviews)
                $reviews_number = ($positive_comments > 0 ? round(($positive_comments / count($array_of_reviews)) * 100) : 0);
            else
                $reviews_number = 100;

            $reviews_text = array(0 => $this->module->l('Any reviews'), 1 => $this->module->l('Poor'), 2 => $this->module->l('Fair'), 3 => $this->module->l('Good'), 4 => $this->module->l('Very Good'), 5 => $this->module->l('Excellent'));

            $this->context->smarty->assign(array(
                'profile_id' => Configuration::get("PSHOW_FBREVIEWS_PAGE_ID"),
                'name_of_shop' => Configuration::get('PSHOW_FBREVIEWS_PAGE_NAME', $this->context->language->id),
                'title_of_reviews_page' => Configuration::get('PSHOW_FBREVIEWS_PAGE_NAME', $this->context->language->id),
                'main_review' => ($reviews_number > 80 ? 5 : ($reviews_number > 60 ? 4 : ($reviews_number > 40 ? 3 : ($reviews_number > 20 ? 2 : ($reviews_number > 0 ? 1 : 0))))),
                'array_of_reviews' => $array_of_reviews,
                'only_positive_reviews' => $only_positive_reviews,
                'display_reviews' => $display_reviews,
                'positive_comments' => $positive_comments,
                'negative_comments' => $negative_comments,
                'main_review_text' => $reviews_text[($reviews_number > 80 ? 5 : ($reviews_number > 60 ? 4 : ($reviews_number > 40 ? 3 : ($reviews_number > 20 ? 2 : ($reviews_number > 0 ? 1 : 0)))))],
                'count_reviews' => count($array_of_reviews)
            ));

            if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
                $this->setTemplate('module:pshowfbreviews/views/templates/front/reviews_new_presta.tpl');
            } else {
                $this->setTemplate('reviews.tpl');
            }
        }
    }
}
