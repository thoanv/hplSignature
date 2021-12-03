<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class BiblockSectionTable extends DataManager
{
    const B_IBLOCK_SECTION = 'b_iblock_section';
    public static function getTableName()
    {
        return self::B_IBLOCK_SECTION;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new DatetimeField('TIMESTAMP_X'),
            new IntegerField('MODIFIED_BY'),
            new DatetimeField('DATE_CREATE'),
            new IntegerField('CREATED_BY'),
            new IntegerField('IBLOCK_ID'),
            new IntegerField('IBLOCK_SECTION_ID'),
            new StringField('ACTIVE'),
            new StringField('GLOBAL_ACTIVE'),
            new IntegerField('SORT'),
            new StringField('NAME'),
            new IntegerField('PICTURE'),
            new IntegerField('LEFT_MARGIN'),
            new IntegerField('RIGHT_MARGIN'),
            new IntegerField('DEPTH_LEVEL'),
            new TextField('DESCRIPTION'),
            new StringField('DESCRIPTION_TYPE'),
            new TextField('SEARCHABLE_CONTENT'),
            new StringField('CODE'),
            new StringField('XML_ID'),
            new StringField('TMP_ID'),
            new IntegerField('DETAIL_PICTURE'),
            new IntegerField('SOCNET_GROUP_ID'),
        );
    }

}