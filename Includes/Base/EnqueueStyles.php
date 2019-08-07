<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use \Includes\Base\BaseController;

class EnqueueStyles extends BaseController
{
	public function register()
	{
		add_action( 'wp_enqueue_scripts', [$this, 'handle'] );
	}

	public function handle()
	{
		wp_register_style('font-awesome.min.css', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

		wp_enqueue_style( 'font-awesome.min.css' );
		wp_enqueue_style( 'wpcc_widget', $this->plugin_url . 'assets/css/wpcc_widget.css' );
	}
}