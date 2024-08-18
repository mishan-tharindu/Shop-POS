<?php

namespace Inc\Core;

class Activator {
    public static function activate() {
        // Code to run during activation
        // For example, setting default options or creating custom database tables

        global $wpdb;
        $table_name = $wpdb->prefix . 'mt_products';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            sku varchar(100) NOT NULL,
            name varchar(255) NOT NULL,
            description text NOT NULL,
            category varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL,
            quantity int NOT NULL,
            supplier varchar(255) NOT NULL,
            images text,
            status varchar(50) DEFAULT 'active',
            PRIMARY KEY (id),
            UNIQUE KEY sku (sku)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}