<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_ella_contractor_media_table extends App_module_migration
{
    public function up($db)
    {
        if (!$db->table_exists(db_prefix() . 'ella_contractor_media')) {
            $db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_media` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `folder_id` int(11) DEFAULT NULL,
                `lead_id` int(11) DEFAULT NULL,
                `file_name` varchar(255) NOT NULL,
                `original_name` varchar(255) NOT NULL,
                `file_type` varchar(100) NOT NULL,
                `file_size` int(11) NOT NULL,
                `description` text,
                `is_default` tinyint(1) DEFAULT 0,
                `active` tinyint(1) DEFAULT 1,
                `date_uploaded` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `folder_id` (`folder_id`),
                KEY `lead_id` (`lead_id`),
                KEY `is_default` (`is_default`),
                KEY `active` (`active`),
                KEY `file_type` (`file_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $db->char_set . ';');
        }
    }

    public function down($db)
    {
        if ($db->table_exists(db_prefix() . 'ella_contractor_media')) {
            $db->query('DROP TABLE `' . db_prefix() . 'ella_contractor_media`');
        }
    }
}
