<?php

namespace Hpl\Signature\Helper;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use OAuth2Config;

class Helper
{
    /**
     * @param $string
     * @return mixed
     */
    public static function stripText($string)
    {
        $from = array("Ã ", "áº£", "Ã£", "Ã¡", "áº¡", "Äƒ", "áº±", "áº³", "áºµ", "áº¯", "áº·", "Ã¢", "áº§", "áº©", "áº«", "áº¥", "áº­", "Ä‘", "Ã¨", "áº»", "áº½", "Ã©", "áº¹", "Ãª", "á»", "á»ƒ", "á»…", "áº¿", "á»‡", "Ã¬", "á»‰", "Ä©", "Ã­", "á»‹", "Ã²", "á»", "Ãµ", "Ã³", "á»", "Ã´", "á»“", "á»•", "á»—", "á»‘", "á»™", "Æ¡", "á»", "á»Ÿ", "á»¡", "á»›", "á»£", "Ã¹", "á»§", "Å©", "Ãº", "á»¥", "Æ°", "á»«", "á»­", "á»¯", "á»©", "á»±", "á»³", "á»·", "á»¹", "Ã½", "á»µ", "Ã€", "áº¢", "Ãƒ", "Ã", "áº ", "Ä‚", "áº°", "áº²", "áº´", "áº®", "áº¶", "Ã‚", "áº¦", "áº¨", "áºª", "áº¤", "áº¬", "Ä", "Ãˆ", "áºº", "áº¼", "Ã‰", "áº¸", "ÃŠ", "á»€", "á»‚", "á»„", "áº¾", "á»†", "ÃŒ", "á»ˆ", "Ä¨", "Ã", "á»Š", "Ã’", "á»Ž", "Ã•", "Ã“", "á»Œ", "Ã”", "á»’", "á»”", "á»–", "á»", "á»˜", "Æ ", "á»œ", "á»ž", "á» ", "á»š", "á»¢", "Ã™", "á»¦", "Å¨", "Ãš", "á»¤", "Æ¯", "á»ª", "á»¬", "á»®", "á»¨", "á»°", "á»²", "á»¶", "á»¸", "Ã", "á»´");
        $to = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "d", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "D", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y");
        return str_replace($from, $to, $string);
    }

    /**
     * @param $string
     * @return string
     */
    public static function cleanUpSpecialChars($string)
    {
        $string = preg_replace(array("`\W`i", "`[-]+`"), "-", $string);
        return trim($string, '-');
    }

    /**
     * @param $string
     * @return string
     */
    public static function makeSlug($string)
    {
        $title = self::stripText($string);
        return self::cleanUpSpecialChars($title);
    }

    /**
     * @param $string
     * @param $format
     * @return string
     * @throws \Exception
     */
    public static function getDatetimeFormat($time,$format){
        $update_date= DateTime :: createFromPhp ( new \ DateTime ( $time ));
        $updated_at = $update_date->format($format);
        return $updated_at;
    }

    /**
     * @param $objId
     * @param bool|false $isDir
     * @return string
     */
    public static function makeStoragePath($objId, $isDir = false)
    {
        $step = 15; // So bit de ma hoa ten thu muc tren 1 cap
        $layer = 3; // So cap thu muc
        $max_bits = PHP_INT_SIZE * 8;
        $result = "";

        for ($i = $layer; $i > 0; $i--) {
            $shift = $step * $i;
            $layer_name = $shift <= $max_bits ? $objId >> $shift : 0;

            $result .= $isDir ? DIRECTORY_SEPARATOR . $layer_name : "/" . $layer_name;
        }

        return $result;
    }

    /**
     * @param $dir
     * @param int $mode
     * @param bool $recursive
     */
    public static function makeDirectory($dir, $mode = 0777, $recursive = true)
    {
        if (!file_exists($dir)) {
            $old_umask = umask(0);
            mkdir($dir, $mode, $recursive);
            umask($old_umask);
        }
    }

