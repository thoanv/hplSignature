<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

class MySignatureComponents extends CBitrixComponent{
    const SEF_DEFAULT_TEMPLATES = array(
    );
    public function __construct($component = null)
    {
        parent::__construct($component);
        if (!Loader::includeModule('hpl.signature')) {
            ShowError(Loc::getMessage('MEETING_NOT_MODULE'));
            return;
        }

    }
    public function executeComponent()
    {
        global $APPLICATION;
        if (empty($this->arParams['SEF_MODE']) || $this->arParams['SEF_MODE'] != 'Y') {
            ShowError(Loc::getMessage('MEETING_SEF_NOT_ENABLED'));
            return;
        }
        if (empty($this->arParams['SEF_FOLDER'])) {
            ShowError(Loc::getMessage('MEETING_SEF_BASE_EMPTY'));
            return;
        }
        if (!is_array($this->arParams['SEF_URL_TEMPLATES'])) {
            $this->arParams['SEF_URL_TEMPLATES'] = array();
        }
        $sefTemplates = array_merge(self::SEF_DEFAULT_TEMPLATES, $this->arParams['SEF_URL_TEMPLATES']);

        $page = CComponentEngine::parseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $sefTemplates,
            $arVariables
        );
        if (empty($page)) {
            $page = 'edit';
            $APPLICATION->SetTitle(Loc::getMessage('CATEGORY_LIST'));
        }
        $this->arResult = array(
            'SEF_FOLDER' => $this->arParams['SEF_FOLDER'],
            'SEF_URL_TEMPLATES' => $sefTemplates,
            'VARIABLES' => $arVariables,
        );
        $this->includeComponentTemplate($page);
    }
}
