<?php
/**
 * Main Notice Bar Class
 *
 * @package HalloweenAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Notice_Bar {

    private static $instance;
    public $settings;
    public $admin_menu;
    public $assets;
    public $display;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_components();
    }

    private function init_components() {
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/notice-bar/class-ha-notice-bar-settings.php';
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/notice-bar/class-ha-notice-bar-assets.php';
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/notice-bar/class-ha-notice-bar-display.php';

        $this->settings = new HA_Notice_Bar_Settings();
        $this->assets = new HA_Notice_Bar_Assets();
        $this->display = new HA_Notice_Bar_Display($this->settings);

        if (is_admin()) {
            require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/notice-bar/admin/class-ha-notice-bar-admin-menu.php';
            $this->admin_menu = new HA_Notice_Bar_Admin_Menu($this->settings);
        }
    }
}
