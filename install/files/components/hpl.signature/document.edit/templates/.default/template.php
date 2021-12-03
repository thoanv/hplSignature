<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');

/** @var CBitrixComponentTemplate $this */

/** @var ErrorCollection $errors */
$errors = $arResult['ERRORS'];
if(count($errors)){
?>
<div class="errors-validate alert alert-danger">
    <ul>
        <?
        foreach ($errors as $error) {
            /** @var Error $error */
            ?>
                <li><?ShowError($error->getMessage());?></li>
            <?
        }
        ?>
    </ul>

</div>

<?
}
CModule::includeModule('crm');
$categories = $arResult['CATEGORIES'];
$groups = $arResult['GROUPS'];
$departments = $arResult['DEPARTMENTS'];
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
                        'name'  => 'Tên đề xuất',
                        'type'  => 'text',
                        'value' => isset($arResult['STORE']['name'])?$arResult['STORE']['name']:'',
                        'required'=> true,
                    ),
                    array(
                        'id'    => 'category_id',
                        'name'  => 'Thư mục lưu',
                        'type'  => 'list_parent',
                        'items' => $categories,
                        'value' => isset($arResult['STORE']['category_id'])?$arResult['STORE']['category_id']:'',
                        'required'=> true,
                    ),
                    array(
                        'id'    => 'proposed_group_id',
                        'name'  => 'Loại đề xuất',
                        'type'  => 'list_parent',
                        'items' => $groups,
                        'value' => isset($arResult['STORE']['proposed_group_id'])?$arResult['STORE']['proposed_group_id']:'',
                        'required'=> true,
                    ),
                    array(
                        'id'    => 'department_id',
                        'name'  => 'Phòng ban',
                        'type'  => 'list_parent_ID',
                        'items' => $departments,
                        'value' => isset($arResult['STORE']['department_id'])?$arResult['STORE']['department_id']:'',
                        'required'=> true,
                    ),
                    array(
                        'id'    => 'direct_manager',
                        'name'  => 'Quản lý trực tiếp',
                        'type'  => 'get_info_user',
                        'array' => '[]',
                        'value' => isset($arResult['STORE']['direct_manager'])? json_decode($arResult['STORE']['direct_manager'], true): [],
                    ),
                    array(
                        'id'    => 'signer',
                        'name'  => 'Trình ký',
                        'type'  => 'get_info_user',
                        'array' => '[]',
                        'value' => isset($arResult['STORE']['signer'])? json_decode($arResult['STORE']['signer'], true): [],
                    ),
//                    array(
//                        'id'    => 'file',
//                        'name'  => 'File văn bản',
//                        'type'  => 'file_upload',
//                        'value' => isset($arResult['STORE']['file'])?$arResult['STORE']['file']:'',
//                    ),
                    array(
                        'id'    => 'file_',
                        'name'  => 'File văn bản',
                        'type'  => 'file_upload_',
                        'value' => isset($arResult['STORE']['file'])?$arResult['STORE']['file']:'',
                    ),
                )
            ),
        ),
        'ERRORS' => $errors,
        'BUTTONS' => array(
            'back_url' => $arResult['BACK_URL'],
            'standard_buttons' => true,
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);