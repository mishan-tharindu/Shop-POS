<?php

namespace Inc\Core;

// Import the Admin class from the Inc\Admin namespace
use Inc\Admin\Admin;
use Inc\PublicArea\PublicArea;
use Inc\Admin\ProductHandler;
use Inc\Admin\Invoice;


class Init {

    private $installDatabaseHanlder;

    public function __construct() {
        
    }

    // public function register_hook() {
    //     add_action('plugins_loaded', [$this, 'init']);
    // }

    public function init() {
        // Initialize other core components
        $this->loadDependencies();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineProductHandlerHooks();
        $this->defineInvoiceHanlderHooks();
    }


    private function loadDependencies() {
        // Autoloaded via Composer
    }

    private function defineAdminHooks() {
        $admin = new Admin();
        $admin->hooks();
    }

    private function definePublicHooks() {
        $public = new PublicArea();
        $public->hooks();
    }

    private function defineProductHandlerHooks(){
        // error_log('defineProductHandlerHooks Called !!!');
        $productHandler = new ProductHandler();
        $productHandler->register_hooks();
    }

    private function defineInvoiceHanlderHooks() {
        $invoiceHanlder = new Invoice();
        $invoiceHanlder->invoice_hooks();
    }


}