    /**
     * @param $s
     * @return int|string
     */
    public static function expriedToTime($s)
    {
        if ($s <= 0) {
            return 0;
        }

        $day = floor($s / 86400);
        if ($day > 0) {
            return $day . Loc::getMessage('HELPER_DAY');
        }

        $hour = floor(($s - $day * 86400) / 3600);
        if ($hour > 0) {
            return $hour . Loc::getMessage('HELPER_HOUR');
        }

        $minute = floor(($s - $hour * 3600) / 60);
        if ($minute > 0) {
            return $minute . Loc::getMessage('HELPER_MIN');
        }

        return 0;
    }

    /**
     * @param $data
     * @param string $currency
     * @return string
     */
    public static function moneyFormat($data, $currency = 'vnÄ‘')
    {
        return number_format($data, 0, '', '.') . ' ' . $currency;
    }

    /**
     * @param $time
     * @return mixed|string
     */
    public static function displayMonth($time)
    {
        $arrMonth = array(
            1 => Loc::getMessage('HELPER_MIN') . ' 1',
            2 => Loc::getMessage('HELPER_MIN') . ' 2',
            3 => Loc::getMessage('HELPER_MIN') . ' 3',
            4 => Loc::getMessage('HELPER_MIN') . ' 4',
            5 => Loc::getMessage('HELPER_MIN') . ' 5',
            6 => Loc::getMessage('HELPER_MIN') . ' 6',
            7 => Loc::getMessage('HELPER_MIN') . ' 7',
            8 => Loc::getMessage('HELPER_MIN') . ' 8',
            9 => Loc::getMessage('HELPER_MIN') . ' 9',
            10 => Loc::getMessage('HELPER_MIN') . ' 10',
            11 => Loc::getMessage('HELPER_MIN') . ' 11',
            12 => Loc::getMessage('HELPER_MIN') . ' 12',
        );

        $month = date('n', strtotime($time));

        if (isset($arrMonth[$month]))
            return $arrMonth[$month];

        return '';
    }

    /**
     * @param $i
     * @return int
     */
    public static function setInt($i)
    {
        return (int)$i;
    }

    /**
     * @return false|string
     */
    public static function date_time()
    {
        return date('d-m-Y');
    }

    /**
     * @param $label
     * @return string
     */
    public static function rendLabel($label)
    {
        return self::lower_text(str_replace(array("-", "_"), ' ', $label));
    }

    /**
     * @param $str
     * @return string
     */
    public static function lower_text($str)
    {
        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    }

