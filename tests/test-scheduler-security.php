<?php
/**
 * Scheduler security regression tests using WordPress sanitizing and escaping.
 *
 * Run: php tests/test-scheduler-security.php /path/to/wordpress
 * Authentication and site settings are test doubles; no database is changed.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

$core = isset( $argv[1] ) ? $argv[1] : dirname( __DIR__, 4 );
define( 'ABSPATH', rtrim( $core, '/\\' ) . '/' );
define( 'WPINC', 'wp-includes' );
define( 'WVT_ROOT', dirname( __DIR__ ) . '/' );
define( 'PWVP_ROOT_PATH', dirname( __DIR__ ) . '/' );
$GLOBALS['scheduler_test_can_edit']        = true;
$GLOBALS['scheduler_test_bad_translation'] = false;

/** Turn warnings into test failures. */
set_error_handler(
	function ( $severity, $message, $file, $line ) {
		throw new ErrorException( $message, 0, $severity, $file, $line );
	}
);

/** Supply deterministic WordPress environment data. */
function get_option( $name ) {
	return 'UTF-8'; }
function _canonical_charset( $charset ) {
	return $charset; }
function is_utf8_charset() {
	return true; }
function wp_is_valid_utf8( $text ) {
	return 1 === preg_match( '//u', $text ); }
function apply_filters( $hook, $value ) {
	return $value; }
function did_action() {
	return false; }
function current_user_can( $capability ) {
	return 'edit_theme_options' === $capability && $GLOBALS['scheduler_test_can_edit']; }
function wp_create_nonce( $action ) {
	return hash( 'sha256', 'test-session:' . $action ); }
function wp_verify_nonce( $nonce, $action ) {
	return hash_equals( wp_create_nonce( $action ), $nonce ); }
function current_time( $format ) {
	if ( 'mysql' === $format ) {
		$now = new DateTime( isset( $GLOBALS['scheduler_test_now'] ) ? $GLOBALS['scheduler_test_now'] : '2026-09-12 12:30:00 UTC' );
		$now->setTimezone( new DateTimeZone( isset( $GLOBALS['scheduler_test_timezone'] ) ? $GLOBALS['scheduler_test_timezone'] : 'UTC' ) );
		return $now->format( 'Y-m-d H:i:s' );
	}
	return '<script>alert(1)</script>';
}
function absint( $value ) {
	return abs( (int) $value ); }
function is_rtl() {
	return false; }
function admin_url( $path ) {
	return 'https://example.org/wp-admin/' . $path; }
function wp_allowed_protocols() {
	return array( 'http', 'https' ); }
function __( $text, $domain = 'default' ) {
	return $GLOBALS['scheduler_test_bad_translation'] ? $text . '<img src=x onerror="alert(1)"><script>alert(1)</script>' : $text; }
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( __( $text, $domain ) ); }
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html__( $text, $domain ); }
function esc_html_x( $text, $context, $domain = 'default' ) {
	return esc_html__( $text, $domain ); }
function selected( $value, $expected = true, $echo = true ) {
	$result = (string) $value === (string) $expected ? ' selected="selected"' : '';
	if ( $echo ) {
		echo $result;
	} return $result; }
function checked( $value, $expected = true, $echo = true ) {
	$result = (string) $value === (string) $expected ? ' checked="checked"' : '';
	if ( $echo ) {
		echo $result;
	} return $result; }
function disabled( $value, $expected = true, $echo = true ) {
	$result = (string) $value === (string) $expected ? ' disabled="disabled"' : '';
	if ( $echo ) {
		echo $result;
	} return $result; }

require ABSPATH . 'wp-includes/formatting.php';
require ABSPATH . 'wp-includes/kses.php';
if ( file_exists( ABSPATH . 'wp-includes/class-wp-token-map.php' ) ) {
	require ABSPATH . 'wp-includes/class-wp-token-map.php'; }
foreach ( array( 'html5-named-character-references.php', 'class-wp-html-attribute-token.php', 'class-wp-html-span.php', 'class-wp-html-doctype-info.php', 'class-wp-html-text-replacement.php', 'class-wp-html-decoder.php', 'class-wp-html-tag-processor.php' ) as $dependency ) {
	$file = ABSPATH . 'wp-includes/html-api/' . $dependency;
	if ( file_exists( $file ) ) {
		require_once $file; }
}

