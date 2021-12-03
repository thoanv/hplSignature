<?
/** var CMain $APPLICATION */


//require_once(rtrim($_SERVER["DOCUMENT_ROOT"], "/\\") . "/local/modules/entity/file_request.php");

use Bitrix\Iblock\SectionTable;
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Hpl\Entity\RequestFileTable;
use Hpl\Entity\RequestTable;

IncludeModuleLangFile(__FILE__);

class MAsset
{
    private static $arr_category = [];
    public static function getTimeRequestSigin($userId,$siginRequest){
        CModule::includeModule('hpl.sigin');
        $strDb = SiginApproveC2Table::getList([
            'filter'=>['user_id'=>$userId,'request_id'=>$siginRequest]
        ])->fetch();
        $result = '';
        if($strDb['status']=="P"){
            $result = '';
        }
        elseif($strDb['status']=="Y"){
           $name = self::getFullName($userId);
           $time =$strDb['created_at'];
            $result = ' <div class="artist-name-reviewer-content">
                           '. $name .'
                        </div>
                        <div class="artist-name-reviewer-content-gmail">
                            ' . $time . '
                        </div>';
        }
        return $result;
    }
    public static function getNameAndImage($user)
    {
        $userImage = \CFile::resizeImageGet(
            $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
            BX_RESIZE_IMAGE_EXACT, false
        );
        $userName = \CUser::formatName(\CSite::getNameFormat(), $user, true, false);
        $userSrc = !empty($userImage['src']) ? $userImage['src'] : '/local/modules/hpl.request/install/images/user_default.png';
        return "<span class='main-grid-cell-content' data-prevent-default='true' >
                       <div class='tasks-grid-username-wrapper'>
                            <a class='task-grid-username'>
                                <span class='tasks-grid-avatar tasks-grid-avatar-extranet' style=\"background-image: url('$userSrc')\"/></span>
                                <span class='tasks-grid-username-inner tasks-grid-avatar-extranet'>$userName</span>
                             </a>
                        </div>
                </span>";
    }

    public static function getNameAndImageById($id)
    {
        $user = UserTable::getById($id)->fetch();
        $data = '';
        if ($user) {
            $userImage = \CFile::resizeImageGet(
                $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
                BX_RESIZE_IMAGE_EXACT, false
            );
            $userName = \CUser::formatName(\CSite::getNameFormat(), $user, true, false);
            $userSrc = !empty($userImage['src']) ? $userImage['src'] : '/local/modules/hpl.request/install/images/user_default.png';
            $data = "<span class='main-grid-cell-content' data-prevent-default='true' >
                       <div class='tasks-grid-username-wrapper'>
                            <a class='task-grid-username'>
                                <span class='tasks-grid-avatar tasks-grid-avatar-extranet' style=\"background-image: url('$userSrc')\"/></span>
                                <span class='tasks-grid-username-inner tasks-grid-avatar-extranet'>$userName</span>
                             </a>
                        </div>
                </span>";
        }
        return $data;
    }

    public static function getUserByDepartment($cate_id)
    {
        global $DB;
        $users=[];
        if($cate_id)
        {
            $res = $DB->Query("select * FROM b_utm_user WHERE VALUE_INT =$cate_id");
            while ($item = $res->Fetch()) {
                $users[]=MAsset::getMember($item['VALUE_ID']);
            }
        }
        return $users;
    }

    public static function getMember($id)
    {
        $user = UserTable::getById($id)->fetch();
        $userImage = \CFile::resizeImageGet(
            $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
            BX_RESIZE_IMAGE_EXACT, false
        );
        $user = [
            'ID' => $user['ID'],
            'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '/local/modules/hpl.request/install/images/user_default.png',
            'NAME' => \CUser::formatName(\CSite::getNameFormat(), $user, true, false),
        ];
        return $user;
    }

    public static function getFullName($userId)
    {
        $user = UserTable::getById($userId)->fetch();
        return \CUser::formatName(\CSite::getNameFormat(), $user, true, false);
    }


    public static function convertDateForm($rqStartDate)
    {
        $sDate = explode('/', $rqStartDate);
        $sDate = $sDate[2] . '-' . $sDate[1] . '-' . $sDate[0];
        return $sDate;
    }



