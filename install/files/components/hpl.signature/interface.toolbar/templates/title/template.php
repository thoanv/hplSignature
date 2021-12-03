<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
/** @var array $arParams */

CJSCore::RegisterExt('popup_menu', array('js' => array('/bitrix/js/main/popup_menu.js')));

\Bitrix\Main\UI\Extension::load("ui.buttons");
\Bitrix\Main\UI\Extension::load("ui.buttons.icons");

$toolbarId = $arParams['TOOLBAR_ID'];
global $DB,$USER;
$items = array();
$moreItems = array();
$enableMoreButton = false;
$sql = "SELECT user_id FROM hpl_settings where lai_xe =1";
$result = $DB->Query($sql)->fetch();
foreach($arParams['BUTTONS'] as $item)
{
	if(!$enableMoreButton && isset($item['NEWBAR']) && $item['NEWBAR'] === true)
	{
		$enableMoreButton = true;
		continue;
	}

	if($enableMoreButton)
	{
		$moreItems[] = $item;
	}
	else
	{
		$items[] = $item;
	}
}

$this->SetViewTarget('inside_pagetitle', 10000);

?><div id="<?=htmlspecialcharsbx($toolbarId)?>" class="pagetitle-container pagetitle-align-right-container" style="padding-right: 12px;"><?
if(!empty($moreItems))
{
	$buttonID = "{$toolbarId}_button";
	?>
	<script type="text/javascript">
		BX.ready(
			function ()
			{
				BX.InterfaceToolBar.create(
					"<?=CUtil::JSEscape($toolbarId)?>",
					BX.CrmParamBag.create(
						{
							"buttonId": "<?=CUtil::JSEscape($buttonID)?>",
							"items": <?=CUtil::PhpToJSObject($moreItems)?>
						}
					)
				);
			}
		);
	</script>
	<button id="<?=htmlspecialcharsbx($buttonID)?>" class="ui-btn ui-btn-md ui-btn-light-border ui-btn-themes ui-btn-icon-setting"></button>
	<?
}
$itemCount = count($items);
for($i = 0; $i < $itemCount; $i++)
{
	$item = $items[$i];

	$type = isset($item['TYPE']) ? $item['TYPE'] : '';
	$class = isset($item['CLASS']) ? $item['CLASS'] : '';
	$text = isset($item['TEXT']) ? htmlspecialcharsbx($item['TEXT']) : '';
	$title = isset($item['TITLE']) ? htmlspecialcharsbx($item['TITLE']) : '';
	$link = isset($item['LINK']) ? htmlspecialcharsbx($item['LINK']) : '#';
	$icon = isset($item['ICON']) ? htmlspecialcharsbx($item['ICON']) : '';
	$onClick = isset($item['ONCLICK']) ? htmlspecialcharsbx($item['ONCLICK']) : '';

	if($type === 'crm-context-menu')
	{
		$buttonID = "{$toolbarId}_button_{$i}";

		$menuItems = isset($item['ITEMS']) && is_array($item['ITEMS']) ? $item['ITEMS'] : array();
		?>
		<button id="<?=htmlspecialcharsbx($buttonID)?>" class="ui-btn ui-btn-primary <?=$class;?>" <?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>>
			<?=$text?>
		</button>
		<?
		if(!empty($menuItems))
		{
			?><script type="text/javascript">
				BX.ready(
					function()
					{
						BX.InterfaceToolBar.create(
							"<?=CUtil::JSEscape($toolbarId)?>",
							BX.CrmParamBag.create(
								{
									"buttonId": "<?=CUtil::JSEscape($buttonID)?>",
									"items": <?=CUtil::PhpToJSObject($menuItems)?>
								}
							)
						);
					}
				);
			</script><?
		}
	}
	elseif($type === 'crm-btn-double')
	{
		$buttonID = "{$toolbarId}_button_{$i}";
		$bindElementID = "{$buttonID}_anchor";
		$menuItems = isset($item['ITEMS']) && is_array($item['ITEMS']) ? $item['ITEMS'] : array();
		?>
		<script type="text/javascript">
			BX.ready(
				function()
				{
					BX.InterfaceToolBar.create(
						"<?=CUtil::JSEscape($toolbarId)?>",
						BX.CrmParamBag.create(
							{
								"buttonId": "<?=CUtil::JSEscape($buttonID)?>",
								"bindElementId": "<?=CUtil::JSEscape($bindElementID)?>",
								"items": <?=CUtil::PhpToJSObject($menuItems)?>,
								"autoClose": true
							}
						)
					);
				}
			);
		</script>
		<a href="<?=$link?>" class="ui-btn ui-btn-primary ui-btn-icon-add crm-btn-toolbar-add-empty reject-style" title="<?=$title?>"<?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>></a>
		<span id="<?=$bindElementID?>" class="ui-btn-primary ui-btn-double">
			<a href="<?=$link?>" class="ui-btn-main crm-btn-toolbar-add reject-style" title="<?=$title?>"<?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>><?=$text?></a>
			<a id="<?=$buttonID?>" class="ui-btn-extra"></a>
		</span>
		<?
	}
	elseif($type==='export_excel'){
	    ?>
        <a class="webform-small-button webform-small-button-transparent <?=($arResult['ENABLE_EXPORT'] ? '' : 'btn-lock')?>" href="<?=$arResult['EXPORT_HREF']?>">
            <span class="webform-small-button-left"></span>
            <span class="webform-button-icon"></span>
            <span class="webform-small-button-text"><?= \Bitrix\Main\Localization\Loc::getMessage('MEETING_EXPORT_EXCEL')?></span>
            <span class="webform-small-button-right"></span>
        </a>

        <?

    }
	else
	{
		?>
		<a href="<?=$link?>" class="ui-btn ui-btn-primary  crm-btn-toolbar-add-empty" title="<?=$title?>"<?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>><?=$title?></a>


        <button type="button" class="ui-btn ui-btn-primary crm-btn-toolbar-add"><?= $title?></button>

        <?
	}
}
?></div><?
$this->EndViewTarget();
