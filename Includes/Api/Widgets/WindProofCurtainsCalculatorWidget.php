<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Api\Widgets;

use WP_Widget;

class WindProofCurtainsCalculatorWidget extends WP_Widget
{

	public $widget_id;
	public $widget_name;
	public $widget_options = [];
	public $control_options = [];

	public function __construct()
	{
		$this->widget_id = 'wind_proof_curtains_calculator_widget';
		$this->widget_name = 'Wind Proof Curtains Calculator';
		$this->widget_options = [
			'classname' => $this->widget_id,
			'description' => $this->widget_name
		];
		$this->control_options = [
			'width' => '100%',
			'height' => 'auto'
		];
	}

	public function register()
	{
		parent::__construct($this->widget_id, $this->widget_name, $this->widget_options, $this->control_options);

		add_action('widgets_init', [$this, 'widgetInit']);
	}

	public function widgetInit()
	{
		register_widget($this);
	}

	public function widget($args, $instance)
	{
		if(!defined('WPCC_PLUGIN_URL')){
			define('WPCC_PLUGIN_URL', plugin_dir_url( dirname( __FILE__, 3 ) ));
		}
		if(!defined('WPCC_PLUGIN_DIR')){
			define('WPCC_PLUGIN_DIR', plugin_dir_path( dirname( __FILE__, 3 ) ));
		}
		echo $args['before_widget'];

		$price_per_squere_meter = get_option('price_per_squere_meter');
		$door_price = get_option('door_price');
		$door_width = get_option('door_width');
		$door_height = get_option('door_height');
		$stripe_public_key = get_option('stripe_public_key');
		$paypal_client_id = get_option('paypal_client_id');
		
		require_once( WPCC_PLUGIN_DIR . 'templates/widget.html.php' );
		require_once( WPCC_PLUGIN_DIR . 'templates/widget.js.php' );

		echo $args['after_widget'];
	}

	public function form($instance)
	{
		echo "<p>Този widget може да се настрои в <a href='/wp-admin/admin.php?page=wpcc_settings'>\"Работен плот/Настройки\"</a></p>";
	}

	public function update($new_instance, $old_instance)
	{
		//
	}

}