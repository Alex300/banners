<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 * Model class for the banners
 *
 * @package Banners
 * @subpackage DB
 * @author Alex
 * @copyright  © Portal30 Studio http://portal30.ru 2011-2013
 *
 * @property int $ba_id
 * @property int $ba_type
 * @property string $ba_title
 * @property string $ba_cat
 * @property string $ba_file
 * @property int $ba_width
 * @property int $ba_height
 * @property string $ba_alt
 * @property string $ba_customcode
 * @property string $ba_clickurl
 * @property string $ba_description
 * @property bool $ba_published
 * @property bool $ba_sticky
 * @property string $ba_publish_up
 * @property string $ba_publish_down
 * @property int $ba_imptotal   // Макс. кол-во показов
 * @property int $ba_impmade    // Сколько показано
 * @property string $ba_lastimp
 * @property int $ba_clicks
 * @property int $bac_id
 * @property int $ba_purchase_type
 * @property int $ba_track_impressions
 * @property int $ba_track_clicks
 *
 * @property BaClient $client Client
 * @property bool $trackClics   Вычисленное значение: вести ежедневную статистику кликов?
 * @property bool $trackImpressions   Вычисленное значение: вести ежедневную статистику показов?
 *
 * @method static BaBanner getById(int $pk)
 * @method static BaBanner[] getList(int $limit = 0, int $offset = 0, string $order = '')
 * @method static BaBanner[] find(mixed $conditions, int $limit = 0, int $offset = 0, string $order = '')
 *
 */
class BaBanner extends BaModelAbstract{

    // === Типы баннеров ===
    const TYPE_UNKNOWN = 0;
    const TYPE_IMAGE = 1;
    const TYPE_FLASH = 2;
    /**
     * Произвольный код
     */
    const TYPE_CUSTOM = 3;

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

    protected $currFile;

    /**
     * Static constructor
     */
    public static function __init(){
        global $db_ba_banners;

        self::$_table_name = $db_ba_banners;
        self::$_primary_key = 'ba_id';
        parent::__init();

    }

    /**
     * @param mixed $data Array or Object - свойства
     *   в свойства заполнять только те поля, что есть в таблице + user_name
     */
    public function __construct($data = false) {
        parent::__construct($data);

        $this->currFile = $this->_data['ba_file'];
    }


    public function getClient($column){
        if (!empty($this->_data['bac_id'])) return BaClient::getById($this->_data['bac_id']);
        return NULL;
    }

    public function setClient($column, $value){
        if($value instanceof BaClient) $this->_data['bac_id'] = $value->bac_id;
        if(is_array($value)) $this->_data['bac_id'] = $value['bac_id'];
        if(is_integer($value)) $this->_data['bac_id'] = $value;
        return NULL;
    }

    /**
     * Вычисленное значение: вести ежедневную статистику кликов?
     * @param $column
     * @return bool
     */
    public function getTrackClics($column){
        global $cfg;

        $trackClics = false;
        if($this->_data['ba_track_clicks'] == 1){
            $trackClics = true;
        }elseif($this->_data['ba_track_clicks'] == -1){
            if(empty($this->client)){
                // Если не установлен клиент, берем настройки по-умолчанию
                if($cfg["plugin"]['banners']['track_clicks'] == 1) $trackClics = true;
            }else{
                if($this->client->bac_track_clicks == 1){
                    $trackClics = true;
                }elseif($this->client->bac_track_clicks == -1 && $cfg["plugin"]['banners']['track_clicks'] == 1){
                    $trackClics = true;
                }
            }
        }

        return $trackClics;
    }

    /**
     * Вычисленное значение: вести ежедневную статистику показов?
     * @param $column
     * @return bool
     */
    public function getTrackImpressions($column){
        global $cfg;

        $track = false;
        if($this->_data['ba_track_impressions'] == 1){
            $track = true;
        }elseif($this->_data['ba_track_impressions'] == -1){
            if(empty($this->client)){
                // Если не установлен клиент, берем настройки по-умолчанию
                if($cfg["plugin"]['banners']['track_impressions'] == 1) $track = true;
            }else{
                if($this->bac_track_impressions == 1){
                    $track = true;
                }elseif($this->client->bac_track_impressions == -1 && $cfg["plugin"]['banners']['track_impressions'] == 1){
                    $track = true;
                }
            }
        }

        return $track;
    }




