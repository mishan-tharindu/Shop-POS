<?php

namespace Inc\PublicArea;

class PublicArea {
    public function hooks() {
        // add_action('wp_enqueue_scripts', array($this, 'enqueueStylesAndScripts'));
    }

    public function enqueueStylesAndScripts() {
        // wp_enqueue_style('csp-public-style', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/css/public.css');
        // wp_enqueue_script('csp-public-script', CLOTHING_SHOP_POS_PLUGIN_URL . 'assets/js/public.js', array('jquery'), '1.0', true);
    }
}