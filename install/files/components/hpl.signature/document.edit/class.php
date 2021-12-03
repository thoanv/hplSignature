<?php
defined('B_PROLOG_INCLUDED') || die;

use Academy\CrmStores\Entity\StoreTable;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;
use Hpl\Signature\Entity\HplSignatureCategoriesTable;
use Hpl\Signature\Entity\HplSignatureProposedGroupsTable;
use Hpl\Signature\Entity\BiblockSectionTable;
use Hpl\Signature\Entity\HplSignatureSignersTable;
use Hpl\Signature\Entity\HplSignatureHistoriesTable;
use Hpl\Signature\Entity\HplSignatureAccountsTable;

class DocumentEditComponent extends CBitrixComponent
{
    const FORM_ID = 'DOCUMENT_EDIT';
    const GRID_ID = 'DOCUMENT_EDIT';

    private $errors, $user;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;
        $this->user = $USER->GetID();
        parent::__construct($component);

        $this->errors = new ErrorCollection();
        if (!Loader::includeModule('hpl.signature')) {
            ShowError(Loc::getMessage('CRMSTORES_NO_MODULE'));
            return;
        }
    }

    public function executeComponent()
    {
        global $APPLICATION;
        $title = 'Thêm mới';
        $categories = HplSignatureCategoriesTable::getList(['select'=> ['id', 'name']])->fetchAll();
        $groups = HplSignatureProposedGroupsTable::getList(['filter' => ['status'=>1],'select'=> ['id', 'name']])->fetchAll();
        $departments = BiblockSectionTable::getList(['filter' => ['IBLOCK_ID'=>3],'select'=> ['ID', 'NAME']])->fetchAll();
        $store = array(
            'name'              => '',
            'security_mode'     => '',
            'category_id'       => '',
            'proposed_group_id'=> '',
            'status'            => '',
            'deadline'          => '',
            'reason'            => '',
            'direct_manager'    => '',
            'department_id'     => '',
            'file'              => '',
        );
        if (intval($this->arParams['CATEGORY_ID']) > 0) {
            $dbStore = HplSignatureDocumentsTable::getById($this->arParams['CATEGORY_ID']);
            $store = $dbStore->fetch();

            if (empty($store)) {
                ShowError(Loc::getMessage('PAGE_NOT_FOUND'));
                return;
            }
        }

        if (!empty($store['id'])) {
            $title = 'Thêm mới';
        }
        $APPLICATION->SetTitle($title);
        if (self::isFormSubmitted()) {
            $savedStoreId = $this->processSave($store);
            if ($savedStoreId > 0) {
                LocalRedirect($this->getRedirectUrl($savedStoreId));
            }
            $submittedStore = $this->getSubmittedStore();
            $store = array_merge($store, $submittedStore);
        }
        $this->arResult =array(
            'FORM_ID'    => self::FORM_ID,
            'GRID_ID'    => self::GRID_ID,
            'IS_NEW'     => empty($store['ID']),
            'TITLE'      => $title,
            'STORE'      => $store,
            'CATEGORIES' => $categories,
            'GROUPS'     => $groups,
            'DEPARTMENTS'=> $departments,
            'BACK_URL'   => $this->getRedirectUrl(),
            'ERRORS'     => $this->errors,
        );

        $this->includeComponentTemplate();
    }

    private function processSave($initialStore)
    {
        $submittedStore = $this->getSubmittedStore();

        $store = array_merge($initialStore, $submittedStore);
        $this->errors = self::validate($store);
        if (!$this->errors->isEmpty()) {
            return false;
        }
        $date = new \Bitrix\Main\Type\DateTime();
        $user = $this->user;
        $store['updated_by'] = $user;
        $store['updated_at'] = $date;
        $list_signers = [];
        foreach ($store['signer'] as $signer){
            $id_signer = intval(substr($signer, 1));
            array_push($list_signers, $id_signer);
        }
        $store['signer']     = json_encode($store['signer']);
        $store['direct_manager']= json_encode($store['direct_manager']);
        $store['extend'] = 'pdf';
//        if(isset($store['file'])){
//            $server = \Bitrix\Main\Context::getCurrent()->getServer();
//            $target_dir    = $server->getDocumentRoot() ."/local/components/hpl.signature/uploads/";
//
//            $year = date('Y');
//            $month = date('m');
//            if(!is_dir($target_dir.'/'.$year)){
//                $this->addDir($target_dir.'/'.$year.'/');
//                $target_dir = $server->getDocumentRoot() ."/local/components/hpl.signature/uploads/".$year.'/';
//            }else{
//                $target_dir = $server->getDocumentRoot() ."/local/components/hpl.signature/uploads/".$year.'/';
//            }
//            if(!is_dir($target_dir.$month)){
//                $this->addDir($target_dir.$month);
//                $target_dir = $target_dir.$month.'/';
//            }else{
//                $target_dir = $target_dir.$month.'/';
//            }
//            $files = $store['file'];
//            $temp = explode(".", $files["name"]);
//            $time = time();
//            $fileName = $temp[0].'_'.$time.'_'.$store['department_id'].'.'.$temp[1];
//            $path_file = "/local/components/hpl.signature/uploads/".$year."/".$month."/";
//
//            //Vị trí file lưu tạm trong server (file sẽ lưu trong uploads, với tên giống tên ban đầu)
//            $target_file   = $target_dir.$fileName;
//            $allowUpload   = true;
//            ////Những loại file được phép upload
//            $allowtypes    = array('docx', 'pdf', 'xlsx', 'doc');
//            //Lấy phần mở rộng của file (docx, pdf, ...)
//            $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
//            $store['extend'] = $fileType;
//            $store['name_file'] = $temp[0];
//            if (!in_array($fileType, $allowtypes ))
//            {
//                $allowUpload = false;
//                $store['extend'] = '';
//                $this->errors = self::validate($store['extend']);
//                if (!$this->errors->isEmpty()) {
//                    return false;
//                }
//            }
//            if ($allowUpload)
//            {
//                if (move_uploaded_file($files["tmp_name"], $target_file))
//                {
//                    $store['file'] = $path_file.$fileName;
//                }
//            }
//        }
        if (!empty($store['ID'])) {
            $result = HplSignatureDocumentsTable::update($store['id'], $store);
        } else {
            $store['created_at'] = $date;
            $store['created_by'] = $user;
            $result = HplSignatureDocumentsTable::add($store);
            $document_id = $result->getId();
            foreach ($list_signers as $signer_id){
                $account = HplSignatureAccountsTable::getList(['filter' => ['user_id'=> $signer_id]])->fetch();
                if(!empty($account)){
                    $signer_store['document_id'] =  $document_id;
                    $signer_store['user_id']     = $signer_id;
                    $signer_store['delegacy']    = 0;
                    $signer_store['method']      = $account['type'];
                    $signer_store['created_at']  = $date;
                    $signer_store['created_by']  = $user;
                    $signer_store['updated_at']  = $date;
                    $signer_store['updated_by']  = $user;
                    HplSignatureSignersTable::add($signer_store);
                }
            }
            $history['document_id'] = $document_id;
            $history['created_by']   = $user;
            $history['created_at']   = $date;
            $history['updated_at']   = $date;
            $history['note']         = 'Trình văn bản ký';
            HplSignatureHistoriesTable::add($history);
        }

        if (!$result->isSuccess()) {
            $this->errors->add($result->getErrors());
        }

        return $result->isSuccess() ? $result->getId() : false;
    }

    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $submittedStore = array(
            'name' => $request->get('name'),
            'category_id' => $request->get('category_id'),
            'proposed_group_id' => $request->get('proposed_group_id'),
            'department_id' => $request->get('department_id'),
            'direct_manager' => $request->get('direct_manager'),
            'signer' => $request->get('signer'),
            'file' => $request->get('pdf_base64'),
        );
        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();
        if (empty($store['name'])) {
            $errors->setError(new Error(Loc::getMessage('ERROR_EMPTY_NAME')));
        }
        if (empty($store['category_id'])) {
            $errors->setError(new Error('Thư mục lưu không được để trống'));
        }
        if (empty($store['proposed_group_id'])) {
            $errors->setError(new Error('Loại đề xuất không được để trống'));
        }
        if (empty($store['department_id'])) {
            $errors->setError(new Error('Phòng ban không được để trống'));
        }
        if (empty($store['file'])) {
            $errors->setError(new Error('File không được để trống'));
        }
        if (empty($store['signer'])) {
            $errors->setError(new Error('Người ký không được để trống'));
        }
        if (isset($store['extend']) && empty($store['extend'])) {
            $errors->setError(new Error('File không đúng định dạng'));
        }
        return $errors;
    }

    private static function isFormSubmitted()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $saveAndView = $request->get('saveAndView');
        $saveAndAdd = $request->get('saveAndAdd');
        $apply = $request->get('apply');
        return !empty($saveAndView) || !empty($saveAndAdd) || !empty($apply);
    }

    private function getRedirectUrl($savedStoreId = null)
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        if (!empty($savedStoreId) && $request->offsetExists('apply')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('DOCUMENT_ID' => $savedStoreId)
            );
        } elseif (!empty($savedStoreId) && $request->offsetExists('saveAndAdd')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('DOCUMENT_ID' => 0)
            );
        }

        $backUrl = $request->get('backurl');
        if (!empty($backUrl)) {
            return $backUrl;
        }

        if (!empty($savedStoreId) && $request->offsetExists('saveAndView')) {
            if(isset($_GET['IFRAME']) && $_GET['IFRAME']==='Y'){
                echo '<script>parent.BX.SidePanel.Instance.close();
                    </script>';
                die;

            }else{
                return CComponentEngine::makePathFromTemplate(
                    $this->arParams['URL_TEMPLATES']
                );
            }
        } else {
            return $this->arParams['SEF_FOLDER'];
        }
    }
    public function addDir($path){
        return (!mkdir($path, 0777) && !is_dir($path));
    }
}