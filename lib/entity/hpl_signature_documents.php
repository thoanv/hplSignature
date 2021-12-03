<?php
namespace Hpl\Signature\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

class HplSignatureDocumentsTable extends DataManager
{
    const HPL_SIGNATURE_DOCUMENTS = 'hpl_signature_documents';
    public static function getTableName()
    {
        return self::HPL_SIGNATURE_DOCUMENTS;
    }

    public static function getMap()
    {
        return array(
            new IntegerField('id', array('primary' => true, 'autocomplete' => true)),
            new StringField('name'),
//            new TextField('follower'),
            new IntegerField('security_mode'),
            new IntegerField('category_id'),
            new IntegerField('proposed_group_id'),
            new IntegerField('status'),
            new DateField('deadline'),
            new StringField('reason'),
            new StringField('extend'),
            new StringField('direct_manager'),
            new StringField('tran_id'),
            new IntegerField('department_id'),
            new TextField('signer'),
            new TextField('name_file'),
            new TextField('file'),
            new TextField('file_signature'),

            new IntegerField('created_by'),
            new IntegerField('updated_by'),
            new DatetimeField('created_at'),
            new DatetimeField('updated_at'),
            'category' => array(
                'data_type' => 'Hpl\Signature\Entity\HplSignatureCategoriesTable',
                'reference' => array('=this.category_id' => 'ref.id'),
            ),
            'department' => array(
                'data_type' => 'Hpl\Signature\Entity\BiblockSectionTable',
                'reference' => array('=this.department_id' => 'ref.id'),
            ),
            'groups' => array(
                'data_type' => 'Hpl\Signature\Entity\HplSignatureProposedGroupsTable',
                'reference' => array('=this.proposed_group_id' => 'ref.id'),
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