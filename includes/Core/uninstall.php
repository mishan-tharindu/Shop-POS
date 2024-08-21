<?php

namespace Inc\Core;

class Uninstall{

    public static function plugin_Uninstall(){

        global $wpdb;

        // Database Table
        $table_customer = $wpdb->prefix .'mt_customer';
        $table_supplier =  $wpdb->prefix .'mt_supplier';
        $table_users =  $wpdb->prefix .'mt_users';
        $table_main_category =  $wpdb->prefix .'mt_main_category';
        $table_sub_category =  $wpdb->prefix .'mt_sub_category';
        $table_products =  $wpdb->prefix .'mt_products';
        $table_grn =  $wpdb->prefix .'mt_grn';
        $table_product_stock =  $wpdb->prefix .'mt_product_stock';
        $table_invoice =  $wpdb->prefix .'mt_invoice';
        $table_return_invoice =  $wpdb->prefix .'mt_return_invoice';
        $table_return_inv_products =  $wpdb->prefix .'mt_return_inv_products';
        $table_return_grn =  $wpdb->prefix .'mt_return_grn';
        $table_return_grn_products =  $wpdb->prefix .'mt_return_grn_products';

        $tables = [
            $table_customer, $table_supplier, $table_users, $table_main_category, $table_sub_category, $table_products, $table_grn, $table_product_stock, $table_invoice,
            $table_return_invoice, $table_return_inv_products, $table_return_grn, $table_return_grn_products
        ];
    
        foreach ($tables as $table) {
            // $wpdb->query("DROP TABLE IF EXISTS $table");
            $wpdb->query("SET FOREIGN_KEY_CHECKS = 0;");
            $wpdb->query("DROP TABLE IF EXISTS $table;");
            $wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");
        }

    }




}