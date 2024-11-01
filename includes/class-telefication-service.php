<?php

/**
 * Communicate with Telefication service.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/includes
 */

/**
 * Send Notification Functionality.
 *
 * Send a message through Telefication service to Telegram Bot.
 *
 * @since      1.0.0
 * @package    Telefication
 * @subpackage Telefication/includes
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication_Service {

	/**
	 * The chat_id of user in Telefication bot.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $chat_id The chat_id of user in Telefication bot.
	 */
	public $chat_id;

	/**
	 * Telefication or Telegram API url for sending notification.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $url Telefication api url.
	 */
	protected $url;

	/**
	 * URL parameters for sending to api.
	 *
	 * @since    1.3.0
	 * @access   protected
	 * @var      array $data parameters.
	 */
	protected $data;

	/**
	 * User own bot token
	 *
	 * @since    1.3.0
	 * @access   protected
	 * @var bool
	 */
	public $telegram_bot_token;


	/**
	 * Options of this plugin.
	 *
	 * @since    1.6.0
	 * @access   private
	 * @var      array $options Options of the plugin from database.
	 */
	protected $options;

	/**
	 * Initialize options.
	 *
	 * @param  array  $options  Telefication options from WP database
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $options ) {

		$this->chat_id = $options['chat_id'];

		// Since 1.3.0
		if ( isset( $options['bot_token'] ) && ! empty( $options['bot_token'] ) ) {
			$this->telegram_bot_token = $options['bot_token'];
		} else {
			$this->telegram_bot_token = false;
		}

		$this->options = get_option( 'telefication' );
	}

	/**
	 * Get chat id from user custom bot
	 *
	 * @return bool
	 * @since 1.4.0
	 *
	 */
	function get_chat_id() {

		$this->url  = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/getUpdates';
		$this->data = array(
			'offset' => '-1',
		);

		$url = $this->url . '?' . http_build_query( $this->data );

		// check if bypass is set
		if ( isset( $this->options['bot_bypass'] ) && ! empty( $this->options['bot_bypass'] ) ) {
			$url = $this->options['bot_bypass'] . '?request=' . str_rot13( urlencode( $url ) );
		}

		$ch           = curl_init();
		$option_array = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
		);
		curl_setopt_array( $ch, $option_array );
		$result = curl_exec( $ch );
		curl_close( $ch );

		$result = json_decode( $result, true );
		if ( 'true' == $result['ok'] ) {
			return $result['result'][0]['message']['from']['id'];
		} else {

			if ( isset( $result['description'] ) ) {
				return $result['description'];
			}

			return false;
		}

	}

	/**
	 * Create Telefication API url to send notification .
	 *
	 * @param  string  $message  Notification Message
	 *
	 * @param  null  $user
	 *
	 * @return bool
	 * @since    1.0.0
	 *
	 */
	public function create_url( $message = '', $user = null ) {


		if ( ! empty( $this->chat_id ) || $user != null ) {

			$user = ( $user != null ) ? $user : $this->chat_id;

			// send to user bot if bot-token is exist
			if ( $this->telegram_bot_token ) {

				$this->url  = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendMessage';
				$this->data = array(
					'parse_mode' => 'html',
					'chat_id'    => $user,
					'text'       => ( isset( $this->options['bot_bypass'] ) && ! empty( $this->options['bot_bypass'] ) ) ? urlencode( $message ) : $message
				);

				return true;

			} else {

				$this->url  = "https://telefication.ir/api/sendNotification";
				$this->data = array(
					'chat_id' => $user,
					'message' => ( isset( $this->options['bot_bypass'] ) && ! empty( $this->options['bot_bypass'] ) ) ? urlencode( $message ) : $message
				);

				return true;

			}
		}

		return false;
	}

	/**
	 * Send Photo
	 *
	 * @param  string  $message
	 * @param  string  $photo
	 * @param  null  $user
	 *
	 * @return bool
	 *
	 * @since 1.7.0
	 */
	public function sendPhoto( $message = '', $photo = '', $user = null ) {

		if ( ! empty( $this->chat_id ) || $user != null ) {

			$user = ( $user != null ) ? $user : $this->chat_id;

			// send to user bot if bot-token is exist
			if ( $this->telegram_bot_token ) {

				$this->url  = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendPhoto';
				$this->data = array(
					'chat_id'    => $user,
					'photo'      => $photo,
					'caption'    => ( isset( $this->options['bot_bypass'] ) && ! empty( $this->options['bot_bypass'] ) ) ? urlencode( $message ) : $message,
					'parse_mode' => 'html',
				);

				return true;
			}
		}

		return false;
	}

	/**
	 * Send notification.
	 *
	 * Call Telefication API url by curl.
	 *
	 * @return bool|mixed
	 * @since    1.0.0
	 *
	 */
	function send_notification() {

		if ( ! empty( $this->url ) ) {

			$url = $this->url . '?' . http_build_query( $this->data );

			// check if bypass is set
			if ( isset( $this->options['bot_bypass'] ) && ! empty( $this->options['bot_bypass'] ) ) {
				$url = $this->options['bot_bypass'] . '?request=' . str_rot13( urlencode( $url ) );
			}

			$ch           = curl_init();
			$option_array = array(
				CURLOPT_URL            => $url,
				CURLOPT_RETURNTRANSFER => true,
			);
			curl_setopt_array( $ch, $option_array );
			$result = curl_exec( $ch );
			curl_close( $ch );

			//if telegram bot token exist, we should parse response in jason mode
			if ( $this->telegram_bot_token ) {

				$result = json_decode( $result, true );
				if ( 'true' == $result['ok'] ) {
					return __( 'Test message sent successfully (To Your Bot)', 'telefication' );
				} else {

					if ( isset( $result['description'] ) ) {
						return $result['description'];
					}

					return false;
				}

			} else {

				if ( 'ok' === $result ) {
					return __( 'Test message sent successfully (To Telefication Bot)', 'telefication' );
				} else {
					return $result;
				}

			}
		}


		return false;
	}

}