<?php
/** @var CBitrixComponentTemplate $this */

$APPLICATION->IncludeComponent(
    'hpl.signature:control_panel',
    '',
    array(
        'ID' => 'MY_SIGNATURE',
        'ACTIVE_ITEM_ID' => 'MY_SIGNATURE',
    ),
    $component
);
$urlTemplates = array(
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
);
$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'POPUP_COMPONENT_NAME' => 'hpl.signature:my-signature.edit',
        'POPUP_COMPONENT_TEMPLATE_NAME' => '',
        'POPUP_COMPONENT_PARAMS' => [
            'ID' => $arResult['VARIABLES']['USER_ID'],
            'URL_TEMPLATES' => $urlTemplates,
            'SEF_FOLDER' => $arResult['SEF_FOLDER'],
        ],
        "USE_PADDING" => false,
        "USE_UI_TOOLBAR" => "Y",
    ],
    $this->getComponent()
); ?>