<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
/** @var array $arParams */
global $APPLICATION;
CJSCore::RegisterExt('popup_menu', array('js' => array('/bitrix/js/main/popup_menu.js')));
\Bitrix\Main\UI\Extension::load("ui.buttons");
\Bitrix\Main\UI\Extension::load("ui.buttons.icons");
Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/components/bitrix/crm.interface.toolbar/templates/slider/style.css');

$toolbarID = $arParams['TOOLBAR_ID'];
$prefix =  $toolbarID.'_';

$items = array();
$moreItems = array();
$communicationPanel = null;
$documentButton = null;
$enableMoreButton = false;

foreach($arParams['BUTTONS'] as $item)
{
	if(!$enableMoreButton && isset($item['NEWBAR']) && $item['NEWBAR'] === true)
	{
		$enableMoreButton = true;
		continue;
	}

	if(isset($item['TYPE']) && $item['TYPE'] === 'crm-communication-panel')
	{
		$communicationPanel = $item;
		continue;
	}

	if(isset($item['TYPE']) && $item['TYPE'] === 'crm-document-button')
	{
		$documentButton = $item;
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

?><div id="<?=htmlspecialcharsbx($toolbarID)?>" class="pagetitle-container pagetitle-align-right-container"><?
if($communicationPanel)
{
		$data = isset($communicationPanel['DATA']) && is_array($communicationPanel['DATA']) ? $communicationPanel['DATA'] : array();
		$multifields = isset($data['MULTIFIELDS']) && is_array($data['MULTIFIELDS']) ? $data['MULTIFIELDS'] : array();

		$enableCall = !(isset($data['ENABLE_CALL']) && $data['ENABLE_CALL'] === false);

		$phones = isset($multifields['PHONE']) && is_array($multifields['PHONE']) ? $multifields['PHONE'] : array();
		$emails = isset($multifields['EMAIL']) && is_array($multifields['EMAIL']) ? $multifields['EMAIL'] : array();
		$messengers = isset($multifields['IM']) && is_array($multifields['IM']) ? $multifields['IM'] : array();

		$callButtonId = "{$toolbarID}_call" ;
		$messengerButtonId = "{$toolbarID}_messenger" ;
		$emailButtonId = "{$toolbarID}_email" ;

		$ownerInfo = isset($data['OWNER_INFO']) && is_array($data['OWNER_INFO']) ? $data['OWNER_INFO'] : array();
		?>
		<div class="crm-entity-actions-container">
			<?if(!$enableCall || empty($phones))
			{
				?><div id="<?=htmlspecialcharsbx($callButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-call-icon-not-available"></div><?
			}
			else
			{
				?><div id="<?=htmlspecialcharsbx($callButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-call-icon"></div><?
			}?>
			<script type="text/javascript">
				BX.ready(
					function()
					{
						BX.InterfaceToolBarPhoneButton.messages =
						{
							telephonyNotSupported: "<?=GetMessageJS('CRM_TOOLBAR_TELEPHONY_NOT_SUPPORTED')?>"
						};
						BX.InterfaceToolBarPhoneButton.create(
							this._id + "_call",
							{
								button: BX("<?=CUtil::JSEscape($callButtonId)?>"),
								data: <?=CUtil::PhpToJSObject($phones)?>,
								ownerInfo: <?=CUtil::PhpToJSObject($ownerInfo)?>
							}
						);
					}
				);
			</script>
			<!--<div class="webform-small-button webform-small-button-transparent crm-contact-menu-sms-icon-not-available"></div>-->
			<?if(empty($emails))
			{
				?><div id="<?=htmlspecialcharsbx($emailButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-mail-icon-not-available"></div><?
			}
			else
			{
				?><div id="<?=htmlspecialcharsbx($emailButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-mail-icon"></div><?
			}?>
			<script type="text/javascript">
				BX.ready(
					function()
					{
						BX.InterfaceToolBarEmailButton.create(
							this._id + "_email",
							{
								button: BX("<?=CUtil::JSEscape($emailButtonId)?>"),
								data: <?=CUtil::PhpToJSObject($emails)?>,
								ownerInfo: <?=CUtil::PhpToJSObject($ownerInfo)?>
							}
						);
					}
				);
			</script>
			<?if(empty($messengers))
			{
				?><div id="<?=htmlspecialcharsbx($messengerButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-im-icon-not-available"></div><?
			}
			else
			{
				?><div id="<?=htmlspecialcharsbx($messengerButtonId)?>" class="webform-small-button webform-small-button-transparent crm-contact-menu-im-icon"></div><?
			}?>
			<script type="text/javascript">
				BX.ready(
					function()
					{
						BX.InterfaceToolBarMessengerButton.create(
							this._id + "_im",
							{
								button: BX("<?=CUtil::JSEscape($messengerButtonId)?>"),
								data: <?=CUtil::PhpToJSObject($messengers)?>,
								ownerInfo: <?=CUtil::PhpToJSObject($ownerInfo)?>
							}
						);
					}
				);
			</script>
		</div>
		<?
}

if(!empty($moreItems))
{
	?>
	<button class="ui-btn ui-btn-md ui-btn-light-border ui-btn-themes ui-btn-icon-setting crm-btn-cogwheel"></button>
	<script type="text/javascript">
		BX.ready(
			function ()
			{
				BX.InterfaceToolBar.create(
					"<?=CUtil::JSEscape($toolbarID)?>",
					BX.CrmParamBag.create(
						{
							"containerId": "<?=CUtil::JSEscape($toolbarID)?>",
							"items": <?=CUtil::PhpToJSObject($moreItems)?>,
							"moreButtonClassName": "crm-btn-cogwheel"
						}
					)
				);
			}
		);
	</script><?
}

if($documentButton)
{
	$documentButtonId = $toolbarID.'_document';
	?>
	<button class="ui-btn ui-btn-md ui-btn-light-border ui-btn-dropdown ui-btn-themes crm-btn-dropdown-document" id="<?=htmlspecialcharsbx($documentButtonId);?>"><?=$documentButton['TEXT'];?></button>
	<script>
		BX.ready(function()
		{
			BX.bind(BX('<?=CUtil::JSEscape($documentButtonId);?>'), 'click', function()
			{
				BX.PopupMenu.show('<?=CUtil::JSEscape($documentButtonId);?>_menu', BX('<?=CUtil::JSEscape($documentButtonId);?>'), <?=CUtil::PhpToJSObject($documentButton['ITEMS']);?>, {
					offsetLeft: 0,
					offsetTop: 0,
					closeByEsc: true,
					className: 'document-toolbar-menu'
				});
			});
		});
	</script>
	<?
}

foreach($items as $item)
{
	$type = isset($item['TYPE']) ? $item['TYPE'] : '';
	$code = isset($item['CODE']) ? $item['CODE'] : '';
	$visible = isset($item['VISIBLE']) ? (bool)$item['VISIBLE'] : true;
	$text = isset($item['TEXT']) ? htmlspecialcharsbx($item['TEXT']) : '';
	$title = isset($item['TITLE']) ? htmlspecialcharsbx($item['TITLE']) : '';
	$link = isset($item['LINK']) ? htmlspecialcharsbx($item['LINK']) : '#';
	$icon = isset($item['ICON']) ? htmlspecialcharsbx($item['ICON']) : '';
	$onClick = isset($item['ONCLICK']) ? htmlspecialcharsbx($item['ONCLICK']) : '';

	if($type === 'crm-context-menu')
	{
		$menuItems = isset($item['ITEMS']) && is_array($item['ITEMS']) ? $item['ITEMS'] : array();

		?><div class="webform-small-button webform-small-button-blue webform-button-icon-triangle-down crm-btn-toolbar-menu"<?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>>
			<span class="webform-small-button-text"><?=$text?></span>
			<span class="webform-button-icon-triangle"></span>
		</div><?

		if(!empty($menuItems))
		{
			?><script type="text/javascript">
				BX.ready(
					function()
					{
						BX.InterfaceToolBar.create(
							"<?=CUtil::JSEscape($toolbarID)?>",
							BX.CrmParamBag.create(
								{
									"containerId": "<?=CUtil::JSEscape($toolbarID)?>",
									"prefix": "",
									"menuButtonClassName": "crm-btn-toolbar-menu",
									"items": <?=CUtil::PhpToJSObject($menuItems)?>
								}
							)
						);
					}
				);
			</script><?
		}
	}
	elseif($type == 'toolbar-conv-scheme')
	{
		$params = isset($item['PARAMS']) ? $item['PARAMS'] : array();

		$typeID = isset($params['TYPE_ID']) ? (int)$params['TYPE_ID'] : 0;
		$schemeName = isset($params['SCHEME_NAME']) ? $params['SCHEME_NAME'] : null;
		$schemeDescr = isset($params['SCHEME_DESCRIPTION']) ? $params['SCHEME_DESCRIPTION'] : null;
		$name = isset($params['NAME']) ? $params['NAME'] : $code;
		$entityID = isset($params['ENTITY_ID']) ? (int)$params['ENTITY_ID'] : 0;
		$entityTypeID = isset($params['ENTITY_TYPE_ID']) ? (int)$params['ENTITY_TYPE_ID'] : CCrmOwnerType::Undefined;
		$isPermitted = isset($params['IS_PERMITTED']) ? (bool)$params['IS_PERMITTED'] : false;
		$lockScript = isset($params['LOCK_SCRIPT']) ? $params['LOCK_SCRIPT'] : '';

		$options = CUserOptions::GetOption("crm.interface.toobar", "conv_scheme_selector", array());
		$hintKey = 'enable_'.strtolower($name).'_hint';
		$enableHint = !(isset($options[$hintKey]) && $options[$hintKey] === 'N');
		$hint = isset($params['HINT']) ? $params['HINT'] : array();

		$iconBtnClassName = $isPermitted ? 'crm-btn-convert' : 'crm-btn-convert crm-btn-convert-blocked';
		$originUrl = $APPLICATION->GetCurPage();

		$labelID = "{$prefix}{$code}_label";
		$buttonID = "{$prefix}{$code}_button";

		if($isPermitted && $entityTypeID === CCrmOwnerType::Lead)
		{
			Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/crm/crm.js');
		}

		?><div class="ui-btn-double ui-btn-primary">
			<button id="<?=htmlspecialcharsbx($labelID);?>" class="ui-btn-main"><?=htmlspecialcharsbx($schemeDescr)?></button>
			<button id="<?=htmlspecialcharsbx($buttonID);?>" class="ui-btn-extra"></button>
		</div>
		<script type="text/javascript">
			BX.ready(
				function()
				{
					//region Toolbar script
					<?$selectorID = CUtil::JSEscape($name);?>
					<?$originUrl = CUtil::JSEscape($originUrl);?>
					<?if($isPermitted):?>
						<?if($entityTypeID === CCrmOwnerType::Lead):?>
							BX.CrmLeadConversionSchemeSelector.create(
								"<?=$selectorID?>",
								{
									typeId: <?=$typeID?>,
									entityId: <?=$entityID?>,
									scheme: "<?=$schemeName?>",
									containerId: "<?=$labelID?>",
									labelId: "<?=$labelID?>",
									buttonId: "<?=$buttonID?>",
									originUrl: "<?=$originUrl?>",
									enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
									hintMessages: <?=CUtil::PhpToJSObject($hint)?>
								}
							);
						<?elseif($entityTypeID === CCrmOwnerType::Deal):?>
							BX.CrmDealConversionSchemeSelector.create(
								"<?=$selectorID?>",
								{
									entityId: <?=$entityID?>,
									scheme: "<?=$schemeName?>",
									containerId: "<?=$labelID?>",
									labelId: "<?=$labelID?>",
									buttonId: "<?=$buttonID?>",
									originUrl: "<?=$originUrl?>",
									enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
									hintMessages: <?=CUtil::PhpToJSObject($hint)?>
								}
							);

							BX.addCustomEvent(window,
								"CrmCreateQuoteFromDeal",
								function()
								{
									BX.CrmDealConverter.getCurrent().convert(
										<?=$entityID?>,
										BX.CrmDealConversionScheme.createConfig(BX.CrmDealConversionScheme.quote),
										"<?=$originUrl?>"
									);
								}
							);
							BX.addCustomEvent(window,
								"CrmCreateInvoiceFromDeal",
								function()
								{
									BX.CrmDealConverter.getCurrent().convert(
										<?=$entityID?>,
										BX.CrmDealConversionScheme.createConfig(BX.CrmDealConversionScheme.invoice),
										"<?=$originUrl?>"
									);
								}
							);
						<?elseif($entityTypeID === CCrmOwnerType::Quote):?>
							BX.CrmQuoteConversionSchemeSelector.create(
								"<?=$selectorID?>",
								{
									entityId: <?=$entityID?>,
									scheme: "<?=$schemeName?>",
									containerId: "<?=$labelID?>",
									labelId: "<?=$labelID?>",
									buttonId: "<?=$buttonID?>",
									originUrl: "<?=$originUrl?>",
									enableHint: <?=CUtil::PhpToJSObject($enableHint)?>,
									hintMessages: <?=CUtil::PhpToJSObject($hint)?>
								}
							);

							BX.addCustomEvent(window,
								"CrmCreateDealFromQuote",
								function()
								{
									BX.CrmQuoteConverter.getCurrent().convert(
										<?=$entityID?>,
										BX.CrmQuoteConversionScheme.createConfig(BX.CrmQuoteConversionScheme.deal),
										"<?=$originUrl?>"
									);
								}
							);

							BX.addCustomEvent(window,
								"CrmCreateInvoiceFromQuote",
								function()
								{
									BX.CrmQuoteConverter.getCurrent().convert(
										<?=$entityID?>,
										BX.CrmQuoteConversionScheme.createConfig(BX.CrmQuoteConversionScheme.invoice),
										"<?=$originUrl?>"
									);
								}
							);
						<?endif;?>
					<?elseif($lockScript !== ''):?>
						var showLockInfo = function()
						{
							<?=$lockScript?>
						};
						BX.bind(BX("<?=$labelID?>"), "click", showLockInfo );
						<?if($entityTypeID === CCrmOwnerType::Deal):?>
							BX.addCustomEvent(window, "CrmCreateQuoteFromDeal", showLockInfo);
							BX.addCustomEvent(window, "CrmCreateInvoiceFromDeal", showLockInfo);
						<?elseif($entityTypeID === CCrmOwnerType::Quote):?>
							BX.addCustomEvent(window, "CrmCreateDealFromQuote", showLockInfo);
							BX.addCustomEvent(window, "CrmCreateInvoiceFromQuote", showLockInfo);
						<?endif;?>
					<?endif;?>
					//endregion
				}
			);
		</script><?
	}
	elseif($type == 'bizproc-starter-button')
	{
		$hasTemplates = is_array($item['DATA']['templates']) && count($item['DATA']['templates']) > 0;
		if ($hasTemplates):

			CJSCore::Init('bp_starter');
			$starterButtonId = "{$toolbarID}_bp_starter";
		?>
		<span class="webform-small-button webform-small-button-transparent crm-bizproc-starter-icon"
			id="<?=htmlspecialcharsbx($starterButtonId)?>" title="<?=GetMessage('CRM_TOOLBAR_BIZPROC_STARTER_LABEL')?>">
		</span>
		<script type="text/javascript">
			BX.ready(
				function()
				{
					var button = BX('<?=CUTil::JSEscape($starterButtonId)?>');
					if (button)
					{
						var config = <?=\Bitrix\Main\Web\Json::encode($item['DATA'])?>;
						if (config.templates && config.templates.length > 0)
						{
							var starter = new BX.Bizproc.Starter(config);
							BX.bind(button, 'click', function(e)
							{
								starter.showTemplatesMenu(button);
							});
						}
					}
				}
			);
		</script>
		<?
		endif;
	}
	else
	{
		?><a target="_top" class="webform-small-button webform-small-button-blue crm-top-toolbar-add<?=$icon !== '' ? " {$icon}" : ''?>" href="<?=$link?>" title="<?=$title?>"<?=$onClick !== '' ? " onclick=\"{$onClick}; return false;\"" : ''?>><?=$text?></a><?
	}
}
?></div><?
$this->EndViewTarget();