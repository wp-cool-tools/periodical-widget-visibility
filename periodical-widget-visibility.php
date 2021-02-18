<?php

/**
 * @link              https://www.kybernetik-services.com/
 * @since             1.0.0
 * @package           Periodical_Widget_Visibility
 *
 * @wordpress-plugin
 * Plugin Name:       Periodical Widget Visibility
 * Plugin URI:        https://wordpress.org/plugins/periodical-widget-visibility/
 * Description:       Control the periodical visibility of each widget based on weekdays within a yearly time period easily.
 * Version:           2.3.5
 * Requires at least: 3.5
 * Requires PHP:      5.2
 * Author:            Kybernetik Services
 * Author URI:        https://www.kybernetik-services.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       periodical-widget-visibility
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-periodical-widget-visibility-activator.php
 */
function activate_periodical_widget_visibility() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-periodical-widget-visibility-activator.php';
	Periodical_Widget_Visibility_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-periodical-widget-visibility-deactivator.php
 */
function deactivate_periodical_widget_visibility() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-periodical-widget-visibility-deactivator.php';
	Periodical_Widget_Visibility_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_periodical_widget_visibility' );
register_deactivation_hook( __FILE__, 'deactivate_periodical_widget_visibility' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-periodical-widget-visibility.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_periodical_widget_visibility() {

	$plugin = new Periodical_Widget_Visibility();
	$plugin->run();

}
run_periodical_widget_visibility();
