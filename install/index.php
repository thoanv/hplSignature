<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

defined('B_PROLOG_INCLUDED') || die;

class hpl_signature extends CModule
{
    const MODULE_ID = 'hpl.signature';
    var $MODULE_ID = self::MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('HP.MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('HP.MODULE_DESC');
        $this->PARTNER_NAME = Loc::getMessage('HP.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('HP.PARTNER_URI');
    }

    function DoInstall()
    {
        ModuleManager::registerModule(self::MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
        $this->addMenu();
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        $step = isset($_GET['step']) ? $_GET['step'] : 0;
        $save_tables = isset($_GET['save_tables']) ? $_GET['save_tables'] : 'N';
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . self::MODULE_ID . "/install/step1.php");
        } elseif ($step == 2) {
            $this->UnInstallFiles();
            $this->removeMenu();
            if ($save_tables != "Y") {
                $this->UnInstallDB();
            }
            ModuleManager::unRegisterModule(self::MODULE_ID);
            $APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . self::MODULE_ID . "/install/step2.php");
        }
    }

    function InstallDB()
    {
        global $DB, $APPLICATION;
        $errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/" . self::MODULE_ID . "/install/db/" . strtolower($DB->type) . "/signature/install.sql");
        if ($errors !== false) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }
    }

    public function UnInstallDB()
    {
        global $APPLICATION, $DB;
        $errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/" . self::MODULE_ID . "/install/db/" . strtolower($DB->type) . "/signature/uninstall.sql");
        if ($errors !== false) {
            $APPLICATION->ThrowException(implode("", $errors));
            return false;
        }
    }

    function InstallFiles()
    {
        $documentRoot = Application::getDocumentRoot();
        CopyDirFiles(
            __DIR__ . '/files/components',
            $documentRoot . '/local/components',
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . '/files/pub',
            $documentRoot . '/company',
            true,
            true
        );
        CUrlRewriter::Add(array(
            'CONDITION' => '#^/hpl.signature/document#',
            'RULE' => '',
            'ID' => 'hpl.signature:document',
            'PATH' => '/company/hpl.signature/document.php',
            'SORT' => 100,
        ));
        CUrlRewriter::Add(array(
            'CONDITION' => '#^/hpl.signature/propose#',
            'RULE' => '',
            'ID' => 'hpl.signature:propose',
            'PATH' => '/company/hpl.signature/propose.php',
            'SORT' => 100,
        ));
        CUrlRewriter::Add(array(
            'CONDITION' => '#^/hpl.signature/categories#',
            'RULE' => '',
            'ID' => 'hpl.signature:categories',
            'PATH' => '/company/hpl.signature/categories.php',
            'SORT' => 100,
        ));

    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/company/hpl.signature');
        DeleteDirFilesEx('/local/components/hpl.signature');

        CUrlRewriter::Delete(array(
            'ID' => 'hpl.signature:tieu-chi',
            'PATH' => '/company/hpl.signature/document.php',
        ));
        CUrlRewriter::Delete(array(
            'ID' => 'hpl.signature:kpi',
            'PATH' => '/company/hpl.signature/propose.php',
        ));
        CUrlRewriter::Delete(array(
            'ID' => 'hpl.signature:setup',
            'PATH' => '/company/hpl.signature/categories.php',
        ));
    }

    private function addMenu()
    {
        // cài  tên menu ở đây
        $itemLink = '/hpl-signature/document';
        $itemText = 'Chữ ký số';
        $itemID = crc32($itemLink);
        $siteID = 's1';
        $newItem = array(
            "TEXT" => $itemText,
            "LINK" => $itemLink,
            "ID" => $itemID,
            "NEW_PAGE" => "N"
        );
        $selfItems = CUserOptions::GetOption("intranet", "left_menu_self_items_" . $siteID);
        if (is_array($selfItems) && !empty($selfItems)) {
            $selfItems[] = $newItem;
        } else {
            $selfItems = array($newItem);
        }
        //add menu custom
        CUserOptions::SetOption("intranet", "left_menu_self_items_" . $siteID, $selfItems);
        //add menu all
        COption::SetOptionString("intranet", "left_menu_items_to_all_" . $siteID, serialize($selfItems), false, $siteID);
    }

    private function removeMenu()
    {
        // xóa ở đây
        global $DB;
        $siteID = 's1';
        $itemId = crc32('/cash/management');
        //delete menu for allff
        $res = $DB->Query("SELECT * FROM b_option WHERE name='left_menu_items_to_all_s1' AND module_id='intranet'");
        if ($res_array = $res->Fetch()) {
            $adminOption = unserialize($res_array['VALUE']);
            foreach ($adminOption as $key => $item) {
                if ($item["ID"] == $itemId) {
                    unset($adminOption[$key]);
                    if (empty($adminOption)) {
                        COption::RemoveOption("intranet", "left_menu_items_to_all_" . $siteID);
                    } else {
                        COption::SetOptionString("intranet", "left_menu_items_to_all_" . $siteID, serialize($adminOption), false, $siteID);
                    }
                    break;
                }
            }
        }
        //delete menu custom
        $selfItems = CUserOptions::GetOption("intranet", "left_menu_self_items_" . $siteID);
        if (is_array($selfItems)) {
            foreach ($selfItems as $key => $item) {
                if ($item["ID"] == $itemId) {
                    unset($selfItems[$key]);
                    break;
                }
            }
            if (!empty($selfItems)) {
                CUserOptions::SetOption("intranet", "left_menu_self_items_" . $siteID, $selfItems);
            } else {
                CUserOptions::DeleteOption("intranet", "left_menu_self_items_" . $siteID);
            }
        }
    }
}
