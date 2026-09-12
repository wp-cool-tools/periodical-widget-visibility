<?php
/**
* Provide a admin area view for the plugin
*
* This file is used to mark up the admin-facing aspects of the plugin.
*
* @link       https://www.kybernetik-services.com/
* @since      1.0.0
*
* @package    Periodical_Widget_Visibility
* @subpackage Periodical_Widget_Visibility/admin/partials
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="pwv-container pwv-collapsed">
	<div class="pwv-scheduler">
		<?php // Authorize changes to this scheduler form. ?>
		<input type="hidden" name="periodical-widget-visibility_nonce" value="<?php echo esc_attr( wp_create_nonce( 'periodical-widget-visibility_save_scheduler' ) ); ?>">
		<h4><?php esc_html_e( 'Periodical Widget Visibility', 'periodical-widget-visibility' ); ?></h4>
		<p><label><?php echo esc_html_x( 'Schedule', 'post action/button label', 'periodical-widget-visibility' ); ?> <select name="periodical-widget-visibility[mode]">
			<option value=""><?php esc_html_e( '&mdash; Select &mdash;', 'periodical-widget-visibility' ); ?></option>
<?php foreach ( $modes as $scheduler_mode ) { ?>
			<option value="<?php echo esc_attr( $scheduler_mode ); ?>"<?php selected( $scheduler_mode, $this->scheduler['mode'] ); ?>><?php echo 'Show' === $scheduler_mode ? esc_html__( 'Show', 'periodical-widget-visibility' ) : esc_html__( 'Hide', 'periodical-widget-visibility' ); ?></option>
<?php } ?>
			</select></label>
		</p>
		<fieldset>
			<legend><?php esc_html_e( 'on', 'periodical-widget-visibility' ); ?></legend>
			<p>
<?php
	$i = 1;
foreach ( $weekday_names as $weekday_name ) {
	?>
				<span class="pwv-weekday"><label><input class="checkbox" type="checkbox"<?php checked( in_array( $i, $this->scheduler['daysofweek'], true ) ); ?> name="periodical-widget-visibility[daysofweek][]" value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $weekday_name ); ?></label></span>
				<?php
				if ( $i < 7 ) {
					?>
				<br>
					<?php
				}
				++$i;
}
?>
			</p>
		</fieldset>
		<fieldset>
			<legend><?php esc_html_e( 'Period in year:', 'periodical-widget-visibility' ); ?></legend>
			<p>
				<label><?php esc_html_e( 'from', 'periodical-widget-visibility' ); ?> <input type="text" name="periodical-widget-visibility[yearly_period_start_day]" value="<?php echo esc_attr( $this->scheduler['yearly_period_start_day'] ); ?>" size="2" maxlength="2"></label>
				<label><?php esc_html_e( 'of month', 'periodical-widget-visibility' ); ?> <select name="periodical-widget-visibility[yearly_period_start_month]">
<?php $this->print_options( $month_names, 'yearly_period_start_month', true ); ?>
				</select></label>
				<br><label><?php esc_html_e( 'to', 'periodical-widget-visibility' ); ?> <input type="text" name="periodical-widget-visibility[yearly_period_end_day]" value="<?php echo esc_attr( $this->scheduler['yearly_period_end_day'] ); ?>" size="2" maxlength="2"></label>
				<label><?php esc_html_e( 'of month', 'periodical-widget-visibility' ); ?> <select name="periodical-widget-visibility[yearly_period_end_month]">
<?php $this->print_options( $month_names, 'yearly_period_end_month', true ); ?>
				</select></label>
			</p>
		</fieldset>
		<p><?php esc_html_e( 'Do you need more options?', 'periodical-widget-visibility' ); ?> <a href="https://www.kybernetik-services.com/shop/wordpress/plugin/periodical-widget-visibility-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=periodical-widget-visibility&utm_content=update-notice" target="_blank"><?php esc_html_e( 'Get the Pro version.', 'periodical-widget-visibility' ); ?></a></p>
	</div><!-- .pwv-scheduler -->
	<p><a href="#" class="button hinjipwv-link"><?php esc_html_e( 'Open scheduler', 'periodical-widget-visibility' ); ?></a></p>
</div><!-- .pwv-container -->
