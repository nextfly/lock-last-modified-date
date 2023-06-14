<?php
/*
Plugin Name: Lock Last Modifed Date
Plugin URI: https://github.com/nextfly/lock-last-modified-date/
Description: With this plugin, you can prevent the last modified date from being updated for minor edits, such as fixing typos or updating thumbnails. <strong><em>Compatible with Classic Editor only.</em></strong>
Version: 0.0.1
Author: Nextfly
Author URI: https://nextflywebdesign.com/
Requires at least: 3.8
Tested up to: 6.2.2
License: GPLv3 or later
Text Domain: nextfly-llmd
*/

// Exit if accessed directly
if (!defined('ABSPATH'))    exit;

if( !class_exists('LockLastModifiedDate') ) :
    class LockLastModifiedDate{
        
        public function __construct() {
            add_action( 'post_submitbox_misc_actions', array( $this, 'add_lock_modified_date_checkbox' ) );
            add_action( 'wp_insert_post_data', array( $this, 'lock_modified_date_update' ), 10, 2 );
        }
        
        /**
         * Add last modified date and checkbox to post edit page
         * 
         * @since 0.0.1
         */
        public function add_lock_modified_date_checkbox(){
            global $post;

            $last_modified = get_the_modified_time( apply_filters( 'nextfly_llmd_modified_time_format', 'F j, Y g:i a' ), $post );
        ?>
            <div class="misc-pub-section misc-pub-lock-last-modified-date">
                <div class="last-modified-timestamp" style="margin-bottom: 5px;">
                    <?php echo sprintf( __( 'Last modified on <strong>%s</strong>', 'nextfly-llmd' ), $last_modified ); ?>
                </div>
                <input type="checkbox" name="lock_modified_date" id="lock_modified_date">
                <label for="lock_modified_date"><?php echo __('Lock Last Modifed Date', 'nextfly-llmd') ?></label>
            </div>
        <?php
        }

        /**
         * Prevent updating modified date if checkbox is checked
         * 
         * @since 0.0.1
         */
        function lock_modified_date_update( $data, $postarr ) {
            if( isset( $_POST['lock_modified_date'] ) ){
                unset($data['post_modified']);
                unset($data['post_modified_gmt']);
            }
            return $data;
        }

    }

    // Instantiate the plugin
    $LockLastModifiedDate = new LockLastModifiedDate();

endif;