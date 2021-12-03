<?php
use Bitrix\Main\Context;
use Bitrix\Main\Grid;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\UI\PageNavigation;
use Hpl\Entity\HplKpiTieuchiTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\UserTable;

class MySignatureComponents extends CBitrixComponent{

    private $user;
    public function __construct($component = null)
    {
        global $USER;
        $this->user = $USER->GetID();
        parent::__construct($component);
        if(!Loader::includeModule('hpl.signature')){
            ShowError('Not found module');
            return;
        }
    }

    public function executeComponent()
    {
        echo 11; die;
        global $APPLICATION;
        $title = 'Chữ kỹ của tôi';
        $user_id = $this->user;
        $user = UserTable::getById($user_id)->fetch();
        if(empty($user)){
            ShowError('Người dùng không tồn tại');
            return;
        }
        $APPLICATION->SetTitle($title);
        $store = array(
            'NAME'          => $user['NAME'],
            'LAST_NAME'     => $user['LAST_NAME'],
            'WORK_PHONE'    => $user['WORK_PHONE'],
            'user_id'       => $user_id,
            'id_card'       => '',
            'use_time'      => 0,
            'img_signature' => '',
            'date_end'      => '',
            'status'        => 0
        );
        $this->arResult = [
            'STORE'     => $store,
        ];
        $this->includeComponentTemplate();
    }
}

