=== Periodical Widget Visibility ===
Contributors: kybernetikservices,Hinjiriyo
Donate link: https://www.paypal.com/donate?hosted_button_id=NSEQX73VHXKS8
Tags: control, day, deutsch, display, german, hide, jetpack, month, period, plan, schedule, scheduler, show, spanish, time, unlimited, visibility, weekdays, widget, widgets, year
Requires at least: 3.5
Requires PHP: 5.2
Tested up to: 6.6
Stable tag: 2.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Control the periodical visibility of each widget based on weekdays within a yearly time period easily.

== Description ==

Control the periodical visibility of each widget based on weekdays within a yearly time period easily.

The plugin is available in English, Spanish (Español) and German (Deutsch). It does not collect any personal data, so it is ready for EU General Data Protection Regulation (GDPR) compliance.

= Demo =
You want to test Periodical Widget Visibility before installing on your site? Try it out on your free dummy site and [click here](https://demo.tastewp.com/periodical-widget-visibility).

= Compatibility with WordPress 5.8 block based widgets =

With WordPress 5.8 the block based widgets were introduced. The technique behind these new widget concept is more complex. One of the changes is, the widgets are now using API call to display.
Extensive programming is required to prepare Periodical Widget Visibility for this new feature. Nothing I could do in the short time I was able to test this feature.
Even so, Periodical Widget Visibility works with WordPress 5.8 and higher. The only adjustment you need to make is to turn off the block-based widgets for a while.
As known from the Gutenberg block editor, there is also a plug-in to deactivate the block-based widgets.

Please download, install and activate [Classic Widgets](https://wordpress.org/plugins/classic-widgets/) from wordpress.org and switch back to the usual widget area.

I'm working hard to make Periodical Widget Visibility compatible with the block based widgets. Both take some time to provide a stable and error-free code base.
Stay tuned for a brand-new version. And thank you for your understanding.

= Show and hide widgets at desired days within a yearly time period repeatedly =

Do you want to show a widget every year on Christmas days, Eastern, or hide it on your summer holidays? This plugin enables you to control the visibility of a widget from your desired start day till the desired end day in the year and on the selected weekdays.

= Much more options available =

If you want to schedule the visibility based on the daytime of each weekday and more precisely defined repetitions [go to the Pro version of the plugin](https://www.kybernetik-services.com/shop/wordpress/plugin/periodical-widget-visibility-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=periodical-widget-visibility&utm_content=update-notice-readme).

= Compatibility with Jetpack =

This plugin works perfectly with Jetpack's "Widget Visibility" module. Both plugins enhance each other to give you great control about when and where to display which widget on your website.

= Languages =

The user interface is available in

* English
* Spanish (Español)
* German (Deutsch)

= More options with the Pro version =

If you are looking for finer filters of the timed visibility take a look at the Pro version of this plugin. That version comes with all options of this plugin and contains additional options:

* **Visibility based on the time on each weekday**: You can fine tune the daily visibility based on the time of the weekday, from a start time to an end time in the day.
* **Visibility based on days in months**: You can control the visibility of the widget based on every day of a month, from 1 to 31, and every month of a year. Since the months can have 28, 29, 30 or 31 days but you want to use the last day of any month there is an extra option ‘last day‘ which picks the last day of a month regardless of the length of a month.
* **Visibility based on weekdays in months**: The option ‘Weekdays of month‘ enables you to select every ordinal number, every day of a week, from Monday to Sunday, and every month of a year to control the visibility of the widget. If you want to pick the last weekday in every month take the option ‘last‘.
* Premium Support – Pro users get premium support whilst free support is offered in the WordPress forums in our spare time

Go to the online shop of [Periodical Widget Visibility Pro](https://www.kybernetik-services.com/shop/wordpress/plugin/periodical-widget-visibility-pro/?utm_source=wordpress_org&utm_medium=plugin&utm_campaign=periodical-widget-visibility&utm_content=update-notice-readme).

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Periodical Widget Visibility'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard
5. Go to the page 'Widgets' and set the visibility period in each widget

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `periodical-widget-visibility.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard
6. Go to the page 'Widgets' and set the visibility period in each widget

= Using FTP =

1. Download `periodical-widget-visibility.zip`
2. Extract the `periodical-widget-visibility` directory to your computer
3. Upload the `periodical-widget-visibility` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard
5. Go to the page 'Widgets' and set the visibility period in each widget

== Frequently Asked Questions ==

= How to use? =

1. Go to the Widget page in the WordPress backend. Every widget is enhanced by easy-to-use fields for time data.
2. Set comfortably the date and time definitions when to show or hide the widget.
3. After you have define the time data just save the widget settings. Done!

= Is there an option page? =

No. That is not neccessary. You set the visibility in each widget on the Widgets page in the backend.

= Do the scheduler settings effect cached pages? =

No. This plugin has no site effects to cache plugins. So it can happen that a cached page shows a widget although the scheduler settings says to hide it, and vice versa.

It is up to your cache settings how the visibility of a widget is considered. Maybe it is helpful to empty the cache automatically once a day.

= Does removing the plugin delete the settings in the database? =

Up to now: no. But you can remove the settings in the database easily with two possibilities:

* Either deactivate (uncheck) the visibility time scheduler in each widget and save the widget settings.
* Or remove the widget out of the widget area.

= Does the plugin work with Jetpack's Widget Visibility module? =

Yes. Both plugins work together perfectly and enhance each other to give you great control about when and where to display which widget.

= Where is the *.pot file for translating the plugin in any language? =

The plugin is ready for right-to-left languages like Arabic or Hebrew.

All texts of the plugin are in the *.pot file. You would find the *.pot file in the 'languages' directory of this plugin. If you would send the *.po file to me I would include it in the next release of the plugin.

== Screenshots ==

1. The first screenshot shows the time controls for every widget in english language
2. The second screenshot shows the time controls for every widget in german language

== Changelog ==

= 2.3.7 =
* added admin notice regarding block based widgets
* Tested with WordPress 5.8

= 2.3.6 =
* const names standardized
* added autoload class
* Tested with WordPress 5.7.1

= 2.3.5 =
* Tested with WordPress 5.6.1
* New branding

= 2.3.4 =
* Tested with WordPress 5.6

= 2.3.3 =
* Tested with WordPress 5.5.1

= 2.3.2 =
* Hide the scheduler form fields in SiteOrigin widgets

= 2.3.1 =
* Tested with WordPress 5.3

= 2.3 =
* Added spanish translation
* Tested with WordPress 4.9.6

= 2.2.1 =
* Added 'Requires PHP' info in readme.txt
* Updated translations due to WordPress 4.9
* Tested with WordPress 4.9.1

= 2.2 =
* Revised sanitation for texts and URLs on the pages
* Revised translations
* Set activation message as dismissible
* Tested with WordPress 4.8

= 2.1 =
* Added closing and opening of the schedulers in the Customizer

= 2.0 =
* Added button for opening and closing the scheduler in the widgets
* Improved: Loads plugin's CSS and script only if the Widget page is loaded
* Revised translations
* Tested with WordPress 4.7.2

= 1.2.1 =
* Tested with WordPress 4.7

= 1.2 =
* Revised uninstall function for WordPress 4.6 due to the introduction of WP_Site_Query class
* Tested with WordPress 4.6

= 1.1.0 =
* Fixed: not saving settings

= 1.0.1 =
* Fixed minor bug

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.3.4 =
Tested with WordPress 5.6

= 2.3.3 =
Tested with WordPress 5.5.1

= 2.3.2 =
Hide the scheduler form fields in SiteOrigin widgets

= 2.3.1 =
Tested with WordPress 5.3

= 2.3 =
Added spanish translation, tested with WordPress 4.9.6

= 2.2.1 =
Added Requires PHP info in readme.txt, updated WP 4.9 translations, tested with WordPress 4.9.1

= 2.2 =
Revised sanitations and translations, tested with WordPress 4.8

= 2.1 =
Added closing and opening of the schedulers in the Customizer

= 2.0 =
Added closing and opening of the schedulers

= 1.2.1 =
Tested with WordPress 4.7

= 1.2 =
Revised uninstall function and tested with WordPress 4.6

= 1.1.0 =
Fixed: not saving settings

= 1.0.1 =
Fixed minor bug

= 1.0.0 =
Initial release.