<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use \Includes\Base\BaseController;

class AdminEnqueueStyles extends BaseController
{
	public function register()
	{
		add_action( 'admin_enqueue_scripts', [$this, 'handle'] );
	}

	public function handle()
	{
		wp_enqueue_style( 'bootstrap.css', $this->plugin_url . 'assets/css/bootstrap.min.css' );
	}
}