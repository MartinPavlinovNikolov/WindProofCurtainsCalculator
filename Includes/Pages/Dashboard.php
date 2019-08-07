<?php
/**
 * Plugin Name: WindProofeCurtainsCalculator
 */
namespace Includes\Pages;

use \Includes\Api\SettingsApi;
use \Includes\Base\BaseController;
use \Includes\Api\Callbacks\DashboardCallbacks;
use \Includes\Api\Widgets\WindProofCurtainsCalculatorWidget;

class Dashboard extends BaseController
{
    public $settings;
    public $pages = [];
    public $subpages = [];

	public function register()
	{
        $this->settings = new SettingsApi();
        $this->callbacks = new DashboardCallbacks();
        $this->setPages()
             ->setSubPages()
             ->setSettings()
             ->setSections()
             ->setFields()
             ->settings->addPages($this->pages)
                       ->withSubPage('Поръчки')
                       ->addSubPages($this->subpages)
                       ->register();

        add_action('wp_ajax_update_email_template', [$this->callbacks, 'updateEmailTemplate']);
        add_action('wp_ajax_nopriv_update_email_template', [$this->callbacks, 'updateEmailTemplate']);

        add_action('wp_ajax_handle_stripe', [$this->callbacks, 'handleStripe']);
        add_action('wp_ajax_nopriv_handle_stripe', [$this->callbacks, 'handleStripe']);

        add_action('wp_ajax_handle_paypal', [$this->callbacks, 'handlePaypal']);
        add_action('wp_ajax_nopriv_handle_paypal', [$this->callbacks, 'handlePaypal']);

        add_action('wp_ajax_send_email_manualy', [$this->callbacks, 'sendEmailManualy']);
        add_action('wp_ajax_nopriv_send_email_manualy', [$this->callbacks, 'sendEmailManualy']);

        add_action('wp_ajax_update_order_status', [$this->callbacks, 'updateOrderStatus']);
        add_action('wp_ajax_update_order_status', [$this->callbacks, 'updateOrderStatus']);

        $widget = new WindProofCurtainsCalculatorWidget();
        $widget->register();
	}

    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Работен плот',
                'menu_title' => 'Работен плот',
                'capability' => 'manage_options',
                'menu_slug' => 'wpcc_dashboard',
                'callback' => [$this->callbacks, 'orders'],
                'icon_url' => 'dashicons-visibility',
                'position' => 1
            ]
        ];
        return $this;
    }

    public function setSubPages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'wpcc_dashboard',
                'page_title' => 'Имейли',
                'menu_title' => 'Имейли',
                'capability' => 'manage_options',
                'menu_slug' => 'wpcc_emails',
                'callback' => [$this->callbacks, 'emails']
            ],
            [
                'parent_slug' => 'wpcc_dashboard',
                'page_title' => 'Настройки',
                'menu_title' => 'Настройки',
                'capability' => 'manage_options',
                'menu_slug' => 'wpcc_settings',
                'callback' => [$this->callbacks, 'settings']
            ]
        ];
        return $this;
    }

    public function setSettings()
    {
        $args = [
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'price_per_squere_meter',
                'callback' => [$this->callbacks, 'wpccUpdatePricePerSquereMeter']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'door_price',
                'callback' => [$this->callbacks, 'wpccUpdateDoorPrice']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'door_width',
                'callback' => [$this->callbacks, 'wpccUpdateDoorWidth']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'door_height',
                'callback' => [$this->callbacks, 'wpccUpdateDoorHeight']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'stripe_public_key',
                'callback' => [$this->callbacks, 'wpccUpdateStripePublicKey']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'stripe_secret_key',
                'callback' => [$this->callbacks, 'wpccUpdateStripeSecretKey']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'paypal_client_id',
                'callback' => [$this->callbacks, 'wpccUpdatePaypalClientId']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'paypal_secret',
                'callback' => [$this->callbacks, 'wpccUpdatePaypalSecret']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_receiver',
                'callback' => [$this->callbacks, 'wpccUpdateMailReceiver']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_host',
                'callback' => [$this->callbacks, 'wpccUpdateMailHost']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_sender',
                'callback' => [$this->callbacks, 'wpccUpdateMailSender']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_username',
                'callback' => [$this->callbacks, 'wpccUpdateMailUsername']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_password',
                'callback' => [$this->callbacks, 'wpccUpdateMailPassword']
            ],
            [
                'option_group' => 'wpcc_options_group',
                'option_name' => 'mail_port',
                'callback' => [$this->callbacks, 'wpccUpdateMailPort']
            ]
        ];
        $this->settings->setSettings($args);
        return $this;
    }

    public function setSections()
    {
        $args = [
            [
                'id' => 'wpcc_admin_index',
                'title' => 'Настройки',
                'callback' => [$this->callbacks, 'wpccAdminSection'],
                'page' => 'wpcc_settings'
            ]
        ];
        $this->settings->setSections($args);
        return $this;
    }

    public function setFields()
    {
        $args = [
            [
                'id' => 'price_per_squere_meter',
                'title' => 'Цена за квадратен метър (в паундове)',
                'callback' => [$this->callbacks, 'wpccPricePerSquereMeter'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'price_per_squere_meter',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'door_price',
                'title' => 'Цена за стандартна врата (в паундове)',
                'callback' => [$this->callbacks, 'wpccDoorPrice'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'door_price',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'door_width',
                'title' => 'Стандартна широчина на врата (в сантиметри)',
                'callback' => [$this->callbacks, 'wpccDoorWidth'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'door_width',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'door_height',
                'title' => 'Стандартна височина на врата (в сантиметри)',
                'callback' => [$this->callbacks, 'wpccDoorHeight'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'door_height',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'stripe_public_key',
                'title' => 'Публичен ключ за Stripe',
                'callback' => [$this->callbacks, 'wpccStripePublicKey'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'stripe_public_key',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'stripe_secret_key',
                'title' => 'Таен ключ за Stripe',
                'callback' => [$this->callbacks, 'wpccStripeSecretKey'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'stripe_secret_key',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'paypal_client_id',
                'title' => 'PayPal client id',
                'callback' => [$this->callbacks, 'wpccPaypalClientId'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'paypal_client_id',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'paypal_secret',
                'title' => 'PayPal secret',
                'callback' => [$this->callbacks, 'wpccPaypalSecret'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'paypal_secret',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_receiver',
                'title' => 'E-mail за получаване на заявките',
                'callback' => [$this->callbacks, 'wpccMailReceiver'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_receiver',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_host',
                'title' => 'SMTP хост',
                'callback' => [$this->callbacks, 'wpccMailHost'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_host',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_sender',
                'title' => 'E-mail от който се изпращат писмата',
                'callback' => [$this->callbacks, 'wpccMailSender'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_sender',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_username',
                'title' => 'Име на изпращача',
                'callback' => [$this->callbacks, 'wpccMailUsername'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_username',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_password',
                'title' => 'E-mail парола',
                'callback' => [$this->callbacks, 'wpccMailPassword'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_password',
                    'class' => 'example-class'
                ]
            ],
            [
                'id' => 'mail_port',
                'title' => 'E-mail порт',
                'callback' => [$this->callbacks, 'wpccMailPort'],
                'page' => 'wpcc_settings',
                'section' => 'wpcc_admin_index',
                'args' => [
                    'label_for' => 'mail_port',
                    'class' => 'example-class'
                ]
            ]
        ];
        $this->settings->setFields($args);
        return $this;
    }
}