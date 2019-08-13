<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Api\Callbacks;

use Includes\Base\BaseController;
use Includes\Base\MpnDevStripe;
use Includes\Base\MpnDevPaypal;
use Includes\Base\Sender;

class DashboardCallbacks extends BaseController
{
	public function orders()
	{
		global $wpdb;

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
			$email_templates = $wpdb->get_results("SELECT * FROM `$table_email_templates`", ARRAY_A);
		}

		$table_orders = $wpdb->prefix . "mpn_dev_plugin_orders";
		$table_customers = $wpdb->prefix . "mpn_dev_plugin_customers";
		$table_walls = $wpdb->prefix . "mpn_dev_plugin_walls";
		$table_dimensions = $wpdb->prefix . "mpn_dev_plugin_dimensions";

		$orders = [];
		$compleatedOrders = [];
		$incompleatedOrders = [];
		foreach($wpdb->get_results("SELECT * FROM `$table_orders` ORDER BY `ordered_at` DESC", ARRAY_A) as $o => $order){
			$order_id = $order['id'];
			$orders[$o]['id'] = $order['id'];
			$orders[$o]['paid'] = $order['paid'];
			$orders[$o]['currency'] = $order['currency'];
			$orders[$o]['ordered_at'] = date('d-m-Y h:m:s', $order['ordered_at']);
			$orders[$o]['compleated_at'] = date('d-m-Y h:m:s', $order['compleated_at']);
			$orders[$o]['color'] = $order['color'];
			$orders[$o]['measurment'] = $order['measurment'];
			$orders[$o]['image_of_the_place'] = plugins_url( '../../', __DIR__ ) . $order['image_of_the_place'];

			$orders[$o]['email_templates'] = $email_templates;

			$orders[$o]['email_templates'][0]['selected'] = $order['status_new_order'];
			$orders[$o]['email_templates'][1]['selected'] = $order['status_processing'];
			$orders[$o]['email_templates'][2]['selected'] = $order['status_manifacturing'];
			$orders[$o]['email_templates'][3]['selected'] = $order['status_send_to_curier'];
			$orders[$o]['email_templates'][4]['selected'] = $order['status_delivered'];
			$orders[$o]['email_templates'][5]['selected'] = $order['status_delivered_fail'];
			$orders[$o]['email_templates'][6]['selected'] = $order['status_canceled'];
			
			if($order['status_canceled'] == 'true'){
				$orders[$o]['status'] = 'canceled';
			} else if($order['status_delivered_fail'] == 'true'){
				$orders[$o]['status'] = 'delivered_fail';
			} else if($order['status_delivered'] == 'true'){
				$orders[$o]['status'] = 'delivered';
			} else if($order['status_send_to_curier'] == 'true'){
				$orders[$o]['status'] = 'send_to_curier';
			} else if($order['status_manifacturing'] == 'true'){
				$orders[$o]['status'] = 'manifacturing';
			} else if($order['status_processing'] == 'true'){
				$orders[$o]['status'] = 'processing';
			} else {
				$orders[$o]['status'] = 'new_order';
			}

			$orders[$o]['email_templates'][0]['sended'] = $order['email_new_order'];
			$orders[$o]['email_templates'][1]['sended'] = $order['email_processing'];
			$orders[$o]['email_templates'][2]['sended'] = $order['email_manifacturing'];
			$orders[$o]['email_templates'][3]['sended'] = $order['email_send_to_curier'];
			$orders[$o]['email_templates'][4]['sended'] = $order['email_delivered'];
			$orders[$o]['email_templates'][5]['sended'] = $order['email_delivered_fail'];
			$orders[$o]['email_templates'][6]['sended'] = $order['email_canceled'];

			$orders[$o]['gateway'] = $order['gateway'];

			$customer = $wpdb->get_results("SELECT * FROM `$table_customers` WHERE `order_id`='$order_id'", ARRAY_A)[0];
			$orders[$o]['user_id'] = $customer['id'];
			$orders[$o]['username'] = $customer['username'];
			$orders[$o]['email'] = $customer['email'];
			$orders[$o]['address'] = $customer['address'];
			$orders[$o]['phone'] = $customer['phone'];

			foreach($wpdb->get_results("SELECT * FROM `$table_walls` WHERE `order_id` = '$order_id' ORDER BY `id` ASC", ARRAY_A) as $w => $wall){
				$wall_id = $wall['id'];
				$orders[$o]['walls'][$w]['id'] = $wall['id'];
				$orders[$o]['walls'][$w]['order_id'] = $wall['order_id'];
				$orders[$o]['walls'][$w]['shape'] = plugins_url( '../../assets/images/small/', __DIR__ ) . $wall['shape'];
				$orders[$o]['walls'][$w]['door_starts_from'] = $wall['door_starts_from'];
				$orders[$o]['walls'][$w]['note'] = $wall['note'];
				foreach($wpdb->get_results("SELECT * FROM `$table_dimensions` WHERE `wall_id` = '$wall_id'", ARRAY_A) as $d => $dimension){
					$orders[$o]['walls'][$w]['dimensions'][$d]['id'] = $dimension['id'];
					$orders[$o]['walls'][$w]['dimensions'][$d]['wall_id'] = $dimension['wall_id'];
					$orders[$o]['walls'][$w]['dimensions'][$d]['letter'] = $dimension['letter'];
					$orders[$o]['walls'][$w]['dimensions'][$d]['value'] = $dimension['value'];
					if($orders[$o]['status_delivered'] == 'true' || $orders[$o]['status_canceled'] == 'true'){
						$compleatedOrders[$o] = $orders[$o];
					} else {
						$incompleatedOrders[$o] = $orders[$o];
					}
				}
			}
		}

