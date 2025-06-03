=== Lock Last Modified Date ===
Contributors: nextfly
Tags: modified date, last modified date, prevent modified date, lock modified date, skip modified date
Tested up to: 6.8
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

With this plugin, you can prevent the last modified date from being updated for minor edits, such as fixing typos or updating thumbnails.

== Description ==

Lock Last Modified Date allows you to control when your post's modified date gets updated. This is particularly useful when:

* Making minor typo corrections
* Updating thumbnails or images
* Making small formatting changes
* Any other minor edits where you don't want to update the last modified date

= Features =

* Compatible with both Classic Editor and Gutenberg
* Simple toggle interface in the post editor
* Maintains the original modified date when enabled
* Works with all post types
* No configuration needed

= Available Filter Hook =
`llmd_modified_time_format( $format )`
Use this filter to customize the date/time format in the editor.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/lock-last-modified-date` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the checkbox in the post editor's publish box to lock/unlock the modified date

== Frequently Asked Questions ==

= Does it support Classic Editor? =
Yes, the plugin is fully compatible with Classic Editor.

= Does it support Gutenberg Editor? =
Yes, the plugin works seamlessly with the Gutenberg editor.

= Where can I find the lock option? =
In Classic Editor: Look for the checkbox in the Publish meta box.
In Gutenberg: Find the toggle in the post settings sidebar under the "Status & Visibility" panel.

== Screenshots ==

1. Classic Editor interface
2. Gutenberg Editor interface

== Changelog ==

= 1.0.0 =
* Initial release
* Added support for both Classic Editor and Gutenberg
* Implemented date locking functionality
* Added filter hook for date format customization

== Upgrade Notice ==

= 1.0.0 =
Initial release with Classic Editor and Gutenberg support.