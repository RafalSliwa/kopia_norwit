<?php

require(dirname(__FILE__) . '/../../config/config.inc.php');
$errorsfb = array();
$list_of_reviews = array();
try {
    $json_str = file_get_contents('https://graph.facebook.com/' . Configuration::get('PSHOW_FBREVIEWS_PAGE_ID') . '/ratings?access_token=' . Configuration::get('PSHOW_FBREVIEWS_ACCESS_TOKEN'));

    $data = json_decode($json_str);
} catch (Exception $e) {
    $errorsfb[] = 'Graph returned an error: ' . $e->getMessage();
}
try {
    if (isset($data) && $data != null) {
        $list_of_reviews = $data;
    }
    if ($list_of_reviews && count($list_of_reviews) > 0) {
        $reviews_from_fb = array();
        $reviews_from_fb['errors'] = $errorsfb;
        $reviews_from_fb['list_of_reviews'] = $list_of_reviews;

        $json_data = json_encode($reviews_from_fb);
        file_put_contents(dirname(__FILE__) . '/list_of_reviews.json', $json_data);
        echo "success";
    } else {
        echo "errors:<br/>";
        echo implode("</br>", $errorsfb);
    }
} catch (Exception $e) {
    echo 'Error with save file: ' . $e->getMessage();
}