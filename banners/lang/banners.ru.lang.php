<?php
/**
 * Cotonti Plugin Banners
 * Banner rotation plugin with statistics
 *
 * @package Banners
 * @author Alex
 * @copyright Portal30 2013 http://portal30.ru
 */

/**
 * Plugin Title & Subtitle
 */

$L['info_desc'] = 'Плагин показа баннеров со статистикой.';

$L['ba_banner_edit'] = "Редактировать баннер";
$L['ba_banner_new'] = "Создание баннера";
$L['ba_banners'] = "Баннеры";
$L['ba_category_no'] = "Для создания баннера нужно создать хотябы одну категорию";
$L['ba_clients'] = "Клиенты";
$L['ba_client_default'] = "Использовать параметры клиента по умолчанию";
$L['ba_sticky_tip'] = "Определяет - является ли баннер 'прикреплённым'. Если один или несколько баннеров в категории являются 'прикреплёнными', они будут показаны первыми по отношению к прочим баннерам. К примеру, если два баннера в категории являются 'прикреплёнными', а третий - нет, то третий баннер не будет показан, если в настройках модуля, отображающего баннеры, выставлена настройка 'Прикреплённые, случайно'. Будут показаны только два первых баннера.";
$L['ba_tracks'] = "Статистика";

$L['ba_alt'] = "Альтернативный текст";
$L['ba_clear_tracks_param'] = "Удалить статистику по выбранным параметрам";
$L['ba_clear_tracks_param_confirm'] = "Данное действие очистит статистику в соотвествии с выбранными фильтрами";
$L['ba_click_url'] = "Url для перехода";
$L['ba_clicks'] = "Клики";
$L['ba_clicks_all'] = "Всего кликов";
$L['ba_client'] = "Клиент";
$L['ba_client_new'] = "Создание клиента";
$L['ba_custom_code'] = "Произвольный код";
$L['ba_deleted_no'] = 'ничего не удалено';
$L['ba_deleted_records'] = 'Удалено %1$s записей';
$L['ba_extrainfo'] = "Дополнительная информация";
$L['ba_from'] = "c";
$L['ba_height'] = "Высота";
$L['ba_impmade'] = "Общее число показов";
$L['ba_imptotal'] = "Максимальное количество показов";
$L['ba_impressions'] = "Показы";
$L['ba_for_file_only'] = "только для изображения / flash";
$L['ba_published'] = "Опубликовано";
$L['ba_publish_down'] = "Завершение публикации";
$L['ba_publish_up'] = "Начало публикации";
$L['ba_purchase_type'] = "Тип оплаты";
$L['ba_saved'] = "Сохранено";
$L['ba_sticky'] = "Прикреплен";
$L['ba_to'] = "по";
$L['ba_track_clicks'] = "Отслеживать клики";
$L['ba_track_clicks_tip'] = "Регистрировать ежедневное число кликов по баннеру.";
$L['ba_track_impressions'] = "Отслеживать показы";
$L['ba_track_impressions_tip'] = "Регистрировать ежедневное число показов (просмотров) баннеров.";
$L['ba_type_file'] = "Изображение / Flash";
$L['ba_unlimited'] = "Неограничено";
$L['ba_width'] = "Ширина";


/**
 * purchase type
 */
$L['ba_pt_yearly'] = "Ежегодно";
$L['ba_pt_monthly'] = "Ежемесячно";
$L['ba_pt_weekly'] = "Еженедельно";
$L['ba_pt_daily'] = "Ежедневно";

/**
 * Error, Message
 */
$L['ba_err_titleempty'] = "Заголовок не может быть пустым";
$L['ba_err_inv_file_type'] = "Недопустимый тип файла";

/**
 * Plugin config
 */
$L['cfg_purchase_type'] = array($L['ba_purchase_type'], "Эти параметры применяются для всех клиентов, но могут быть
    переопределены для некоторых из них индивидуально.");
$L['cfg_purchase_type_params'] = array(
   $L['ba_unlimited'],
   $L['ba_pt_yearly'],
   $L['ba_pt_monthly'],
   $L['ba_pt_weekly'],
   $L['ba_pt_daily']
);
$L['cfg_track_impressions'] = array($L['ba_track_impressions'], $L['ba_track_impressions_tip']);
$L['cfg_track_clicks'] = array($L['ba_track_clicks'], $L['ba_track_clicks_tip']);