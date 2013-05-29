<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 * Model class for the clients
 *
 * @package Banners
 * @author Alex
 * @copyright  © Portal30 Studio http://portal30.ru 2011-2013
 *
 * @property int $bac_id
 * @property string $bac_title
 * @property string $bac_email
 * @property string $bac_extrainfo
 * @property bool $bac_published
 * @property int $bac_purchase_type
 * @property int $bac_track_clicks
 * @property string $bac_track_impressions
 *
 * @method static BaClient getById(int $pk)
 * @method static BaClient[] getList(int $limit = 0, int $offset = 0, string $order = '')
 * @method static BaClient[] find(mixed $conditions, int $limit = 0, int $offset = 0, string $order = '')
 *
 */
class BaClient extends BaModelAbstract{

    // === Типы оплаты ===
    const PURCHASE_DEFAULT = -1;
    const PURCHASE_UNLIMITED = 1;
    const PURCHASE_YEARLY = 2;
    const PURCHASE_MONTHLY = 3;
    const PURCHASE_WEEKLY = 4;
    const PURCHASE_DAILY = 5;

    /**
     * @var string
     */
    public static $_table_name = '';

    /**
     * @var string
     */
    public static $_primary_key = '';

    /**
     * Column definitions
     * @var array
     */
    public static $_columns = array();

    /**
     * Static constructor
     */
    public static function __init(){
        global $db_ba_clients;

        self::$_table_name = $db_ba_clients;
        self::$_primary_key = 'bac_id';
        parent::__init();

    }

    /**
     * Retireve a key => val list of clients from the database.
     * @param bool|int $published
     * This is written to get a list for selecting currencies. Therefore it asks for enabled
     * @return array
     */
    public static function getKeyValPairsList($published=false) {
        global $db, $db_ba_clients;

        $where = array();

        if($published){
            $where['published'] = "bac_published=1";
        }
        if(count($where) > 0){
            $where = "WHERE ".implode('AND', $where);
        }else{
            $where = '';
        }

        $q = "SELECT bac_id, bac_title FROM `$db_ba_clients` $where ORDER BY `bac_title` ASC";
        $sql = $db->query($q);

        return $sql->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // === Методы для работы с шаблонами ===
    /**
     * Returns client tags for coTemplate
     *
     * @param BaClient|int $client
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @internal param \BaClient|int $thread OcThread object or ID
     * @return array|void
     */
    public static function generateTags($client, $tagPrefix = '', $cacheitem = true){
        global $cfg, $L, $usr;

        static $extp_first = null, $extp_main = null;
        static $cache = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('banners.client.tags.first');
            $extp_main = cot_getextplugins('banners.client.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( is_object($client) && is_array($cache[$client->bac_id]) ) {
            $temp_array = $cache[$client->bac_id];
        }elseif (is_int($client) && is_array($cache[$client])){
            $temp_array = $cache[$client];
        }else{
            if (is_int($client) && $client > 0){
                $client = self::getById($client);
            }
            $purchase = array(
                BaClient::PURCHASE_DEFAULT => $L['Default'],
                BaClient::PURCHASE_UNLIMITED => $L['ba_unlimited'],
                BaClient::PURCHASE_YEARLY => $L['ba_pt_yearly'],
                BaClient::PURCHASE_MONTHLY => $L['ba_pt_monthly'],
                BaClient::PURCHASE_WEEKLY => $L['ba_pt_weekly'],
                BaClient::PURCHASE_DAILY => $L['ba_pt_daily']
            );
            $temp_array = array();
            if ($client->bac_id > 0){
                $item_link = cot_url('admin', array('m'=>'other', 'p'=>'banners', 'n'=>'clients', 'a'=>'edit',
                                                        'id'=>$client->bac_id));
                $temp_array = array(
                    'URL' => $item_link,
                    'ID' => $client->bac_id,
                    'TITLE' => htmlspecialchars($client->bac_title),
                    'PUBLISHED' => $client->bac_published ? $L['Yes'] : $L['No'],
                    'PURCHASE' => $client->bac_purchase_type,
                    'PURCHASE_TEXT' => $purchase[$client->bac_purchase_type],
                );

                /* === Hook === */
                foreach ($extp_main as $pl)
                {
                    include $pl;
                }
                /* ===== */
                $cacheitem && $cache[$client->bac_id] = $temp_array;
            }else{
                // Клиента не существует
            }
        }
        $return_array = array();
        foreach ($temp_array as $key => $val){
            $return_array[$tagPrefix . $key] = $val;
        }

        return $return_array;
    }
}

// Class initialization for some static variables
BaClient::__init();