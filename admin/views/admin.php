<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WpSimpleMailSender
 * @author    Enrique Chavez <noone@tmeister.net>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="POST">
        <?php settings_fields( 'wp-simple-email-group' ); ?>
        <?php do_settings_sections( 'wp-simple-email-sender' ); ?>
        <?php submit_button(); ?>
    </form>

</div>
