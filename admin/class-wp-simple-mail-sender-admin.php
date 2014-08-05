<?php
/**
 * WP Simple Mail Sender
 *
 * @package   WpSimpleMailSenderAdmin
 * @author    Enrique Chavez <noone@tmeister.net>
 * @license   GPL-2.0+
 * @link      http://tmeister.net
 * @copyright 2014
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-wp-simple-mail-sender.php`
 *
 * @package WpSimpleMailSenderAdmin
 * @author  Enrique Chavez <noone@tmeister.net>
 */
class WpSimpleMailSenderAdmin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = WpSimpleMailSender::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Adding Plugin Settings

		add_action( 'admin_init', array( $this, 'add_wp_simple_email_settings' ) );
		add_filter( 'wp_mail_from', array($this, 'change_mail_from') );
		add_filter( 'wp_mail_from_name', array($this, 'change_mail_name') );


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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WpSimpleMailSender::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'WP Simple Email Sender Settings', $this->plugin_slug ),
			__( 'WP Single Email', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Add Plugin Settings
	 *
	 * @since 1.0.0
	 */
	public function add_wp_simple_email_settings() {
		register_setting( 'wp-simple-email-group', 'wses-main-options' );

    	add_settings_section( 'wses-global-options', '', array($this, 'general_settings_option'), 'wp-simple-email-sender' );

    	add_settings_field(
    		'wses-global-options',
    		__('General Settings', $this->plugin_slug),
    		array($this, 'general_options_field'),
    		'wp-simple-email-sender',
    		'wses-global-options'
    	);

    	add_settings_field(
    		'wses-from-name',
    		__('From Name', $this->plugin_slug),
    		array($this, 'from_option_field'),
    		'wp-simple-email-sender',
    		'wses-global-options'
    	);

    	add_settings_field(
    		'wses-from-address',
    		__('From Address', $this->plugin_slug),
    		array($this, 'address_option_field'),
    		'wp-simple-email-sender',
    		'wses-global-options'
    	);

	}

	/**
	 * Add Page description
	 *
	 * @since 1.0.0
	 */
	public function general_settings_option() {
		echo __('This is a very simple plugin to change the sender address and name when WordPress send an email.', $this->plugin_slug);
	}

	/**
	 * Add Name Option Field
	 *
	 * @since 1.0.0
	 */
	public function from_option_field(){
		$settings = (array) get_option( 'wses-main-options' );
		$from = ( isset($settings['from-name'])) ? esc_attr( $settings['from-name'] ) : false;
	    $html = '<input type="text" name="wses-main-options[from-name]" value="'.$from.'" />';
	    $html .= '<p class="description">'.__('Ex. John Doe.', $this->plugin_slug).'</p>';
	    echo $html;
	}

	/**
	 * Add Address Option Field
	 *
	 * @since 1.0.0
	 */
	public function address_option_field(){
		$settings = (array) get_option( 'wses-main-options' );
		$from = ( isset($settings['from-address'])) ? esc_attr( $settings['from-address'] ) : false;
	    $html = '<input type="text" name="wses-main-options[from-address]" value="'.$from.'" />';
	    $html .= '<p class="description">'.__('Ex. my@email.com', $this->plugin_slug).'</p>';
	    echo $html;
	}

	/**
	 * Add General Option Field
	 *
	 * @since 1.0.0
	 */
	public function general_options_field() {
		$settings = (array) get_option( 'wses-main-options' );

		$use_global = ( isset($settings['global'])) ? $settings['global'] : false;

		$label = __( 'Check this option to use the "Site Title" and the "E-mail Address" settings from the <a href="%s">General Options</a>.', $this->plugin_slug );
		$general_url = get_admin_url(null, "/options-general.php");

		$label = sprintf($label . ' Ex. <strong>%s &lt;%s&gt;</strong>', $general_url, get_option('blogname'), get_option('admin_email'));

    	$html = '<input type="checkbox" id="wses-global-optionse" name="wses-main-options[global]" value="1"' . checked( 1, $use_global, false ) . '/>';
    	$html .= '<label for="wses-global-optionse">'.$label.'</label>';
    	$html .= '<p class="description">'.__('If this option is selected the values in the fields below will be ignored.', $this->plugin_slug).'</p>';

    	echo $html;
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