		return require_once( "$this->plugin_path/templates/orders.php" );
	}

	public function emails()
	{
		global $wpdb;
		$table_email_templates = $wpdb->prefix . "mpn_dev_plugin_email_templates";
		$email_templates = $wpdb->get_results("SELECT * FROM `$table_email_templates`", ARRAY_A);

		$table_email_to_me = $wpdb->prefix . "mpn_dev_plugin_email_to_me";
		$email_to_me = $wpdb->get_results("SELECT * FROM `$table_email_to_me`", ARRAY_A)[0];

		return require_once( "$this->plugin_path/templates/emails.php" );
	}

    public function updateEmailTemplate()
    {
    	global $wpdb;
		$id = $_POST['id'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];

		$wpdb->update(
			$wpdb->prefix . "mpn_dev_plugin_email_templates",
			["body" => $body, "subject" => $subject],
			["id" => $id]
		);

        echo 'Успешно записан!';
        wp_die();
    }

    public function updateEmailToMe()
    {
    	global $wpdb;
		$id = $_POST['id'];
		$subject = $_POST['subject'];
		$body = $_POST['body'];

		$wpdb->update(
			$wpdb->prefix . "mpn_dev_plugin_email_to_me",
			["body" => $body, "subject" => $subject],
			["id" => $id]
		);

        echo 'Успешно записан!';
        wp_die();
    }

    public function deleteOrder()
    {
    	global $wpdb;
		$order_id = $_POST['order_id'];

		$table_walls = $wpdb->prefix . "mpn_dev_plugin_walls";
		foreach($wpdb->get_results("SELECT * FROM `$table_walls` WHERE order_id='$order_id'", ARRAY_A) as $wall){
			$wpdb->delete( $wpdb->prefix . "mpn_dev_plugin_dimensions", ["wall_id" => $wall['id']] );
		}

		$wpdb->delete( $wpdb->prefix . "mpn_dev_plugin_walls", ["order_id" => $order_id] );

		$wpdb->delete( $wpdb->prefix . "mpn_dev_plugin_orders", ["id" => $order_id] );

        wp_die();
    }

    public function updateOrderStatus()
    {
    	global $wpdb;
		$id = $_POST['order_id'];
		$status = 'status_'.$_POST['status'];
		$email = 'email_'.$_POST['status'];

		$wpdb->update(
			$wpdb->prefix . "mpn_dev_plugin_orders",
			[
				'status_new_order' => null,
				'status_processing' => null,
				'status_manifacturing' => null,
				'status_send_to_curier' => null,
				'status_delivered' => null,
				'status_delivered_fail' => null,
				'status_canceled' => null,
				"compleated_at" => null
			],
			[
				"id" => $id
			]
		);

		if($_POST['status'] == 'send_to_curier' || $_POST['status'] == 'canceled'){
			$wpdb->update(
				$wpdb->prefix . "mpn_dev_plugin_orders",
				[$status => 'true', "compleated_at" => time()],
				["id" => $id]
			);
		} else {
			$wpdb->update(
				$wpdb->prefix . "mpn_dev_plugin_orders",
				[$status => 'true'],
				["id" => $id]
			);
		}

		if($wpdb->get_results("SELECT * FROM `$table_orders` WHERE id='$id'", ARRAY_A)[0][$email] == 'true'){
			echo '<span class="dashicons dashicons-yes"></span>';
		} else {
			echo 'Уведоми';
		}
		
        wp_die();
    }

    public function handleStripe()
    {
    	try {
			$MpnDevStripe = new MpnDevStripe();
			$MpnDevStripe->store();
		} catch (Exception $e) {
			echo 'error';
		}

        wp_die();
    }

    public function handlePaypal()
    {
    	try {
		    $MpnDevPaypal = new MpnDevPaypal();
		    $MpnDevPaypal->store();
		} catch (\Exception $e) {
		    echo 'error';
		}
        wp_die();
    }

    public function sendEmailManualy()
    {
    	(new Sender)->send();
        wp_die();
    }

	public function settings()
	{
		return require_once( "$this->plugin_path/templates/settings.php" );
	}

	public function wpccAdminSection($input)
	{
		echo '';
	}

	public function wpccUpdatePricePerSquereMeter($input)
	{
		$input = (string) $input * 100;
		return $input;
	}

	public function wpccUpdateDoorPrice($input)
	{
		$input = (string) $input * 100;
		return $input;
	}

	public function wpccUpdateDoorWidth($input)
	{
		return $input;
	}

	public function wpccUpdateDoorHeight($input)
	{
		return $input;
	}

	public function wpccUpdateStripePublicKey($input)
	{
		return $input;
	}

	public function wpccUpdateStripeSecretKey($input)
	{
		return $input;
	}

	public function wpccUpdatePaypalClientId($input)
	{
		return $input;
	}

	public function wpccUpdatePaypalSecret($input)
	{
		return $input;
	}

	public function wpccUpdateMailSender($input)
	{
		return $input;
	}

	public function wpccUpdateMailHost($input)
	{
		return $input;
	}

	public function wpccUpdateMailReceiver($input)
	{
		return $input;
	}

	public function wpccUpdateMailUsername($input)
	{
		return $input;
	}

	public function wpccUpdateMailPassword($input)
	{
		return $input;
	}

	public function wpccUpdateMailPort($input)
	{
		return $input;
	}

	public function wpccPricePerSquereMeter($input)
	{
		$value = esc_attr( get_option( 'price_per_squere_meter' ) != null ? get_option( 'price_per_squere_meter' ) / 100 : 2 );
		echo '<input type="number" class="regular-text" name="price_per_squere_meter" value="'.$value.'" placeholder="цена">';
	}

	public function wpccDoorPrice($input)
	{
		$value = esc_attr( get_option( 'door_price' ) != null ? get_option( 'door_price' ) / 100 : 3 );
		echo '<input type="number" class="regular-text" name="door_price" value="'.$value.'" placeholder="цена">';
	}

	public function wpccDoorWidth($input)
	{
		$value = esc_attr( get_option( 'door_width' ) != null ? get_option( 'door_width' ) : 90 );
		echo '<input type="number" class="regular-text" name="door_width" value="'.$value.'" placeholder="широчина на вратата">';
	}

	public function wpccDoorHeight($input)
	{
		$value = esc_attr( get_option( 'door_height' ) != null ? get_option( 'door_height' ) : 200 );
		echo '<input type="number" class="regular-text" name="door_height" value="'.$value.'" placeholder="височина на вратата">';
	}

	public function wpccStripePublicKey($input)
	{
		$value = esc_attr( get_option( 'stripe_public_key' ) != null ? get_option( 'stripe_public_key' ) : 'pk_test_lVZX6Oqmev8YxYF9Ub1a4TNp00tlWJ7s94' );
		echo '<input type="text" class="regular-text" name="stripe_public_key" value="'.$value.'" placeholder="public key">';
	}

	public function wpccStripeSecretKey($input)
	{
		$value = esc_attr( get_option( 'stripe_secret_key' ) != null ? get_option( 'stripe_secret_key' ) : 'sk_test_vp02kYk98HLycQE9dOe6p62p00cxA91GvY' );
		echo '<input type="text" class="regular-text" name="stripe_secret_key" value="'.$value.'" placeholder="secret key">';
	}

	public function wpccPaypalClientId($input)
	{
		$value = esc_attr( get_option( 'paypal_client_id' ) != null ? get_option( 'paypal_client_id' ) : 'AcJcCA-CNQxsWNU5a1coBsL6nVS0vBVW1UUsxphGyF_2hi-YxsqBXS6uMNChpPXGl0qeuO9c_UmF8USG' );
		echo '<input type="text" class="regular-text" name="paypal_client_id" value="'.$value.'" placeholder="client id">';
	}

	public function wpccPaypalSecret($input)
	{
		$value = esc_attr( get_option( 'paypal_secret' ) != null ? get_option( 'paypal_secret' ) : 'ED92OKReYpmG1dBwecryqj3ul0k3G5HEhEQucUlRm-gj0KAJ6fP2U-Y7xC-YNyZ8l8AqiKER85q4Waql' );
		echo '<input type="text" class="regular-text" name="paypal_secret" value="'.$value.'" placeholder="secret">';
	}

	public function wpccMailReceiver($input)
	{
		$value = esc_attr( get_option( 'mail_receiver' ) != null ? get_option( 'mail_receiver' ) : 'contact@windproofcurtains.co.uk' );
		echo '<input type="text" class="regular-text" name="mail_receiver" value="'.$value.'" placeholder="имейл за получаване">';
	}

	public function wpccMailHost($input)
	{
		$value = esc_attr( get_option( 'mail_host' ) != null ? get_option( 'mail_host' ) : 'mail.windproofcurtains.co.uk' );
		echo '<input type="text" class="regular-text" name="mail_host" value="'.$value.'" placeholder="smtp името на хост сървъра">';
	}

	public function wpccMailSender($input)
	{
		$value = esc_attr( get_option( 'mail_sender' ) != null ? get_option( 'mail_sender' ) : 'contact@windproofcurtains.co.uk' );
		echo '<input type="text" class="regular-text" name="mail_sender" value="'.$value.'" placeholder="имейл за изпращане">';
	}

	public function wpccMailUsername($input)
	{
		$value = esc_attr( get_option( 'mail_username' ) != null ? get_option( 'mail_username' ) : 'contact@windproofcurtains.co.uk' );
		echo '<input type="text" class="regular-text" name="mail_username" value="'.$value.'" placeholder="име">';
	}

	public function wpccMailPassword($input)
	{
		$value = esc_attr( get_option( 'mail_password' ) != null ? get_option( 'mail_password' ) : '[]=EDfCxcAku' );
		echo '<input type="text" class="regular-text" name="mail_password" value="'.$value.'" placeholder="парола">';
	}

	public function wpccMailPort($input)
	{
		$value = esc_attr( get_option( 'mail_port' ) != null ? get_option( 'mail_port' ) : 465 );
		echo '<input type="number" class="regular-text" name="mail_port" value="'.$value.'" placeholder="порт">';
	}
}