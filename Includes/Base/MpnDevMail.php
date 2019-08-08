<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MpnDevMail extends PHPMailer {

	public $mpn_host;
	public $mpn_my_username;
	public $mpn_my_sender;
	public $mpn_my_receiver;
	public $mpn_password;
	public $mpn_port;

	public function __construct($auth)
	{
		parent::__construct($auth);
		$this->CharSet = 'UTF-8';
		if(get_site_url() === "http://wordpress.local"){
			$this->mpn_host = 'smtp.mailtrap.io';
			$this->mpn_my_username = get_option('mail_username');
			$this->mpn_my_sender = get_option('mail_sender');
			$this->mpn_my_receiver = get_option('mail_receiver');
			$this->mpn_password = '85c8e9259654b0';
			$this->mpn_port = 465;
			//Server settings
		    $this->isSMTP();// Set mailer to use SMTP
		    $this->Host       = $this->mpn_host;// Specify main and backup SMTP servers
		    $this->SMTPAuth   = true;// Enable SMTP authentication
		    $this->Username   = 'e7a37e0739cdf7';// SMTP username
		    $this->Password   = $this->mpn_password;// SMTP password
			$this->SMTPSecure = 'tls';// Enable TLS encryption, `ssl` also accepted
			$this->Port       = $this->mpn_port;// TCP port to connect to
		} else {
			$this->mpn_host = get_option('mail_host');
			$this->mpn_my_username = get_option('mail_username');
			$this->mpn_my_sender = get_option('mail_sender');
			$this->mpn_my_receiver = get_option('mail_receiver');
			$this->mpn_password = get_option('mail_password');
			$this->mpn_port = (int) get_option('mail_port');
			//Server settings
		    $this->isSMTP();// Set mailer to use SMTP
		    $this->Host       = $this->mpn_host;// Specify main and backup SMTP servers
		    $this->SMTPAuth   = true;// Enable SMTP authentication
		    $this->Username   = $this->mpn_my_sender;// SMTP username
		    $this->Password   = $this->mpn_password;// SMTP password
			$this->SMTPOptions = array(
	                    'ssl' => array(
	                        'verify_peer' => false,
	                        'verify_peer_name' => false,
	                        'allow_self_signed' => true
	                    )
	                );
			$this->SMTPSecure = 'ssl';// Enable TLS encryption, `ssl` also accepted
			$this->Port       = $this->mpn_port;// TCP port to connect to
		}
	}

	public function sendOnCustomerMakeOrder($data)
	{
		try {
		    //Recipients
		    $this->setFrom($this->mpn_my_sender, $this->mpn_my_username);
		    $this->addAddress($this->mpn_my_receiver);// Add a recipient

		    // Content
		    $this->isHTML(true);// Set email format to HTML
		    $this->Subject = $data['subject'];
		    $this->Body    = $data['body'];
		    $this->AltBody = $data['alt_body'];

		    $this->send();
		    return true;
		} catch (Exception $e) {
		    return false;
		}
	}

	public function sendToCustomerOnOrder($data)
	{
		try {
		    //Recipients
		    $this->setFrom($this->mpn_my_sender, $this->mpn_my_username);
		    $this->addAddress($data['to']);// Add a recipient

		    // Content
		    $this->isHTML(true);// Set email format to HTML
		    $this->Subject = $data['subject'];
		    $this->Body    = $data['body'];
		    $this->AltBody = $data['alt_body'];

		    $this->send();
		    return true;
		} catch (Exception $e) {
			echo "<pre>"; print_r($e->getMessage()); echo "</pre>"; exit();
		    return false;
		}
	}
}