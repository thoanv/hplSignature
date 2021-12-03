<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;


/** @var CBitrixComponentTemplate $this */

$APPLICATION->IncludeComponent(
    'hpl.signature:control_panel',
    '',
    array(
        'ID' => 'PROPOSE',
        'ACTIVE_ITEM_ID' => 'PROPOSE',
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
                'TEXT' => Loc::getMessage('ADD'),
                'TITLE' => Loc::getMessage('ADD'),
                'LINK' => CComponentEngine::makePathFromTemplate($urlTemplates['EDIT'], array('CAT_ID' => 0)),
                'ICON' => 'btn-add',
            ),
        )
    ),
    $this->getComponent(),
    array('HIDE_ICONS' => 'Y')
);

$APPLICATION->IncludeComponent(
    'hpl.signature:propose.list',
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
                            "/hpl-kpi/tieu-chi/(\\d+)/edit"
                        ],
                        options: {
                            width: 556,
                            cacheable: false,
                            allowChangeHistory: false,
                            events:{
                                onClose:function(){
                                    var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
                                    var gridObject = BX.Main.gridManager.getById('TIEU_CHI');

                                    if (gridObject.hasOwnProperty('instance')){
                                        gridObject.instance.reloadTable('POST', reloadParams);
                                    }
                                }
                            }
                        }
                    },
                    {
                        condition: [
                            "/hpl-kpi/tieu-chi/(\\d+)/"
                        ],
                        options: {
                            width: 556,
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

