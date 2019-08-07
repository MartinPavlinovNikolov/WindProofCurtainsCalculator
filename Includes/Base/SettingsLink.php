<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use \Includes\Base\BaseController;

 class SettingsLink extends BaseController
 {
 	public function register()
 	{
 		add_filter("plugin_action_links_$this->plugin_name", [$this, 'handle']);
 	}

 	public function handle($links)
 	{
        $links[] = '<a href="options-general.php?page=wind_proofe_curtains_calculator">Работен плот</a>';
        return $links;
 	}
 }