    public static function getAllMembers()
    {
        global $USER;
        $arMember = UserTable::getList(array('filter' => ['ACTIVE' => 'Y', '!=ID' => $USER->GetID()]))->fetchAll();
        $members = [];
        foreach ($arMember as $arUser) {
            $userImage = \CFile::resizeImageGet(
                $arUser['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
                BX_RESIZE_IMAGE_EXACT, false
            );
            $members[] = [
                'ID' => $arUser['ID'],
                'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '/local/modules/nms.asset/install/images/user_default.png',
                'NAME' => \CUser::formatName(\CSite::getNameFormat(), $arUser, true, false)
            ];
        }
        return $members;
    }


    public static function getDate($date1,$date2){

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        return $days;
    }
    public static function getCurrentDate()
    {
        date_default_timezone_set('Asia/Bangkok');
        return date("Y-m-d");
    }
    public static function checkPermissionSetup($userId,$permission){
        $settings = \Hpl\Entity\SettingTable::getList([
            'filter'=>['user_id'=>$userId,$permission=>1]
        ])->fetch();
        if(!empty($settings))
            return true;
    }

    public static function checkStatus($userID,$requestId)
    {
        global $DB;
        $res = $DB->query("Select status_driver from hpl_request where user_driver =$userID")->fetch();
        return $res['status_driver'];
    }

    public static function getSummary($userId)
    {
        $dbFilter = RequestTable::getList([
            'filter'=>['user_driver'=>$userId]
        ])->fetch();
        return $dbFilter['name'];
    }
    public static function viewFile($requsetId){
        if(!Loader::includeModule('hpl.request')){

            return;
        }
        global $DB;
        $strRequest = RequestFileTable::getList([
            'filter'=>['request_id'=>$requsetId]
        ])->fetchAll();
        $view = [];
        $arrayAllowsDocument = ['docx','xlsx','pdf'];
        $arrayAllowPictures = ['png','jpg','gif'];
        foreach ($strRequest as $item){
            $arrayFile = explode('.',$item['source']);
             $source = $item['source'];
            if(in_array($arrayFile[2],$arrayAllowsDocument)){
                $view[]="<a class='photo' href='$source'>Xem File</a>";
            }
            if(in_array($arrayFile[2],$arrayAllowPictures)){
                $view[] = '<img style="margin-top:5px;" src="' . $item['source'] .'"  width="150px" height="150px" />';
            }
        }
        $view = implode('</br>',$view);
        return $view;
    }
    public static function viewFileDocument($requsetId){
        if(!Loader::includeModule('hpl.request')){
            return;
        }
        global $DB;
        $str = "Select * from hpl_request_file where request_document_id = $requsetId";
        $strRequest = $DB->Query($str)->fetch();
        $view = [];
        $arrayAllowsDocument = ['docx','xlsx','pdf'];
        $arrayAllowPictures = ['png','jpg','gif'];
        foreach ($strRequest as $item){
            $arrayFile = explode('.',$item['source']);
            $source = $item['source'];
            if(in_array($arrayFile[2],$arrayAllowsDocument)){
                $view[]="<a class='photo' href='$source'>Xem File</a>";
            }
            if(in_array($arrayFile[2],$arrayAllowPictures)){
                $view[] = '<img style="margin-top:5px;" src="' . $item['source'] .'"  width="150px" height="150px" />';
            }
        }
        $view = implode('</br>',$view);
        return $view;
    }
    public static function getUserHead($userId)
    {
        $skipAbsent = CModule::IncludeModule('intranet');

        $userDepartmentId = array();
        $userIterator = CUser::GetByID($userId);
        if ($user = $userIterator->Fetch())
        {
            if (isset($user["UF_DEPARTMENT"]))
            {
                if (!is_array($user["UF_DEPARTMENT"]))
                    $user["UF_DEPARTMENT"] = array($user["UF_DEPARTMENT"]);

                foreach ($user["UF_DEPARTMENT"] as $v)
                    $userDepartmentId[] = $v;
            }
        }

        $userDepartments = array();
        $departmentIBlockId = COption::GetOptionInt('intranet', 'iblock_structure');
        foreach ($userDepartmentId as $departmentId)
        {
            $ar = array();
            $dbPath = CIBlockSection::GetNavChain($departmentIBlockId, $departmentId);
            while ($arPath = $dbPath->GetNext())
                $ar[] = $arPath["ID"];

            $userDepartments[] = array_reverse($ar);
        }

        $heads = array();
        $absentHeads = array();
        $maxLevel = 1;
        foreach ($userDepartments as $arV)
        {
            foreach ($arV as $level => $deptId)
            {
                if ($maxLevel > 0 && $level + 1 > $maxLevel)
                    break;

                $dbRes = CIBlockSection::GetList(
                    array(),
                    array(
                        'IBLOCK_ID' => $departmentIBlockId,
                        'ID'        => $deptId,
                    ),
                    false,
                    array('ID', 'UF_HEAD')
                );
                while ($arRes = $dbRes->Fetch())
                {
                    if ($arRes["UF_HEAD"] == $userId || intval($arRes["UF_HEAD"]) <= 0)
                    {
                        $maxLevel++;
                        continue;
                    }

                    if ($skipAbsent && CIntranetUtils::IsUserAbsent($arRes["UF_HEAD"]))
                    {
                        if (!isset($absentHeads[$level]))
                            $absentHeads[$level] = array();

                        $absentHeads[$level][] = $arRes["UF_HEAD"];
                        $maxLevel++;
                        continue;
                    }
                    if (!in_array($arRes["UF_HEAD"], $heads))
                        $heads[] = $arRes["UF_HEAD"];
                }
            }
        }

        if (!$heads && $absentHeads)
        {
            reset($absentHeads);
            $heads = current($absentHeads);
        }

        return $heads;
    }
    public static function getDepartmentUser($userId){
        global $DB;
        $queryDepartment = "Select NAME from b_iblock_section where ID in (select  VALUE_INT from b_utm_user WHERE value_id = $userId) ";
        $resultDB = $DB->Query($queryDepartment)->fetch();
        return $resultDB['NAME'];
    }
    public static function checkStatusSigin($requestId,$user_id){
        global $DB;
        $str = $DB->Query("Select id from hpl_sigin_approve_c2 where request_id = $requestId and user_id =$user_id")->fetch();
       echo "<pre>";print_r($str);die;
        if(count($str['id'])>0)
           return true;
       return false;
    }

}

?>
