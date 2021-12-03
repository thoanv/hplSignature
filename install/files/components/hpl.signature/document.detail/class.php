<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;
use Hpl\Signature\Entity\HplSignatureSignersTable;

class DocumentDetailComponents extends CBitrixComponent
{
    const FORM_ID = 'DOCUMENT_DETAIL';
    private $user;

    public function __construct(CBitrixComponent $component = null)
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
        global $APPLICATION;

        $APPLICATION->SetTitle('Chi tiết');

        $dbStore = HplSignatureDocumentsTable::getList([
            'filter' => ['id'=> $this->arParams['ID']],
            'select' => [
                'group_name' => 'GROUPS.name',
                'cate_name'  => 'CATEGORY.name',
                'department_name' => 'DEPARTMENT.NAME',
                '*'
            ],
        ]);
        $store = $dbStore->fetch();
        if (empty($store)) {
            ShowError(Loc::getMessage('NOT_FOUND'));
            return;
        }
        $signers = HplSignatureSignersTable::getList([
            'filter' => ['documents_id' => $this->arParams['ID']]
        ])->fetchAll();
        $APPLICATION->SetTitle($store['name']);
        $this->arResult = array(
            'FORM_ID'   => self::FORM_ID,
            'STORE'     => $store,
            'SIGNERS'   => $signers,
        );

        $this->includeComponentTemplate();
    }
}

