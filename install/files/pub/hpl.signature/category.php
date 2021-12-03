<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->IncludeComponent(
    'hpl.signature:category',
    '',
    array(
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/hpl-signature/category/',
        'SEF_URL_TEMPLATES' => array(
            'details'   => '#CATEGORY_ID#/',
            'edit'      => '#CATEGORY_ID#/edit/',
        )
    ),
    false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
