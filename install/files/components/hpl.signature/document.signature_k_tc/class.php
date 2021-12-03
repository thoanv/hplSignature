<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/document.signature/templates/.default/fpdf/fpdf.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/document.signature/templates/.default/fpdf/fpdi.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/local/modules/hpl.signature/lib/helper/OAuth2Config.php');
define('FPDF_FONTPATH',$_SERVER['DOCUMENT_ROOT'].'/local/components/hpl.signature/document.signature/templates/.default/fonts');
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Hpl\Signature\Helper\Helper;
use Hpl\Signature\Entity\HplSignatureDocumentsTable;
use Hpl\Signature\Entity\HplSignatureAccountsTable;

class DocumentDetailComponents extends CBitrixComponent
{
    const FORM_ID = 'DOCUMENT_DETAIL';
    private $errors, $user;

    public function __construct(CBitrixComponent $component = null)
    {
        global $USER;
        $this->user = $USER->GetID();
        parent::__construct($component);
        $this->errors = new ErrorCollection();
        if(!Loader::includeModule('hpl.signature')){
            ShowError('Not found module');
            return;
        }
    }

    public function executeComponent()
    {
        global $APPLICATION;
        $config = new OAuth2Config();
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

        $signer = HplSignatureAccountsTable::getList([
            'filter' => ['user_id' => $this->user]
        ])->fetch();
        $APPLICATION->SetTitle($store['name']);
        $errors = new ErrorCollection();
        if (self::isFormSubmitted()) {
            $context = Context::getCurrent();
            $request = $context->getRequest();
            $data = $request->getValues();
            if(isset($data['access_token'])){
                $height = $request->get('heightValue');
                $width = $request->get('widthValue');
                $img_signatre = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$signer['img_signature']));
                $payload = [
                    'client_id' => $config->client_id,
                    'client_secret' => $config->client_secret,
                    'username' => $signer['login'],
                    'password' => $signer['password'],
                    'grant_type' => 'password'
                ];
                $curl = curl_init();
                curl_setopt_array($curl,[
                    CURLOPT_URL => $config->token_url,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/x-www-form-urlencoded",
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => http_build_query($payload)
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response);
                if(isset($response->error)){
                    echo ('ERROR : '.$response->error_description);
                    exit();
                }else{
                    $_SESSION['access_token_vnpt'] = $response->access_token;
                    $_SESSION['refresh_token_vnpt'] = $response->refresh_token;

                    $data_getCredentical = '{}';
                    $msg = Helper::api_get_credentical_curl();

                    $credentials = $msg->content[0];
                    //3 get certBase64
                    $msg = Helper::api_get_certBase64([
                        "credentialId" => $credentials,
                        "certificates" => "chain",
                        "certInfo" => true,
                        "authInfo" => true
                    ]);

                    $certBase64 = $msg->cert->certificates[0];
                    $certBase64 = str_replace("\r\n","",$certBase64);
                    $unsignDataBase64 = $store['file'];
                    /*
                    rectangle : llx,lly,urx,ury
                    Trong đó llx,lly là tọa độ, Gốc tọa độ ở điểm dưới cùng bên trái
                    urx = llx + chiều dài chữ ký
                    ury = lly + chiều cao chữ ký
                    */
                    $options = [
                        "fontColor" => "000000",
                        "fontName" => "Time", // 3 option : Time/Roboto/Arial
                        "fontSize" => 10,
                        "fontStyle"=> 0, //0:Normal,1:Bold,2:Italic,3:Bold&Italic,4:Underline
                        "imageSrc" => $img_signatre,
                        "visibleType" => 5, //1:TextOnly, 2:TEXT_WITH_LOGO_LEFT, 3:LOGO_ONLY, 4:TEXT_WITH_LOGO_TOP, 5:TEXT_WITH_BACKGROUND
                        "comment" => [
                    //
                        ],
                        "signatures" => [
                            [
                                "page" => 1,
                                "rectangle" => '0,500,'.$width.','.$height.''
                            ],
                        ]
                    ];
                    $data_sign = [
                        'credentialId' => $credentials,
                        'refTranId' => Helper::getGUID(),
                        'description' => 'Test php signer',
                        'datas' => [
                            [
                                "name" => $store['name'].".pdf",
                                "dataBase64" => $unsignDataBase64,
                                "options" => json_encode($options),
                            ]
                        ]
                    ];
                    $msg = Helper::api_sign_curl($data_sign);
                    $tranId = isset($msg->content->tranId) ? $msg->content->tranId : "";

                    if($tranId != ""){
                        $dataTran['tran_id'] = $tranId;
                        $result = HplSignatureDocumentsTable::update($store['id'], $dataTran);
                        if($result->isSuccess()){
                            $errors->setError(new Error('Gửi xác nhận ký thành công vui lòng vào App để xác nhận'));
                        }
                    }else{
                        echo("Ký số thất bại");
                        exit();
                    }

                }
            }
            if(isset($data['download'])){
                if($store['tran_id']){
                    $data_getTranInfo = [
                        "tranId" => $store['tran_id']
                    ];
                    $msg = Helper::api_get_tranInfo_curl($data_getTranInfo);
                    $status = $msg->content->tranStatus;
                    if($status != 1){
                        $errors->setError(new Error('Bạn chưa thực hiện đầy đủ các thao tác trên App. Mời thực hiện lại'));
                    }else{
                        $dataSigned = $msg->content->documents[0]->dataSigned;
                        $datas['file'] = $dataSigned;
                        HplSignatureDocumentsTable::update($store['id'], $datas);
//                        if($result->isSuccess()){
//                            $errors->setError(new Error('Đã '));
//                        }
                    }
                }else{
                    echo("Vui lòng check trên App để xác nhận bạn đã bỏ qua bước xác nhận");
                    exit();
                }

            };
        }
        $this->errors = $errors;

