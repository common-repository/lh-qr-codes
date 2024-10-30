=== LH QR Codes ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-qr-codes/
Tags: Login, QR Code, thumbnail, self hosted, svg, png
Requires at least: 4.0
Tested up to: 4.9
Stable tag: trunk

Provides the full set of QR functionality for your WordPress site

== Description ==

Embed QR codes throughout your site using template functions and shortcodes. All QR codes are configurable and are generated and hosted locally (unlike other plugins) and use the SVG format (to minimise bandwidth).

== Installation ==

1. Upload plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the template function or shortcode, see the FAQ for usage.

== Frequently Asked Questions ==

= How do I include a QR code on my site? =

QR codes can either be included by using the new template function the_post_qrcode(), which functions very similarly to the native template function the_post_thumbnail(), or insert the shortcode [lh_qr_code] into a post, page, or cpt, or finally use the LH QR Code wiget to display a QR code in your sidebar or other widgetable areas.


= What arguments can I pass to the the_post_qrcode() function? =
The function takes two arguments, both are optional. The first is $size, this is an integer value of the height and width of the generated QR Codes, if not specied the svg image created will be 150 pixels. The second is $attribute, this is an associative array with the following keys:

fore_color; the foreground colour of the QR, in hex, if not specified black (0x000000) is used

back_color; the background colour of the QR, in hex, if not specified white (0xFFFFFF) is used

margin; the margin around the edge of the QR, in pixels, if not specified it is 3 pixels

= What attributes can be passed to the shortcode? =
The [lh_qr_code] shortcode takes the following attributes

text; the text that the QR code is displaying, if it is not specified the permalink of the post/page that shortocde is being used on will be what the QR code dsiplays. However you could change it to an email address or the text contents of a Vcard file for example.

urlencode; whether to pass the text (set above) or permalink through a urlencode function, by default it does not, but if you need to urlencode a string eg a vcard string then set urlencode="1"

fore_color; the foreground colour of the QR, in hex, if not specified black (0x000000) is used

format; the format of the QR code, svg or png, if not specified svg is used

back_color; the background colour of the QR, in hex, if not specified white (0xFFFFFF) is used

margin; the margin around the edge of the QR, if not specified it is 3 pixels


== Changelog ==

**1.00 May 14, 2016**  
Initial release.

== Changelog ==

**1.01 June 27, 2016**  
Permalinks regeneration and widget added.

**1.02 June 28, 2016**  
Allow QR codes produced by shortcode to be png as well as SVG

**1.03 June 28, 2016**  
Fix for those with Illegal string offset (hopefully)

**1.04 March 03, 2017**  
Added urlencode attribute

**1.05 March 30, 2017**  
Use isset

**1.06 March 04, 2018**  
Background and foreground color fix