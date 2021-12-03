<?php
defined('B_PROLOG_INCLUDED') || die;

use Academy\CrmStores\Entity\StoreTable;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Hpl\Entity\HplSignatureCategoriesTable;
use Hpl\Entity\HplSignatureAccountsTable;

class CatgoryEditComponent extends CBitrixComponent
{
    const FORM_ID = 'CATEGORY_EDIT';
    const GRID_ID = 'CATEGORY_EDIT';

    private $errors, $user;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;
        $this->user = $USER->GetID();
        parent::__construct($component);

        $this->errors = new ErrorCollection();
        if (!Loader::includeModule('hpl.signature')) {
            ShowError(Loc::getMessage('PAGE_NOT_FOUND'));
            return;
        }
    }

    public function executeComponent()
    {
        global $APPLICATION;
        $title = 'Chữ ký của tôi';
        $user_id = $this->user;
        $user = UserTable::getById($user_id)->fetch();

        //Check user đã tồn tại
        $store = array(
            'NAME'          => $user['NAME'],
            'LAST_NAME'     => $user['LAST_NAME'],
            'WORK_PHONE'    => $user['WORK_PHONE'],
            'type'          => '',
            'supplier'      => '',
            'id_card'       => '',
            'use_time'      => 0,
            'img_signature' => '',
            'date_end'      => '',
            'status'        => 0
        );
        $userExist = HplSignatureAccountsTable::getList(['filter'=> ['user_id'=> $user_id]])->fetch();
        if(!empty($userExist)){
            $store['id']        = $userExist['id'];
            $store['type']      = $userExist['type'];
            $store['supplier']  = $userExist['supplier'];
            $store['id_card']   = $userExist['id_card'];
            $store['use_time']  = $userExist['use_time'];
            $store['img_signature'] = $userExist['img_signature'];
            $store['date_end']  = $userExist['date_end'];
            $store['status']    = $userExist['status'];
        }
        if(empty($user)){
            ShowError('Người dùng không tồn tại');
            return;
        }

        if (intval($this->arParams['USER_ID']) > 0) {
            $dbStore = HplSignatureCategoriesTable::getById($this->arParams['CATEGORY_ID']);
            $store = $dbStore->fetch();

            if (empty($store)) {
                ShowError(Loc::getMessage('PAGE_NOT_FOUND'));
                return;
            }
        }

        if (!empty($store['id'])) {
            $title = 'Chữ ký của tôi';
        }
        $APPLICATION->SetTitle($title);
        if (self::isFormSubmitted()) {
            $savedStoreId = $this->processSave($store);
            if ($savedStoreId > 0) {
                LocalRedirect($this->getRedirectUrl($savedStoreId));
            }

            $submittedStore = $this->getSubmittedStore();
            $store = array_merge($store, $submittedStore);
        }

        $this->arResult =array(
            'FORM_ID'   => self::FORM_ID,
            'GRID_ID'   => self::GRID_ID,
            'IS_NEW'    => empty($store['ID']),
            'TITLE'     => $title,
            'STORE'     => $store,
            'BACK_URL'  => $this->getRedirectUrl(),
            'ERRORS'    => $this->errors,
        );

        $this->includeComponentTemplate();
    }

    private function processSave($initialStore)
    {
        global $DB;
        $submittedStore = $this->getSubmittedStore();
        $store = array_merge($initialStore, $submittedStore);
        $this->errors = self::validate($store);

        if (!$this->errors->isEmpty()) {
            return false;
        }
        $user = $this->user;
        if(isset($store['file'])){
            $server = \Bitrix\Main\Context::getCurrent()->getServer();
            $target_dir    = $server->getDocumentRoot() ."/local/components/hpl.signature/uploads/signatures/";
            $files = $store['file'];
            $temp = explode(".", $files["name"]);
            $fileName = $user.'.'.$temp[1];
            $path_file = "/local/components/hpl.signature/uploads/signatures/";

            $target_file   = $target_dir.$fileName;

            if (move_uploaded_file($files["tmp_name"], $target_file))
            {
                $store['img_signature'] = "'".htmlspecialcharsbx($path_file.$fileName)."'";
            }
        }
        $store['type'] = $submittedStore['type'] ? "'".htmlspecialcharsbx($submittedStore['type'])."'" : 'NULL';
        $store['supplier'] = $submittedStore['supplier'] ?  "'".htmlspecialcharsbx($submittedStore['supplier'])."'": 'NULL';
        $store['date_end'] = $submittedStore['date_end'] ? $this->convertDate($submittedStore['date_end']) : 'NULL';
        $store['status']   = $submittedStore['status'] ? $submittedStore['status'] : 0;
        $store['updated_at'] = "'".date("Y-m-d H:i:s")."'";
        unset($store['LAST_NAME']);
        unset($store['WORK_PHONE']);
        unset($store['NAME']);
        unset($store['file']);
        if (!empty($store['id'])) {
            unset($store['file']);
            $result = $DB->Update(HplSignatureAccountsTable::HPL_SIGNATURE_ACCOUNTS,$store, "WHERE id=" . $store['id'], '', true);
        } else {
            $store['created_at'] = "'".date("Y-m-d H:i:s")."'";;
            $store['created_by'] = $user;
            $store['user_id'] = $user;
            $result=  $DB->Insert(HplSignatureAccountsTable::HPL_SIGNATURE_ACCOUNTS,$store, '', true);

//            $result = HplSignatureAccountsTable::add($store);
        }

        if (!$result) {
            $this->errors->add($result->getErrors());
        }

        return $result ? $result : false;
    }
    private function convertDate($date, $check = false){
        $arrayDate = explode(' ', $date);
        $day = explode('/', $arrayDate[0]);
        $result = $day[2].'-'.$day[1].'-'.$day[0].' '.$arrayDate[1];
        $date = date('Y-m-d',strtotime($result));
        return '"'.$date.'"';
    }
    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $submittedStore = array(
            'type' => $request->get('type'),
            'supplier' => $request->get('supplier'),
            'id_card' => $request->get('id_card'),
            'use_time' => $request->get('use_time'),
            'file' =>  $_FILES['file'],
            'date_end' => $request->get('date_end'),
            'status' => $request->get('status'),
        );
        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();
        return $errors;
    }

    private static function isFormSubmitted()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $saveAndView = $request->get('saveAndView');
        $saveAndAdd = $request->get('saveAndAdd');
        $apply = $request->get('apply');
        return !empty($saveAndView) || !empty($saveAndAdd) || !empty($apply);
    }

    private function getRedirectUrl($savedStoreId = null)
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        if (!empty($savedStoreId) && $request->offsetExists('apply')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('STORE_ID' => $savedStoreId)
            );
        } elseif (!empty($savedStoreId) && $request->offsetExists('saveAndAdd')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('STORE_ID' => 0)
            );
        }

        $backUrl = $request->get('backurl');
        if (!empty($backUrl)) {
            return $backUrl;
        }
    }
}