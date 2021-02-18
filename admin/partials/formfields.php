<?php

/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://www.kybernetik-services.com/
* @since      1.0.0
*
* @package    Periodical_Widget_Visibility
* @subpackage Periodical_Widget_Visibility/admin/partials
*/
?>
<div class="pwv-container pwv-collapsed">
	<div class="pwv-scheduler">
		<h4><?php esc_html_e( 'Periodical Widget Visibility', 'periodical-widget-visibility' ); ?></h4>
		<p><label><?php $text = 'Schedule'; echo esc_html_x( $text, 'post action/button label' ); ?> <select name="periodical-widget-visibility[mode]">
			<option value=""><?php $text = '&mdash; Select &mdash;'; esc_html_e( $text );?></option>
<?php foreach ( $modes as $mode ) { ?> 
			<option value="<?php echo $mode; ?>"<?php selected( $mode, $this->scheduler[ 'mode' ] );?>><?php esc_html_e( $mode );?></option> 
<?php } ?>
			</select></label>
		</p>
		<fieldset>
			<legend><?php esc_html_e( 'on', 'periodical-widget-visibility' ); ?></legend>
			<p>
<?php
	$i = 1;
	foreach ( $weekday_names as $weekday_name ) { ?>
				<span class="pwv-weekday"><label><input class="checkbox" type="checkbox"<?php checked( in_array( $i, $this->scheduler[ 'daysofweek' ] ) ); ?> name="periodical-widget-visibility[daysofweek][]" value="<?php echo $i; ?>"><?php echo esc_html( $weekday_name ); ?></label></span><?php
		if ( $i < 7 ) { ?><br><?php }
		$i++; 
	}
?>
			</p>
		</fieldset>
		<fieldset>
			<legend><?php esc_html_e( 'Period in year:', 'periodical-widget-visibility' ); ?></legend>
			<p>
				<label><?php esc_html_e( 'from', 'periodical-widget-visibility' ); ?> <input type="text" name="periodical-widget-visibility[yearly_period_start_day]" value="<?php echo $this->scheduler[ 'yearly_period_start_day' ]; ?>" size="2" maxlength="2"></label>
				<label><?php esc_html_e( 'of month', 'periodical-widget-visibility' ); ?> <select name="periodical-widget-visibility[yearly_period_start_month]">
<?php $this->print_options( $month_names, 'yearly_period_start_month', true ); ?>
				</select></label>
				<br><label><?php esc_html_e( 'to', 'periodical-widget-visibility' ); ?> <input type="text" name="periodical-widget-visibility[yearly_period_end_day]" value="<?php echo $this->scheduler[ 'yearly_period_end_day' ]; ?>" size="2" maxlength="2"></label>
				<label><?php esc_html_e( 'of month', 'periodical-widget-visibility' ); ?> <select name="periodical-widget-visibility[yearly_period_end_month]">
<?php $this->print_options( $month_names, 'yearly_period_end_month', true ); ?>
				</select></label>
			</p>
		</fieldset>
	</div><!-- .pwv-scheduler -->
	<p><a href="#" class="button hinjipwv-link"><?php esc_html_e( 'Open scheduler', 'periodical-widget-visibility' ); ?></a></p>
</div><!-- .pwv-container -->