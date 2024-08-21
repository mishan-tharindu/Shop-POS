<?php

namespace Inc\Core;

class Deactivator {

    public function __construct() {

    }

    public static function deactivate() {
         // Code to run during deactivation
        // For example, cleaning up options or temporary data

        // echo " Plugin Deactivate !!! ";

        // Debug.log(" Plugin Deactivate !!! ");
        error_log('Check: Plugin Deactivate !!!' );
        // dropDataBase();
        


    }


}