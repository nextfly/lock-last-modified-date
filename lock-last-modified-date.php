<?php
/**
 * Plugin Name: Lock Last Modified Date
 * Plugin URI: https://github.com/nextfly/lock-last-modified-date/
 * Description: Prevent last modified date updates for minor edits. Compatible with Classic Editor and Gutenberg.
 * Version: 1.0.0
 * Author: NEXTFLY® Web Design
 * Author URI: https://nextflywebdesign.com/
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * License: GPLv3 or later
 * Text Domain: lock-last-modified-date
 *
 * @package LockLastModifiedDate
 * @since 1.0.0
 * @author NEXTFLY® Web Design
 * @link https://nextflywebdesign.com/
 */

defined('ABSPATH') || exit;

/**
 * Main class for the Lock Last Modified Date plugin.
 *
 * @package LockLastModifiedDate
 * @since 1.0.0
 */
final class LockLastModifiedDate {
    /**
     * Meta key for the last modified date lock.
     *
     * @var string
     */
    private const META_KEY = '_llmd_date_locked';

    /**
     * Instance of LockLastModifiedDate.
     *
     * @var LockLastModifiedDate|null
     */
    private static ?LockLastModifiedDate $instance = null;

    /**
     * Singleton pattern implementation
     *
     * @since 1.0.0
     */
    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    /**
     * Private constructor to prevent direct instantiation
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->initHooks();
    }

    /**
     * Initialize WordPress hooks
     *
     * @since 1.0.0
     */
    private function initHooks(): void {
        // Classic Editor.
        add_action('post_submitbox_misc_actions', [$this, 'renderLockModifiedDateCheckbox']);

        // Shared.
        add_action('wp_insert_post_data', [$this, 'handleModifiedDateUpdate'], 10, 2);
        add_filter('llmd_modified_time_format', [$this, 'getDateTimeFormat']);

        // Gutenberg.
        if ($this->isGutenbergActive()) {
            add_action('init', [$this, 'registerMetaFields']);
            add_action('enqueue_block_editor_assets', [$this, 'enqueueGutenbergAssets']);
        }
    }

    /**
     * Check if Gutenberg is active.
     *
     * @since 1.0.0
     *
     * @return bool True if Gutenberg is active, false otherwise.
     */
    private function isGutenbergActive(): bool {
        return function_exists('register_block_type');
    }

    /**
     * Get the datetime format for the modified date
     *
     * @since 1.0.0
     *
     * @param string $format Optional. The format to use.
     * @return string The datetime format.
     */
    public function getDateTimeFormat(string $format = ''): string {
        if (empty($format)) {
            $format = get_option('date_format') . ' ' . get_option('time_format');
        }
        return $format;
    }

    /**
     * Render the lock modified date checkbox in post editor
     *
     * @since 1.0.0
     */
    public function renderLockModifiedDateCheckbox(): void {
        global $post;

        // Get WordPress date and time format settings.
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $datetime_format = $date_format . ' ' . $time_format;

        // Apply filter to allow customization.
        $datetime_format = apply_filters('llmd_modified_time_format', $datetime_format);

        $lastModified = get_the_modified_time($datetime_format, $post);
        $isLocked = get_post_meta($post->ID, self::META_KEY, true);

        require_once plugin_dir_path(__FILE__) . 'templates/checkbox.php';
    }

