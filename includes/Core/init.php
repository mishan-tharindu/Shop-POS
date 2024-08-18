<?php

namespace Inc\Core;

// Import the Admin class from the Inc\Admin namespace
use Inc\Admin\Admin;
use Inc\PublicArea\PublicArea;

class Init {
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


}