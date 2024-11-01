<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://telefication.ir/wordpress-plugin
 * @since             1.0.0
 * @package           Telefication
 *
 * @wordpress-plugin
 * Plugin Name:       Telefication
 * Plugin URI:        https://telefication.ir/wordpress-plugin
 * Description:       Send notifications via Telegram to your own Bot or @teleficationbot (a telegram bot). you can start it here <a href="https://t.me/teleficationbot">Telefication Bot</a>
 * Version:           1.8.1
 * Author:            Foad Tahmasebi
 * Author URI:        http://daskhat.ir/
 * Text Domain:       telefication
 * Domain Path:       /languages
 * License:           GPLv2 or later
 *
 * Telefication is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Telefication is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Telefication. If not, see Wordpress root.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'TELEFICATION_VERSION', '1.8.1' );

/**
 * Plugin basename.
 */
define( 'TELEFICATION_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_telefication() {

}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_telefication() {

}

register_activation_hook( __FILE__, 'activate_telefication' );
register_deactivation_hook( __FILE__, 'deactivate_telefication' );

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-telefication.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_telefication() {

	$plugin = new Telefication();
	$plugin->run();

}

run_telefication();

// Ajax Messages
if ( isset( $_REQUEST['action'] ) ) {

	// Since 1.3.0
	if ( $_REQUEST['action'] == 'telefication_test_message' ) {
		do_action( 'wp_ajax_send_telefication_test_message' );
	}

	// Since 1.4.0
	if ( $_REQUEST['action'] == 'telefication_get_chat_id' ) {
		do_action( 'wp_ajax_get_telefication_chat_id' );
	}
}


/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 *
 * @since 1.4.0
 *
 * @param $upgrader_object Array
 * @param $options Array
 */
function telefication_upgrade_completed( $upgrader_object, $options ) {
	// The path to our plugin's main file
	$our_plugin = TELEFICATION_BASENAME;

	// If an update has taken place and the updated type is plugins and the plugins element exists
	if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {

		// Iterate through the plugins being updated and check if ours is there
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == $our_plugin ) {
				// Set a transient to record that our plugin has just been updated
				set_transient( 'telefication_updated', 1 );
			}
		}
	}
}

/**
 * Display notice to review settings
 *
 * @since 1.4.0
 */
function telefication_display_update_notice() {
	// Check the transient to see if we've just updated the plugin
	if ( get_transient( 'telefication_updated' ) ) {
		printf( '<div class="notice notice-success"><p>' . __( 'Thanks for updating <b>Telefication</b>. Please review the <a href="%s">Setting</a>.',
				'telefication' ) . '</p></div>',
			admin_url( 'options-general.php?page=telefication-setting' ) );
		delete_transient( 'telefication_updated' );
	}
}


add_action( 'upgrader_process_complete', 'telefication_upgrade_completed', 10, 2 );
add_action( 'admin_notices', 'telefication_display_update_notice' );
