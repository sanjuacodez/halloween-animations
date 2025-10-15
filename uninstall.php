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
$roles = array('administrator', 'editor');
foreach ($roles as $role_name) {
    $role = get_role($role_name);
    if ($role) {
        $role->remove_cap('manage_halloween_animations');
    }
}

// Clean up any scheduled events (if we had any)
wp_clear_scheduled_hook('halloween_animations_cleanup');

// Remove any custom database tables (if we had any)
global $wpdb;
// Example: $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}halloween_stats");

// Clear any cached data
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}