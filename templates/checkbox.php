<?php
/**
 * Template for the last modified date lock checkbox
 *
 * @package LockLastModifiedDate
 * @since 1.0.0
 */

defined('ABSPATH') || exit; ?>

<div class="misc-pub-section misc-pub-lock-last-modified-date">
    <div class="last-modified-timestamp">
        <span class="dashicons dashicons-clock"></span>
        <?php printf(
            /* translators: %s: last modified date */
            esc_html__('Last modified on %s', 'lock-last-modified-date'),
            sprintf('<strong>%s</strong>', esc_html($lastModified))
        ); ?>
    </div>
    <div class="lock-modified-date-control">
        <?php wp_nonce_field('nextfly_llmd_lock_modified_date_action', 'nextfly_llmd_lock_modified_date_nonce'); ?>
        <input 
            type="checkbox" 
            name="nextfly_llmd_lock_modified_date" 
            id="nextfly_llmd_lock_modified_date"
            <?php checked($isLocked); ?>
        >
        <label for="nextfly_llmd_lock_modified_date">
            <?php esc_html_e('Lock Last Modified Date', 'lock-last-modified-date'); ?>
        </label>
    </div>
</div>