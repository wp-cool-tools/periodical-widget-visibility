<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.kybernetik-services.com/
 * @since      1.0.0
 *
 * @package    Periodical_Widget_Visibility
 * @subpackage Periodical_Widget_Visibility/admin
 * @author     Kybernetik Services <wordpress@kybernetik.com.de>
 */
class Periodical_Widget_Visibility_Admin {

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
	 * @var      string    $plugin_version    The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Form field ids
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $field_ids   distinct ids of form field elements
	 */
	private $field_ids;

	/**
	 * Current widget time settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $scheduler   scheduler settings for the current widget
	 */
	private $scheduler;

	/**
	 * Current day on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_dd    current day
	 */
	private $current_dd;

	/**
	 * Current month on server
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $current_mm    current month
	 */
	private $current_mm;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_slug       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->plugin_version = $version;
		
		// not in use, just for the po-editor to display the translation on the plugins overview list
		$foo = __( 'Control the periodical visibility of each widget based on weekdays within a yearly time period easily.', 'periodical-widget-visibility' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

		// load only if we are on the Widgets page
		if ( 'widgets.php' == $hook_suffix ) {
			if( is_rtl() ) {
				wp_enqueue_style( $this->plugin_slug, WVT_URL . 'admin/css/periodical-widget-visibility-admin-rtl.css', array(), $this->plugin_version, 'all' );
			} else {
				wp_enqueue_style( $this->plugin_slug, WVT_URL . 'admin/css/periodical-widget-visibility-admin.css', array(), $this->plugin_version, 'all' );
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		// load only if we are on the Widgets page
		if ( 'widgets.php' == $hook_suffix ) {
			// scripts for the admin pages
			wp_enqueue_script( $this->plugin_slug, WVT_URL . 'admin/js/periodical-widget-visibility-admin.js', array( 'jquery' ), $this->plugin_version, false );

			// translations in scripts
			$translations = array(
				'open_scheduler' => __( 'Open scheduler', 'periodical-widget-visibility' ),
				'close_scheduler' => __( 'Close scheduler', 'periodical-widget-visibility' ),
			);
			wp_localize_script( $this->plugin_slug, 'pwv_i18n', $translations );
		}

	}

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    1.0.0
	 */
	public function display_activation_message() {
		
		$text_1 = 'Appearance';
		$text_2 = 'Widgets';
		
		if ( is_rtl() ) {
			$sep = '&lsaquo;';
			// set link #1
			$link_1 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( 'widgets.php' ) ),
				esc_html__( $text_2 ),
				$sep,
				esc_html__( $text_1 )
			);
		} else {
			$sep = '&rsaquo;';
			// set link #1
			$link_1 = sprintf(
				'<a href="%s">%s %s %s</a>',
				esc_url( admin_url( 'widgets.php' ) ),
				esc_html__( $text_1 ),
				$sep,
				esc_html__( $text_2 )
			);
		}
		
		// set whole message
		printf(
			'<div class="updated notice is-dismissible"><p>%s</p></div>',
			sprintf( 
				esc_html__( 'Welcome to Periodical Widget Visibility! You can set the time based visibility in each widget on the page %s.', 'periodical-widget-visibility' ),
				$link_1
			)
		);

	}

    /**
     * Print a message about the block based widgets in since WP 5.8
     *
     * @since    2.3.7
     */
    public function display_wp58_message() {

        printf(
            '<div class="notice-warning notice"><p>%s</p></div>',
            sprintf(
                __( '<b>Important:</b> You are using WordPress 5.8 or higher. Periodical Widget Visibility is currently not compatible with the newly introduced block based widgets. To continue using Periodical Widget Visibility, please install and activate the plug-in %s. It brings back the usual widgets and Periodical Widget Visibility is working as expected.', 'periodical-widget-visibility' ),
                '<a href="https://wordpress.org/plugins/classic-widgets/" target="_blank">Classic Widgets</a>'
            )
        );

    }

