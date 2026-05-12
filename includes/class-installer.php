<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Installer {
    const DB_VERSION_OPTION = 'agp_totem_db_version';

    public static function activate() {
        self::create_tables();
        update_option(self::DB_VERSION_OPTION, AGP_TOTEM_VERSION);

        if (false === get_option(AGP_Totem_Settings::OPTION_KEY, false)) {
            add_option(AGP_Totem_Settings::OPTION_KEY, AGP_Totem_Settings::defaults());
        }
    }

    public static function create_tables() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $leads_table = $wpdb->prefix . 'agp_totem_leads';
        $products_table = $wpdb->prefix . 'agp_totem_products';
        $categories_table = $wpdb->prefix . 'agp_totem_categories';

        $sql_leads = "CREATE TABLE {$leads_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            ticket_number VARCHAR(40) NOT NULL,
            created_at DATETIME NOT NULL,
            branch VARCHAR(100) NOT NULL,
            category_id BIGINT UNSIGNED NULL,
            product_id BIGINT UNSIGNED NULL,
            customer_name VARCHAR(190) NOT NULL,
            customer_whatsapp VARCHAR(30) NOT NULL,
            customer_email VARCHAR(190) NULL,
            customer_location VARCHAR(190) NOT NULL,
            inquiry_type VARCHAR(100) NOT NULL,
            comment TEXT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'nueva',
            assigned_user_id BIGINT UNSIGNED NULL,
            internal_notes TEXT NULL,
            source VARCHAR(30) NOT NULL DEFAULT 'totem',
            user_agent VARCHAR(255) NULL,
            ip_partial VARCHAR(45) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY ticket_number (ticket_number),
            KEY created_at (created_at),
            KEY branch (branch),
            KEY status (status),
            KEY category_id (category_id),
            KEY assigned_user_id (assigned_user_id)
        ) {$charset_collate};";

        $sql_products = "CREATE TABLE {$products_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(190) NOT NULL,
            brand VARCHAR(190) NOT NULL,
            model VARCHAR(190) NOT NULL,
            category_id BIGINT UNSIGNED NOT NULL,
            short_description TEXT NOT NULL,
            main_uses TEXT NOT NULL,
            image_id BIGINT UNSIGNED NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            display_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY category_id (category_id),
            KEY is_active (is_active),
            KEY display_order (display_order)
        ) {$charset_collate};";

        $sql_categories = "CREATE TABLE {$categories_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(190) NOT NULL,
            slug VARCHAR(190) NOT NULL,
            type VARCHAR(50) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            display_order INT NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY type (type),
            KEY is_active (is_active),
            KEY display_order (display_order)
        ) {$charset_collate};";

        dbDelta($sql_leads);
        dbDelta($sql_products);
        dbDelta($sql_categories);
    }
}
