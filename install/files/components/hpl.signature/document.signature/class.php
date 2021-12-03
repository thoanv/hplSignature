<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/vendor/autoload.php');
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;
use Hpl\Signature\Entity\HplSignatureSignersTable;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
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
//        $docxPath = $_SERVER['DOCUMENT_ROOT'].$store['file'];
//        $pdfPath = $_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/uploads/2021/11/DANH MỤC SẢN PHẨM BÁN_1636106761_18.pdf';
//        $domPdfPath = realpath($_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/vendor/dompdf/dompdf');
//        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
//        \PhpOffice\PhpWord\Settings::setDefaultFontName('Tahoma');
//        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
//        $phpWord = new \PhpOffice\PhpWord\PhpWord();
//        $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxPath);
//
//        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');
//        $xmlWriter->save($pdfPath);
//        echo $domPdfPath; die;



        $signer = \Hpl\Signature\Entity\HplSignatureAccountsTable::getList([
            'filter' => ['user_id' => $this->user]
        ])->fetch();
        $APPLICATION->SetTitle($store['name']);
        $this->arResult = array(
            'FORM_ID'   => self::FORM_ID,
            'STORE'     => $store,
            'SIGNER'   => $signer,
        );

        $this->includeComponentTemplate();
    }
}

