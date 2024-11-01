<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/admin/partials
 */

$active_tab     = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general_options';
$general_option = '';
$own_bot_option = '';
$channel_option = '';

if ( $active_tab == 'own_bot_options' ) {
	$general_option = "hide";
	$channel_option = 'hide';

} elseif ( $active_tab == 'channel_options' ) {
	$own_bot_option = "hide";
	$general_option = "hide";
} else {
	$own_bot_option = "hide";
	$channel_option = 'hide';
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap" id="telefication">
    <h1><?php _e( 'Telefication Setting', 'telefication' ); ?> </h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=telefication-setting&tab=general_options" data-tab="telefication-general-setting"
           class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Setting',
				'telefication' ); ?></a>
        <a href="?page=telefication-setting&tab=own_bot_options" data-tab="telefication-own-bot-setting"
           class="nav-tab <?php echo $active_tab == 'own_bot_options' ? 'nav-tab-active' : ''; ?>">🤖 <?php _e( 'My Own Bot',
				'telefication' ); ?></a>
        <a href="?page=telefication-setting&tab=channel_options" data-tab="telefication-channel-setting"
           class="nav-tab <?php echo $active_tab == 'channel_options' ? 'nav-tab-active' : ''; ?>">📢 <?php _e( 'Send To Channel',
				'telefication' ); ?></a>

    </h2>
    <div class="telefication-wrapper">

        <form method="post" action="options.php">

			<?php settings_fields( 'telefication_option_group' ); ?>

            <div class="tab-container <?php echo $general_option ?>" id="telefication-general-setting">
				<?php do_settings_sections( 'telefication-setting' ); ?>
            </div>

            <div class="tab-container <?php echo $own_bot_option ?>" id="telefication-own-bot-setting">
				<?php do_settings_sections( 'telefication-own-bot-setting' ); ?>

                <h2><span class='dashicons dashicons-editor-help'></span> <?php _e( 'How To Use My Own Bot',
						'telefication' ) ?></h2>
                <p>
					<?php

					printf( __( '1. Go to %s and start it, send <code>/newbot</code> and follow instructions to create your own bot. at the end, you will receive your bot token. See %s for more information.',
						'telefication' ),
						'<a href="https://t.me/botfather" target="_blank">@botfather</a>',
						'<a href="https://core.telegram.org/bots#6-botfather" target="_blank">' . __( 'here',
							'telefication' ) . '</a>'
					);
					?>
                    <br>

					<?php _e( '2. Insert your bot token at input above and save changes. ', 'telefication' ); ?>
                    <br>
					<?php _e( '3. Go to the general options tab and test your bot by sending a test message ',
						'telefication' ); ?>
                    <br>
                    <br>

					<?php _e( "* Don't forget to start your own bot", 'telefication' ); ?>
                </p>
            </div>

            <div class="tab-container <?php echo $channel_option ?>" id="telefication-channel-setting">
				<?php do_settings_sections( 'telefication-channel-setting' ); ?>
            </div>

			<?php submit_button(); ?>

        </form>
    </div>
    <p class="telefication-footer-note">
        <span class="dashicons dashicons-star-filled"></span>
		<?php
		printf( __( 'If you like Telefication please leave us a %s rating. A huge thank you from us in advance!',
			'telefication' ),
			'<a href="https://wordpress.org/support/plugin/telefication/reviews/#new-post" target="_blank">★★★★★</a>' );
		?>

        <br>
        <span class="dashicons dashicons-lightbulb"></span>
		<?php _e( 'We will be happy if you let us know your suggestions.', 'telefication' ); ?>
    </p>
</div>