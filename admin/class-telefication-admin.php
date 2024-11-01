<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Generate setting pageDefines the plugin name, version, and enqueue the admin-specific stylesheet.
 *
 * @package    Telefication
 * @subpackage Telefication/admin
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $options Options of the plugin from database.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param  string  $plugin_name  The name of this plugin.
	 * @param  string  $version  The version of this plugin.
	 * @param  array  $options  Telefication options from WP database
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version, $options ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->options     = $options;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param  string  $hook  Hook name
	 *
	 * @since    1.0.0
	 *
	 */
	public function enqueue_styles( $hook ) {

		if ( $hook === 'settings_page_telefication-setting' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/telefication-admin.css', array(),
				$this->version, 'all' );
		}
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @param  string  $hook  Hook name
	 *
	 * @since    1.1.0
	 *
	 */
	public function enqueue_scripts( $hook ) {

		if ( $hook === 'settings_page_telefication-setting' ) {

			// Register the script
			wp_register_script( 'telefication-admin-js', plugin_dir_url( __FILE__ ) . 'js/telefication-admin.js',
				array( 'jquery' ), $this->version, true );

			// Localize the script with new data
			$translation_array = array(
				'error_occurred'     => __( 'An error occurred', 'telefication' ),
				'test_message'       => __( 'This is a test message from Telefication', 'telefication' ),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'bot_token_is_empty' => __( 'Your bot token is not set!', 'telefication' )
			);
			wp_localize_script( 'telefication-admin-js', 'telefication', $translation_array );

			// Enqueued script with localized data.
			wp_enqueue_script( 'telefication-admin-js' );
		}
	}

	/**
	 * Add link of Telefication setting page in plugins page.
	 *
	 * @since 1.0.0
	 */
	public function add_action_links( $links ) {

		$telefication_links = '<a href="' . admin_url( 'options-general.php?page=telefication-setting' ) . '">' .
		                      __( 'Settings', 'telefication' ) . '</a>';
		array_push( $links, $telefication_links );

		return $links;
	}

	/**
	 * Add Telefication setting page in admin area.
	 *
	 * @since 1.0.0
	 */
	public function add_telefication_page() {

		add_options_page(
			__( 'Telefication Settings', 'telefication' ),
			__( 'Telefication', 'telefication' ),
			'manage_options',
			'telefication-setting',
			array( $this, 'create_telefication_page' )
		);

	}

	/**
	 * Create Telefication setting page display.
	 *
	 * @since 1.0.0
	 */
	public function create_telefication_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/telefication-admin-display.php';

	}

	/**
	 * Add setting sections and fields to Telefication setting page.
	 *
	 * @since 1.0.0
	 */
	public function init_telefication_page() {

		register_setting(
			'telefication_option_group',
			'telefication',
			array( $this, 'sanitize_input' )
		);

		add_settings_section(
			'general_setting_section',
			__( 'General Setting', 'telefication' ),
			array( $this, 'general_setting_section_callback' ),
			'telefication-setting'
		);

		add_settings_field(
			'chat_id', // ID
			__( 'Telefication Chat ID', 'telefication' ),
			array( $this, 'chat_id_callback' ),
			'telefication-setting',
			'general_setting_section'
		);

		add_settings_field(
			'notify_for',
			__( 'Notify Me For:', 'telefication' ),
			array( $this, 'notify_for_callback' ),
			'telefication-setting',
			'general_setting_section'
		);


		// TELEFICATION OWN BOT SETTINGS
		add_settings_section(
			'own_bot_setting_section',
			__( 'My Own Bot Setting', 'telefication' ),
			array( $this, 'own_bot_setting_section_callback' ),
			'telefication-own-bot-setting'
		);

		add_settings_field(
			'bot_token', // ID
			__( 'Your Bot Token', 'telefication' ),
			array( $this, 'bot_token_callback' ),
			'telefication-own-bot-setting',
			'own_bot_setting_section'
		);

		add_settings_field(
			'bot_bypass', // ID
			__( 'Bypass URL (optional)', 'telefication' ),
			array( $this, 'bot_bypass_callback' ),
			'telefication-own-bot-setting',
			'own_bot_setting_section'
		);

		// TELEFICATION CHANNEL SETTINGS
		add_settings_section(
			'channel_setting_section',
			__( 'Channel Setting', 'telefication' ),
			array( $this, 'channel_setting_section_callback' ),
			'telefication-channel-setting'
		);

		add_settings_field(
			'send_to_channel_enable', // ID
			__( 'Enable Send To Channel', 'telefication' ),
			array( $this, 'telefication_send_to_channel_enable_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_username', // ID
			__( 'Your Channel Username', 'telefication' ),
			array( $this, 'telefication_channel_username_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_notification_template', // ID
			__( 'Message Template', 'telefication' ),
			array( $this, 'telefication_channel_notification_template_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_featured_image_enable', // ID
			__( 'Featured Image', 'telefication' ),
			array( $this, 'telefication_channel_featured_image_enable_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_post_type', // ID
			__( 'For These Post Types', 'telefication' ),
			array( $this, 'telefication_channel_post_type_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);
	}

	/**
	 * Sanitize user inputs.
	 *
	 * @param  array  $input  Inputs from setting form.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
	public function sanitize_input( $input ) {

		if ( isset( $input['chat_id'] ) ) {
			$input['chat_id'] = sanitize_text_field( $input['chat_id'] );
		}

		if ( isset( $input['match_emails'] ) ) {

			$new_emails = [];
			$emails     = explode( ',', $input['match_emails'] );
			foreach ( $emails as $email ) {
				$email = trim( $email );
				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$new_emails[] = $email;
				}
			}
			$input['match_emails'] = implode( ',', $new_emails );
		}

		if ( isset( $input['bot_token'] ) ) {
			$input['bot_token'] = sanitize_text_field( $input['bot_token'] );
		}

		if ( isset( $input['channel_notification_template'] ) ) {
			$input['channel_notification_template'] = urlencode( $input['channel_notification_template'] );
		}

		return $input;
	}

	/**
	 * General setting Section callback to print information.
	 *
	 * @since 1.0.0
	 */
	public function general_setting_section_callback() {

		printf( '<p>' . __( 'You can use <b>your own Telegram Bot</b> or join to %s at Telegram to receive notifications.',
				'telefication' ) . '</p>',
			'<a href="https://t.me/teleficationbot" target="_blank">@teleficationbot</a>' );

	}

	/**
	 * Generate chat_id field display
	 *
	 * @since 1.0.0
	 */
	public function chat_id_callback() {

		printf(
			'<input type="text" id="chat_id" name="telefication[chat_id]" value="%s" /> ' .
			'<a href="#" id="test_message" class="button">' . __( 'Send test message', 'telefication' ) . '</a>' .
			'<p class="description">' . __( 'Please enter your Telegram chat id. You can get it from @teleficationbot',
				'telefication' ) . '</p>',

			isset( $this->options['chat_id'] ) ? esc_attr( $this->options['chat_id'] ) : ''
		);

		$description = __( 'If you use your own bot, you cat get your chat id by pressing this button. ',
			'telefication' );
		$disable     = 'disable';

		if ( isset( $this->options['bot_token'] ) && ! empty( $this->options['bot_token'] ) ) {

			$disable     = '';
			$description = __( 'Start your bot or send a message to it, then press this button to get your ID',
				'telefication' );
		}

		echo "<div class='$disable'>";
		echo '<br><a href="#" id="get_chat_id" class="button">' . __( 'Get Your Chat ID From Your Own Bot',
				'telefication' ) . '</a>';
		echo '<p class="description">' . $description . '</p>';
		echo '</div>';
	}

	/**
	 * Generate send email body option checkbox field
	 *
	 * @since 1.2.0
	 */
	public function notify_for_callback() {


		if ( isset( $this->options['email_notification'] ) ) {
			$email_notification_checked = checked( 1, $this->options['email_notification'], false );
		}
		if ( isset( $this->options['send_email_body'] ) ) {
			$send_email_body_checked = checked( 1, $this->options['send_email_body'], false );
		}
		if ( isset( $this->options['display_recipient_email'] ) ) {
			$display_recipient_email_checked = checked( 1, $this->options['display_recipient_email'], false );
		}
		if ( isset( $this->options['is_woocommerce_only'] ) ) {
			$is_woocommerce_only_checked = checked( 1, $this->options['is_woocommerce_only'], false );
		}
		if ( isset( $this->options['include_billing_info'] ) ) {
			$include_billing_info_checked = checked( 1, $this->options['include_billing_info'], false );
		}
		if ( isset( $this->options['include_items_detail'] ) ) {
			$include_items_detail_checked = checked( 1, $this->options['include_items_detail'], false );
		}
		if ( isset( $this->options['notify_on-hold_order'] ) ) {
			$notify_onhold_order_checked = checked( 1, $this->options['notify_on-hold_order'], false );
		}
		if ( isset( $this->options['notify_failed_order'] ) ) {
			$notify_failed_order_checked = checked( 1, $this->options['notify_failed_order'], false );
		}
		if ( isset( $this->options['notify_processing_order'] ) ) {
			$notify_processing_order_checked = checked( 1, $this->options['notify_processing_order'], false );
		}
		if ( isset( $this->options['notify_pending_order'] ) ) {
			$notify_pending_order_checked = checked( 1, $this->options['notify_pending_order'], false );
		}
		if ( isset( $this->options['notify_completed_order'] ) ) {
			$notify_completed_order_checked = checked( 1, $this->options['notify_completed_order'], false );
		}
		if ( isset( $this->options['notify_canceled_order'] ) ) {
			$notify_canceled_order_checked = checked( 1, $this->options['notify_canceled_order'], false );
		}
		if ( isset( $this->options['notify_refunded_order'] ) ) {
			$notify_refunded_order_checked = checked( 1, $this->options['notify_refunded_order'], false );
		}
		if ( isset( $this->options['include_shipping_info'] ) ) {
			$include_shipping_info_checked = checked( 1, $this->options['include_shipping_info'], false );
		}
		if ( isset( $this->options['new_comment_notification'] ) ) {
			$new_comment_notification_checked = checked( 1, $this->options['new_comment_notification'], false );
		}
		if ( isset( $this->options['new_post_notification'] ) ) {
			$new_post_notification_checked = checked( 1, $this->options['new_post_notification'], false );
		}
		if ( isset( $this->options['new_user_notification'] ) ) {
			$new_user_notification_checked = checked( 1, $this->options['new_user_notification'], false );
		}
		/*
		 * Notify for emails
		 */
		printf(
			'<div class="field-set"><input class="has-sub" type="checkbox" id="email_notification" name="telefication[email_notification]" value="1" %s/>' .
			'<label for="email_notification"><b>' . __( 'E-mails:',
				'telefication' ) . '</b> ' . __( 'notify me for WP emails', 'telefication' ) . '</label><br> ',
			isset( $email_notification_checked ) ? $email_notification_checked : ''
		);

		//Send email body?
		printf(
			'<div style="display:%s;" class="setting-fields-group"><input type="checkbox" id="send_email_body" name="telefication[send_email_body]" value="1" %s/>' .
			'<label for="send_email_body"><b>' . __( 'Send Email Body:',
				'telefication' ) . '</b> ' . __( 'If enabled, you will receive email body too.',
				'telefication' ) . '</label><br>',
			isset( $email_notification_checked ) ? 'block' : 'none',
			isset( $send_email_body_checked ) ? $send_email_body_checked : ''
		);

		//add recipient email to notification?
		printf(
			'<input type="checkbox" id="display_recipient_email" name="telefication[display_recipient_email]" value="1" %s/>' .
			'<label for="display_recipient_email"><b>' . __( 'Display Recipient Email:',
				'telefication' ) . '</b> ' . __( 'If enabled, the recipient email will be added to notifications.',
				'telefication' ) . '</label><br>',
			isset( $display_recipient_email_checked ) ? $display_recipient_email_checked : ''
		);

		//Filter recipients
		printf(
			'<br><b>' . __( 'Email(s):',
				'telefication' ) . '</b><br><input type="text" id="match_emails" name="telefication[match_emails]" value="%s" /> ' .
			'<p class="description">' . __( 'Notify me only of the emails that are sent to this list. (Comma separated.) <br> Leave it empty if you want to get all notifications.',
				'telefication' ) . '</p></div></div>',

			isset( $this->options['match_emails'] ) ? esc_attr( $this->options['match_emails'] ) : ''
		);
		// END Of Field Set


		// is woocommerce active
		if ( ! defined( 'WC_VERSION' ) ) {
			$woocommerce_is_active = '<p>' . __( '⚠ Woocommerce is not active!', 'telefication' ) . '</p>';
		}
		//Notify for new orders
		printf(
			'<div class="field-set"><input class="has-sub %s" type="checkbox" id="is_woocommerce_only" name="telefication[is_woocommerce_only]" value="1" %s/>' .
			'<label class="%s" for="is_woocommerce_only">' . __( '<b>Orders:</b> Enable this to get notified for new and updated woocommerce orders.',
				'telefication' ) . '</label><br>',
			isset( $woocommerce_is_active ) ? 'disable' : '',
			isset( $is_woocommerce_only_checked ) ? $is_woocommerce_only_checked : '',
			isset( $woocommerce_is_active ) ? 'disable' : ''
		);

		//Include items info
		printf(
			'<div style="display:%s;" class="setting-fields-group"><input type="checkbox" id="include_items_detail" name="telefication[include_items_detail]" value="1" %s/>' .
			'<label for="include_items_detail"><b>' . __( 'Include Items', 'telefication' ) . '</b></label><br>',
			isset( $is_woocommerce_only_checked ) ? 'block' : 'none',
			isset( $include_items_detail_checked ) ? $include_items_detail_checked : ''
		);

		//Include shipping info
		printf(
			'<input type="checkbox" id="include_shipping_info" name="telefication[include_shipping_info]" value="1" %s/>' .
			'<label for="include_shipping_info"><b>' . __( 'Include Shipping Info', 'telefication' ) . '</b></label><br>',
			isset( $include_shipping_info_checked ) ? $include_shipping_info_checked : ''
		);

		//include billing info
		printf(
			'<input type="checkbox" id="include_billing_info" name="telefication[include_billing_info]" value="1" %s/>' .
			'<label for="include_billing_info"><b>' . __( 'Include Billing Info', 'telefication' ) . '</b></label><br><hr>' .
			'<b>' . __( 'Notify if order is:', 'telefication' ) . '</b>  (<a href="https://docs.woocommerce.com/document/managing-orders/#section-1" target="_blank" rel="noreferrer noopener" >'.__('Order Statuses', 'telefication').'</a>)<br><br>',
			isset( $include_billing_info_checked ) ? $include_billing_info_checked : ''
		);

		//notify on-hold order
		printf(
			'<input type="checkbox" id="notify_on-hold_order" name="telefication[notify_on-hold_order]" value="1" %s/>' .
			'<label for="notify_on-hold_order"><b>' . __( 'On Hold', 'telefication' ) . '</b></label><br>',
			isset( $notify_onhold_order_checked ) ? $notify_onhold_order_checked : ''
		);

		//notify processing order
		printf(
			'<input type="checkbox" id="notify_processing_order" name="telefication[notify_processing_order]" value="1" %s/>' .
			'<label for="notify_processing_order"><b>' . __( 'Processing', 'telefication' ) . '</b></label><br>',
			isset( $notify_processing_order_checked ) ? $notify_processing_order_checked : ''
		);

		//notify pending order
		printf(
			'<input type="checkbox" id="notify_pending_order" name="telefication[notify_pending_order]" value="1" %s/>' .
			'<label for="notify_pending_order"><b>' . __( 'Pending', 'telefication' ) . '</b></label><br>',
			isset( $notify_pending_order_checked ) ? $notify_pending_order_checked : ''
		);

		//notify Completed order
		printf(
			'<input type="checkbox" id="notify_completed_order" name="telefication[notify_completed_order]" value="1" %s/>' .
			'<label for="notify_completed_order"><b>' . __( 'Completed', 'telefication' ) . '</b></label><br>',
			isset( $notify_completed_order_checked ) ? $notify_completed_order_checked : ''
		);

		//notify Canceled order
		printf(
			'<input type="checkbox" id="notify_canceled_order" name="telefication[notify_canceled_order]" value="1" %s/>' .
			'<label for="notify_canceled_order"><b>' . __( 'Canceled', 'telefication' ) . '</b></label><br>',
			isset( $notify_canceled_order_checked ) ? $notify_canceled_order_checked : ''
		);

		//notify Refunded order
		printf(
			'<input type="checkbox" id="notify_refunded_order" name="telefication[notify_refunded_order]" value="1" %s/>' .
			'<label for="notify_refunded_order"><b>' . __( 'Refunded', 'telefication' ) . '</b></label><br>',
			isset( $notify_refunded_order_checked ) ? $notify_refunded_order_checked : ''
		);

		//notify failed order
		printf(
			'<input type="checkbox" id="notify_failed_order" name="telefication[notify_failed_order]" value="1" %s/>' .
			'<label for="notify_failed_order"><b>' . __( 'Failed', 'telefication' ) . '</b></label></div></div>',
			isset( $notify_failed_order_checked ) ? $notify_failed_order_checked : ''
		);

		// END Of Field Set

		// Notify for new comments
		printf(
			'<input type="checkbox" id="new_comment_notification" name="telefication[new_comment_notification]" value="1" %s/>' .
			'<label for="new_comment_notification"><b>' . __( 'New Comment:',
				'telefication' ) . '</b> ' . __( 'Enable this to get notified for new comments',
				'telefication' ) . '</label><br> ',
			isset( $new_comment_notification_checked ) ? $new_comment_notification_checked : ''
		);

		// Notify for new posts
		printf(
			'<input type="checkbox" id="new_post_notification" name="telefication[new_post_notification]" value="1" %s/>' .
			'<label for="new_post_notification"><b>' . __( 'New Post:',
				'telefication' ) . '</b> ' . __( 'Enable this to get notified for new post',
				'telefication' ) . '</label><br> ',
			isset( $new_post_notification_checked ) ? $new_post_notification_checked : ''
		);

		// Notify for new posts
		printf(
			'<input type="checkbox" id="new_user_notification" name="telefication[new_user_notification]" value="1" %s/>' .
			'<label for="new_user_notification"><b>' . __( 'New User:',
				'telefication' ) . '</b> ' . __( 'Enable this to get notified for new user registration',
				'telefication' ) . '</label><br> ',
			isset( $new_user_notification_checked ) ? $new_user_notification_checked : ''
		);

	}


	// Own Bot Setting Page

	/**
	 * Own bot setting Section callback to print information.
	 *
	 * @since 1.3.0
	 */
	public function own_bot_setting_section_callback() {

		echo "<p>" . __( 'If you insert your own bot token, Telefication will send notifications to your bot directly!',
				'telefication' ) . "<br>";
	}

	/**
	 * Generate bot_token field display
	 *
	 * @since 1.3.0
	 */
	public function bot_token_callback() {

		printf(
			'<input type="text" id="bot_token" name="telefication[bot_token]" value="%s" /> ' .
			'<p class="description">' . __( 'Please enter your bot token .', 'telefication' ) . '</p>',

			isset( $this->options['bot_token'] ) ? esc_attr( $this->options['bot_token'] ) : ''
		);

	}

	/**
	 * Generate bot_bypass field display
	 *
	 * @since 1.6.0
	 */
	public function bot_bypass_callback() {

		printf(
			'<input type="text" id="bot_bypass" name="telefication[bot_bypass]" value="%s" /> ' .
			'<p class="description">' . __( "e.g. https://example.com/path/bypass.php <br> If your server does not have access to <code>https://telegram.org</code>, <br> upload <code>bypass.php</code> on another host/server which that has access and enter its url here. You can find the <code>bypass.php</code> file from here:", 'telefication' ) . ' https://t.me/telefication/5 </p>',

			isset( $this->options['bot_bypass'] ) ? esc_attr( $this->options['bot_bypass'] ) : ''
		);
	}

	// Channel Setting Page


	/**
	 * Section description
	 *
	 * @since 1.5.0
	 */
	public function channel_setting_section_callback() {

		echo "<p>" . __( 'Sending new posts to your channel! (you should add your own bot as an administrator of your channel with "Post Messages" permission)',
				'telefication' ) . "<br>";
	}

	/**
	 * Enable send to channel
	 *
	 * @since 1.5.0
	 */
	public function telefication_send_to_channel_enable_callback() {

		if ( isset( $this->options['send_to_channel_enable'] ) ) {
			$send_to_channel_enable_checked = checked( 1, $this->options['send_to_channel_enable'], false );
		}

		printf(
			'<input class="has-sub" type="checkbox" id="send_to_channel_enable" name="telefication[send_to_channel_enable]" value="1" %s/>' .
			'<label for="send_to_channel_enable">' . __( 'enable to send new posts to your channel',
				'telefication' ) . '</label> ',
			isset( $send_to_channel_enable_checked ) ? $send_to_channel_enable_checked : ''
		);


	}

	/**
	 * Channel username
	 *
	 * @since 1.5.0
	 */
	public function telefication_channel_username_callback() {

		printf(
			'<input type="text" id="channel_username" class="half" name="telefication[channel_username]" value="%s" placeholder="@telefication"/> ' .
			'<p class="description">' . __( 'Please enter your channel username. (e.g. @telefication)',
				'telefication' ) . '</p>',

			isset( $this->options['channel_username'] ) ? esc_attr( $this->options['channel_username'] ) : ''
		);

	}

	/**
	 * Channel Post Template
	 *
	 * @since 1.5.0
	 */
	public function telefication_channel_notification_template_callback() {

		$template = "<b>{title}</b>, 🍕

{content},

{excerpt}, 

{post_link}, 

{post_category}

#new_{post_type}";

		// is woocommerce active
		if ( ! defined( 'WC_VERSION' ) ) {
			$woocommerce_is_active = __( '⚠ Woocommerce is not active!', 'telefication' );
		}

		printf(
			'<div class="telefication-half">' .
			'<b>' . __( 'Main Template', 'telefication' ) . '</b>: ' .
			'<textarea id="channel_notification_template" name="telefication[channel_notification_template]" >%s</textarea> ' .
			'</div>' .
			'<div class="telefication-half">' .
			'<b class="%s">' . __( 'Woocommerce Product Template', 'telefication' ) . ':</b> %s' .
			'<textarea class="%s" id="channel_woocommerce_template" name="telefication[channel_woocommerce_template]" >%s</textarea> ' .
			'</div>' .
			'<p class="description">' . __( 'Your channel post template .', 'telefication' ) . '<br>' .
			__( 'You can use these variables:',
				'telefication' ) . ' {title}, {content}, {excerpt}, {post_link}, {post_category}, {product_category}, {product_sale_price}, {product_price}' . '<br>' .
			__( 'And these tags:', 'telefication' ) . ' b, i, a, pre, code, u, del' . '</p>',

			isset( $this->options['channel_notification_template'] ) ? esc_attr( urldecode( $this->options['channel_notification_template'] ) ) : $template,
			isset( $woocommerce_is_active ) ? 'disable' : '',
			isset( $woocommerce_is_active ) ? $woocommerce_is_active : '',
			isset( $woocommerce_is_active ) ? 'disable' : '',
			isset( $this->options['channel_woocommerce_template'] ) ? esc_attr( urldecode( $this->options['channel_woocommerce_template'] ) ) : $template
		);


	}

	public function telefication_channel_featured_image_enable_callback() {

		if ( isset( $this->options['channel_featured_image_enable'] ) ) {
			$channel_featured_image_enable_checked = checked( 1, $this->options['channel_featured_image_enable'], false );
		}

		printf(
			'<input class="has-sub" type="checkbox" id="channel_featured_image_enable" name="telefication[channel_featured_image_enable]" value="1" %s/>' .
			'<label for="channel_featured_image_enable">' . __( 'If enabled, Posts will send as image to your channel (if you enable this, your notification template length should not be more than 200 characters)', 'telefication' ) . '</label> ',
			isset( $channel_featured_image_enable_checked ) ? $channel_featured_image_enable_checked : ''
		);

	}

	/**
	 * Select Post Type
	 *
	 * @since 1.5.0
	 */
	public function telefication_channel_post_type_callback() {

		$telefication_channel_post_type = [];

		if ( isset( $this->options['telefication_channel_post_type'] ) ) {
			$telefication_channel_post_type = $this->options['telefication_channel_post_type'];
		}


		foreach ( get_post_types( '', 'names' ) as $post_type ) {
			$checked = '';
			if ( array_key_exists( $post_type,
					$telefication_channel_post_type ) && $telefication_channel_post_type[ $post_type ] === "1" ) {
				$checked = 'checked';
			}

			printf( '<div class="one-third">
			<input class="has-sub" type="checkbox" name="telefication[telefication_channel_post_type][%s]" value="1" %s>%s</div>',
				$post_type, $checked, $post_type

			);
		}

	}

}
