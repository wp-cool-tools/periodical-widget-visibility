<?php

/**
 * Fired during plugin activation
 *
 * @link       http://stehle-internet.de/
 * @since      1.0.0
 *
 * @package    Periodical_Widget_Visibility
 * @subpackage Periodical_Widget_Visibility/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Periodical_Widget_Visibility
 * @subpackage Periodical_Widget_Visibility/includes
 * @author     Martin Stehle <shop@stehle-internet.de>
 */
class Periodical_Widget_Visibility_Activator {

	/**
	 * Set flag for activation message.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// store the flag into the db to trigger the display of a message after activation
		set_transient( 'periodical-widget-visibility', '1', 60 );
	}

}