    /**
     * Save data
     * @param BaBanner|array|null $data
     * @return int id of saved record
     */
    public function save($data = null){
        global $sys, $usr;

        if(!$data) $data = $this->_data;

        if ($data instanceof BaBanner) {
            $data = $data->toArray();
        }

        $data['ba_created'] = date('Y-m-d H:i:s', $sys['now']);
        $data['ba_created_by'] = $usr['id'];

        if(!$data['ba_id']) {
            // Добавить новую запись
            $data['ba_updated'] = date('Y-m-d H:i:s', $sys['now']);
            $data['ba_updated_by'] = $usr['id'];
        }
        $id = parent::save($data);

        // Проверка файла и удаление при необходимости
        if(!empty($this->currFile) && isset($data['ba_file']) && $this->currFile !=  $data['ba_file'] &&
            file_exists($this->currFile)) unlink($this->currFile);

        if($id){
            if(!$data['ba_id']) {
                cot_log("Added new banner # {$id} - {$data['ba_title']}",'adm');
            }else{
                cot_log("Edited banner # {$id} - {$data['ba_title']}",'adm');
            }
        }
        return $id;
    }

    /**
     * Засчитать показ
     */
    public function impress(){
        global $db, $db_ba_tracks, $sys;

        $this->_data['ba_impmade'] += 1;
        $this->_data['ba_lastimp'] = microtime(true);
        $this->save();

        // Ежедневная статистика
        if($this->trackImpressions){
            $trackDate = date('Y-m-d H', $sys['now']).':00:00';

            $sql = "SELECT `track_count` FROM $db_ba_tracks
                WHERE track_type=1 AND ba_id={$this->_data['ba_id']} AND track_date='{$trackDate}'";

            $count = $db->query($sql)->fetchColumn();

            if ($count){
                // update count
                $data = array(
                    'track_count' => $count + 1
                );
                $db->update($db_ba_tracks, $data,
                    "track_type=1 AND ba_id={$this->_data['ba_id']} AND track_date='{$trackDate}'");
            }else{
                // insert new count
                $data = array(
                    'track_count' => 1,
                    'track_type' => 1,
                    'ba_id' => (int)$this->_data['ba_id'],
                    'track_date' => $trackDate
                );
                $db->insert($db_ba_tracks, $data);
            }
        }
    }

    /**
     * Засчитать клик
     */
    public function click(){
        global $db, $db_ba_tracks, $sys;

        $this->_data['ba_clicks'] += 1;
        $this->save();

        // Ежедневная статистика
        if($this->trackClics){
            $trackDate = date('Y-m-d H', $sys['now']).':00:00';

            $sql = "SELECT `track_count` FROM $db_ba_tracks
                WHERE track_type=2 AND ba_id={$this->_data['ba_id']} AND track_date='{$trackDate}'";

            $count = $db->query($sql)->fetchColumn();

            if ($count){
                // update count
                $data = array(
                    'track_count' => $count + 1
                );
                $db->update($db_ba_tracks, $data,
                    "track_type=2 AND ba_id={$this->_data['ba_id']} AND track_date='{$trackDate}'");
            }else{
                // insert new count
                $data = array(
                    'track_count' => 1,
                    'track_type' => 2,
                    'ba_id' => (int)$this->_data['ba_id'],
                    'track_date' => $trackDate
                );
                $db->insert($db_ba_tracks, $data);
            }
        }
    }

    public function delete(){
        global $db, $db_ba_tracks;

        $id = $this->_data['ba_id'];
        $file = $this->_data['ba_file'];
        $currFile = $this->currFile;

        parent::delete();

        if(file_exists($file)) unlink($file);
        if(file_exists($currFile)) unlink($currFile);

        // Удалить статистику
        $db->delete($db_ba_tracks, "ba_id={$id}");

    }


