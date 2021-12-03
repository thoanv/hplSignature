<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Hpl\Entity\SettingTable;

/** @global CMain $APPLICATION */
global $APPLICATION,$USER,$DB;
$isAdmin = $USER->IsAdmin();

$arResult['ACTIVE_ITEM_ID'] = isset($arParams['ACTIVE_ITEM_ID']) ? $arParams['ACTIVE_ITEM_ID'] : '';
$stdItems['MY_SIGNATURE'] = array(
    'ID' => 'MY_SIGNATURE',
    'MENU_ID' => 'menu_my_signature',
    'NAME' => Loc::getMessage('MY_SIGNATURE'),
    'TITLE' => Loc::getMessage('MY_SIGNATURE'), //title
    'URL' => CComponentEngine::MakePathFromTemplate('/hpl-signature/my-signature'),
    'ICON' => 'event',
);
$stdItems['DOCUMENT'] = array(
    'ID' => 'DOCUMENT',
    'MENU_ID' => 'menu_document',
    'NAME' => Loc::getMessage('DOCUMENT'),
    'TITLE' => Loc::getMessage('DOCUMENT'), //title
    'URL' => CComponentEngine::MakePathFromTemplate('/hpl-signature/document'),
    'ICON' => 'event',
);
$stdItems['PROPOSE'] = array(
    'ID' => 'PROPOSE',
    'MENU_ID' => 'menu-propose',
    'NAME' => Loc::getMessage('PROPOSE'),
    'TITLE' => Loc::getMessage('PROPOSE'), //title
    'URL' => CComponentEngine::MakePathFromTemplate('/hpl-signature/propose'),
    'ICON' => 'event',
);
$stdItems['CATEGORY'] = array(
    'ID' => 'CATEGORY',
    'MENU_ID' => 'menu-category',
    'NAME' => Loc::getMessage('CATEGORY'),
    'TITLE' => Loc::getMessage('CATEGORY'), //title
    'URL' => CComponentEngine::MakePathFromTemplate('/hpl-signature/category'),
    'ICON' => 'event',
);
$arResult['ITEMS'] = &array_values($stdItems);
unset($items);
$this->IncludeComponentTemplate();
