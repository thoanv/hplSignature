<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureCategoriesTable extends DataManager
{
    const HPL_SIGNATURE_CATEGORIES = 'hpl_signature_categories';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_CATEGORIES;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new StringField('name'),
            new IntegerField('permission'),
            new IntegerField('parent_id'),
            new StringField('individual'),
            new TextField('description'),
            new IntegerField('updated_by'),
            new IntegerField('created_by'),
            new DatetimeField('created_at', array(
                'required' => false,
            )),
            new DatetimeField('updated_at', array(
                'required' => false,
            )),
            'created_by' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.created_by' => 'ref.id'),
            ),
            'updated_by' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.updated_by' => 'ref.id'),
            ),
        );
    }

}