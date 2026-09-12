<?php
/**
 * Isolated uninstall checks; all database operations are test doubles.
 *
 * Run: php tests/test-uninstall-standards.php [single]
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'WP_UNINSTALL_PLUGIN', true );
$GLOBALS['uninstall_test_current_site'] = 999;
$GLOBALS['uninstall_test_stack']        = array();
$GLOBALS['uninstall_test_deleted']      = array();
$GLOBALS['uninstall_test_single']       = isset( $argv[1] ) && 'single' === $argv[1];

/** Simulate an authorized uninstall without loading WordPress. */
function current_user_can( $capability ) {
	return 'delete_plugins' === $capability;
}

/** Select the requested installation type. */
function is_multisite() {
	return ! $GLOBALS['uninstall_test_single'];
}

/** Verify that every site is requested without a default result limit. */
function get_sites( $args ) {
	if ( array(
		'fields' => 'ids',
		'number' => 0,
	) !== $args ) {
		throw new RuntimeException( 'Unexpected site query.' );
	}
	return range( 1, 102 );
}

/** Track site switches and reject an unbalanced previous iteration. */
function switch_to_blog( $site_id ) {
	if ( 999 !== $GLOBALS['uninstall_test_current_site'] ) {
		throw new RuntimeException( 'Site context was not restored between iterations.' );
	}
	$GLOBALS['uninstall_test_stack'][]      = $GLOBALS['uninstall_test_current_site'];
	$GLOBALS['uninstall_test_current_site'] = $site_id;
}

/** Restore the previous site context. */
function restore_current_blog() {
	$GLOBALS['uninstall_test_current_site'] = array_pop( $GLOBALS['uninstall_test_stack'] );
}

/** Record option deletions without touching a database. */
function delete_option( $name ) {
	$GLOBALS['uninstall_test_deleted'][ $GLOBALS['uninstall_test_current_site'] ][] = $name;
}

/** Stand in for transient and network option deletion. */
function delete_transient( $name ) {}
function delete_site_option( $name ) {}

require dirname( __DIR__ ) . '/uninstall.php';
$expected_sites = $GLOBALS['uninstall_test_single'] ? array( 999 ) : range( 1, 102 );
$slug           = basename( dirname( __DIR__ ) );
if ( array_keys( $GLOBALS['uninstall_test_deleted'] ) !== $expected_sites ) {
	throw new RuntimeException( 'Cleanup skipped or added sites.' );
}
foreach ( $expected_sites as $site_id ) {
	if ( ! in_array( 'widget_' . $slug, $GLOBALS['uninstall_test_deleted'][ $site_id ], true ) ) {
		throw new RuntimeException( 'Scheduler cleanup missing.' );
	}
}
if ( 999 !== $GLOBALS['uninstall_test_current_site'] || array() !== $GLOBALS['uninstall_test_stack'] ) {
	throw new RuntimeException( 'Uninstall left a switched site context.' );
}
echo $slug . ': uninstall passed for ' . count( $expected_sites ) . " site(s).\n";
