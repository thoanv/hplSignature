<?
/** var CMain $APPLICATION */

use Bitrix\Iblock\SectionTable;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Main\Access\User\UserSubordinate;
use Let\Cash\Entity\HRChiNhanhTable;

IncludeModuleLangFile(__FILE__);

class LetCash
{
    private static $arr_category = [];

    public static function getNameAndImage($user)
    {
        $userImage = \CFile::resizeImageGet(
            $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
            BX_RESIZE_IMAGE_EXACT, false
        );
        $userName = \CUser::formatName(\CSite::getNameFormat(), $user, true, false);
        $userSrc = !empty($userImage['src']) ? $userImage['src'] : '/local/modules/let.cash/install/images/user_default.png';
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
            $userSrc = !empty($userImage['src']) ? $userImage['src'] : '/local/modules/let.cash/install/images/user_default.png';
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
            'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '/local/modules/let.cash/install/images/user_default.png',
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

    public static function listHour()
    {
        $start = "00:00";
        $end = "24:00";
        $tStart = strtotime($start);
        $tEnd = strtotime($end);
        $tNow = $tStart;
        $data = [];
        while ($tNow <= $tEnd) {
            $hour = date("H:i", $tNow);
            $data[$hour] = $hour;
            $tNow = strtotime('+30 minutes', $tNow);
        }
        return $data;
    }

    public static function roundDownToMinuteInterval($minuteInterval = 30, $end_time = '')
    {
        $dateTime = new DateTime(date('Y-m-d H:i:s'));
        if (!empty($end_time)) {
            $dateint = mktime(date('H') + 2, date('i'), date('s'), date('m'), date('d'), date('Y'));
            $dateTime = new DateTime(date('Y-m-d H:i:s', $dateint));
        }
        $rs = $dateTime->setTime(
            $dateTime->format('H'),
            floor($dateTime->format('i') / $minuteInterval) * $minuteInterval,
            0
        );
        return $rs->format("H:i");
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
                'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '/local/modules/let.cash/install/images/user_default.png',
                'NAME' => \CUser::formatName(\CSite::getNameFormat(), $arUser, true, false)
            ];
        }
        return $members;
    }

    public static function assetType($val = false)
    {

        if ($val) {
            return [
                '1' => Loc::getMessage('FIX_ASSET'),
                '2' => Loc::getMessage('INVISIBLE_ASSET'),
            ];
        } else {
            return [
                '0' => '',
                '1' => Loc::getMessage('FIX_ASSET'),
                '2' => Loc::getMessage('INVISIBLE_ASSET'),
            ];
        }
    }

    public static function assetUnit()
    {
        return ['', Loc::getMessage('BOOK'), Loc::getMessage('SHEET'), Loc::getMessage('PIECE'), Loc::getMessage('BOX'), Loc::getMessage('SET'), Loc::getMessage('METER'), Loc::getMessage('KG')];
    }

    public static function assetStatus()
    {
        return ['P' => Loc::getMessage('PENDING'), 'Y' => Loc::getMessage('USING'), 'N' => Loc::getMessage('STOP_USING')];
    }

    public static function assetYear()
    {
        $currentYear = date('Y');
        $data[''] = '';
        foreach (range(2010, $currentYear) as $value) {
            $data[$value] = $value;
        }
        return $data;
    }

    public static function getUnit($key)
    {
        $unitArr = self::assetUnit();
        return isset($unitArr[$key]) ? $unitArr[$key] : '';
    }

    public static function getType($key)
    {
        $typeArr = self::assetType();
        return isset($typeArr[$key]) ? $typeArr[$key] : '';
    }

    public static function getStatus($key)
    {
        $statusArr = self::assetStatus();
        return isset($statusArr[$key]) ? $statusArr[$key] : '';
    }

    public static function getDepartment()
    {
        $categories = SectionTable::getList([
            'filter' => ['IBLOCK_ID' => 5],
            'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'IBLOCK_ID', 'DEPTH_LEVEL'],
        ])->fetchAll();
        self::$arr_category[''] = '';
        self::showCategories($categories);
        return self::$arr_category;
    }

    private function showCategories($categories, $parent_id = NULL, $char = '')
    {
        foreach ($categories as $item) {
            if ($item['IBLOCK_SECTION_ID'] == $parent_id) {
                self::$arr_category[$item['ID']] = $char . $item['NAME'];
                self::showCategories($categories, $item['ID'], $char . '--');
            }
        }
    }

    /**
     * list ra chi nhánh đầu tiên user đang quản lý hoặc theo user_id
     * @param $userId
     * @return array
     */
    public static function getBranchUserJoined($userId = null)
    {
        global $USER, $DB;
        $userID = $userId ?? $USER->GetID();
        $entityIds = UserSubordinate::getDepartmentsByUserId($USER->GetID());
        $res = $DB->Query("SELECT * FROM let_hr_chinhanh WHERE dept_id IN (" .implode(",",$entityIds) .")", "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $item = $res->Fetch();
        return $item['id'];
    }

    public static function searchItem($id, $arraySearch, $where) {
         $neededObject = array_filter(
            $arraySearch,
            function ($e) use ($id, $where) {
                return intval($e[$where]) === intval($id);
            }
        );
        return $neededObject;
    }

}

?>