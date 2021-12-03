<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->IncludeComponent(
    'hpl.signature:my-signature',
    '',
    array(
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/hpl-signature/my-signature/',
        'SEF_URL_TEMPLATES' => array()
    ),
    false
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');