    /**
     * @param int $length
     * @return string
     */
    public static function take_key($length = 10)
    {
        $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ123456789";
        $validCharNumber = strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            $result .= $validCharacters[$index];
        }
        return $result;
    }

    /**
     * @param $text
     * @param $len
     * @param string $end
     * @return string
     */
    public static function text_limit($text, $len, $end = '...')
    {
        mb_internal_encoding('UTF-8');
        if ((mb_strlen($text, 'UTF-8') > $len)) {
            $text = mb_substr($text, 0, $len, 'UTF-8');
        }

        return $text . $end;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getDiffDays($start_date, $end_date)
    {
        $first_date = strtotime($start_date);
        $second_date = strtotime($end_date);
        $offset = $second_date - $first_date;
        $result = array();
        for ($i = 0; $i <= floor($offset / 24 / 60 / 60); $i++) {
            $result[] = date('Y-m-d', strtotime($start_date . ' + ' . $i . '  days'));
        }
        return $result;
    }

    /**
     * Get hour export excel
     * @param $data
     * @return mixed|string
     */
    public static function calHourExcel($data)
    {
        $exData = explode(' ', $data);
        $result = '';
        if (count($exData) == 2) {
            $hour = str_replace('h', '', $exData[0]);
            $min = str_replace('h', '', $exData[1]);
            $result = $hour . '.' . number_format($min / 6, 0);
        }
        if (count($exData) == 1) {
            $text = $exData[0];
            $checkHour = self::checkText($text, 'h');
            $checkMin = self::checkText($text, 'm');
            if ($checkHour) {
                $result = str_replace('h', '', $text);
            }
            if ($checkMin) {
                $minute = str_replace('m', '', $text);
                $result = '0.' . number_format($minute / 6, 0);
            }
        }
        return $result;
    }

    /**
     * @param $needle
     * @param $haystack
     * @return bool|int
     */
    private static function checkText($needle, $haystack)
    {
        return strpos($needle, $haystack);
    }

    public static function getInformationUser($arrUser, $user_id = 0)
    {
        $txt_signer = '';
        if(count($arrUser)){
            foreach ($arrUser as $user_id){
                $idUser = substr($user_id, 1);
                $user = UserTable::getById($idUser)->fetch();
                $userImage = \CFile::resizeImageGet(
                    $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
                    BX_RESIZE_IMAGE_EXACT, false
                );
                $user = [
                    'ID' => $user['ID'],
                    'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '',
                    'NAME' => \CUser::formatName(\CSite::getNameFormat(), $user, true, false),
                ];
                $style = $user['IMAGE'] ? 'style="background-image: url('.$user['IMAGE'].'); background-size: 100%;"' : '';
                $txt_signer = $txt_signer.'<span class="ui-item-detail-stream-content-employee" data-toggle="tooltip" data-placement="top"  title="'.$user['NAME'].'" '.$style.'></span>';
            }
        }
        if($user_id>0){
            $user = UserTable::getById($user_id)->fetch();
            $userImage = \CFile::resizeImageGet(
                $user['PERSONAL_PHOTO'], array('width' => 38, 'height' => 38),
                BX_RESIZE_IMAGE_EXACT, false
            );
            $user = [
                'ID' => $user['ID'],
                'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '',
                'NAME' => \CUser::formatName(\CSite::getNameFormat(), $user, true, false),
            ];
            $style = $user['IMAGE'] ? 'style="background-image: url('.$user['IMAGE'].'); background-size: 100%;"' : '';
            $txt_signer = $txt_signer.'<span class="ui-item-detail-stream-content-employee" data-toggle="tooltip" data-placement="top"  title="'.$user['NAME'].'" '.$style.'></span>';

        }

        $result = '<div class="ui-item-detail-stream-content-employee-wrap">'.$txt_signer.'</div>';
        return $result;
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
            'IMAGE' => !empty($userImage['src']) ? $userImage['src'] : '',
            'NAME' => \CUser::formatName(\CSite::getNameFormat(), $user, true, false),
            'LOGIN'=> $user['LOGIN'],
            'WORK_POSITION' => $user['WORK_POSITION']
        ];

        return $user;
    }
    public static function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public static function getGUID(){
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }

    public static function api_get_credentical_curl(){
        $access_token = $_SESSION['access_token_vnpt'];
        $config = new OAuth2Config();
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => "https://rmgateway.vnptit.vn/csc/credentials/list",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{}'
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $msg = json_decode($response);
        curl_close($curl);
        if($httpcode != 200){
            print_r('<pre>');
            print_r($response);
            print_r('</pre>');
            exit();
        }
        return $msg;
    }
    public static function api_get_certBase64($data){
        $access_token = $_SESSION['access_token_vnpt'];
        $config = new OAuth2Config();
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => "https://rmgateway.vnptit.vn/csc/credentials/info",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $msg = json_decode($response);
        curl_close($curl);
        if($httpcode != 200){
            print_r('<pre>');
            print_r($response);
            print_r('</pre>');
            exit();
        }
        return $msg;
    }

    public static function api_sign_curl($data){
        $access_token = $_SESSION['access_token_vnpt'];
        $config = new OAuth2Config();
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => "https://rmgateway.vnptit.vn/csc/signature/sign",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $msg = json_decode($response);
        curl_close($curl);
        if($httpcode != 200){
            print_r('<pre>');
            print_r($response);
            print_r('</pre>');
            exit();
        }
        return $msg;
    }

    public static function api_service_get_hash($url,$data){
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $msg = json_decode($response);
        curl_close($curl);
        if($httpcode != 200){
            print_r('<pre>');
            print_r($response);
            print_r('</pre>');
            exit();
        }
        return $msg;
    }

    public static  function api_get_tranInfo_curl($data){
        $access_token = $_SESSION['access_token_vnpt'];
        $config = new OAuth2Config();
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => "https://rmgateway.vnptit.vn/csc/credentials/gettraninfo",
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access_token,
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $msg = json_decode($response);
        curl_close($curl);
        if($httpcode != 200){
            print_r('<pre>');
            print_r($response);
            print_r('</pre>');
            exit();
        }
        return $msg;
    }
}