<?php
$params = array(
    'Hpl\Signature\Helper' => 'lib/helper/helper.php',
    "Hpl\Signature\Entity\HplSignatureAccountsTable"      => "lib/entity/hpl_signature_accounts.php",
    "Hpl\Signature\Entity\HplSignatureCategoriesTable"    => "lib/entity/hpl_signature_categories.php",
    "Hpl\Signature\Entity\HplSignatureCommentsTable"      => "lib/entity/hpl_signature_comments.php",
    "Hpl\Signature\Entity\HplSignatureDocumentsTable"     => "lib/entity/hpl_signature_documents.php",
    "Hpl\Signature\Entity\HplSignatureHistoriesTable"     => "lib/entity/hpl_signature_histories.php",
    "Hpl\Signature\Entity\HplSignatureProposedGroupsTable"=> "lib/entity/hpl_signature_proposed_groups.php",
    "Hpl\Signature\Entity\HplSignatureSignersTable"       => "lib/entity/hpl_signature_signers.php",
    "Hpl\Signature\Entity\BiblockSectionTable"            => "lib/entity/b_iblock_section.php",
);
$documentRoot = rtrim($_SERVER["DOCUMENT_ROOT"], "/\\");
$moduleName = 'hpl.signature';
foreach ($params as $value) {
    if (file_exists($documentRoot . "/local/modules/" . $moduleName . "/" . $value))
        require_once($documentRoot . "/local/modules/" . $moduleName . "/" . $value);
}
?>



