=== oik-rwd ===
Contributors: bobbingwide
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: shortcodes, smart, lazy
Requires at least: 3.9
Tested up to: 6.4-RC1
Stable tag: 0.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Use the oik responsive web design plugin to dynamically create responsive CSS classes for width and height. 
Use the [bw_rwd] shortcode to define the CSS class names that will be used in the content

Examples:

[bw_rwd class=w50m5,w50m1p2 ]

will generate responsive classes as follows:
w50m5 - initial total width of 50% with 5% margin-right
w50m1p2 - initial total width of 50% with 1% margin-right and 2% padding

As the window gets sized then automatically generated media queries adjust the width, padding and margin
to provide some responsive behaviour. 

[bw_rwd] without any parameters will enable automatic responsive class interception on any shortcode.

If you want automatic responsive class interception to happen site wide then visit oik options > Responsive Web Design and ensure "Intercept classes" is checked.

 

== Installation ==
1. Upload the contents of the oik-rwd plugin to the `/wp-content/plugins/oik-rwd' directory
1. Activate the oik-rwd plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= What class names can I use? =

For dynamic width classes the format of the class name is wnnn followed by

pnn / mnn / pnnmnn / mnnpnn

where codes for padding and margin are:
p = padding left and right
m = margin left and right

l = padding/margin left
r = padding/margin right

nnn = required width in percentage e.g. (50) Expected values from 0 to 100
 

For width classes if a value is not specified then it is set to 0.
   

For dynamic height classes the format of the class name is hnnn followed by 
pnn / mnn / pnnmnn / mnnpnn

where the codes are:

p = padding top and bottom
m = margin top and bottom 

t = padding/margin top
b = padding/margin bottom

For height classes the CSS property: value declaration is not created when not specified
This allows for multiple class names to be combined
   

= Which devices are supported? =
In version 0.2 the media queries that are generated are hardcoded.
The primary declaration is intended for devices with a max-width exceeding 768 pixels
For each class three media query definitions are created:
@media screen and ( max-width: 768px ) - tablet portrait
@media screen and ( max-width: 480px ) - smart phone landscape
@media screen and ( max-width: 320px ) - smart phone portrait

= Is the code device aware? =
No, not in this version. 


= What if I need borders? =
The code does not allow for borders.
If you need these then you should allow for this yourself when specifying the width.

Alternatively use the CSS outline property.
Use the oik-css plugin, oik custom CSS or another mechanism to define the CSS for the border settings.



== Screenshots ==
1. oik-rwd in action - width 1366 pixels
2. oik-rwd in action - width 571 pixels
3. oik-rwd in action - width 640 pixels
4. oik-rwd in action - iPhone display

== Upgrade Notice ==
= 0.5.3 = 
Update for support for PHP 8.1 and PHP 8.2

= 0.5.2 =
Tested with WordPress 4.7.3.

= 0.5.1 =
Tested with WordPress 4.5.2. Now dependent upon oik v3.0.0 or higher.

= 0.5 = 
Required where sidebars and main body margins need to be taken into account. Now dependent upon oik v2.3

= 0.4 = 
Required if you want to use padding or margins greater than 10%.

= 0.3 = 
Required for [bw_rpt] shortcode in oik-bob-bing-wide plugin. Tested up to WordPress 3.9.1

= 0.2 =
Never officially released.

= 0.1 =
This version is dependent upin the oik base plugin

== Changelog == 
= 0.5.3 = 
* Changed: Support PHP 8.1 and PHP 8.2 #2
* Tested: With WordPress 6.4-RC1 and WordPress Multisite
* Tested: With PHP 8.0, PHP 8.1 and PHP 8.2
* Tested: With PHPUnit 9.6

= 0.5.2 = 
* Tested: With WordPress 4.7.3, added assets

= 0.5.1 =
* Changed: Now dependent upon oik v3.0.0 or higher
* Tested: With WordPress 4.5.2 and WordPress MultiSite

= 0.5 =
* Added: Window width breakpoint (px ) field to specify the max-width setting when it needs to be higher than 768px.
* Changed: oik_rwd_default_media_rules() uses oik_rwd_adjusted_max_width_for_context() to determine the user defined breakpoint

= 0.4 = 
* Fixed: Infinite loop when padding or margin exceeded the mapping values. [api oik_rwd_apply_width_mapping].

= 0.3 = 
* Changed: [bw_rwd] shortcode now accepts positional parameter 0 for the class names
* Changed: [bw_rwd] with no parameters will force "Intercept classes" for the rest of the page
* Added: Allows setting of the Intercept classes option
* Changed: Dependency logic. Now dependent upon oik v2.2
* Changed: Implements "oik_add_shortcodes" action for lazy loading of shortcodes
* Added: Implements "oik_responsive_column_class" filter-hook to dynamically alter non responsive classes ( e.g. w50pc ) to responsive
* Added: Admin menu page ( admin/oik-rwd.php )
* Added: For future use - oik-settings.php - OO implementation for handling oik settings - temporary location
* Added: For future use - oik-rwd-mq.php - OIK_rwd_mq_settings class - Media Query settings for oik-rwd 

= 0.2 =  
* Changed: Tweaks to the media queries for better behaviour on small devices.

= 0.1 =
* Added: New plugin implementing the [bw_rwd] shortcode

== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](http://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**

