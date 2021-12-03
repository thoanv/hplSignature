<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
    'PARAMETERS' => array(
        'SEF_MODE' => array(
            'details' => array(
                'NAME' => Loc::getMessage('CRMSTORES_DETAILS_URL_TEMPLATE'),
                'DEFAULT' => '#CATEGORY_ID#/',
                'VARIABLES' => array('CATEGORY_ID')
            ),
            'edit' => array(
                'NAME' => Loc::getMessage('CRMSTORES_EDIT_URL_TEMPLATE'),
                'DEFAULT' => '#CATEGORY_ID#/edit/',
                'VARIABLES' => array('CATEGORY_ID')
            )
        )
    )
);
