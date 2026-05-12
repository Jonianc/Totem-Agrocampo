<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('agp_totem_settings');
delete_option('agp_totem_db_version');
