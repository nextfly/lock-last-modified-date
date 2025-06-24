# Lock Last Modified Date - WordPress Plugin

With this plugin, you can prevent the last modified date from being updated for minor edits, such as fixing typos or updating thumbnails.

## Features

* Compatible with both Classic Editor and Gutenberg
* Simple toggle interface in the post editor
* Maintains the original modified date when enabled
* Works with all post types
* No configuration needed

## Requirements

* WordPress 5.0 or higher
* PHP 7.4 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/lock-last-modified-date` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the checkbox in the post editor's publish box to lock/unlock the modified date

## Developer Notes

### Available Filter Hook

`nextfly_llmd_modified_time_format( $format )`
Use this filter to customize the date/time format in the editor.

## Current Version: 1.0.0