<?php
/**
 * Plugin Name: Seasonal Effects & Notice Bar (formerly Halloween Animations)
 * Plugin URI: https://github.com/sanjuacodez/seasonal-effects
 * Description: Add festive seasonal animations and a versatile notice bar to your WordPress site. Perfect for Halloween, Christmas, New Year, Black Friday, and any special occasion!
 * Version: 2.3.0
 * Author: Sanjay Shankar
 * Author URI: https://sanjayshankar.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: halloween-animations
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 *
 * @package Halloween_Animations
 * @author Sanjay Shankar <me@sanjayshankar.me>
 * @link https://sanjayshankar.me
 * @link https://github.com/sanjuacodez
 * @link https://profiles.wordpress.org/sanjuacodez/
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants (keep backward compatibility)
if (!defined('HALLOWEEN_ANIMATIONS_VERSION')) {
    define('HALLOWEEN_ANIMATIONS_VERSION', '2.3.0');
}
if (!defined('HALLOWEEN_ANIMATIONS_PLUGIN_DIR')) {
    define('HALLOWEEN_ANIMATIONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('HALLOWEEN_ANIMATIONS_PLUGIN_URL')) {
    define('HALLOWEEN_ANIMATIONS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('HALLOWEEN_ANIMATIONS_PLUGIN_FILE')) {
    define('HALLOWEEN_ANIMATIONS_PLUGIN_FILE', __FILE__);
}

/**
 * Main plugin class - Backward compatible
 */
class Halloween_Animations {
    
