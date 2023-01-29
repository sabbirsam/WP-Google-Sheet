<?php

namespace GSS\Inc;

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');
/**
 * Deactivated plugin
 */
class GSS_Deactivate{

    public static function gss_deactivate(){ 
        define( 'WP_DEBUG', false );
        define( 'WP_DEBUG_LOG', false );
        flush_rewrite_rules();
    }
}