    /**
	 * Add the widget conditions to each widget in the admin.
	 *
	 * @param $widget unused.
	 * @param $return unused.
	 * @param array $widget_settings The widget settings.
	 */
	public function display_time_fields( $widget, $return, $widget_settings ) {

		global $wp_locale;
		
		// get translated month names only once
		$month_names = array();
		foreach ( range( 1, 12 ) as $i ) {
			$month_names[ $i ] = $wp_locale->get_month( $i );
		}
		
		// get translated weekday names only once
		$weekdays = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		
		$weekday_names = array();
		foreach ( range( 1, 6 ) as $i ) {
			$weekday_names[ $i ] = $wp_locale->get_weekday( $i ); // mon - sat
		}
		$weekday_names[ 7 ] = $wp_locale->get_weekday( 0 ); // Sunday
		
		// get translated ordernumber names only once
		$ordernumber_names = array(
			'1' => __( 'first', 'periodical-widget-visibility' ),
			'2' => __( 'second', 'periodical-widget-visibility' ),
			'3' => __( 'third', 'periodical-widget-visibility' ),
			'4' => __( 'fourth', 'periodical-widget-visibility' ),
			'5' => __( 'fifth', 'periodical-widget-visibility' ),
			'6' => __( 'last', 'periodical-widget-visibility' ),
		);
		
		$modes = array( 'Show', 'Hide' );
		$frequencies = array( 'yearly_period' );
		
		$this->field_ids = array();
		$this->scheduler = array();

		// prepare html elements ids for weekdays' start and end times
		$weekday_abbrevs = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
		foreach( $weekday_abbrevs as $day ) {
			foreach( array( 'start', 'end' ) as $boundary ) {
				foreach( array( 'hh', 'mn', 'ss' ) as $field_name ) {
					$name = $day . '-' . $boundary . '-' . $field_name; // e.g. 'mon-start-hh'
					$this->field_ids[ $name ] = $widget->get_field_id( $name );
				}
			}
		}

		// check and sanitize stored settings; if not set: set them to current time
		if ( isset( $widget_settings[ 'periodical-widget-visibility' ] ) ) {
			$this->scheduler = $widget_settings[ 'periodical-widget-visibility' ];
		}

		// modes
		if ( ! ( isset( $this->scheduler[ 'mode' ] ) and in_array( $this->scheduler[ 'mode' ], $modes ) ) ) {
			$this->scheduler[ 'mode' ] = '';
		}

		// repetition rate
		/*if ( ! ( isset( $this->scheduler[ 'frequency' ] ) and in_array( $this->scheduler[ 'frequency' ], $frequencies ) ) ) {*/
			$this->scheduler[ 'frequency' ] = $frequencies[ 0 ];
		/*}*/

		// weekdays
		$this->sanitize_multiple_ids( 'daysofweek', 1, 7, true );

		// get yearly_period_start_month values
		$this->sanitize_single_id( 'yearly_period_start_month' );
		
		// get yearly_period_end_month values
		$this->sanitize_single_id( 'yearly_period_end_month' );
		
		// set current date and time vars
		// (neccessary to write it once more instead of re-use $this->xx because we are here in a non-object context)
		$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
		$current_mm = idate( 'm', $timestamp ); // get month number as integer
		$current_dd = idate( 'd', $timestamp ); // get day number as integer

		// set timestamps of yearly period start and end
		foreach( array( 'yearly_period_start', 'yearly_period_end' ) as $boundary ) {
			// month
			$name_month = $boundary . '_month';
			$var_month = isset( $this->scheduler[ $name_month ] ) ? absint( $this->scheduler[ $name_month ] ) : $current_mm;
			$var_month = ( 1 <= $var_month and $var_month <= 12 ) ? $var_month : $current_mm;
			$this->scheduler[ $name_month ] = $var_month;

			// day
			$name_day = $boundary . '_day';
			$var_day = isset( $this->scheduler[ $name_day ] ) ? absint( $this->scheduler[ $name_day ] ) : $current_dd;
			$var_day = ( 1 <= $var_day and $var_day <= 31 ) ? $var_day : $current_dd;
			$this->scheduler[ $name_day ] = $var_day;
			
			$this->scheduler[ 'timestamps' ][ $boundary ] = mktime(
				0, // hour
				0, // minute
				0, // second
				$var_month,
				$var_day,
				1970 // year
			);
		}

		// print additional input fields in widget
		include WVT_ROOT . 'admin/partials/formfields.php';
		
		// return null because new fields are added
		return null;
	}

