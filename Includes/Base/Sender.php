<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use Includes\Base\MpnDevMail;
use \Exception;

class Sender {

	private $order_id;
	private $email_slug;
	private $email;

	public function __construct()
	{
		$this->order_id = $_POST["order_id"];
		$this->email_slug = $_POST["email_slug"];
	}

	public function send()
	{
		try
		{
			$this->setOrder()
				 ->setCustomer()
				 ->getEmail()
				 ->replaceShortCodes()
				 ->sendEmail();
		}
		catch (Exception $e)
		{
			echo "Нещо се обърка. Моля опитайте отново...";exit();
		}
		echo "success";
	}

	private function setOrder()
	{
		global $wpdb;
		$table_orders = $wpdb->prefix . "mpn_dev_plugin_orders";
		$this->order = $wpdb->get_results("SELECT * FROM `$table_orders` WHERE `id`='".$this->order_id."'")[0];
		return $this;
	}

	private function setCustomer()
	{
		global $wpdb;
		$table_customers = $wpdb->prefix . "mpn_dev_plugin_customers";
		$this->customer = $wpdb->get_results("SELECT * FROM `$table_customers` WHERE `order_id`='".$this->order_id."'")[0];
		return $this;
	}

	private function getEmail()
	{
		global $wpdb;
		$table_emails = $wpdb->prefix . "mpn_dev_plugin_email_templates";
		$this->email = $wpdb->get_results("SELECT * FROM `$table_emails` WHERE `slug`='".$this->email_slug."'")[0];
		return $this;
	}

	private function replaceShortCodes()
	{
		$rules = [
			'[username]',
			'[phone]',
			'[address]',
			'[customer_email]',
			'[paid_at]',
			'[paid]',
			'[order]',
			'[ordered_at]',
			'[finished_at]'
		];
		$replacements = [
			$this->customer->username,
			$this->customer->phone,
			$this->customer->address,
			$this->customer->email,
			date('d-m-Y h:m:s', $this->order->ordered_at),
			($this->order->paid / 100) . $this->order->currency,
			$this->order->id,
			date('d-m-Y h:m:s', $this->order->ordered_at),
			($this->order->compleated_at == null ? '' : date('d-m-Y h:m:s', $this->order->compleated_at))
		];
		foreach($rules as $index => $rule){
			$this->email->body = str_replace($rule, $replacements[$index], $this->email->body);
		}
		$this->email->body = nl2br($this->email->body);
		return $this;
	}

	private function sendEmail()
	{
		$successfullSended = (new MpnDevMail(true))->sendToCustomerOnOrder([
			'to' => $this->customer->email,
			'subject' => $this->email->subject,
			'body' => $this->email->body,
			'alt_body' => strip_tags($this->email->body)
		]);
		if(!$successfullSended){
			throw new \Exception("Error Processing Request", 1);
		}

		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . "mpn_dev_plugin_orders",
			['email_'.$this->email_slug => 'true'],
			["id" => $this->order_id]
		);

		return $this;
	}
}