<?php
/**
 * Calendar regression tests for the scheduler's stored reference values.
 *
 * Run: php tests/test-scheduler-standards.php /path/to/wordpress
 * Includes the security suite and does not change the WordPress database.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

require __DIR__ . '/test-scheduler-security.php';
require dirname( __DIR__ ) . '/includes/class-' . ( $is_pro ? 'Periodical_Widget_Visibility_Pro' : 'Periodical_Widget_Visibility' ) . '_Public.php';
$public_class                              = $is_pro ? 'Periodical_Widget_Visibility_Pro_Public' : 'Periodical_Widget_Visibility_Public';
$GLOBALS['scheduler_test_bad_translation'] = false;
$standards_start                           = $GLOBALS['scheduler_test_count'];
$initial_timezone                          = date_default_timezone_get();
$widget                                    = new stdClass();
$widget->option_name                       = 'widget_scheduler_test';
$widget->number                            = 1;

/** Save and evaluate a fixture through the actual plugin callbacks. */
function scheduler_evaluate_fixture( $input, $instant, $site_timezone, $expected ) {
	global $slug, $class, $public_class, $widget;
	$GLOBALS['scheduler_test_now']      = $instant;
	$GLOBALS['scheduler_test_timezone'] = $site_timezone;
	$_POST                              = array(
		$slug . '_nonce' => wp_create_nonce( $slug . '_save_scheduler' ),
		$slug            => $input,
	);
	$settings                           = $class::widget_update( array( 'title' => 'Calendar fixture' ), array(), array() );
	scheduler_assert( isset( $settings[ $slug ] ), 'Fixture failed to save.' );
	$result = $public_class::filter_widget( $settings, $widget );
	scheduler_assert( $expected === ( false !== $result ), 'Incorrect visibility at ' . $instant . ' in ' . $site_timezone );
	return $settings;
}

$fixture                    = $valid;
$fixture['repetition_year'] = '';
$fixture['clear_cache']     = '0';
$fixture['daysofweek']      = array( '1', '2', '3', '4', '5', '6', '7' );
$fixture['sat-start-hh']    = '0';
$fixture['sat-start-mn']    = '0';
$fixture['sat-end-hh']      = '23';
$fixture['sat-end-mn']      = '59';

// A PHP timezone changed by another plugin must not alter stored dates or visibility.
$baseline = null;
foreach ( array( 'UTC', 'America/Los_Angeles', 'Asia/Tokyo' ) as $php_timezone ) {
	date_default_timezone_set( $php_timezone );
	$settings = scheduler_evaluate_fixture( $fixture, '2026-09-12 23:30:00 UTC', 'Europe/Berlin', true );
	if ( null === $baseline ) {
		$baseline = $settings;
	}
	scheduler_assert( $baseline === $settings, 'PHP timezone changed the stored schedule.' );
	scheduler_assert( 0 === $settings[ $slug ]['timestamps']['yearly_period_start'], 'January 1 reference changed.' );

	// The site is already on Sunday although UTC is still on Saturday.
	$sunday               = $fixture;
	$sunday['daysofweek'] = array( '7' );
	scheduler_evaluate_fixture( $sunday, '2026-09-12 23:30:00 UTC', 'Europe/Berlin', true );
	$sunday['mode'] = 'Hide';
	scheduler_evaluate_fixture( $sunday, '2026-09-12 23:30:00 UTC', 'Europe/Berlin', false );

	// Numeric weekday strings in older stored settings remain compatible.
	$legacy                        = $settings;
	$legacy[ $slug ]['daysofweek'] = array( '7' );
	scheduler_assert( false !== $public_class::filter_widget( $legacy, $widget ), 'Legacy weekday strings stopped matching.' );
}

// Inclusive annual date boundaries and a site-local New Year.
$annual                              = $fixture;
$annual['yearly_period_start_month'] = '9';
$annual['yearly_period_start_day']   = '12';
$annual['yearly_period_end_month']   = '9';
$annual['yearly_period_end_day']     = '12';
scheduler_evaluate_fixture( $annual, '2026-09-11 22:00:00 UTC', 'Europe/Berlin', true );
scheduler_evaluate_fixture( $annual, '2026-09-12 21:59:00 UTC', 'Europe/Berlin', true );
scheduler_evaluate_fixture( $annual, '2026-09-12 22:00:00 UTC', 'Europe/Berlin', false );

if ( $is_pro ) {
	$year                    = $fixture;
	$year['repetition_year'] = '2027';
	scheduler_evaluate_fixture( $year, '2026-12-31 23:30:00 UTC', 'Europe/Berlin', true );
	scheduler_evaluate_fixture( $year, '2026-12-31 23:30:00 UTC', 'America/New_York', false );

	// Last-day rules cover February in both leap years and ordinary years.
	$monthly              = $fixture;
	$monthly['frequency'] = 'monthly_days';
	$monthly['months']    = array( '2' );
	$monthly['monthdays'] = array( '32' );
	scheduler_evaluate_fixture( $monthly, '2028-02-29 12:00:00 UTC', 'UTC', true );
	scheduler_evaluate_fixture( $monthly, '2028-02-28 12:00:00 UTC', 'UTC', false );
	scheduler_evaluate_fixture( $monthly, '2027-02-28 12:00:00 UTC', 'UTC', true );

	// Ordinal weekday rules must not depend on the PHP timezone or overflow a month.
	$ordinal                      = $fixture;
	$ordinal['frequency']         = 'yearly_every_weekday';
	$ordinal['yearly_day_orders'] = array( '2' );
	$ordinal['yearly_weekdays']   = array( '1' );
	$ordinal['yearly_months']     = array( '9' );
	scheduler_evaluate_fixture( $ordinal, '2026-09-14 12:00:00 UTC', 'UTC', true );
	scheduler_evaluate_fixture( $ordinal, '2026-09-21 12:00:00 UTC', 'UTC', false );
	$ordinal['yearly_day_orders'] = array( '6' );
	scheduler_evaluate_fixture( $ordinal, '2026-09-28 12:00:00 UTC', 'UTC', true );
	$ordinal['yearly_day_orders'] = array( '5' );
	scheduler_evaluate_fixture( $ordinal, '2026-10-05 12:00:00 UTC', 'UTC', false );

	// Weekday times follow the site's clock across a daylight saving transition.
	$dst                 = $fixture;
	$dst['sun-start-hh'] = '3';
	$dst['sun-start-mn'] = '0';
	$dst['sun-end-hh']   = '3';
	$dst['sun-end-mn']   = '30';
	scheduler_evaluate_fixture( $dst, '2026-03-29 00:59:00 UTC', 'Europe/Berlin', false );
	scheduler_evaluate_fixture( $dst, '2026-03-29 01:00:00 UTC', 'Europe/Berlin', true );
	scheduler_evaluate_fixture( $dst, '2026-03-29 01:30:00 UTC', 'Europe/Berlin', true );
	scheduler_evaluate_fixture( $dst, '2026-03-29 01:31:00 UTC', 'Europe/Berlin', false );
}

// An earlier filter's rejection must not be replaced by the scheduler.
scheduler_assert( false === $public_class::filter_widget( false, $widget ), 'Earlier widget rejection was lost.' );
date_default_timezone_set( $initial_timezone );
echo $slug . ': ' . ( $GLOBALS['scheduler_test_count'] - $standards_start ) . " calendar assertions passed.\n";
