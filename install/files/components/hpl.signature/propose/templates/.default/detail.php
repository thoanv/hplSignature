<?php
$urlTemplates = array(
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['detail'],
);


$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'POPUP_COMPONENT_NAME' => 'hpl.kpi:tieuchi.detail',
        'POPUP_COMPONENT_TEMPLATE_NAME' => '',
        'POPUP_COMPONENT_PARAMS' => [
            'ID' => $arResult['VARIABLES']['CAT_ID'],
            'URL_TEMPLATES' => $urlTemplates,
            'SEF_FOLDER' => $arResult['SEF_FOLDER'],
        ],
        "USE_PADDING" => false,
        "USE_UI_TOOLBAR" => "Y",
    ],
    $this->getComponent()
); ?>