    private static $instance = null;
    private $admin;
    private $frontend;
    private $notice_bar;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
        $this->check_version_update();
    }
    
    private function load_dependencies() {
        // Load Notice Bar Module (New Architecture)
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/notice-bar/class-ha-notice-bar.php';
        $this->notice_bar = HA_Notice_Bar::get_instance();
        
        // Load Seasonal Effects Module
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/seasonal-effects/class-halloween-animations-admin.php';
        $this->admin = new Halloween_Animations_Admin();
        
        require_once HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/seasonal-effects/class-halloween-animations-frontend.php';
        $this->frontend = new Halloween_Animations_Frontend();
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
        
        // Add admin notice for rebranding
        add_action('admin_notices', array($this, 'show_rebrand_notice'));
    }
    
    /**
     * Check for version updates and migrate settings if needed
     */
    private function check_version_update() {
        $current_version = get_option('halloween_animations_version', '1.0.0');
        
        if (version_compare($current_version, '2.1.0', '<')) {
            $this->migrate_to_v21();
            update_option('halloween_animations_version', '2.1.0');
        }
        
        if (version_compare($current_version, '2.2.0', '<')) {
            $this->migrate_to_v22();
            update_option('halloween_animations_version', '2.2.0');
        }

        if (version_compare($current_version, '2.3.0', '<')) {
            update_option('halloween_animations_version', '2.3.0');
        }
    }
    
    /**
     * Migrate settings for v2.1.0 (backward compatible)
     */
    private function migrate_to_v21() {
        $settings = get_option('halloween_animations_settings', array());
        
        // Add new default settings without overwriting existing ones
        $new_defaults = array(
            'plugin_mode' => 'seasonal', // 'halloween' or 'seasonal'
            'show_rebrand_notice' => true
        );
        
        foreach ($new_defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }
        
        update_option('halloween_animations_settings', $settings);
    }
    
    /**
     * Migrate settings for v2.2.0 (Add all seasonal effects)
     */
    private function migrate_to_v22() {
        $settings = get_option('halloween_animations_settings', array());
        
        // Add seasonal effects defaults without overwriting existing ones
        $seasonal_defaults = array(
            // Black Friday / Cyber Monday
            'sale_tags_enabled' => false,
            'sale_tags_count' => 8,
            'sale_tags_text' => "💰 50% OFF\n🏷️ SALE!\n🛍️ BUY NOW\n💳 75% OFF\n🎁 DEALS\n⚡ FLASH SALE",
            'sale_tags_bg_color' => '#ff0000',
            'sale_tags_text_color' => '#ffffff',
            'shopping_icons_enabled' => false,
            'shopping_icons_count' => 5,
            'matrix_rain_enabled' => false,
            'matrix_rain_count' => 10,
            'glitch_effect_enabled' => false,
            
            // Christmas
            'snowflakes_enabled' => false,
            'snowflakes_count' => 30,
            'snowflakes_speed' => 'medium',
            'snowflakes_size' => 15,
            'christmas_lights_enabled' => false,
            'ornaments_enabled' => false,
            'ornaments_count' => 5,
            
            // New Year
            'fireworks_enabled' => false,
            'fireworks_frequency' => 'medium',
            'confetti_enabled' => false,
            'confetti_count' => 40,
            'balloons_enabled' => false,
            'balloons_count' => 8,
            
            // Valentine's Day
            'hearts_enabled' => false,
            'hearts_count' => 15,
            'heart_confetti_enabled' => false,
            'heart_confetti_count' => 20,
            'pulsating_hearts_enabled' => false,
            'pulsating_hearts_count' => 5,
            
            // Easter
            'easter_eggs_enabled' => false,
            'easter_eggs_count' => 10,
            'bunny_enabled' => false
        );
        
        foreach ($seasonal_defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }
        
        update_option('halloween_animations_settings', $settings);
    }
    
    /**
     * Show one-time rebrand notice to existing users
     */
    public function show_rebrand_notice() {
        $settings = get_option('halloween_animations_settings', array());
        
        if (!isset($settings['show_rebrand_notice']) || !$settings['show_rebrand_notice']) {
            return;
        }
        
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'halloween-animations') === false) {
            return;
        }
        
        ?>
        <div class="notice notice-info is-dismissible" id="halloween-rebrand-notice">
            <h3>🎉 <?php esc_html_e('Plugin Upgraded: Now Supports All Occasions!', 'halloween-animations'); ?></h3>
            <p>
                <strong><?php esc_html_e('Great news!', 'halloween-animations'); ?></strong> 
                <?php esc_html_e('Your Halloween Animations plugin has been upgraded to support all seasonal occasions - Halloween, Christmas, New Year, Black Friday, Valentine\'s Day, and more!', 'halloween-animations'); ?>
            </p>
            <p>
                ✅ <?php esc_html_e('All your existing Halloween settings are preserved', 'halloween-animations'); ?><br>
                ✅ <?php esc_html_e('New Templates tab with 8 quick presets', 'halloween-animations'); ?><br>
                ✅ <?php esc_html_e('Improved interface and better organization', 'halloween-animations'); ?><br>
                ✅ <?php esc_html_e('100% backward compatible - nothing breaks!', 'halloween-animations'); ?>
            </p>
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations&tab=templates')); ?>" class="button button-primary">
                    <?php esc_html_e('Explore New Templates', 'halloween-animations'); ?>
                </a>
                <button type="button" class="button" id="dismiss-rebrand-notice">
                    <?php esc_html_e('Got it, thanks!', 'halloween-animations'); ?>
                </button>
            </p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#dismiss-rebrand-notice, #halloween-rebrand-notice .notice-dismiss').on('click', function() {
                $.post(ajaxurl, {
                    action: 'dismiss_halloween_rebrand_notice',
                    nonce: '<?php echo esc_js(wp_create_nonce('dismiss_rebrand_notice')); ?>'
                });
            });
        });
        </script>
        <?php
        
        add_action('wp_ajax_dismiss_halloween_rebrand_notice', array($this, 'dismiss_rebrand_notice'));
    }
    
    /**
     * Dismiss rebrand notice
     */
    public function dismiss_rebrand_notice() {
        check_ajax_referer('dismiss_rebrand_notice', 'nonce');
        
        $settings = get_option('halloween_animations_settings', array());
        $settings['show_rebrand_notice'] = false;
        update_option('halloween_animations_settings', $settings);
        
        wp_send_json_success();
    }
    
    public function activate() {
        $existing_settings = get_option('halloween_animations_settings');
        
        // Only set defaults if this is a fresh install
        if (false === $existing_settings || !is_array($existing_settings)) {
            $default_settings = array(
                'plugin_enabled' => true,
                'mobile_enabled' => true,
                'display_pages' => array('all'),
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
                'sound_enabled' => false,
                'sound_mode' => 'ambient',
                'sound_volume' => 50,
                'sound_interval' => 15,
                'selected_sounds' => array(),
                'plugin_mode' => 'seasonal',
                'show_rebrand_notice' => false, // Don't show for new installs
                
                // Black Friday / Cyber Monday
                'sale_tags_enabled' => false,
                'sale_tags_count' => 8,
                'shopping_icons_enabled' => false,
                'shopping_icons_count' => 5,
                
                // Christmas
                'snowflakes_enabled' => false,
                'snowflakes_count' => 30,
                'snowflakes_speed' => 'medium',
                'christmas_lights_enabled' => false,
                'ornaments_enabled' => false,
                'ornaments_count' => 5,
                
                // New Year
                'fireworks_enabled' => false,
                'fireworks_frequency' => 'medium',
                'confetti_enabled' => false,
                'confetti_count' => 40,
                'balloons_enabled' => false,
                'balloons_count' => 8,
                
                // Valentine's Day
                'hearts_enabled' => false,
                'hearts_count' => 15,
                'hearts_speed' => 'medium',
                'roses_enabled' => false,
                'roses_count' => 5,
                
                // Easter
                'easter_eggs_enabled' => false,
                'easter_eggs_count' => 10,
                'bunny_enabled' => false
            );
            
            update_option('halloween_animations_settings', $default_settings);
        } else {
            // Existing installation - show rebrand notice
            $existing_settings['show_rebrand_notice'] = true;
            update_option('halloween_animations_settings', $existing_settings);
        }
        
        // Save version
        update_option('halloween_animations_version', HALLOWEEN_ANIMATIONS_VERSION);
        
        // flush_rewrite_rules(); // Removed to prevent potential issues in Playground
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('halloween-animations', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function add_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=halloween-animations') . '">' . __('Settings', 'halloween-animations') . '</a>';
        $notice_bar_link = '<a href="' . admin_url('admin.php?page=ha-notice-bar') . '">' . __('Notice Bar', 'halloween-animations') . '</a>';
        $templates_link = '<a href="' . admin_url('admin.php?page=halloween-animations&tab=templates') . '" style="color: #d63638; font-weight: bold;">' . __('Templates', 'halloween-animations') . '</a>';
        
        array_unshift($links, $templates_link, $notice_bar_link, $settings_link);
        return $links;
    }
}

// Initialize the plugin
function halloween_animations_init() {
    return Halloween_Animations::get_instance();
}

// Start the plugin
halloween_animations_init();