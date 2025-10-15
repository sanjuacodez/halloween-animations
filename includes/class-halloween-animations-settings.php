<?php
/**
 * Settings functionality for WP Halloween plugin
 *
 * @package Halloween_Animations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings class
 */
class Halloween_Animations_Settings {

    /**
     * Get all settings with defaults
     */
    public static function get_settings() {
        $defaults = array(
            'bats_enabled' => false,
            'bats_count' => 5,
            'bats_speed' => 'medium',
            'ghosts_enabled' => false,
            'ghosts_count' => 3,
            'ghosts_speed' => 'medium',
            'pumpkin_enabled' => false,
            'pumpkin_speed' => 'medium',
            'leaves_enabled' => false,
            'leaves_count' => 10,
            'spiders_enabled' => false,
            'spiders_count' => 2,
            'fog_enabled' => false,
            'display_pages' => array('all'),
            'post_types' => array(),
            'categories' => array(),
            'excluded_pages' => array(),
            'mobile_enabled' => true,
            'sound_enabled' => false,
            'sound_mode' => 'ambient',
            'selected_sounds' => array('spooky-wind.mp3', 'owl-hooting.mp3'),
            'sound_interval' => 15,
            'sound_volume' => 50
        );

        $settings = get_option('halloween_animations_settings', array());
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Get a specific setting
     */
    public static function get_setting($key, $default = null) {
        $settings = self::get_settings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    /**
     * Update a specific setting
     */
    public static function update_setting($key, $value) {
        $settings = self::get_settings();
        $settings[$key] = $value;
        return update_option('halloween_animations_settings', $settings);
    }

    /**
     * Reset all settings to defaults
     */
    public static function reset_settings() {
        return delete_option('halloween_animations_settings');
    }

    /**
     * Export settings
     */
    public static function export_settings() {
        $settings = self::get_settings();
        return json_encode($settings, JSON_PRETTY_PRINT);
    }

    /**
     * Import settings
     */
    public static function import_settings($json_data) {
        $data = json_decode($json_data, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Invalid JSON data', 'halloween-animations'));
        }

        // Validate imported data
        $validated_data = array();
        $allowed_keys = array(
            'bats_enabled', 'bats_count', 'bats_speed',
            'ghosts_enabled', 'ghosts_count', 'ghosts_speed',
            'pumpkin_enabled', 'pumpkin_speed',
            'leaves_enabled', 'leaves_count',
            'spiders_enabled', 'spiders_count',
            'fog_enabled',
            'display_pages', 'post_types', 'categories', 'excluded_pages',
            'mobile_enabled', 'sound_enabled'
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_keys)) {
                $validated_data[$key] = $value;
            }
        }

        if (empty($validated_data)) {
            return new WP_Error('no_valid_data', __('No valid settings found in import data', 'halloween-animations'));
        }

        return update_option('halloween_animations_settings', $validated_data);
    }

    /**
     * Get animation speed in milliseconds
     */
    public static function get_speed_ms($speed) {
        switch ($speed) {
            case 'slow':
                return 8000;
            case 'fast':
                return 3000;
            case 'medium':
            default:
                return 5000;
        }
    }

    /**
     * Check if user can manage Halloween settings
     */
    public static function current_user_can_manage() {
        return current_user_can('manage_halloween_animations') || current_user_can('manage_options');
    }
}