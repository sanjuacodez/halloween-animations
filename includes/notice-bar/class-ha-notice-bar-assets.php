<?php
/**
 * Assets Class
 *
 * @package HalloweenAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Notice_Bar_Assets {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'ha-notice-bar',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'includes/notice-bar/assets/css/banner.css',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );
    }
}
