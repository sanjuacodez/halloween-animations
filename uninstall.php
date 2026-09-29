<?php
/**
 * Uninstall script for Halloween plugin
 * 
 * @package Halloween_Animations
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('halloween_animations_settings');

// Remove any transients
delete_transient('halloween_animations_cache');

// Remove capabilities from roles
function halloween_animations_remove_capabilities() {
    $ha_roles = array('administrator', 'editor');
    foreach ($ha_roles as $ha_role_name) {
        $ha_role = get_role($ha_role_name);
        if ($ha_role) {
            $ha_role->remove_cap('manage_halloween_animations');
        }
    }
}
halloween_animations_remove_capabilities();

// Clean up any scheduled events (if we had any)
wp_clear_scheduled_hook('halloween_animations_cleanup');

// Remove any custom database tables (if we had any)
global $wpdb;
// Example: $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}halloween_stats");

// Clear any cached data
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}