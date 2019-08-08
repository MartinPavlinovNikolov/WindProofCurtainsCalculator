<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

 class Activator
 {
 	public static function activate()
 	{
 		flush_rewrite_rules();

 		global $wpdb;
		$table_name = $wpdb->prefix . "mpn_dev_plugin_orders";
		$table_name1 = $wpdb->prefix . "mpn_dev_plugin_customers";
		$table_name2 = $wpdb->prefix . "mpn_dev_plugin_walls";
		$table_name3 = $wpdb->prefix . "mpn_dev_plugin_dimensions";
		$table_name4 = $wpdb->prefix . "mpn_dev_plugin_setup";
		$table_name5 = $wpdb->prefix . "mpn_dev_plugin_email_templates";
		$table_name6 = $wpdb->prefix . "mpn_dev_plugin_email_to_me";

		$charset_collate = $wpdb->get_charset_collate();

		$orders = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT NOT NULL AUTO_INCREMENT,
			paid INT,
			currency text(55) NOT NULL,
			ordered_at TEXT NOT NULL,
			compleated_at TEXT,
			color text(55) NOT NULL,
			measurment text(55) NOT NULL,
			image_of_the_place text(255) NOT NULL,
			status_new_order text(255),
			status_processing text(255),
			status_manifacturing text(255),
			status_send_to_curier text(255),
			status_delivered text(255),
			status_delivered_fail text(255),
			status_canceled text(255),
			email_new_order text(255),
			email_processing text(255),
			email_manifacturing text(255),
			email_send_to_curier text(255),
			email_delivered text(255),
			email_delivered_fail text(255),
			email_canceled text(255),
			gateway text(55) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$customers = "CREATE TABLE IF NOT EXISTS $table_name1 (
			id INT NOT NULL AUTO_INCREMENT,
			order_id INT NOT NULL,
			username text(255) NOT NULL,
			email text(255) NOT NULL,
			address text(255) NOT NULL,
			phone text(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$walls = "CREATE TABLE IF NOT EXISTS $table_name2 (
			id INT NOT NULL AUTO_INCREMENT,
			order_id INT NOT NULL,
			shape text(255) NOT NULL,
			door_starts_from INT,
			note text(255),
			PRIMARY KEY  (id)
		) $charset_collate;";

		$dimensions = "CREATE TABLE IF NOT EXISTS $table_name3 (
			id INT NOT NULL AUTO_INCREMENT,
			wall_id INT NOT NULL,
			letter TEXT NOT NULL,
			value INT NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$setup = "CREATE TABLE IF NOT EXISTS $table_name4 (
			id INT NOT NULL AUTO_INCREMENT,
			stripe_public_key TEXT NOT NULL,
			stripe_secret_key TEXT NOT NULL,
			paypal_client_id TEXT NOT NULL,
			paypal_secret TEXT NOT NULL,
			mail_host TEXT NOT NULL,
			mail_username TEXT NOT NULL,
			mail_password TEXT NOT NULL,
			mail_port TEXT NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$email_templates = "CREATE TABLE IF NOT EXISTS $table_name5 (
			id INT NOT NULL AUTO_INCREMENT,
			title TEXT NOT NULL,
			subject TEXT NOT NULL,
			body TEXT NOT NULL,
			slug TEXT NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$email_to_me = "CREATE TABLE IF NOT EXISTS $table_name6 (
			id INT NOT NULL AUTO_INCREMENT,
			title TEXT NOT NULL,
			subject TEXT NOT NULL,
			body TEXT NOT NULL,
			slug TEXT NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $orders );
		dbDelta( $customers );
		dbDelta( $walls );
		dbDelta( $dimensions );
		dbDelta( $setup );
		dbDelta( $email_templates );
		dbDelta( $email_to_me );

		$table_email_templates = $wpdb->prefix . "mpn_dev_plugin_email_templates";
		$email_templates = $wpdb->get_results("SELECT * FROM `$table_email_templates`", ARRAY_A);
		if($email_templates === null || count($email_templates) < 1){
			$wpdb->insert($table_email_templates, [
				'title' => 'Поръчката e получена',
				'subject' => 'new order.',
				'body' => 'new order',
				'slug' => 'new_order'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Поръчката се обработва',
				'subject' => 'the order is under review.',
				'body' => 'processing',
				'slug' => 'processing'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Продуктът се произвежда',
				'subject' => 'the product is in the process of being manufactured.',
				'body' => 'manifacturing',
				'slug' => 'manifacturing'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Предадено',
				'subject' => 'order was send to curier.',
				'body' => 'send to curier',
				'slug' => 'send_to_curier'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Поръчката е доставена',
				'subject' => 'order was delivered.',
				'body' => 'delivered',
				'slug' => 'delivered'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Поръчката е неуспешно доставена',
				'subject' => 'delivering the order was failed.',
				'body' => 'delivered fail',
				'slug' => 'delivered_fail'
			]);
			$wpdb->insert($table_email_templates, [
				'title' => 'Поръчката е отказана',
				'subject' => 'order was canceled.',
				'body' => 'canceled',
				'slug' => 'canceled'
			]);
		}
		$table_email_to_me = $wpdb->prefix . "mpn_dev_plugin_email_to_me";
		$email_to_me = $wpdb->get_results("SELECT * FROM `$table_email_to_me`", ARRAY_A);
		if($email_to_me === null || count($email_to_me) < 1){
			$wpdb->insert($table_email_to_me, [
				'title' => 'Уведомление за нова поръчка',
				'subject' => 'you have a new order.',
				'body' => 'you have a new order',
				'slug' => 'to_me'
			]);
		}
 	}
 }