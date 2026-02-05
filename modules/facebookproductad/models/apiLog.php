<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}
class apiLog extends \ObjectModel
{
    /** @var int id_error * */
    public $id_error;

    // ** @var string values **/
    public $error_message;

    // ** @var string values **/
    public $page_event;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_api_log',
        'primary' => 'id_error',
        'fields' => [
            'error_message' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'page_event' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    /**
     * get the api log for an event and or date range
     *
     * @param string $eventType
     * @param string $dateStart
     *
     * @return mixed
     */
    public static function getApiLogErrorMessage($eventType = null, $dateStart = null)
    {
        $logOutput = [];
        $query = new \DbQuery();
        $query->select('fal.error_message, fal.page_event');
        $query->from('fpa_api_log', 'fal');

        if (!empty($eventType)) {
            $query->where('fal.page_event="' . \pSQL($eventType) . '"');
        }

        if (!empty($dateStart)) {
            $query->where('fal.date_add >="' . \pSQL($dateStart) . '"');
        }

        $query->where('fal.id_shop=' . (int) \FacebookProductAd::$iShopId);
        $dataLog = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!empty($dataLog) && is_array($dataLog)) {
            foreach ($dataLog as $key => $data) {
                $logOutput[$key][] = json_decode($data['error_message'], true);
                $logOutput[$key][] = $data['page_event'];
            }
        }

        return $logOutput;
    }
}
