<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;


/** @var CBitrixComponentTemplate $this */

$APPLICATION->IncludeComponent(
    'hpl.signature:control_panel',
    '',
    array(
        'ID' => 'DOCUMENT',
        'ACTIVE_ITEM_ID' => 'DOCUMENT',
    ),
    $component
);

//$APPLICATION->setTitle(Loc::getMessage('CALENDAR_BOOK_ROOM'));
$urlTemplates = array(
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['detail'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
    'SIGNATURE' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['signature'],
);
$APPLICATION->IncludeComponent(
    'hpl.signature:interface.toolbar',
    'title',
    array(
        'TOOLBAR_ID' => 'CRMSTORES_TOOLBAR',
        'BUTTONS' => array(
            array(
                'TEXT' => Loc::getMessage('ADD'),
                'TITLE' => Loc::getMessage('ADD'),
                'LINK' => CComponentEngine::makePathFromTemplate($urlTemplates['EDIT'], array('DOCUMENT_ID' => 0)),
                'ICON' => 'btn-add',
            ),
        )
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);

$APPLICATION->IncludeComponent(
    'hpl.signature:document.list',
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
                            "/hpl-signature/document/(\\d+)/edit"
                        ],
                        options: {
                            width: 1000,
                            cacheable: false,
                            allowChangeHistory: false,
                            events:{
                                onClose:function(){
                                    var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
                                    var gridObject = BX.Main.gridManager.getById('DOCUMENT');
                                    console.log(gridObject);

                                    if (gridObject !== null) {
                                        gridObject.instance.reload();
                                    }
                                }
                            }
                        }
                    },
                    {
                        condition: [
                            "/hpl-signature/document/(\\d+)/signature"
                        ],
                        options: {
                            cacheable: false,
                            allowChangeHistory: false,
                            events:{
                                onClose:function(){
                                    var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
                                    var gridObject = BX.Main.gridManager.getById('DOCUMENT');
                                    console.log(gridObject);

                                    if (gridObject !== null) {
                                        gridObject.instance.reload();
                                    }
                                }
                            }
                        }
                    },
                    {
                        condition: [
                            "/hpl-signature/document/(\\d+)/"
                        ],
                        options: {
                            cacheable: false,
                            allowChangeHistory: false,
                            events:{
                                onClose:function(){
                                    var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
                                    var gridObject = BX.Main.gridManager.getById('DOCUMENT');
                                    console.log(gridObject);

                                    if (gridObject !== null) {
                                        gridObject.instance.reload();
                                    }
                                }
                            }
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