    /**
     * Handle the modified date update
     *
     * @since 1.0.0
     *
     * @param array $data The data array.
     * @param array $postarr The post array.
     * @return array The modified data array.
     */
    public function handleModifiedDateUpdate(array $data, array $postarr): array {
        if (!isset($postarr['ID'])) {
            return $data;
        }

        // Check user permissions first - user must be able to edit the specific post
        if (!current_user_can('edit_post', $postarr['ID'])) {
            return $data;
        }

        $shouldLock = false;

        if ((isset($postarr['post_content']) && has_blocks($postarr['post_content'])) && wp_is_serving_rest_request()) {
            // For REST API requests (Block Editor), verify nonce from headers
            $nonce = null;
            
            // WordPress sends nonce in X-WP-Nonce header for REST requests
            if (isset($_SERVER['HTTP_X_WP_NONCE'])) {
                $nonce = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_WP_NONCE']));
            }
            
            // Verify the REST nonce
            if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
                // If nonce verification fails, keep existing value
                $shouldLock = (bool) get_post_meta($postarr['ID'], self::META_KEY, true);
            } else {
                // Get current meta value
                $currentLockState = get_post_meta($postarr['ID'], self::META_KEY, true);

                // Safely get and sanitize the REST request data
                $rawInput = file_get_contents('php://input');
                $restRequest = null;
                $incomingLockState = null;
                
                // Validate and sanitize JSON input
                if (!empty($rawInput)) {
                    $restRequest = json_decode($rawInput, true);
                    
                    // Validate JSON was parsed successfully and is an array
                    if (json_last_error() === JSON_ERROR_NONE && is_array($restRequest)) {
                        // Check if meta exists and is an array
                        if (isset($restRequest['meta']) && is_array($restRequest['meta'])) {
                            // Check if our specific meta key exists
                            if (array_key_exists(self::META_KEY, $restRequest['meta'])) {
                                $rawValue = $restRequest['meta'][self::META_KEY];
                                
                                // Sanitize and validate the boolean value
                                if (is_bool($rawValue)) {
                                    $incomingLockState = $rawValue;
                                } elseif (is_string($rawValue)) {
                                    // Sanitize string input and convert to boolean
                                    $sanitizedValue = sanitize_text_field($rawValue);
                                    $incomingLockState = filter_var($sanitizedValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                                } elseif (is_numeric($rawValue)) {
                                    // Handle numeric input (0/1)
                                    $incomingLockState = (bool) intval($rawValue);
                                }
                                // If none of the above, $incomingLockState remains null (invalid data)
                            }
                        }
                    }
                }

                // Use incoming value if valid, otherwise use current value
                $shouldLock = $incomingLockState ?? $currentLockState;
            }
        } else {
            // Verify nonce for Classic Editor submissions
            if (isset($_POST['lock_modified_date_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['lock_modified_date_nonce'])), 'lock_modified_date_action')) {
                $shouldLock = filter_var(isset($_POST['lock_modified_date']) && sanitize_text_field(wp_unslash($_POST['lock_modified_date'])) === 'on', FILTER_VALIDATE_BOOLEAN);
                update_post_meta($postarr['ID'], self::META_KEY, esc_attr($shouldLock));
            } else {
                // If no valid nonce, keep existing value
                $shouldLock = (bool) get_post_meta($postarr['ID'], self::META_KEY, true);
            }
        }

        if ($shouldLock) {
            unset($data['post_modified'], $data['post_modified_gmt']);
        }

        return $data;
    }

    /**
     * Register meta fields for Gutenberg.
     *
     * @since 1.0.0
     */
    public function registerMetaFields(): void {
        register_post_meta('', self::META_KEY, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    /**
     * Enqueue Gutenberg assets.
     *
     * @since 1.0.0
     */
    public function enqueueGutenbergAssets(): void {
        $asset_file = include plugin_dir_path(__FILE__) . 'build/gutenberg.asset.php';

        wp_enqueue_script(
            'llmd-gutenberg',
            plugins_url('build/gutenberg.js', __FILE__),
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );

        wp_localize_script('llmd-gutenberg', 'llmdData', [
            'metaKey' => self::META_KEY
        ]);
    }

    /**
     * Prevent cloning of the instance
     *
     * @since 1.0.0
     */
    private function __clone() {
    }

    /**
     * Prevent unserializing of the instance
     *
     * @since 1.0.0
     */
    public function __wakeup() {
    }
}

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
add_action('plugins_loaded', [LockLastModifiedDate::class, 'getInstance']);
