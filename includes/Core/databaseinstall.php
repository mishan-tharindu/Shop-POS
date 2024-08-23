<?php

namespace Inc\Core;

class DatabaseInstall{

    public function database_install(){
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

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_customer(
                `idcustomers` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NULL,
                `telephone` VARCHAR(45) NULL,
                `status` INT NULL,
                PRIMARY KEY(`idcustomers`)
            ); CREATE TABLE $table_supplier(
                `idsupplier` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NULL,
                `telephone` VARCHAR(45) NULL,
                `address` VARCHAR(45) NULL,
                `email` VARCHAR(45) NULL,
                `status` INT NULL,
                PRIMARY KEY(`idsupplier`)
            ); CREATE TABLE $table_users(
                `iduser` INT NOT NULL AUTO_INCREMENT,
                `first_name` VARCHAR(45) NULL,
                `last_name` VARCHAR(45) NULL,
                `telephone` VARCHAR(45) NULL,
                `address` VARCHAR(45) NULL,
                `nic` VARCHAR(45) NULL,
                `status` VARCHAR(45) NULL,
                PRIMARY KEY(`iduser`)
            ); CREATE TABLE $table_main_category(
                `idmain_category` INT NOT NULL AUTO_INCREMENT,
                `main_cat_name` VARCHAR(45) NULL,
                `main_cat_slug` VARCHAR(45) NULL,
                PRIMARY KEY(`idmain_category`)
            ); CREATE TABLE $table_sub_category(
                `idsub_category` INT NOT NULL AUTO_INCREMENT,
                `sub_cat_name` VARCHAR(45) NULL,
                `sub_cat_slug` VARCHAR(45) NULL,
                `idmain_category` INT,
                PRIMARY KEY(`idsub_category`),
                FOREIGN KEY(`idmain_category`) REFERENCES $table_main_category(`idmain_category`)
            ); CREATE TABLE $table_products(
                `idproducts` INT NOT NULL AUTO_INCREMENT,
                `product_name` VARCHAR(45) NULL,
                `discription` VARCHAR(45) NULL,
                `size` VARCHAR(45) NULL,
                `images` LONGTEXT NULL,
                `status` INT NULL,
                `idsub_category` INT,
                PRIMARY KEY(`idproducts`),
                FOREIGN KEY(`idsub_category`) REFERENCES $table_sub_category(`idsub_category`)
            ); CREATE TABLE $table_grn(
                `idgrn` INT NOT NULL AUTO_INCREMENT,
                `date` DATE NULL,
                `time` TIME NULL,
                `grn_number` VARCHAR(150) NULL,
                `idsupplier` INT,
                PRIMARY KEY(`idgrn`),
                FOREIGN KEY(`idsupplier`) REFERENCES $table_supplier(`idsupplier`)
            ); CREATE TABLE $table_product_stock(
                `idproduct_stock` INT NOT NULL AUTO_INCREMENT,
                `sku` VARCHAR(255) NULL,
                `qty` INT NULL,
                `buying_price` VARCHAR(45) NULL,
                `selling_price` VARCHAR(45) NULL,
                `status` INT NULL,
                `discount` VARCHAR(45) NULL,
                `idproducts` INT,
                `idgrn` INT,
                PRIMARY KEY(`idproduct_stock`),
                FOREIGN KEY(`idproducts`) REFERENCES $table_products(`idproducts`),
                FOREIGN KEY(`idgrn`) REFERENCES $table_grn(`idgrn`)
            ); CREATE TABLE $table_invoice(
                `idinvoice` VARCHAR(255) NOT NULL,
                `date` DATE NULL,
                `time` TIME NULL,
                `qty` INT NULL,
                `discount` VARCHAR(45) NULL,
                `status` INT NULL,
                `payment` VARCHAR(45) NULL,
                `idproduct_stock` INT,
                `idcustomers` INT,
                PRIMARY KEY(`idinvoice`),
                FOREIGN KEY(`idproduct_stock`) REFERENCES $table_product_stock(`idproduct_stock`),
                FOREIGN KEY(`idcustomers`) REFERENCES $table_customer(`idcustomers`)
            ); CREATE TABLE $table_return_invoice(
                `idreturn_invoice` VARCHAR(255) NOT NULL,
                `date` DATE NULL,
                `time` TIME NULL,
                `status` INT NULL,
                `idinvoice` VARCHAR(255),
                PRIMARY KEY(`idreturn_invoice`),
                FOREIGN KEY(`idinvoice`) REFERENCES $table_invoice(`idinvoice`)
            ); CREATE TABLE $table_return_inv_products(
                `idreturn_inv_products` INT NOT NULL AUTO_INCREMENT,
                `qty` VARCHAR(45) NULL,
                `note` TEXT NULL,
                `status` INT NULL,
                `idreturn_invoice` VARCHAR(255),
                `idproduct_stock` INT,
                PRIMARY KEY(`idreturn_inv_products`),
                FOREIGN KEY(`idreturn_invoice`) REFERENCES $table_return_invoice(`idreturn_invoice`),
                FOREIGN KEY(`idproduct_stock`) REFERENCES $table_product_stock(`idproduct_stock`)
            ); CREATE TABLE $table_return_grn(
                `idreturn_grn` VARCHAR(255) NOT NULL,
                `date` DATE NULL,
                `time` TIME NULL,
                `status` INT NULL,
                `idgrn` INT,
                PRIMARY KEY(`idreturn_grn`),
                FOREIGN KEY(`idgrn`) REFERENCES $table_grn(`idgrn`)
            ); CREATE TABLE $table_return_grn_products(
                    `idreturn_grn_products` INT NOT NULL AUTO_INCREMENT,
                    `qty` VARCHAR(45) NULL,
                    `note` TEXT NULL,
                    `status` INT NULL,
                    `idreturn_grn` VARCHAR(255),
                    `idproduct_stock` INT,
                    PRIMARY KEY(`idreturn_grn_products`),
                    FOREIGN KEY(`idreturn_grn`) REFERENCES $table_return_grn(`idreturn_grn`),
                    FOREIGN KEY(`idproduct_stock`) REFERENCES $table_product_stock(`idproduct_stock`)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Check for any errors
        if ($wpdb->last_error) {
            error_log('DB Error: ' . $wpdb->last_error);
        }
    }

}