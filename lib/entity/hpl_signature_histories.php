<?php
namespace Hpl\Signature\Entity;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureHistoriesTable extends DataManager
{
    const HPL_SIGNATURE_HISTORIES = 'hpl_signature_histories';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_HISTORIES;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new StringField('note'),
            new IntegerField('document_id'),

            new IntegerField('created_by'),
            new DatetimeField('updated_at'),
            new DatetimeField('created_at'),
            'document' => array(
                'data_type' => 'Hpl\Signature\Entity\HplSignatureDocumentsTable',
                'reference' => array('=this.documents_id' => 'ref.id'),
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