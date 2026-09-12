<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.kybernetik-services.com/
 * @since      1.2
 *
 * @package    Periodical_Widget_Visibility
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// if not allowed to delete plugins go to plugins page
if ( ! current_user_can( 'delete_plugins' ) ) {
	wp_die( esc_html__( 'Sorry, you are not allowed to delete plugins for this site.', 'periodical-widget-visibility' ) );
}

// clean up the database considering multisite installation
if ( is_multisite() ) {

	// Retrieve every site ID without the deprecated network API or result limit.
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $site_ids as $site_id ) {
		// switch to next blog
		switch_to_blog( $site_id );

		// remove widgets
		delete_option( 'widget_periodical-widget-visibility' );

		// Balance each switch before moving to the next site.
		restore_current_blog();
	}
} else {
	// remove widgets
	delete_option( 'widget_periodical-widget-visibility' );
}
