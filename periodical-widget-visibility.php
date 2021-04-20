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
 * Version:           2.3.6
 * Requires at least: 3.5
 * Requires PHP:      5.2
 * Author:            Kybernetik Services
 * Author URI:        https://www.kybernetik-services.com/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=periodical-widget-visibility&utm_content=author
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       periodical-widget-visibility
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WVT_ROOT', plugin_dir_path( __FILE__ ) );
define( 'WVT_URL', plugin_dir_url( __FILE__ ) );

function pwv_autoloader( $class_name )
{
    if ( false !== strpos( $class_name, 'Periodical_Widget_Visibility' ) ) {
        include WVT_ROOT . 'includes/class-' . $class_name . '.php';
    }
}
spl_autoload_register('pwv_autoloader');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-periodical-widget-visibility-activator.php
 */
function activate_periodical_widget_visibility() {
	Periodical_Widget_Visibility_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-periodical-widget-visibility-deactivator.php
 */
function deactivate_periodical_widget_visibility() {
	Periodical_Widget_Visibility_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_periodical_widget_visibility' );
register_deactivation_hook( __FILE__, 'deactivate_periodical_widget_visibility' );

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
