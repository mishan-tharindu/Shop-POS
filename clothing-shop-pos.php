<?php
/*
Plugin Name: Clothing Shop POS
Plugin URI:  http://yourwebsite.com/
Description: A comprehensive POS system for clothing shops.
Version:     1.0
Author:      Mishan Tharindu
Author URI:  http://yourwebsite.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: clothing-shop-pos
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register Composer Autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Define constants
define('CLOTHING_SHOP_POS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CLOTHING_SHOP_POS_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the main plugin class
use Inc\Core\Init;

// Instance of the main plugin class
function run_clothing_shop_pos() {
    $init = new Init();
    $init->init();
}

// Hook into the 'plugins_loaded' action to run the plugin
add_action('plugins_loaded', 'run_clothing_shop_pos');

register_activation_hook(__FILE__, array('Inc\Core\Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Inc\Core\Deactivator', 'deactivate'));
register_uninstall_hook( __FILE__, array('Inc\Core\Uninstall', 'plugin_Uninstall') );

