<?php

namespace Inc\Admin;

class Admin {

    private $productPage;
    private $pospage;
    private $categoryPage;

    public function __construct() {
        $this->productPage = new ProductPage();
        $this->posPage = new PosPage();
        $this->categoryPage = new CategoryPage();
    }

    public function hooks() {

        // error_log('Admin.php hook Function !!!');

        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // hooks

        add_action('admin_post_register_product', array('Inc\Admin\ProductHandler', 'register_product'));
        add_action('admin_post_delete_product', array('Inc\Admin\ProductHandler', 'delete_product'));
        add_action('admin_post_update_product', array('Inc\Admin\ProductHandler', 'update_product'));
        add_action('admin_post_save_category', ['Inc\Admin\ProductHandler', 'register_main_category']);
        add_action('admin_post_update_category', ['Inc\Admin\ProductHandler', 'update_category']);
        add_action('admin_post_delete_category', ['Inc\Admin\ProductHandler', 'delete_category']);

    }

    public function addAdminMenu() {
        add_menu_page('Clothing Shop POS', 'POS Settings', 'manage_options', 'clothing-shop-pos', array($this, 'displaySettingsPage'));

        add_submenu_page(
            'clothing-shop-pos',        // The slug of the parent page
            'POS',        // The title of the submenu page
            'Pos Menu',                  // The menu title
            'manage_options',           // The capability required for this menu to be displayed to the user
            'pos-menu',   // The slug by which this submenu will be identified
            array($this->posPage, 'display') // The function to call to display the submenu page content
        );

        add_submenu_page(
            'clothing-shop-pos',        // The slug of the parent page
            'Category',        // The title of the submenu page
            'Category',                  // The menu title
            'manage_options',           // The capability required for this menu to be displayed to the user
            'category-menu',   // The slug by which this submenu will be identified
            array($this->categoryPage, 'view_category_page') // The function to call to display the submenu page content
        );

        add_submenu_page(
            'clothing-shop-pos', 
            'Product Management', 
            'Products', 
            'manage_options', 
            'product-management', 
            array($this->productPage, 'display')
        );

    }

    public function displaySettingsPage() {
        echo '<div class="wrap"><h1>Clothing Shop POS Settings</h1></div>';
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('csp-admin-script', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/js/admin.js', array('jquery'), '1.0', true);
        wp_enqueue_script('csp-print-admin-script', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/js/print.js', array(), null, true);
    
        wp_enqueue_style('admin-product-css', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/css/product-regi.css');

        wp_enqueue_script('pos-js', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/admin/js/pos.js', array('jquery'), '1.0.0', true);
        wp_localize_script('pos-js', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('search_product_nonce')
        ));


    }


    
}