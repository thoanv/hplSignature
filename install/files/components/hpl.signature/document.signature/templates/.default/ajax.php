<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/hpl.signature/lib/entity/hpl_signature_documents.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/hpl.signature/lib/entity/hpl_signature_accounts.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/hpl.signature/lib/entity/hpl_signature_signers.php');

use Bitrix\Main;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;
use Hpl\Signature\Entity\HplSignatureAccountsTable;
use Hpl\Signature\Entity\HplSignatureSignersTable;

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
$date_current = new Main\Type\DateTime();
$user = $USER->GetID();
switch ($action){
    case 'SAVEFILEPDF':
        $document_id = isset($_POST['document_id']) ? $_POST['document_id'] : 0;
        $signature_id = isset($_POST['signature_id']) ? $_POST['signature_id'] : 0;
        $x = isset($_POST['x']) ? $_POST['x'] : 0;
        $y = isset($_POST['y']) ? $_POST['y'] : 0;
        $w = isset($_POST['x']) ? $_POST['w'] : 0;
        $h = isset($_POST['y']) ? $_POST['h'] : 0;
        $p = isset($_POST['p']) ? $_POST['p'] : 0;
        if ($document_id && $signature_id) {
            $document  = HplSignatureDocumentsTable::getById($document_id)->fetch();
            $account = HplSignatureAccountsTable::getById($signature_id)->fetch();
            //Chèn image vào file pdf
            require_once('./fpdf/fpdf.php');
            require_once('./fpdf/fpdi.php');
            $pdf = new FPDI();
            $pageCount =  $pdf->setSourceFile($_SERVER['DOCUMENT_ROOT'].$document['file']);

            for ($i = 1; $i <= $pageCount; $i++) {
//                $pdf->importPage($i);
//                $pdf->AddPage();
//                $pdf->useTemplate($i);
                $pdf->AddPage();
                $imported = $pdf->importPage($i);
                $pdf->useTemplate($imported);

                if($i == $p){
                    $pdf->Image($_SERVER['DOCUMENT_ROOT']. $account['img_signature'],  $x*0.26458333333333, $y*0.26458333333333, $w*0.26458333333333, $h*0.26458333333333, 'png');
                    $x = $x+1;
                    $y = $y+$h+1;
                    $pdf->SetXY($x,$y);
                }
            }

//            $pdf->Image($_SERVER['DOCUMENT_ROOT']. $account['img_signature'],  10, 60, 50, 24);
            $pdf->Output($_SERVER['DOCUMENT_ROOT'].$document['file'], "F");

            $date = new DateTime($document['created-at']);
            $year = $date->format('Y');
            $month= $date->format('m');
            $time = time();
            $file_new = '/local/components/hpl.signature/uploads/'.$year.'/'.$month.'/'.$document['name_file'].'_'.$time.'_'.$document['department_id'].'.'.$document['extend'];
            $store_doc['file']  = $file_new;
            $store_doc['updated_by'] = $user;
            $store_doc['updated_at'] = $date_current;
            rename ($_SERVER['DOCUMENT_ROOT'].$document['file'],$_SERVER['DOCUMENT_ROOT'].$file_new);
            HplSignatureDocumentsTable::update($document['id'], $store_doc);
            $signature = HplSignatureSignersTable::getList([
                'filter' => ['document_id' => $document_id, 'user_id' => $account['user_id']]
            ])->fetch();
            if(!empty($signature)){
                $store['status'] = 1;
                $store['updated_at'] = $date_current;
                $store['updated_by'] = $user;
                $result = HplSignatureSignersTable::update($signature['id'], $store);
                if($result){
                    $sendResponse('Update completed!');
                }
            }
            $sendResponse("Errors params!");
        } else {
            $sendResponse("Errors params!");
        }
        break;

}