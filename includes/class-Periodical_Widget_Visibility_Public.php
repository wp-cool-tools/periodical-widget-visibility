<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.kybernetik-services.com/
 * @since      1.0.0
 *
 * @package    Periodical_Widget_Visibility
 * @subpackage Periodical_Widget_Visibility/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Periodical_Widget_Visibility
 * @subpackage Periodical_Widget_Visibility/public
 * @author     Kybernetik Services <wordpress@kybernetik.com.de>
 */
class Periodical_Widget_Visibility_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The ID of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_slug       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->version = $version;

	}

	/**
	 * Determine whether the widget should be displayed based on time set by the user.
	 *
	 * @since    1.0.0
	 * @param array $widget_settings The widget settings.
	 * @return array Settings to display or bool false to hide.
	 */
	public static function filter_widget( $widget_settings ) {

		$plugin_slug = 'periodical-widget-visibility';
		$weekdays = array(
			1 => 'mon',
			2 => 'tue',
			3 => 'wed',
			4 => 'thu',
			5 => 'fri',
			6 => 'sat',
			7 => 'sun',
		);

		// quit (= show widget) if no stored settings for this plugin
		if ( ! isset( $widget_settings[ $plugin_slug ] ) ) {
			return $widget_settings;
		}
		
		// check existence of required data, quit if not available
		foreach ( array( 'mode', 'frequency', 'timestamps', 'daysofweek' ) as $required ) {
			if ( ! isset( $widget_settings[ $plugin_slug ][ $required ] ) ) {
				return $widget_settings;
			}			
		}

		// get values of blog's current date and time
		$current_timestamp		= (int) current_time( 'timestamp' ); // get current local blog timestamp
		$current[ 'month' ]		= (int) date( 'n', $current_timestamp ); // get current month; 1 to 12
		$current[ 'monthday' ]	= (int) date( 'j', $current_timestamp ); // get current day of the month; 1 to 31
		$current[ 'weekday' ]	= (int) date( 'N', $current_timestamp ); // get current ISO-8601 numeric representation of the day of the week; 1 (for Monday) through 7 (for Sunday)
		$current[ 'date' ] = mktime( 
			0, // hour
			0, // minute
			0, // second
			$current[ 'month' ],
			$current[ 'monthday' ],
			1970 // year
		);
		
		// initialize visibility trigger
		$is_match = false;
		
		// action per weekday and daytime
		if (
			in_array( $current[ 'weekday' ], $widget_settings[ $plugin_slug ][ 'daysofweek' ] )
		) {
			// action per frequency
			/*
			switch ( $widget_settings[ $plugin_slug ][ 'frequency' ] ) {
				case 'yearly_period':
			*/
				
					// check desired values, else quit
					if ( ! ( 
						isset( $widget_settings[ $plugin_slug ][ 'timestamps' ][ 'yearly_period_start' ] )
						and isset( $widget_settings[ $plugin_slug ][ 'timestamps' ][ 'yearly_period_end' ] )
					) ) {
						return $widget_settings;
					}
					
					// sanitize stored values
					$custom[ 'date_start' ]	= absint( $widget_settings[ $plugin_slug ][ 'timestamps' ][ 'yearly_period_start' ] );
					$custom[ 'date_end' ]	= absint( $widget_settings[ $plugin_slug ][ 'timestamps' ][ 'yearly_period_end' ] );
					
					// calculate visibility of widget
					if ( 
						$custom[ 'date_start' ] <= $current[ 'date' ]
						and $current[ 'date' ] <= $custom[ 'date_end' ]
					) {
						$is_match = true;
					}
			/*
					break;
					
				// if unknown value quit (= show widget)
				default:
					return $widget_settings;

			} // switch(frequency)
			*/
		}
	
		$is_opposite = ( 'Hide' == $widget_settings[ $plugin_slug ][ 'mode' ] ) ? true : false;

		// if current time matches custom time description
		if ( $is_match ) {
			return ( $is_opposite ) ? false : $widget_settings; // if opposite hide widget else show widget
		} else {
			return ( $is_opposite ) ? $widget_settings : false; // if opposite show widget else hide widget
		}
	}

}
