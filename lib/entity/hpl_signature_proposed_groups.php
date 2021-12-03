<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureProposedGroupsTable extends DataManager
{
    const HPL_SIGNATURE_PROPOSED_GROUPS = 'hpl_signature_proposed_groups';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_PROPOSED_GROUPS;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new StringField('name', array(
                'required' => false,
            )),
            new IntegerField('status', array(
                'required' => false,
            )),
            new IntegerField('updated_by', array(
                'required' => false,
            )),
            new IntegerField('created_by', array(
                'required' => false,
            )),
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