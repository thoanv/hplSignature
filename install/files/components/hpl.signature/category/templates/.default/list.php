<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;


/** @var CBitrixComponentTemplate $this */

$APPLICATION->IncludeComponent(
    'hpl.signature:control_panel',
    '',
    array(
        'ID' => 'CATEGORY',
        'ACTIVE_ITEM_ID' => 'CATEGORY',
    ),
    $component
);

//$APPLICATION->setTitle(Loc::getMessage('CALENDAR_BOOK_ROOM'));
$urlTemplates = array(
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['details'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
);
$APPLICATION->IncludeComponent(
    'hpl.signature:interface.toolbar',
    'title',
    array(
        'TOOLBAR_ID' => 'CRMSTORES_TOOLBAR',
        'BUTTONS' => array(
            array(
                'TEXT'  => "Thêm mới",
                'TITLE' => "Thêm mới",
                'LINK'  => CComponentEngine::makePathFromTemplate($urlTemplates['EDIT'], array('CATEGORY_ID' => 0)),
                'ICON'  => 'btn-add',
            ),
        )
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);

$APPLICATION->IncludeComponent(
    'hpl.signature:category.list',
    '',
    array(
        'URL_TEMPLATES' => $urlTemplates,
        'SEF_FOLDER' => $arResult['SEF_FOLDER'],
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y',)
);
?>
<script>
    BX.ready(function()
    {
        <?php
        if (empty(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('IFRAME')))
        {
        \Bitrix\Main\UI\Extension::load(['sidepanel']);
        ?>
        BX.SidePanel.Instance.bindAnchors({
            rules:
                [
                    {
                        condition: [
                            "/hpl-signature/category/(\\d+)/edit"
                        ],
                        options: {
                            width: 1000,
                            cacheable: false,
                            allowChangeHistory: false,
                            events:{
                                onClose:function(){
                                    var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
                                    var gridObject = BX.Main.gridManager.getById('DOCUMENT');

                                    if (gridObject.hasOwnProperty('instance')){
                                        gridObject.instance.reloadTable('POST', reloadParams);
                                    }
                                }
                            }
                        }
                    },
                    {
                        condition: [
                            "/hpl-signature/category/(\\d+)/"
                        ],
                        options: {
                            cacheable: false,
                            allowChangeHistory: false
                        }
                    }

                ]
        });
        <?php
        }
        ?>

    });
</script>
<style>
    .crm-btn-toolbar-add{
        display:none;
    }
</style>