/** Supply hostile locale labels and widget IDs for output checks. */
class Scheduler_Test_Locale {
	public function get_month( $number ) {
		return 'Month <script>alert(1)</script>'; }
	public function get_weekday( $number ) {
		return 'Day <script>alert(1)</script>'; }
}
class Scheduler_Test_Widget {
	public function get_field_id( $name ) {
		return $name . '\" onfocus=\"alert(1)'; }
}
$GLOBALS['wp_locale'] = new Scheduler_Test_Locale();

/** Stop at the first regression. */
function scheduler_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message ); }
	++$GLOBALS['scheduler_test_count'];
}
$GLOBALS['scheduler_test_count'] = 0;
$slug                            = basename( dirname( __DIR__ ) );
$is_pro                          = 'periodical-widget-visibility-pro' === $slug;
$class                           = $is_pro ? 'Periodical_Widget_Visibility_Pro_Admin' : 'Periodical_Widget_Visibility_Admin';
require dirname( __DIR__ ) . '/includes/class-' . $class . '.php';
$nonce_name = $slug . '_nonce';
$valid      = array(
	'mode'                      => 'Show',
	'frequency'                 => 'yearly_period',
	'daysofweek'                => array( '1', '6', '7' ),
	'monthdays'                 => array( '1', '32' ),
	'months'                    => array( '1', '9' ),
	'yearly_day_orders'         => array( '1' ),
	'yearly_weekdays'           => array( '6' ),
	'yearly_months'             => array( '9' ),
	'yearly_period_start_day'   => '1',
	'yearly_period_start_month' => '1',
	'yearly_period_end_day'     => '31',
	'yearly_period_end_month'   => '12',
	'sat-start-hh'              => '08',
	'sat-start-mn'              => '15',
	'sat-end-hh'                => '17',
	'sat-end-mn'                => '45',
	'repetition_year'           => '2026',
	'clear_cache'               => '1',
);
$old        = array(
	$slug => array(
		'mode'     => 'Hide',
		'sentinel' => 'keep',
	),
);
$incoming   = array(
	'title' => 'Updated title',
	$slug   => array( 'injected' => true ),
);
$preserved  = array(
	'title' => 'Updated title',
	$slug   => $old[ $slug ],
);

// Rejected requests must preserve the old schedule and unrelated widget changes.
foreach ( array( null, 'invalid', array( 'bad' ) ) as $nonce ) {
	$_POST = array( $slug => $valid );
	if ( null !== $nonce ) {
		$_POST[ $nonce_name ] = $nonce; }
	scheduler_assert( $preserved === $class::widget_update( $incoming, array(), $old ), 'Invalid nonce changed settings.' );
}
$_POST                              = array(
	$nonce_name => wp_create_nonce( $slug . '_save_scheduler' ),
	$slug       => $valid,
);
$GLOBALS['scheduler_test_can_edit'] = false;
scheduler_assert( $preserved === $class::widget_update( $incoming, array(), $old ), 'Missing capability changed settings.' );
$GLOBALS['scheduler_test_can_edit'] = true;
scheduler_assert( false === $class::widget_update( false, array(), $old ), 'Earlier rejection was lost.' );
foreach ( array( null, 'bad', array(), array( 'mode' => array( 'Show' ) ) ) as $payload ) {
	$_POST = array( $nonce_name => wp_create_nonce( $slug . '_save_scheduler' ) );
	if ( null !== $payload ) {
		$_POST[ $slug ] = $payload; }
	scheduler_assert( $preserved === $class::widget_update( $incoming, array(), $old ), 'Missing or malformed form changed settings.' );
}

// Valid submissions must save normalized selections and dates on the first save.
$_POST = array(
	$nonce_name => wp_create_nonce( $slug . '_save_scheduler' ),
	$slug       => $valid,
);
$saved = $class::widget_update( array( 'title' => 'New' ), array(), array() );
scheduler_assert( 'Show' === $saved[ $slug ]['mode'], 'Valid mode was not saved.' );
scheduler_assert( array( 1, 6, 7 ) === $saved[ $slug ]['daysofweek'], 'Weekdays were not normalized.' );
scheduler_assert( 12 === $saved[ $slug ]['yearly_period_end_month'], 'Month was not normalized.' );
if ( $is_pro ) {
	scheduler_assert( '2026' === $saved[ $slug ]['repetition_year'], 'Valid year was rejected.' );
	scheduler_assert( 'visible' === $saved[ $slug ]['current_status'], 'Initial status ignored the new times.' );
	scheduler_assert( 1 === $saved[ $slug ]['clear_cache'], 'Cache flag was not saved.' );
	foreach ( array( 'monthly_days', 'yearly_every_weekday', 'yearly_period' ) as $frequency ) {
		$_POST[ $slug ]['frequency'] = $frequency;
		$result                      = $class::widget_update( array(), array(), array() );
		scheduler_assert( $frequency === $result[ $slug ]['frequency'], 'Valid frequency was lost.' );
	}
	foreach ( array( 'abc2026xyz', '2026" onfocus="alert(1)', array( '2026' ), "2026\nextra" ) as $year ) {
		$_POST[ $slug ]['repetition_year'] = $year;
		$result                            = $class::widget_update( array(), array(), array() );
		scheduler_assert( '' === $result[ $slug ]['repetition_year'], 'Malformed year was saved.' );
	}
}

