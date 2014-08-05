<?php
/**
 * WP Simple Mail Sender
 *
 * @package   WpSimpleMailSender
 * @author    Enrique Chavez <noone@tmeister.net>
 * @license   GPL-2.0+
 * @link      http://tmeister.net
 * @copyright 2014
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-wp-simple-mail-sender-admin.php`
 *
 * @package WpSimpleMailSender
 * @author  Enrique Chavez <noone@tmeister.net>
 */
class WpSimpleMailSender {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-simple-mail-sender';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'wp_mail_from', array($this, 'change_mail_from') );
		add_filter( 'wp_mail_from_name', array($this, 'change_mail_name') );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Change the mail from value
	 *
	 * @since 1.0.0
	 */
	public function change_mail_from($email){
		$settings = (array) get_option( 'wses-main-options' );

		if( isset( $settings['global'] )  && $settings['global'] == 1) {
			return get_option('admin_email');
		}

		if( isset( $settings['from-address'] ) && strlen($settings['from-address'])) {
			return $settings['from-address'];
		}

		return $email;

	}

	/**
	 * Change the mail from value
	 *
	 * @since 1.0.0
	 */
	public function change_mail_name($name){
		$settings = (array) get_option( 'wses-main-options' );

		if( isset( $settings['global'] )  && $settings['global'] == 1) {
			return get_option('blogname');
		}

		if( isset( $settings['from-name'] ) && strlen($settings['from-name']) ){
			return $settings['from-name'];
		}

		return $name;

	}
}