	/**
	 * On an AJAX update of the widget settings, sanitize and return the display conditions.
	 *
	 * @param	array	$new_widget_settings	New settings for this instance as input by the user.
	 * @param	array	$old_widget_settings	Old settings for this instance.
	 * @return	array	$widget_settings		Processed settings.
	 */
	public static function widget_update( $widget_settings, $new_widget_settings, $old_widget_settings ) {
		
		$scheduler = array();
		$plugin_slug = 'periodical-widget-visibility';
		
		// sanitize user input

		// if neither activated nor weekday checked, save time and quit now without settings
		if ( empty( $_POST[ $plugin_slug ][ 'mode' ] ) ) {
			// if former settings are in the widget_settings: delete them
			if ( isset( $widget_settings[ $plugin_slug ] ) ) {
				unset( $widget_settings[ $plugin_slug ] );
			}
			return $widget_settings;
		}
		
		// get weekdays values
		$sanitized_daysofweek = array_map( 'absint', $_POST[ $plugin_slug ][ 'daysofweek' ] ); // convert values from string to positive integers
		$scheduler[ 'daysofweek' ] = array();
		foreach ( range( 1, 7 ) as $dayofweek ) {
			if ( in_array( $dayofweek, $sanitized_daysofweek ) ) {
				$scheduler[ 'daysofweek' ][] = $dayofweek;
			}
		}

		// if no valid weekday given, save time and quit now without settings
		if ( 
			empty( $scheduler[ 'daysofweek' ] )
		) {
			if ( isset( $widget_settings[ $plugin_slug ] ) ) {
				unset( $widget_settings[ $plugin_slug ] );
			}
			return $widget_settings;
		}

		// set scheduler mode; quit if no valid value
		if ( isset( $_POST[ $plugin_slug ][ 'mode' ] ) and in_array( $_POST[ $plugin_slug ][ 'mode' ], array( 'Show', 'Hide' ) ) ) {
			$scheduler[ 'mode' ] = $_POST[ $plugin_slug ][ 'mode' ];
		} else {
			if ( isset( $widget_settings[ $plugin_slug ] ) ) {
				unset( $widget_settings[ $plugin_slug ] );
			}
			return $widget_settings;
		}

		// set repetition rate if valid, else default
		$scheduler[ 'frequency' ] = 'yearly_period';

		// set current date and time vars
		// (neccessary to write it once more instead of re-use $this->xx because we are here in a non-object context)
		$timestamp = current_time( 'timestamp' ); // get current local blog timestamp
		$current_mm = idate( 'm', $timestamp ); // get month number as integer
		$current_dd = idate( 'd', $timestamp ); // get day number as integer

		// set timestamps of widget start and end
		foreach( array( 'yearly_period_start', 'yearly_period_end' ) as $boundary ) {
			// month
			$name_month = $boundary . '_month';
			$var_month = isset( $_POST[ $plugin_slug ][ $name_month ] ) ? absint( $_POST[ $plugin_slug ][ $name_month ] ) : $current_mm;
			$var_month = ( 1 <= $var_month and $var_month <= 12 ) ? $var_month : $current_mm;
			$scheduler[ $name_month ] = $var_month;

			// day
			$name_day = $boundary . '_day';
			$var_day = isset( $_POST[ $plugin_slug ][ $name_day ] ) ? absint( $_POST[ $plugin_slug ][ $name_day ] ) : $current_dd;
			$var_day = ( 1 <= $var_day and $var_day <= 31 ) ? $var_day : $current_dd;
			$scheduler[ $name_day ] = $var_day;
			
			$scheduler[ 'timestamps' ][ $boundary ] = mktime(
				0, // hour
				0, // minute
				0, // second
				$var_month,
				$var_day,
				1970 // year
			);
		}

		// set timestamps of weekdays' starts and ends
		foreach( array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ) as $month ) {
			foreach( array( 'start', 'end' ) as $boundary ) {
				$key = $month . '-' . $boundary;
				
				if ( 'start' == $boundary ) {
					$default_hh = 0;
					$default_mn = 0;
				} else {
					$default_hh = 23;
					$default_mn = 59;
				}

				// hour
				$name = $key . '-hh';
				$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $default_hh;
				$time[ $name ] = ( 0 <= $var and $var <= 23 ) ? $var : $default_hh;
				// minute
				$name = $key . '-mn';
				$var = isset( $_POST[ $plugin_slug ][ $name ] ) ? absint( $_POST[ $plugin_slug ][ $name ] ) : $default_mn;
				$time[ $name ] = ( 0 <= $var and $var <= 59 ) ? $var : $default_mn;
				// second
				$name = $key . '-ss';
				$time[ $name ] = 0;
				
				$scheduler[ 'timestamps' ][ $key ] = mktime(
					$time[ $key . '-hh' ],
					$time[ $key . '-mn' ],
					$time[ $key . '-ss' ],
					1,
					1,
					1970
				);
			}
		}

		// return sanitized user settings
		$widget_settings[ $plugin_slug ] = $scheduler;
		return $widget_settings;
	}

	/**
	 * Print the selection field options by a given associated array
	 *
	 * @param $arr.
	 */
	private function print_options( $arr, $field, $not_multiple = false ) {
		foreach ( $arr as $key => $name ) {
			$is_selected = $not_multiple ? ( $key == $this->scheduler[ $field ] ) : in_array( $key, $this->scheduler[ $field ] );
			
			print "\t\t\t";
			printf( '<option value="%s"%s>%s</option>', strval( $key ), selected( $is_selected, true, false ), esc_html( $name ) );
			print "\n";
		}
	}
	
	/**
	 * Sanitize and set values for multi-selection fields
	 *
	 */
	private function sanitize_multiple_ids( $key, $start = 1, $end = 12, $all = false ) {
		
		if ( isset( $this->scheduler[ $key ] ) ) {
			$sanitized = array_map( 'absint', $this->scheduler[ $key ] ); // convert values from string to positive integers
			foreach ( range( $start, $end ) as $i ) {
				if ( in_array( $i, $sanitized ) ) {
					$this->scheduler[ $key ][] = $i;
				}
			}
		} else {
			// default: first checked or all checked
			if ( $all ) {
				$this->scheduler[ $key ] = range( $start, $end );
			} else {
				$this->scheduler[ $key ] = array( $start );
			}
		}
		
	}

	/**
	 * Sanitize and set value for single-selection fields
	 *
	 */
	private function sanitize_single_id( $key, $start = 1, $end = 12 ) {
		
		if ( isset( $this->scheduler[ $key ] ) ) {
			$sanitized = absint( $this->scheduler[ $key ] ); // convert value from string to positive integer
			foreach ( range( $start, $end ) as $i ) {
				if ( $i == $sanitized ) {
					$this->scheduler[ $key ] = $i;
					break;
				}
			}
		} else {
			// default: first checked or all checked
			$this->scheduler[ $key ] = $start;
		}
		
	}

}
