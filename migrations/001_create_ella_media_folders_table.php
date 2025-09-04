<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_ella_media_folders_table extends App_module_migration
{
    public function up($db)
    {
        if (!$db->table_exists(db_prefix() . 'ella_media_folders')) {
            $db->query('CREATE TABLE `' . db_prefix() . 'ella_media_folders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `lead_id` int(11) DEFAULT NULL,
                `is_default` tinyint(1) DEFAULT 0,
                `active` tinyint(1) DEFAULT 1,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `lead_id` (`lead_id`),
                KEY `is_default` (`is_default`),
                KEY `active` (`active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $db->char_set . ';');
        }
    }

    public function down($db)
    {
        if ($db->table_exists(db_prefix() . 'ella_media_folders')) {
            $db->query('DROP TABLE `' . db_prefix() . 'ella_media_folders`');
        }
    }
}