        $this->arResult = array(
            'FORM_ID'   => self::FORM_ID,
            'STORE'     => $store,
            'SIGNER'    => $signer,
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
        $data['category_id'] = $store['category_id'];
        $data['classify_id'] = $store['classify_id'];
        $data['note'] = $store['note'];
        $data['updated_at'] = $date;
        $data['updated_by'] = $user;
        $data['money'] = str_replace(',', '', $store['money']);
        $store['time_only'] = $this->convertDate($store['time_only']);
        $data['time_only'] = new Type\Date($store['time_only'], 'Y-m-d');

        if (!empty($store['id'])) {
            $result = HplCostItemsTable::update($store['id'], $data);
            if (!$result->isSuccess()) {
                $this->errors->add($result->getErrors());
            }
        }

        return $result->isSuccess() ? $result->getId() : false;
    }

    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $submittedStore = array(
            'category_id'   => $request->get('category_id'),
            'time_only'     => $request->get('time_only'),
            'money'         => $request->get('money'),
            'classify_id'   => $request->get('classify_id'),
            'note'          => $request->get('note'),
        );
        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();
        if (empty($store['category_id'])) {
            $errors->setError(new Error('Tên danh mục không được để trống'));
        }
        if (empty($store['time_only'])) {
            $errors->setError(new Error('Ngày áp dụng không được để trống'));
        }
        if (empty($store['money'])) {
            $errors->setError(new Error('Số tiền không được để trống'));
        }
        if (empty($store['classify_id'])) {
            $errors->setError(new Error('Phân loại không được để trống'));
        }
        return $errors;
    }

    private static function isFormSubmitted()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $accessToken = $request->get('access_token');
        $saveAndAdd = $request->get('download');
        $apply = $request->get('apply');
        return !empty($accessToken) || !empty($saveAndAdd) || !empty($apply);
    }

    private function getRedirectUrl($savedStoreId = null)
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        if (!empty($savedStoreId) && $request->offsetExists('apply')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('CLASSIFY_ID' => $savedStoreId)
            );
        } elseif (!empty($savedStoreId) && $request->offsetExists('saveAndAdd')) {
            return CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                array('CLASSIFY_ID' => 0)
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
}

