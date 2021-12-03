CREATE TABLE `hpl_signature_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) UNIQUE NOT NULL,
    `parent_id` int(11) NULL,
    `individual` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N',
    `permission` tinyint(4) DEFAULT 0,
    `description` text,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    `created_by` int(4) NOT NULL,
    `updated_by` int(4) NOT NULL,
    PRIMARY KEY (`ID`)
);
CREATE TABLE `hpl_signature_accounts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `type` varchar(100) COLLATE utf8_unicode_ci,
      `supplier` varchar(255) COLLATE utf8_unicode_ci,
      `id_card` int(11),
      `use_time` int(4),
      `img_signature` longtext,
      `date_end` date,
      `status` tinyint(4) DEFAULT 0,
      `created_at` datetime NOT NULL,
      `updated_at` datetime NOT NULL,
      `created_by` int(4) NOT NULL,
      PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hpl_signature_documents` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
--     `follower` text NOT NULL,
    `signer` text NOT NULL,
    `security_mode` tinyint(4) DEFAULT 0,
    `category_id` int(11) NOT NULL,
    `proposed_group_id` int(11) NOT NULL,
    `status` tinyint(4) DEFAULT 0,
    `deadline` date,
    `reason` varchar(255),
    `extend` varchar(255),
    `direct_manager` varchar(255) NULL,
    `department_id` int(11) NOT NULL,
    `name_file` varchar(255) NULL,
    `file` longtext NULL,
    `file_signature` text NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    `created_by` int(4) NOT NULL,
    `updated_by` int(4) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hpl_signature_proposed_groups` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(255) UNIQUE NOT NULL,
     `status` tinyint(4) DEFAULT 0,
     `created_at` datetime NOT NULL,
     `updated_at` datetime NOT NULL,
     `created_by` int(4) NOT NULL,
     `updated_by` int(4) NOT NULL,
     PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hpl_signature_histories` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `document_id` int(11) NOT NULL,
   `note` varchar(255) NOT NULL,
   `created_at` datetime NOT NULL,
   `created_by` int(4) NOT NULL,
   PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hpl_signature_signers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `delegacy` int(11) NOT NULL,
    `status` tinyint(4) DEFAULT 0,
    `method` tinyint(4) DEFAULT 0,
    `authentication` varchar (255),
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    `created_by` int(4) NOT NULL,
    `updated_by` int(4) NOT NULL,
    PRIMARY KEY (`ID`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `hpl_signature_comments` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `document_id` int(11) NOT NULL,
     `user_id` int(11) NOT NULL,
     `comment` varchar(255) UNIQUE NOT NULL,
     `created_at` datetime NOT NULL,
     `updated_at` datetime NOT NULL,
     PRIMARY KEY (`ID`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
