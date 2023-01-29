<?php
/**
 * 
 */
namespace GSS\Inc;

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');
/**
 * Activate class here
 */
class GSS_Activate{

    public static function gss_activate(){ //make it static so I can call it direct on a function
        define( 'WP_DEBUG', true );
        define( 'WP_DEBUG_LOG', true );
        flush_rewrite_rules();
    }
}