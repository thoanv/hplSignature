<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureAccountsTable extends DataManager
{
    const HPL_SIGNATURE_ACCOUNTS = 'hpl_signature_accounts';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_ACCOUNTS;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new IntegerField('user_id', array(
                'required' => false,
            )),
            new IntegerField('id_card', array(
                'required' => false,
            )),
            new IntegerField('type', array(
                'required' => false,
            )),
            new IntegerField('supplier', array(
                'required' => false,
            )),
            new IntegerField('use_time', array(
                'required' => false,
            )),
            new TextField('img_signature', array(
                'required' => false,
            )),
            new StringField('login', array(
                'required' => false,
            )),
            new StringField('password', array(
                'required' => false,
            )),
            new DateField('date_end', array(
                'required' => false,
            )),
            new IntegerField('status', array(
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
            'user' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.user_id' => 'ref.id'),
            ),
            'updated_by' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.updated_by' => 'ref.id'),
            ),
        );
    }

}