<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

 class Deactivator
 {
 	public static function deactivate()
 	{
 		flush_rewrite_rules();
 	}
 }