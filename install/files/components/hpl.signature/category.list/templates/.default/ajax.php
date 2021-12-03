<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/hpl.kpi/lib/entity/hpl_kpi_tieuchi.php');

use Bitrix\Main;
use Hpl\Entity\HplKpiTieuchiTable;

global $USER, $DB, $APPLICATION;
CUtil::JSPostUnescape();
$APPLICATION->ShowAjaxHead();
$el = new CIBlockElement();
$action = isset($_POST['ajax_action']) ? $_POST['ajax_action'] : null;
if (empty($action))
    die('Unknown action!');
$action = strtoupper($action);

$sendResponse = function ($data, array $errors = array(), $plain = false) {
    if ($data instanceof Bitrix\Main\Result) {
        $errors = $data->getErrorMessages();
        $data = $data->getData();
    }

    $result = array('DATA' => $data, 'ERRORS' => $errors);
    $result['SUCCESS'] = count($errors) === 0;
    if (!defined('PUBLIC_AJAX_MODE')) {
        define('PUBLIC_AJAX_MODE', true);
    }
    $GLOBALS['APPLICATION']->RestartBuffer();
    header('Content-Type: application/x-javascript; charset=' . LANG_CHARSET);

    if ($plain) {
        $result = $result['DATA'];
    }

    echo \Bitrix\Main\Web\Json::encode($result);
    CMain::FinalActions();
    die();
};
$sendError = function ($error) use ($sendResponse) {
    $sendResponse(array(), array($error));
};
switch ($action){
    case 'SAVEITEM':
        $col = isset($_POST['col']) ? $_POST['col'] : '';
        $tieuchiId = isset($_POST['tieuchi_id']) ? $_POST['tieuchi_id'] : '';
        $checked = isset($_POST['checked']) ? $_POST['checked'] : 'N';
        if (!empty($tieuchiId) && !empty($col)) {

            $dbSetting = HplKpiTieuchiTable::getById($tieuchiId)->fetch();
            if (!empty($dbSetting['id'])) {
                $arFields = array(
                    $col => "'".htmlspecialcharsbx($checked)."'",
                    'updated_at'=>"'".date("Y-m-d H:i:s")."'"
                );
                $result = $DB->Update(HplKpiTieuchiTable::HPL_KPI_TIEUCHI, $arFields, "WHERE id=" . $tieuchiId, '', true);
                if($result){
                    $sendResponse('Update completed!');
                }
            } else {

                $arFields = array(
                    "user_id" => intval($userId),
                    $col => $checked == 1 ? 1 : 0,
                    'updated_at'=>"'".date("Y-m-d H:i:s")."'"
                );
                /* echo "<pre>";

                 print_r($arFields);
                 die;*/


                if ($DB->Insert(HplTieuchiTable::getTableName(), $arFields)) {
                    $sendResponse('Update completed!');

                } else {
                    var_dump($DB->Insert(HplTieuchiTable::getTableName(), $arFields));
                }
            }

        } else {
            $sendResponse("Errors params!");
        }
        break;
    case 'COPYITEM':
        $tieuchiId = isset($_POST['tieuchi_id']) ? $_POST['tieuchi_id'] : '';
        if (!empty($tieuchiId)) {
            $dbSetting = HplKpiTieuchiTable::getById($tieuchiId)->fetch();
            if($dbSetting){
                $user_copy = $USER->GetID();
                $store['name']       = $dbSetting['name'].' (sao chép)';
                $store['status']     = $dbSetting['status'];
                $store['created_by'] = $user_copy;
                $store['updated_by'] = $user_copy;
                $store['created_at'] = new Main\Type\DateTime();
                $store['updated_at'] = new Main\Type\DateTime();
                $result = HplKpiTieuchiTable::add($store);
                if($result){
                    $sendResponse('Update completed!');
                }
            }
            $sendResponse("Errors params!");
        }else{
            $sendResponse("Errors params!");
        }
        break;
}