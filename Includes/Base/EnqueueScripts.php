<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use \Includes\Base\BaseController;

class EnqueueScripts extends BaseController
{
	public function register()
	{
		add_action( 'wp_enqueue_scripts', [$this, 'handle'] );
	}

	public function handle()
	{
		wp_register_script('stripe', 'https://js.stripe.com/v3/', ['jquery']);
		wp_register_script('checkout', 'https://checkout.stripe.com/checkout.js');
		wp_register_script('vue', 'https://cdn.jsdelivr.net/npm/vue', ['jquery', 'stripe', 'checkout']);
		wp_register_script('axios', 'https://unpkg.com/axios/dist/axios.min.js', ['jquery', 'stripe', 'checkout', 'vue']);

		wp_enqueue_script('stripe');
		wp_enqueue_script('checkout');
		wp_enqueue_script('vue');
		wp_enqueue_script('axios');
	}
}