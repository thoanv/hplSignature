<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/** @var CBitrixComponentTemplate $this */

/** @var ErrorCollection $errors */
$errors = $arResult['ERRORS'];
foreach ($errors as $error) {
    /** @var Error $error */
    ShowError($error->getMessage());
}
CModule::includeModule('crm');
$categories = $arResult['CATEGORIES'];
$APPLICATION->IncludeComponent(
    'hpl.signature:interface.form',
    'edit',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'FORM_ID' => $arResult['FORM_ID'],
        'ENABLE_TACTILE_INTERFACE' => 'Y',
        'SHOW_SETTINGS' => 'Y',
//        'TITLE' => $arResult['TITLE'],
        'IS_NEW' => $arResult['IS_NEW'],
        'DATA' => $arResult['STORE'],
        'TABS' => array(
            array(
                'id' => 'tab_1',
                'name' => "Thông tin",
                'title' => "Thông tin",
                'display' => false,
                'fields' => array(
                    array(
                        'id'    => 'name',
                        'name'  => 'Tên thư mục ',
                        'type'  => 'text',
                        'value' => isset($arResult['STORE']['name'])?$arResult['STORE']['name']:''
                    ),
                    array(
                        'id'    => 'parent_id',
                        'name'  => 'Thư mục cha',
                        'type'  => 'list_parent',
                        'items' => $categories,
                        'value' => isset($arResult['STORE']['parent_id'])?$arResult['STORE']['parent_id']:''
                    ),
                    array(
                        'id'    => 'individual',
                        'name'  => 'Cá nhân',
                        'type'  => 'checkbox',
                        'value' => isset($arResult['STORE']['individual'])?$arResult['STORE']['individual']:''
                    ),
                    array(
                        'id'    => 'description',
                        'name'  => 'Mô tả',
                        'type'  => 'textarea',
                        'value' => isset($arResult['STORE']['description'])?$arResult['STORE']['description']:'',
                    ),
                )
            ),
        ),
        'BUTTONS' => array(
            'back_url' => $arResult['BACK_URL'],
            'standard_buttons' => true,
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);