// Explicitly disabling a schedule and clearing all weekdays must still work.
$_POST[ $slug ]         = $valid;
$_POST[ $slug ]['mode'] = '';
$result                 = $class::widget_update( $incoming, array(), $old );
scheduler_assert( ! isset( $result[ $slug ] ), 'Explicit disable did not remove the schedule.' );
foreach ( array( null, '1', array(), array( array( '1' ) ) ) as $days ) {
	$_POST[ $slug ] = $valid;
	unset( $_POST[ $slug ]['daysofweek'] );
	if ( null !== $days ) {
		$_POST[ $slug ]['daysofweek'] = $days; }
	$result = $class::widget_update( $incoming, array(), $old );
	scheduler_assert( ! isset( $result[ $slug ] ), 'Invalid weekdays were accepted.' );
}
$_POST[ $slug ]                              = $valid;
$_POST[ $slug ]['daysofweek']                = array( '1', '1', '7', '99', '-2', '2bad', array( '3' ) );
$_POST[ $slug ]['yearly_period_start_day']   = array( '1' );
$_POST[ $slug ]['yearly_period_start_month'] = '99';
$_POST[ $slug ]['sat-start-hh']              = array( '8' );
$result                                      = $class::widget_update( array(), array(), array() );
scheduler_assert( array( 1, 7 ) === $result[ $slug ]['daysofweek'], 'Invalid list entries survived.' );
scheduler_assert( 12 === $result[ $slug ]['yearly_period_start_day'], 'Invalid day did not use the fallback.' );
scheduler_assert( 9 === $result[ $slug ]['yearly_period_start_month'], 'Invalid month did not use the fallback.' );

// Render the real template and reject executable attributes or tags.
$reflection = new ReflectionClass( $class );
$admin      = $reflection->newInstanceWithoutConstructor();
$property   = $reflection->getProperty( 'plugin_slug' );
$property->setValue( $admin, $slug );
$property = $is_pro ? $reflection->getProperty( 'license_page_slug' ) : null;
if ( $property ) {
	$property->setValue( $admin, 'license' ); }
$stored                                     = $saved;
$stored[ $slug ]['daysofweek']              = array( '1', array( '2' ), '<script>alert(1)</script>' );
$stored[ $slug ]['yearly_period_start_day'] = '\" onfocus=\"alert(1)';
$stored[ $slug ]['repetition_year']         = '2026\" onfocus=\"alert(1)';
ob_start();
$admin->display_time_fields( new Scheduler_Test_Widget(), null, $stored );
$html = ob_get_clean();
scheduler_assert( false === strpos( $html, '<script>' ), 'Rendered script element.' );
scheduler_assert( false === strpos( $html, ' onfocus="' ), 'Rendered an injected attribute.' );
scheduler_assert( false !== strpos( $html, 'name="' . $nonce_name . '"' ), 'Form nonce missing.' );
scheduler_assert( false !== strpos( $html, wp_create_nonce( $slug . '_save_scheduler' ) ), 'Form nonce action mismatch.' );
$GLOBALS['scheduler_test_bad_translation'] = true;
ob_start();
$admin->display_activation_message();
$admin->display_wp58_message();
$html = ob_get_clean();
scheduler_assert( false === strpos( $html, '<script>' ) && 0 === preg_match( '/<[^>]*\sonerror\s*=/i', $html ), 'Unsafe translated notice.' );
scheduler_assert( false !== strpos( $html, '<a href=' ), 'Safe notice links were removed.' );
echo $slug . ': ' . $GLOBALS['scheduler_test_count'] . " assertions passed.\n";
