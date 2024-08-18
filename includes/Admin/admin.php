<?php

namespace Inc\Admin;

class Admin {

    private $productPage;

    public function __construct() {
        $this->productPage = new ProductPage();
    }

    public function hooks() {
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // hooks

        add_action('admin_post_register_product', array('Inc\Admin\ProductHandler', 'register_product'));
        add_action('admin_post_delete_product', array('Inc\Admin\ProductHandler', 'delete_product'));
        add_action('admin_post_update_product', array('Inc\Admin\ProductHandler', 'update_product'));
    }

    public function addAdminMenu() {
        add_menu_page('Clothing Shop POS', 'POS Settings', 'manage_options', 'clothing-shop-pos', array($this, 'displaySettingsPage'));
        add_menu_page('Product Management', 'Products', 'manage_options', 'product-management', array($this->productPage, 'display'));
    }

    public function displaySettingsPage() {
        echo '<div class="wrap"><h1>Clothing Shop POS Settings</h1></div>';
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('csp-admin-script', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/js/admin.js', array('jquery'), '1.0', true);
    
        wp_enqueue_style('admin-product-css', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/css/product-regi.css');

    }
    
}