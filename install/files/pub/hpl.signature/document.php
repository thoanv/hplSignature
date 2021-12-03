<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->IncludeComponent(
    'hpl.signature:document',
    '',
    array(
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/hpl-signature/document/',
        'SEF_URL_TEMPLATES' => array(
            'details'   => '#DOCUMENT_ID#/',
            'edit'      => '#DOCUMENT_ID#/edit/',
        )
    ),
    false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
