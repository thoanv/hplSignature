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
            ShowError(Loc::getMessage('CRMSTORES_NO_MODULE'));
            return;
        }
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $title = 'Thêm mới';
        $categories = HplSignatureCategoriesTable::getList(['select'=> ['id', 'name']])->fetchAll();
        $store = array(
            'name' => '',
            'description' => '',
            'parent_id' => '',
            'individual' => 'Y'
        );
        if (intval($this->arParams['CATEGORY_ID']) > 0) {
            $dbStore = HplSignatureCategoriesTable::getById($this->arParams['CATEGORY_ID']);
            $store = $dbStore->fetch();

            if (empty($store)) {
                ShowError(Loc::getMessage('PAGE_NOT_FOUND'));
                return;
            }
        }

        if (!empty($store['id'])) {
            $title = 'Thêm mới';
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
            'CATEGORIES'=> $categories,
            'BACK_URL'  => $this->getRedirectUrl(),
            'ERRORS'    => $this->errors,
        );

        $this->includeComponentTemplate();
    }

    private function processSave($initialStore)
    {
        $submittedStore = $this->getSubmittedStore();

        $store = array_merge($initialStore, $submittedStore);

        $this->errors = self::validate($store);

        if (!$this->errors->isEmpty()) {
            return false;
        }
        $date = new \Bitrix\Main\Type\DateTime();
        $user = $this->user;
        $store['updated_by'] = $user;
        $store['updated_at'] = $date;
        if (!empty($store['ID'])) {
            $result = HplSignatureCategoriesTable::update($store['id'], $store);
        } else {
            $store['created_at'] = $date;
                $store['created_by'] = $user;
            $result = HplSignatureCategoriesTable::add($store);
        }

        if (!$result->isSuccess()) {
            $this->errors->add($result->getErrors());
        }

        return $result->isSuccess() ? $result->getId() : false;
    }

    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $submittedStore = array(
            'name' => $request->get('name'),
            'parent_id' => $request->get('parent_id'),
            'individual' => $request->get('individual'),
            'description' => $request->get('description'),
        );

        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();

        if (empty($store['name'])) {
            $errors->setError(new Error(Loc::getMessage('ERROR_EMPTY_NAME')));
        }
        $dbName = HplSignatureCategoriesTable::getList([
            'filter'=> ['name' => $store['name']]
        ])->fetch();
        if (!empty($dbName)) {
            $errors->setError(new Error(Loc::getMessage('UNIQUE_NAME')));
        }

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

        if (!empty($savedStoreId) && $request->offsetExists('saveAndView')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['DETAIL'],
                array('STORE_ID' => $savedStoreId)
            );
        } else {
            return $this->arParams['SEF_FOLDER'];
        }
    }
}