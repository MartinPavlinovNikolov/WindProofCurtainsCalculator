<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes;

final class Init
{
	public static function get_services()
	{
		return [
			Pages\Dashboard::class,
			Base\AdminEnqueueStyles::class,
			Base\AdminEnqueueScripts::class,
			Base\EnqueueStyles::class,
			Base\EnqueueScripts::class,
			Base\SettingsLink::class
		];
	}

	public static function register_services()
	{
		foreach(self::get_services() as $class){
			$service = self::instantiate( $class );
			if( method_exists($service, 'register') ){
				$service->register();
			}
		}
	}

	private static function instantiate( $class )
	{
		$service = new $class();
		return $service;
	}
}