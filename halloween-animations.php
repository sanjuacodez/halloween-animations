<?php
/**
 * Plugin Name: Halloween Animations
 * Plugin URI: https://github.com/sanjuacodez/halloween-animations
 * Description: Add spooky Halloween animations to your WordPress site including flying bats, floating ghosts, falling leaves, and more with full admin control.
 * Version: 1.0.0
 * Author: Sanjay Shankar
 * Author URI: https://sanjayshankar.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: halloween-animations
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 *
 * @package Halloween_Animations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HALLOWEEN_ANIMATIONS_VERSION', '1.0.0');
define('HALLOWEEN_ANIMATIONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HALLOWEEN_ANIMATIONS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HALLOWEEN_ANIMATIONS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class Halloween_Animations {

    /**
     * The single instance of the class
     */
    private static $instance = null;

    /**
     * Ensures only one instance of the plugin is loaded
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'), 0);
        
        // Plugin action links
        add_filter('plugin_action_links_' . HALLOWEEN_ANIMATIONS_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Load required files
        $this->includes();
        
        // Initialize admin
        if (is_admin()) {
            new Halloween_Animations_Admin();
        }
        
        // Initialize frontend
        if (!is_admin()) {
            new Halloween_Animations_Frontend();
        }
    }

    /**
     * Include required files
     */
    private function includes() {
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/class-halloween-animations-admin.php';
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/class-halloween-animations-frontend.php';
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/class-halloween-animations-settings.php';
    }

    /**
     * Add settings link to plugin action links
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=halloween-animations') . '">' . __('Settings', 'halloween-animations') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add additional links to plugin row meta
     */
    public function plugin_row_meta($links, $file) {
        if (HALLOWEEN_ANIMATIONS_PLUGIN_BASENAME === $file) {
            $row_meta = array(
                'docs' => '<a href="https://github.com/sanjuacodez/halloween-animations#readme" target="_blank">' . __('Documentation', 'halloween-animations') . '</a>',
                'support' => '<a href="https://wordpress.org/support/plugin/halloween-animations/" target="_blank">' . __('Support', 'halloween-animations') . '</a>',
                'review' => '<a href="https://wordpress.org/support/plugin/halloween-animations/reviews/#new-post" target="_blank">' . __('Write a Review', 'halloween-animations') . '</a>'
            );
            
            return array_merge($links, $row_meta);
        }
        
        return $links;
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
            'bats_enabled' => false,
            'bats_count' => 5,
            'bats_speed' => 'medium',
            'ghosts_enabled' => false,
            'ghosts_count' => 3,
            'pumpkin_enabled' => false,
            'leaves_enabled' => false,
            'leaves_count' => 10,
            'spiders_enabled' => false,
            'spiders_count' => 2,
            'fog_enabled' => false,
            'display_pages' => array('all'),
            'excluded_pages' => array(),
            'mobile_enabled' => true,
            'sound_enabled' => false
        );
        
        add_option('halloween_animations_settings', $default_options);
        
        // Add capability to administrator
        $role = get_role('administrator');
        if ($role) {
            $role->add_cap('manage_halloween_animations');
        }
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Remove capability
        $role = get_role('administrator');
        if ($role) {
            $role->remove_cap('manage_halloween_animations');
        }
    }
}

/**
 * Initialize the plugin
 */
function halloween_animations() {
    return Halloween_Animations::instance();
}

// Start the plugin
halloween_animations();