    // === Методы для работы с шаблонами ===
    /**
     * Returns banner tags for coTemplate
     *
     * @param BaBanner|int $banner BaBanner object or ID
     * @param string $tagPrefix Prefix for tags
     * @param bool $cacheitem Cache tags
     * @return array|void
     * @todo при включенном кеше, если в категории кол-во баннеров равно кол-ву выводимых - ajax не исползовать
     *       для показа баннеров, но в этом случае нужно чистить кеш при добавлении/редактировании баннера
     *       очищать весь кеш для большого сайта - накладно
     */
    public static function generateTags($banner, $tagPrefix = '', $cacheitem = true){
        global $cfg, $L, $usr, $structure, $cache_ext;

        static $extp_first = null, $extp_main = null;
        static $cache = array();

        if (is_null($extp_first)){
            $extp_first = cot_getextplugins('banners.tags.first');
            $extp_main = cot_getextplugins('banners.tags.main');
        }

        /* === Hook === */
        foreach ($extp_first as $pl){
            include $pl;
        }
        /* ===== */

        if ( is_object($banner) && is_array($cache[$banner->ba_id]) ) {
            $temp_array = $cache[$banner->ba_id];
        }elseif (is_int($banner) && is_array($cache[$banner])){
            $temp_array = $cache[$banner];
        }else{
            if (is_int($banner) && $banner > 0){
                $banner = self::getById($banner);
            }
            if ($banner->ba_id > 0){
                $item_link = cot_url('admin', array('m'=>'other', 'p'=>'banners', 'a'=>'edit',
                                                    'id'=>$banner->ba_id));

                $temp_array = array(
                    'EDIT_URL' => $item_link,
                    'URL' => $banner->ba_clickurl,
                    'ID' => $banner->ba_id,
                    'TITLE' => htmlspecialchars($banner->ba_title),
                    'STICKY' => $banner->ba_sticky,
                    'STICKY_TEXT' => $banner->ba_sticky ? $L['Yes'] : $L['No'],
                    'CLIENT_TITLE' => htmlspecialchars($banner->client->bac_title),
                    'IMPTOTAL' => $banner->ba_imptotal,
                    'IMPTOTAL_TEXT' => ($banner->ba_imptotal > 0) ? $banner->ba_imptotal : $L['ba_unlimited'],
                    'IMPMADE' => $banner->ba_impmade,
                    'CLICKS' => $banner->ba_clicks,
                    'CATEGORY' => $banner->ba_cat,
                    'CATEGORY_TITLE' => htmlspecialchars($structure['banners'][$banner->ba_cat]['title']),
                    'CLICKS_PERSENT' => ($banner->ba_impmade > 0) ?
                                        round($banner->ba_clicks / $banner->ba_impmade * 100 , 0)." %" : '0 %',
                    'WIDTH' => $banner->ba_width,
                    'HEIGHT' => $banner->ba_height,
                    'TYPE' => $banner->ba_type,
                    'PUBLISHED' => $banner->ba_published ? $L['Yes'] : $L['No'],
                    'CLASS' => '',
                    'CACHE' => 0

                );

                if (!empty($cache_ext) && $usr['id'] == 0 && $cfg['cache_' . $cache_ext]){
                    // учесть кеширование - запрашивать баннер аяксом
                    $temp_array['CLASS'] = 'banner-loading';
                    $temp_array['CACHE'] = 1;
                    $image = cot_rc('banner_load', array(
                        'width' => $banner->ba_width,
                        'height' => $banner->ba_height
                    ));
                    $temp_array['BANNER'] = cot_rc('banner', array(
                        'banner' => $image
                    ));
                }else{
                    // Вывод обычным образом
                    $url = cot_url('banners', 'a=click&id='.$banner->ba_id);

                    if(!empty($banner->ba_file)){
                        $image = false;
                        if($banner->ba_type ==  BaBanner::TYPE_IMAGE){
                            // расчитаем размеры картинки:
                            $w = $banner->ba_width;
                            $h = $banner->ba_height;
                            $image = cot_rc('banner_image', array(
                                'file' => $banner->ba_file,
                                'alt' => $banner->ba_alt,
                                'width' => $w,
                                'height' => $h
                            ));

                        }elseif($banner->ba_type ==  BaBanner::TYPE_FLASH){
                            $w = $banner->ba_width;
                            $h = $banner->ba_height;
                            $image = cot_rc('banner_flash', array(
                                'file' => $banner->ba_file,
                                'width' => $w,
                                'height' => $h
                            ));
                        }
                        if(!empty($image)){
                            if(!empty($banner->ba_clickurl)){
                                $image = cot_rc_link($url, $image, array('target' => '_blank'));
                            }
                            $temp_array['BANNER'] = cot_rc('banner', array(
                                'banner' => $image
                            ));
                        }
                    }
                    if($banner->ba_type ==  BaBanner::TYPE_CUSTOM){
                        $temp_array['BANNER'] = cot_rc('banner', array(
                            'banner' => $banner->ba_customcode
                        ));
                    }
                }


                /* === Hook === */
                foreach ($extp_main as $pl)
                {
                    include $pl;
                }
                /* ===== */
                $cacheitem && $cache[$banner->ba_id] = $temp_array;
            }else{
                // Диалога не существует
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
BaBanner::__init();