<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   WpSimpleMailSender
 * @author    Enrique Chavez <noone@tmeister.net>
 * @license   GPL-2.0+
 * @link      http://tmeister.net
 * @copyright 2014
 *
 * @wordpress-plugin
 * Plugin Name:       WP Simple Mail Sender
 * Plugin URI:        https://wordpress.org/plugins/wp-simple-mail-sender
 * Description:       WP Simple Mail Sender is a very simple plugin to change the sender address and name in outgoing emails, you can use the Site Title and the Admin Email or custom values.
 * Version:           1.0.2
 * Author:            Enrique Chavez
 * Author URI:        http://tmeister.net
 * Text Domain:       wp-simple-mail-sender-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/tmeister/wp-simple-email-sender
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-simple-mail-sender.php' );


add_action( 'plugins_loaded', array( 'WpSimpleMailSender', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-simple-mail-sender-admin.php' );
	add_action( 'plugins_loaded', array( 'WpSimpleMailSenderAdmin', 'get_instance' ) );

}
