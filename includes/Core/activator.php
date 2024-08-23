<?php

namespace Inc\Core;

class Activator {

    public function __construct() {
        error_log('Check: Activator Constructer !!! ' );
    }

    public function activator(){
        error_log('Check: Activator Constructer !!! ' );

    }

    public static function activate() {
        error_log('Check: Plugin Active' );
        // Code to run during activation
        // For example, setting default options or creating custom database tables

        self::setup_database();

    }

    public static function setup_database(){
        $installDatabaseHanlder = new DatabaseInstall();
        $installDatabaseHanlder -> database_install();
    }

}