<?php

/**
 * The file that defines the core plugin class
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Telefication
 * @subpackage Telefication/includes
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Telefication_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $options Options of the plugin from database.
	 */
	protected $options;

	/**
	 * Post id to sent to channel
	 *
	 * @since 1.7.0
	 * @access   protected
	 * @var
	 */
	protected $post_id;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'TELEFICATION_VERSION' ) ) {
			$this->version = TELEFICATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'telefication';
		$this->options     = get_option( 'telefication' );

		$this->load_dependencies();
		$this->set_locale();
		$this->core_functionality();

		if ( is_admin() ) {
			$this->define_admin_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Telefication_Loader. Orchestrates the hooks of the plugin.
	 * - Telefication_i18n. Defines internationalization functionality.
	 * - Telefication_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Html2TextException.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Html2Text.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-i18n.php';

		/**
		 * The class responsible for communicate with Telefication server.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-service.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		if ( is_admin() ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-telefication-admin.php';
		}

		$this->loader = new Telefication_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Telefication_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Telefication_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Add hooks according to the settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function core_functionality() {

		// Notify for email - Filter
		if ( isset( $this->options['email_notification'] ) && '1' == $this->options['email_notification'] ) {
			$this->loader->add_filter( 'wp_mail', $this, 'telefication_filter_wp_mail' );
		}

		// New order action
		if ( isset( $this->options['is_woocommerce_only'] ) && '1' == $this->options['is_woocommerce_only'] ) {
			// if woocommerce is active
			$this->loader->add_action( 'woocommerce_order_status_changed', $this, 'telefication_action_woocommerce_order_status_changed', 10, 4 );
		}

		// New comment action
		if ( isset( $this->options['new_comment_notification'] ) && '1' == $this->options['new_comment_notification'] ) {

			$this->loader->add_action( 'wp_insert_comment', $this, 'telefication_action_wp_insert_comment' );
		}

		// New post action
		if ( isset( $this->options['new_post_notification'] ) && '1' == $this->options['new_post_notification'] ) {

			$this->loader->add_action( 'transition_post_status', $this, 'telefication_action_publish_post', 10, 3 );
		}

		// New post action for Send to channel
		if ( isset( $this->options['send_to_channel_enable'] ) && '1' == $this->options['send_to_channel_enable'] &&
		     isset( $this->options['bot_token'] ) && ! empty( $this->options['bot_token'] ) ) {

			$this->loader->add_action( 'transition_post_status', $this, 'telefication_action_publish_post_send_to_channel', 10, 3 );
		}

		// New user action
		if ( isset( $this->options['new_user_notification'] ) && '1' == $this->options['new_user_notification'] ) {

			$this->loader->add_action( 'user_register', $this, 'telefication_action_user_register', 10 );
		}
	}

	/**
	 * New User Registration Action
	 *
	 * @param $user_id
	 *
	 * @since 1.4.0
	 *
	 */
	public function telefication_action_user_register( $user_id ) {

		//notification body
		$message = get_bloginfo( 'name' ) . ":\n\n";
		$message .= __( 'New User Registered.', 'telefication' ) . "\n\n";

		$message .= site_url();

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}
	}

	/**
	 * New Post Publish Action
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 *
	 * @since 1.4.0
	 *
	 */
	public function telefication_action_publish_post( $new_status, $old_status, $post ) {

		// if new post published
		if ( 'publish' === $new_status && 'publish' !== $old_status ) {

			// is post type is post, for notification
			if ( $post->post_type === 'post' ) {

				//notification body
				$message = get_bloginfo( 'name' ) . ":\n\n";
				$message .= __( 'New Post: ', 'telefication' ) . "\n-----\n\n";
				$message .= $post->post_title . "\n\n";

				$message .= __( 'Post URL: ', 'telefication' ) . get_permalink( $post->ID );

				$telefication_service = new Telefication_Service( $this->options );

				if ( $telefication_service->create_url( $message ) ) {
					$telefication_service->send_notification();
				}
			}

		}

	}

	/**
	 * New Post Publish Action For Sending To Channel
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 *
	 * @since 1.7.0
	 *
	 */

	public function telefication_action_publish_post_send_to_channel( $new_status, $old_status, $post ) {
		// if new post published
		if ( 'publish' === $new_status && 'publish' !== $old_status ) {
			if ( isset( $this->options['channel_username'] ) && ! empty( $this->options['channel_username'] ) ) {
				if ( isset( $this->options['telefication_channel_post_type'] ) ) {

					$telefication_channel_post_type = $this->options['telefication_channel_post_type'];
					$post_type                      = $post->post_type;

					// if post type is allowed
					if ( array_key_exists( $post_type, $telefication_channel_post_type ) && $telefication_channel_post_type[ $post_type ] === "1" ) {
						$this->post_id = $post->ID;
						//add it to shutdown to send after saving meta data
						add_action( 'shutdown', array( $this, 'sendToChannel' ), 10 );
					}
				}
			}
		}
	}

	/**
	 * Send Post To Channel
	 *
	 * @since 1.7.0
	 */
	public function sendToChannel() {

		$post                 = get_post( $this->post_id );
		$channel_post         = $this->telefication_create_channel_post( $post );
		$telefication_service = new Telefication_Service( $this->options );
		$thumbnail_url        = get_the_post_thumbnail_url( $post );

		if ( isset( $this->options['channel_featured_image_enable'] ) &&
		     $this->options['channel_featured_image_enable'] == '1' &&
		     ! empty( $thumbnail_url )
		) {
			if ( $telefication_service->sendPhoto( $channel_post,
				$thumbnail_url,
				$this->options['channel_username'] ) ) {

				$telefication_service->send_notification();
			}

		} else {
			if ( $telefication_service->create_url( $channel_post,
				$this->options['channel_username'] ) ) {
				$telefication_service->send_notification();
			}
		}
	}

	/**
	 * Make channel post by template
	 *
	 * @param $post
	 *
	 * @return mixed|string
	 * @since 1.5.0
	 *
	 */
	public function telefication_create_channel_post( $post ) {

		$dictionary = array(
			'{title}'              => strip_tags( $post->post_title, '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{content}'            => strip_tags( $post->post_content, '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{excerpt}'            => strip_tags( $post->post_excerpt, '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{post_link}'          => strip_tags( wp_get_shortlink( $post->ID ), '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{post_category}'      => strip_tags( get_the_category( $post->ID )[0]->name, '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{post_type}'          => strip_tags( $post->post_type, '<b><i><a><code><pre><u><ins><s><strike><del>' ),
			'{product_price}'      => '',
			'{product_sale_price}' => '',
			'{product_category}'   => '',
		);

		// product information
		if ( $post->post_type == 'product' ) {

			$product = wc_get_product( $post->ID );

			$dictionary['{product_price}']      = $product->get_regular_price();
			$dictionary['{product_sale_price}'] = $product->get_sale_price();
			$dictionary['{product_category}']   = strip_tags( get_the_terms( $post->ID, 'product_cat' )[0]->name, '<b><i><a><code><pre><u><ins><s><strike><del>' );
		}

		$message = '';
		if ( $post->post_type != 'product' ) {
			if ( isset( $this->options['channel_notification_template'] ) ) {
				$message = str_replace( array_keys( $dictionary ), $dictionary,
					urldecode( $this->options['channel_notification_template'] ) );
			}
		} else {
			if ( isset( $this->options['channel_woocommerce_template'] ) ) {
				$message = str_replace( array_keys( $dictionary ), $dictionary,
					urldecode( $this->options['channel_woocommerce_template'] ) );
			}
		}

		return $message;
	}

	/**
	 * New Comment Action
	 *
	 * @param $comment_ID
	 *
	 * @return bool
	 * @since 1.4.0
	 *
	 */
	public function telefication_action_wp_insert_comment( $comment_ID ) {

		$status = wp_get_comment_status( $comment_ID );

		if ( $status == 'spam' ) {
			return false;
		}

		$comment_text = get_comment_text( $comment_ID );

		//notification body
		$message = get_bloginfo( 'name' ) . ":\n\n";
		$message .= __( 'New Comment: ', 'telefication' ) . "\n-----\n\n";
		$message .= $comment_text . "\n\n";

		$message .= __( 'Comment Link: ', 'telefication' ) . get_comment_link( $comment_ID );

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}
	}

	/**
	 * Add filter callback function for 'wp_email'
	 *
	 * Get email subject, generate notification body and send notification
	 *
	 * @param  array  $email_args
	 *
	 * @return array $email_args
	 * @throws Html2TextException
	 * @since    1.0.0
	 * @access   public
	 */
	public function telefication_filter_wp_mail( $email_args ) {

		//check for if notification is not allowed for this recipient.
		if ( isset( $this->options['match_emails'] ) && ! empty( $this->options['match_emails'] ) ) {
			$emails = explode( ',', $this->options['match_emails'] );

			if ( ! in_array( $email_args['to'], $emails ) ) {
				return $email_args;
			}
		}

		// check for adding recipient email
		$to = ( isset( $this->options['display_recipient_email'] ) && '1' == $this->options['display_recipient_email'] ) ? $email_args['to'] : '';

		$message = get_bloginfo( 'name' ) . ": " . $to . "\n\n";
		$message .= $email_args['subject'] . "\n\n";

		// check for adding email body option
		if ( isset( $this->options['send_email_body'] ) && '1' == $this->options['send_email_body'] ) {
			$message .= Html2Text::convert( $email_args['message'], true ) . "\n\n";
		}

		$message .= site_url();

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}

		return $email_args;
	}

	/**
	 * Add action callback function for 'woocommerce_thankyou'
	 *
	 * Get order data, generate notification body and send notification for woocommerce new order
	 *
	 * @param $order_id
	 * @param $status_from
	 * @param $status_to
	 * @param $order
	 *
	 * @return bool
	 * @since    1.0.0
	 * @access   public
	 */

	public function telefication_action_woocommerce_order_status_changed( $order_id, $status_from, $status_to, $order ) {

		if ( ! isset( $this->options[ 'notify_' . $status_to . '_order' ] ) ||
		     '1' != $this->options[ 'notify_' . $status_to . '_order' ] ) {
			return true;
		}

		// Get an instance of the WC_Order object
		$order_data = $order->get_data();

		$shipping_info = '';
		if ( isset( $this->options['include_shipping_info'] ) && $this->options['include_shipping_info'] == '1' ) {

			$shipping_info .= __( 'Shipping Info:', 'telefication' ) . " \n-----\n";

			$shipping_info .= __( 'Name: ', 'telefication' ) . $order_data['shipping']['first_name'] . " " . $order_data['shipping']['last_name'] . "\n";
			$shipping_info .= __( 'Country: ', 'telefication' ) . $order_data['shipping']['country'] . "\n";
			$shipping_info .= __( 'City: ', 'telefication' ) . $order_data['shipping']['city'] . "\n";

			$shipping_info .= __( 'Postcode: ', 'telefication' ) . $order_data['shipping']['postcode'] . "\n";
			$shipping_info .= __( 'State: ', 'telefication' ) . $order_data['shipping']['state'] . "\n";

			$shipping_info .= __( 'Address 1: ', 'telefication' ) . $order_data['shipping']['address_1'] . "\n";
			$shipping_info .= __( 'Address 2: ', 'telefication' ) . $order_data['shipping']['address_2'] . "\n\n";
		}

		$billing_info = '';
		if ( isset( $this->options['include_billing_info'] ) && $this->options['include_billing_info'] == '1' ) {

			$billing_info .= __( 'Billing Info:', 'telefication' ) . " \n-----\n";

			$billing_info .= __( 'Name: ', 'telefication' ) . $order_data['billing']['first_name'] . " " . $order_data['billing']['last_name'] . "\n";

			$billing_info .= __( 'Email: ', 'telefication' ) . $order_data['billing']['email'] . "\n";
			$billing_info .= __( 'Phone: ', 'telefication' ) . $order_data['billing']['phone'] . "\n";

			$billing_info .= __( 'Country: ', 'telefication' ) . $order_data['billing']['country'] . "\n";
			$billing_info .= __( 'City: ', 'telefication' ) . $order_data['billing']['city'] . "\n";

			$billing_info .= __( 'Postcode: ', 'telefication' ) . $order_data['billing']['postcode'] . "\n";
			$billing_info .= __( 'State: ', 'telefication' ) . $order_data['billing']['state'] . "\n";

			$billing_info .= __( 'Address 1: ', 'telefication' ) . $order_data['billing']['address_1'] . "\n";
			$billing_info .= __( 'Address 2: ', 'telefication' ) . $order_data['billing']['address_2'] . "\n\n";
		}


		$items = ""; // items_detail
		if ( isset( $this->options['include_items_detail'] ) && $this->options['include_items_detail'] == '1' ) {

			foreach ( $order->get_items() as $item_key => $item_values ) {

				$item_data = $item_values->get_data();

				$product_name = $item_data['name'];
				$quantity     = $item_data['quantity'];

				$items .= "$product_name * $quantity \n";
			}
			$items .= "\n";
		}

		//notification body
		$message = get_bloginfo( 'name' ) . ":\n\n";
		$message .= __( 'New order: ', 'telefication' ) . ' [' . $status_to . "] \n-----\n\n";
		$message .= $items;
		$message .= __( 'Total: ', 'telefication' ) . $order_data['total'] . "\n\n";

		$message .= $shipping_info;
		$message .= $billing_info;

		$message .= site_url();

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Telefication_Admin( $this->plugin_name, $this->version, $this->options );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_telefication_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'init_telefication_page' );

		// Since 1.3.0
		$this->loader->add_action( 'wp_ajax_send_telefication_test_message', $this, 'send_test_message' );
		$this->loader->add_action( 'wp_ajax_get_telefication_chat_id', $this, 'get_chat_id' );

		// add link of Telefication setting page in plugins page
		$this->loader->add_filter( 'plugin_action_links_' . TELEFICATION_BASENAME, $plugin_admin, 'add_action_links' );
	}

	/**
	 * Ajax sent test message
	 *
	 * @since    1.3.0
	 */
	public function send_test_message() {

		$message = ( isset( $_REQUEST['message'] ) ) ? $_REQUEST['message'] : __( 'This Is Test', 'telefication' );

		if ( ! isset( $_REQUEST['chat_id'] ) || empty( $_REQUEST['chat_id'] ) ) {
			_e( 'Please enter ID', 'telefication' );
			die;
		}

		$telefication_service          = new Telefication_Service( $this->options );
		$telefication_service->chat_id = $_REQUEST['chat_id'];

		if ( $telefication_service->create_url( $message ) ) {
			echo $telefication_service->send_notification();
		}
		die;
	}

	/**
	 * Ajax get chat id
	 *
	 * @since    1.4.0
	 */
	public function get_chat_id() {

		if ( ! isset( $_REQUEST['bot_token'] ) || empty( $_REQUEST['bot_token'] ) ) {
			_e( 'Please enter bot token', 'telefication' );
			die;
		}

		$telefication_service                     = new Telefication_Service( $this->options );
		$telefication_service->telegram_bot_token = $_REQUEST['bot_token'];

		echo empty( $telefication_service->get_chat_id() ) ? __( 'Please send something to your Bot (e.g. say hello :) ) so Telefication can get your id.',
			'telefication' ) : $telefication_service->get_chat_id();
		die;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

}
