<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Base;

use Includes\Base\MpnDevMail;

class MpnDevPaypal {

  private $json;
  private $mail;

  public function __construct()
  {
    $this->json = json_decode(stripslashes($_POST['data']), true);
    $this->json['order']['image'] = "uploaded_images/" . basename($_FILES['image']['tmp_name'] . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $this->mail = new MpnDevMail(true);
  }

  public function store()
  {
    $this->validate()
       ->calculateAmount()
       ->saveImageOfThePlace()
       ->saveOrderInDB()
       ->sendEmailToOwner()
       ->sendEmailToCustomer()
       ->returnResponse();
  }

  private function validate()
  {
    return $this;
  }

  private function returnResponse()
  {
    echo 'success';
  }

  private function calculateAmount()
  {
    return $this;
  }

  private function sendEmailToCustomer()
  {
    $this->mail->sendToCustomerOnOrder([
      'to' => $this->json['order']['email'],
      'subject' => $this->getMailSubjectForCustomerOnCustomerMakeOrder(),
      'body' => $this->getMailContentForCustomerOnCustomerMakeOrder(),
      'alt_body' => strip_tags($this->getMailContentForCustomerOnCustomerMakeOrder())
    ]);
    return $this->resetMail();
  }

  private function sendEmailToOwner()
  {
    $this->mail->sendOnCustomerMakeOrder([
      'subject' => $this->getMailSubjectForOwnerOnCustomerMakeOrder(),
      'body' => $this->getMailContentForOwnerOnCustomerMakeOrder(),
      'alt_body' => strip_tags($this->getMailContentForOwnerOnCustomerMakeOrder())
    ]);
    return $this->resetMail();
  }

  private function resetMail()
  {
    $this->mail = new MpnDevMail(true);
    return $this;
  }

  private function getMailSubjectForCustomerOnCustomerMakeOrder()
  {
    global $wpdb;
    $table_email_templates = $wpdb->prefix . "mpn_dev_plugin_email_templates";
    return $wpdb->get_results("SELECT * FROM `$table_email_templates`", ARRAY_A)[0]['subject'];
  }

  private function getMailContentForCustomerOnCustomerMakeOrder()
  {
    global $wpdb;
    $table_email_templates = $wpdb->prefix . "mpn_dev_plugin_email_templates";
    return $this->replaceShortCodes($wpdb->get_results("SELECT * FROM `$table_email_templates`", ARRAY_A)[0]['body']);
  }

  private function getMailSubjectForOwnerOnCustomerMakeOrder()
  {
    global $wpdb;
    $table_email_to_me = $wpdb->prefix . "mpn_dev_plugin_email_to_me";
    return $this->replaceShortCodes($wpdb->get_results("SELECT * FROM `$table_email_to_me`", ARRAY_A)[0]['subject']);
  }

  private function getMailContentForOwnerOnCustomerMakeOrder()
  {
    global $wpdb;
    $table_email_to_me = $wpdb->prefix . "mpn_dev_plugin_email_to_me";
    return $this->replaceShortCodes($wpdb->get_results("SELECT * FROM `$table_email_to_me`", ARRAY_A)[0]['body']);
  }

  private function replaceShortCodes($body)
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
      $this->json['order']['username'],
      $this->json['order']['phone'],
      $this->json['order']['address'],
      $this->json['order']['email'],
      date('d-m-Y h:m:s', $this->json['order']['ordered_at']),
      ($this->json['order']['price'] / 100) . $this->json['order']['currency'],
      $this->json['order']['id'],
      date('d-m-Y h:m:s', $this->json['order']['ordered_at']),
      null
    ];

    foreach($rules as $index => $rule){
      $body = str_replace($rule, $replacements[$index], $body);
    }
    $body = nl2br($body);
    return $body;
  }

  private function saveImageOfThePlace()
  {
      move_uploaded_file($_FILES['image']['tmp_name'], plugin_dir_path( dirname( __FILE__, 2 )).$this->json['order']['image']);
      return $this;
  }

  private function saveOrderInDB()
  {
    global $wpdb;

    $paid = $this->json['order']['price'];
    $this->json['order']['currency'] = $currency = 'gbp';//todo
    $this->json['order']['ordered_at'] = $ordered_at = time();
    $color = $this->json['order']['selected_color'];
    $measurment = $this->json['order']['measurment'];
    $image_of_the_place = $this->json['order']['image'];
    $order_status = 'new_order';
    $gateway = 'paypal';

    $username = $this->json['order']['username'];
    $email = $this->json['order']['email'];
    $address = $this->json['order']['address'];
    $phone = $this->json['order']['phone'];

    $wpdb->insert( 
      $wpdb->prefix . "mpn_dev_plugin_orders",
      array(
        'paid' => $paid,
        'currency' => $currency,
        'ordered_at' => $ordered_at,
        'color' => $color,
        'measurment' => $measurment,
        'image_of_the_place' => $image_of_the_place,
        'status_new_order' => 'true',
        'email_new_order' => 'true',
        'gateway' => $gateway
      ) 
    );
    $this->json['order']['id'] = $order_id = $wpdb->insert_id;

    $wpdb->insert( 
      $wpdb->prefix . "mpn_dev_plugin_customers",
      array(
        'order_id' => $order_id,
        'username' => $username,
        'email' => $email,
        'address' => $address,
        'phone' => $phone
      ) 
    );

    foreach($this->json['order']['walls'] as $wall){

      $current_svg_name = ($wall['shape_id'] . '.svg');
      $door_starts_from = $wall['door_starts_from'] != 0 ? $wall['door_starts_from'] : null;
      $section_additional_information = $wall['additional_information'];

      $wpdb->insert( 
        $wpdb->prefix . "mpn_dev_plugin_walls",
        array(
          'order_id' => $order_id,
          'shape' => $current_svg_name,
          'door_starts_from' => $door_starts_from,
          'note' => $section_additional_information
        ) 
      );
      $wall_id = $wpdb->insert_id;

      foreach($wall['shape_dimensions'] as $dimension){
        $letter = $dimension['letter'];
        $value = $dimension['value'];

        $wpdb->insert( 
          $wpdb->prefix . "mpn_dev_plugin_dimensions",
          array(
            'wall_id' => $wall_id,
            'letter' => $letter,
            'value' => $value
          ) 
        );
      }
    }

    return $this;
  }
  
}
