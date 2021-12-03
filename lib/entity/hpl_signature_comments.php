<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureCommentsTable extends DataManager
{
    const HPL_SIGNATURE_COMMENTS = 'hpl_signature_comments';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_COMMENTS;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new IntegerField('user_id'),
            new IntegerField('document_id'),
            new StringField('comment'),
            new TextField('file'),
            new IntegerField('updated_by'),
            new IntegerField('created_by'),
            new DatetimeField('created_at'),
            new DatetimeField('updated_at'),
            'document' => array(
                'data_type' => 'Hpl\Signature\Entity\HplSignatureDocumentsTable',
                'reference' => array('=this.documents_id' => 'ref.id'),
            ),
            'user' => array(
                'data_type' => 'Bitrix\Main\UserTable',
                'reference' => array('=this.user_id' => 'ref.id'),
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