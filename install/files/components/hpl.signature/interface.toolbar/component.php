<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!is_array($arParams['BUTTONS']))
	$arParams['BUTTONS'] = array();
global $APPLICATION;
$arParams['TOOLBAR_ID'] = isset($arParams['TOOLBAR_ID']) && $arParams['TOOLBAR_ID'] !== ''
	? preg_replace('/[^a-z0-9_]/i', '', $arParams['TOOLBAR_ID'])
	: 'toolbar_'.(strtolower(randString(5)));
$excelMode = false;
$enableExport = true;
if (isset($_REQUEST['excel']) && $_REQUEST['excel'] === 'Y' && $this->enableExport)
    $excelMode = 'Y';
$arResult['ENABLE_EXPORT'] = $excelMode;
$arResult['EXPORT_HREF'] = $enableExport?$APPLICATION->GetCurPageParam('excel=Y'): 'javascript: viOpenTrialPopup(\'excel-export\');';
$this->IncludeComponentTemplate();


