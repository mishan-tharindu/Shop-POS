<?php

namespace Inc\Core;

class Activator {

    public function activator(){

        $this->installDatabaseHandlerHooks();

    }

    public static function activate() {
        error_log('Check: Plugin Active' );
        // Code to run during activation
        // For example, setting default options or creating custom database tables
    }

    private function installDatabaseHandlerHooks(){
        $installDatabaseHanlder = new DatabaseInstall();
        $installDatabaseHanlder -> database_install();
    }

}