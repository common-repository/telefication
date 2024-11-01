=== Telefication ===
Contributors: arshen
Tags: telegram, wordpress, notification, woocommerce, email, order, channel, automation
Requires at least: 3.1.0
Tested up to: 5.8.1
Requires PHP: 5.6
Stable tag: 1.8.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get a notification on Telegram by your own bot or send new posts to telegram channel. Notification for emails, new Woocommerce orders, new comments, new posts and new users.

== Description ==

## Telegram plugin for Wordpress!

Do you want receive notification from your wordpress in your telegram? or get a notification for new orders from Woocommerce? This plugin is for you.

Telefication send Wordpress emails and events as a notification to your Telegram through your own bot.

This plugin use [Telefication](https://telefication.ir) service to send notifications to Telegram. Since version 1.3.0 you can use your own Telegram bot to get notifications directly.

Feature List:

*   Use the bypass method for servers or hosts that don't have access to Telegram
*   Send new posts or products to Telegram channel. (customizable template for channels posts and woocommerce products)
*   Send posts to channel with their featured image.
*   Send woocommerce products to the channel with the "photo", "price" and "sale price" on publish.
*   You can use your own Telegram Bot to get notifications directly and send posts to channel.
*   Send notification to Telegram user or group.
*   Send email subject as a Telegram notification.
*   Send email body as a Telegram notification.
*   Display recipient email address in notifications.
*   Send Woocommerce detailed new and updated order notification to Telegram.
*   Notify for new comments.
*   Notify for new Posts.
*   Notify for new users.

###How to use Telefication - Youtube
https://www.youtube.com/watch?v=DYo6c-CSt_M&index=4&list=PLqz_gha_vREnuMdVuj8pOxNj6MJ39d2eX

== Installation ==

1. Upload the entire `Telefication` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Join [@teleficationbot](https://t.me/teleficationbot) and get id. (If you want more than one user get notified, you can add @teleficationbot to groups.)
4. Go to Telefication setting under Settings menu
5. Insert your id in Telefication ID field and save settings.

== Frequently Asked Questions ==

= Installation Instructions =

1. Upload the entire `Telefication` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

###If you want use your own bot:
3. Go to Telefication setting under Settings menu and go to "My Own Bot" tab.
4. Follow instructions there to create your own bot and insert your bot token then save changes .
5. Go to "General Setting" tab and press "Get your ID" button then save changes.

###If you want use Telefication service:
3. Join [@teleficationbot](https://t.me/teleficationbot) and get id. (If you want more than one user get notified, you can add @teleficationbot to groups.)
4. Go to Telefication setting under Settings menu
5. Insert your id in Telefication ID field and save changes.

= How do I get notifications? =

You get notifications through @teleficationbot which is a Telegram bot.
Since version 1.3.0 you can use your own bot to get notifications.

= Is there a limit to the number of notifications? =

Unlimited, If you use your own bot and, 50 Notifications per 24 hours, If you use Telefication service.

== Screenshots ==

1. Telefication general setting page.
2. Telefication Custom bot setting page
3. Telegram messages.

== Changelog ==

= 1.8.1 =
* [removed] bypass.php file removed due to security. you can faind it from here: https://t.me/telefication/5

= 1.8.0 =
* [fix] fixed displaying items issue for woocommerce orders.
* [add] you can specify when an order notification should send (order statuses).
* [add] you can include/exclude displaying items on the woocommerce order notification.

= 1.7.1 =
* [fix] fixed the repeated notifications problem for new orders.

= 1.7.0 =
* [fix] now bypass works on your own bot too.
* [fix] from now on, you can send posts to channel only with your own bot.
* [add] enable an option to send post to channel with its featured image.
* [add] send woocommerce products to the channel with the "photo", "price" and "sale price".
* [add] custom template for woocommerce new product that sends to channel .

= 1.6.1 =
* [fix] some fixes to bypass method

= 1.6.0 =
* [add] bypass method for servers that haven't access to https://telegram.org.
* [add] more detail on woocommerce new order notification (you can choose).
* [add] more clean notification for html emails.

= 1.5.2 =
* [fix] it doesn't send notification for spam comments anymore.
* [fix] typo fix.

= 1.5.1 =
* [fix] fix issue of saving message template to some databases.
* [fix] fix get chat id from your own bot
* [optimize] Now when you're using {post_link} in message template , it prints short-link of post.

= 1.5.0 =
* [Add] Now you can send new posts to Telegram channel (customizable template for channels posts).
* [fix] Replace site url with post's and comment's url in their notifications.

= 1.4.0 =
* [Add] Get your chat ID from Telefication setting page when you use your own bot.
* [Add] Notify for new comments
* [Add] Notify for new posts
* [Add] Notify for new users
* [Add] Now, you have an option to cancel notifications for emails.

= 1.3.0 =
* [Add] A new feature to use your own Telegram bot

= 1.2.1 =
* [Fix] sending not allowed html tag problem.

= 1.2.0 =
* [Add] option to send email body.
* [Add] option to display recipient email.

= 1.1.0 =
* [Add] Send test message button.
* [Add] Emails field to filter notifications by recipients email.

= 1.0.0 =
* The first release.

