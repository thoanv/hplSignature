<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;
use Hpl\Signature\Helper\Helper;
$this->addExternalCss('/local/components/hpl.signature/themes/css/font-awesome.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/bootstrap.min.css');
$this->addExternalCss('/local/components/hpl.signature/themes/css/style.css');
$this->addExternalJs('/local/components/hpl.signature/themes/js/jquery-3.1.1.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/bootstrap.bundle.min.js');
$this->addExternalJs('/local/components/hpl.signature/themes/js/script.js');
/** @var CBitrixComponentTemplate $this */

if (!Loader::includeModule('crm')) {
    ShowError(Loc::getMessage('CRMSTORES_NO_CRM_MODULE'));
    return;
}

$asset = Asset::getInstance();
$asset->addJs('/bitrix/js/crm/interface_grid.js');

$gridManagerId = $arResult['GRID_ID'] . '_MANAGER';

$rows = array();
    foreach ($arResult['STORES'] as $store) {
    if($store['extend'] === 'docx'){
        $store['extend'] = '<img width="30" src="/local/components/hpl.signature/themes/images/word.png">';
    }elseif($store['extend'] === 'xslx' || $store['extend'] === 'doc'){
        $store['extend'] = '<img width="30" src="/local/components/hpl.signature/themes/images/xlsx.png">';
    }else{
        $store['extend'] = '<img width="30" src="/local/components/hpl.signature/themes/images/pdf.png">';
    }
    $info = '<div class="info-propose">
                <h6 class="font-weight-bold name">'.$store['name'].'</h6>
                <p class="detail">
                    <span class="dots">Nhóm:'.$store['group_name'].'</span>
                    <span>Phòng ban: '.$store['department_name'].'</span>
                </p>
                <p class="detail">
                    <span>Tải lên lúc : '.$store['created_at'].'</span>
                </p>
                <p class="detail">
                    <span>Cập nhập mới nhất : '.$store['updated_at'].'</span>
                </p>
            </div>';
    $store['name'] = $info;
    $signers = json_decode($store['signer'], true);

    $store['signer'] = Helper::getInformationUser($signers);
    $store['created_by'] = Helper::getInformationUser([], $store['created_by']);

    $viewUrl = CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['DETAIL'],
        array('DOCUMENT_ID' => $store['id'])
    );
    $editUrl = CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['EDIT'],
        array('DOCUMENT_ID' => $store['id'])
    );
    $signatureUrl = CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['SIGNATURE'],
        array('DOCUMENT_ID' => $store['id'])
    );

    $deleteUrlParams = http_build_query(array(
        'action_button_' . $arResult['GRID_ID'] => 'delete',
        'ID' => array($store['ID']),
        'sessid' => bitrix_sessid()
    ));
    $deleteUrl = $arParams['SEF_FOLDER'] . '?' . $deleteUrlParams;

    $rows[] = array(
        'id' => $store['ID'],
        'actions' => array(
            array(
                'TITLE' => Loc::getMessage('ACTION_DETAIL_TITLE'),
                'TEXT' => Loc::getMessage('ACTION_DETAIL_TITLE'),
                'ONCLICK' => 'BX.SidePanel.Instance.open(\''.$viewUrl.'\');',
                'DEFAULT' => true
            ),
            array(
                'TITLE' => 'Ký',
                'TEXT' => 'Ký',
                'ONCLICK' => 'BX.SidePanel.Instance.open(\''.$signatureUrl.'\');',
                'DEFAULT' => true
            ),
            array(
                'TITLE' => Loc::getMessage('ACTION_EDIT_TITLE'),
                'TEXT' => Loc::getMessage('ACTION_EDIT_TEXT'),
                'ONCLICK' => 'BX.Crm.Page.open(' . Json::encode($editUrl) . ')',
            ),
            array(
                'TITLE' => Loc::getMessage('ACTION_DELETE_TITLE'),
                'TEXT' => Loc::getMessage('ACTION_DELETE_TEXT'),
                'ONCLICK' => 'BX.CrmUIGridExtension.processMenuCommand(' . Json::encode($gridManagerId) . ', BX.CrmUIGridMenuCommand.remove, { pathToRemove: ' . Json::encode($deleteUrl) . ' })',
            )
        ),
        'data' => $store,
    );
}

$snippet = new Snippet();

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.grid',
    'titleflex',
    array(
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'ROWS' => $rows,
        'PAGINATION' => $arResult['PAGINATION'],
        'SORT' => $arResult['SORT'],
        'FILTER' => $arResult['FILTER'],
        'FILTER_PRESETS' => $arResult['FILTER_PRESETS'],
        'IS_EXTERNAL_FILTER' => false,
        'ENABLE_LIVE_SEARCH' => $arResult['ENABLE_LIVE_SEARCH'],
        'DISABLE_SEARCH' => $arResult['DISABLE_SEARCH'],
        'ENABLE_ROW_COUNT_LOADER' => true,
        'AJAX_ID' => '',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_LOADER' => null,
        'ACTION_PANEL' => array(
            'GROUPS' => array(
                array(
                    'ITEMS' => array(
                        $snippet->getRemoveButton(),
                        $snippet->getForAllCheckbox(),
                    )
                )
            )
        ),
        'EXTENSION' => array(
            'ID' => $gridManagerId,
            'CONFIG' => array(
                'ownerTypeName' => 'STORE',
                'gridId' => $arResult['GRID_ID'],
                'serviceUrl' => $arResult['SERVICE_URL'],
            ),
            'MESSAGES' => array(
                'deletionDialogTitle' => Loc::getMessage('CRMSTORES_DELETE_DIALOG_TITLE'),
                'deletionDialogMessage' => Loc::getMessage('CRMSTORES_DELETE_DIALOG_MESSAGE'),
                'deletionDialogButtonTitle' => Loc::getMessage('CRMSTORES_DELETE_DIALOG_BUTTON'),
            )
        ),
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y',)
);