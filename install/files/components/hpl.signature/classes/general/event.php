<?
/** var CMain $APPLICATION */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

class AssetEvent
{
    const Attending = "H','Y";
    const Declined = 'N';

    public static function addNewEventCalender($topic, $start_date, $end_date, $description, $remind, $members)
    {
        global $USER;
        $ownerId = $USER->GetID();
        $newMeeting = 'Y';
        $arFields = array(
            'CAL_TYPE' => 'company_calendar',
            'OWNER_ID' => $ownerId,
            "CREATED_BY" => $ownerId,
            "DT_FROM" => $start_date,
            "DT_TO" => $end_date,
            'NAME' => htmlspecialcharsback($topic),
            'DESCRIPTION' => CCalendar::ParseHTMLToBB(htmlspecialcharsback($description)),
            'PRIVATE_EVENT' => '1',
            'IMPORTANCE' => 'normal',
            'TZ_FROM' => 'Asia/Ho_Chi_Minh',
            'TZ_TO' => 'Asia/Ho_Chi_Minh',
            'RRULE' => array(),
            "IS_MEETING" => 1
        );
        $arFields['REMIND'] = array(
            array(
                'type' => 'min',
                'count' => $remind
            ),
        );
        if ($arFields['IS_MEETING']) {
            $arFields['ATTENDEES_CODES'] = array();
            if (is_array($members)) {
                foreach ($members as $attId) {
                    $arFields['ATTENDEES_CODES'][] = 'U' . intVal($attId);
                }
            }
            if ($newMeeting && !in_array($ownerId, $members))
                $arFields['ATTENDEES_CODES'][] = 'U' . intVal($ownerId);
            $arFields['ATTENDEES'] = CCalendar::GetDestinationUsers($arFields['ATTENDEES_CODES']);
            $arFields['MEETING_HOST'] = $ownerId;
            $arFields['MEETING'] = array(
                'HOST_NAME' => CCalendar::GetUserName($ownerId),
                'TEXT' => '',
                'OPEN' => false,
                'NOTIFY' => true,
                'REINVITE' => true
            );
        }
        $intEventID = CCalendar::SaveEvent(array(
            'arFields' => $arFields,
            'userId' => $ownerId,
            'autoDetectSection' => true,
            'autoCreateSection' => true
        ));
        return $intEventID;

    }

    public static function deleteEvent($event_id)
    {
        if (CCalendar::GetReadonlyMode() || !CCalendarType::CanDo('calendar_type_view', CCalendar::GetType()))
            return CCalendar::ThrowError(Loc::getMessage('EC_ACCESS_DENIED'));
        $res = CCalendar::DeleteEvent(intVal($event_id));
        if ($res !== true)
            return CCalendar::ThrowError(strlen($res) > 0 ? $res : Loc::getMessage('EC_EVENT_DEL_ERROR'));
        return $res;
    }

    public static function getStatusEvent($event_id, $status)
    {
        global $DB;
        $users = [];
        if($event_id) {
            $result = $DB->Query("SELECT CREATED_BY FROM b_calendar_event WHERE PARENT_ID = $event_id AND MEETING_STATUS IN('$status') AND ID != $event_id ORDER BY DATE_CREATE desc", false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            while ($item = $result->fetch()) {
                $user_id = $item['CREATED_BY'];
                $users[] = MMeeting::getFullName($user_id);
            }
            return implode(';', $users);
        }
    }
}

?>