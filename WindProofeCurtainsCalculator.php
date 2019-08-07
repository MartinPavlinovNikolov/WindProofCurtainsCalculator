<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */

defined('ABSPATH') or die("No no no...");

if( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php') ){
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

function activate_this_plugin(){
	Includes\Base\Activator::activate();
}
register_activation_hook( __FILE__, 'activate_this_plugin' );

function deactivate_this_plugin(){
	Includes\Base\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_this_plugin' );

if( class_exists('Includes\\Init') ){
    Includes\Init::register_services();
}