<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;

class HplSignatureSignersTable extends DataManager
{
    const HPL_SIGNATURE_SIGNERS = 'hpl_signature_signers';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_SIGNERS;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new IntegerField('user_id'),
            new IntegerField('document_id'),
            new IntegerField('delegacy'),
            new IntegerField('status'),
            new IntegerField('updated_by'),
            new IntegerField('created_by'),
            new DatetimeField('created_at'),
            new DatetimeField('updated_at'),
            'user' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.user_id' => 'ref.id'),
            ),
            'delegacy' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.delegacy' => 'ref.id'),
            ),
            'document' => array(
                'data_type' => 'Hpl\Signature\Entity\HplSignatureDocumentsTable',
                'reference' => array('=this.document_id' => 'ref.id'),
            ),
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