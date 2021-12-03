<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->IncludeComponent(
    'hpl.signature:propose',
    '',
    array(
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/hpl-signature/propose/',
        'SEF_URL_TEMPLATES' => array(
            'details'   => '#PROPOSE_ID#/',
            'edit'      => '#PROPOSE_ID#/edit/',
        )
    ),
    false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
