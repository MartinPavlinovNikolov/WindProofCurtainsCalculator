<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use \Includes\Base\BaseController;

class AdminEnqueueScripts extends BaseController
{
	public function register()
	{
		add_action( 'admin_enqueue_scripts', [$this, 'handle'] );
	}

	public function handle()
	{
		wp_register_script('vue', 'https://cdn.jsdelivr.net/npm/vue', ['jquery']);
		wp_register_script('axios', 'https://unpkg.com/axios/dist/axios.min.js', ['jquery', 'vue']);

		wp_enqueue_script( 'popper.js', $this->plugin_url . 'assets/js/popper.min.js' );
		wp_enqueue_script( 'bootstrap.js', $this->plugin_url . 'assets/js/bootstrap.min.js' );
		wp_enqueue_script('vue');
		wp_enqueue_script('axios');
	}
}