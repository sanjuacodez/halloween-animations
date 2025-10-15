<?php
/**
 * Frontend functionality for WP Halloween plugin
 *
 * @package Halloween_Animations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend class
 */
class Halloween_Animations_Frontend {

    private $settings;

    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('halloween_animations_settings', array());
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'output_halloween_elements'));
    }

    /**
     * Check if effects should be displayed on current page
     */
    private function should_display_effects() {
        // Check if mobile is disabled and user is on mobile
        if (!$this->get_setting('mobile_enabled') && wp_is_mobile()) {
            return false;
        }

        $display_pages = $this->get_setting('display_pages', array('all'));

        // If 'all' is selected, display everywhere
        if (in_array('all', $display_pages)) {
            return true;
        }

        // Check specific page types
        if (in_array('home', $display_pages) && is_front_page()) {
            return true;
        }

        if (in_array('posts', $display_pages) && is_single()) {
            return true;
        }

        if (in_array('pages', $display_pages) && is_page()) {
            return true;
        }

        // Check custom post types
        $post_types = $this->get_setting('post_types', array());
        if (!empty($post_types)) {
            foreach ($post_types as $post_type) {
                if (is_singular($post_type)) {
                    return true;
                }
            }
        }

        // Check categories
        $categories = $this->get_setting('categories', array());
        if (!empty($categories) && is_category()) {
            $current_cat = get_queried_object();
            if ($current_cat && in_array('cat_' . $current_cat->term_id, $categories)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get setting value with default
     */
    private function get_setting($key, $default = false) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        if (!$this->should_display_effects()) {
            return;
        }

        // Check if any animation is enabled
        $animations_enabled = false;
        $animations = array('bats', 'ghosts', 'pumpkin', 'leaves', 'spiders', 'fog');
        
        foreach ($animations as $animation) {
            if ($this->get_setting($animation . '_enabled')) {
                $animations_enabled = true;
                break;
            }
        }

        if (!$animations_enabled) {
            return;
        }

        wp_enqueue_script(
            'halloween-animations-frontend',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/halloween-animations.js',
            array('jquery'),
            HALLOWEEN_ANIMATIONS_VERSION,
            true
        );

        wp_enqueue_style(
            'halloween-animations-frontend',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/halloween-animations.css',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );

                // Localize script with settings
        wp_localize_script('halloween-animations-frontend', 'halloween_animations_frontend', array(
            'settings' => $this->get_animation_settings(),
            'pluginUrl' => HALLOWEEN_ANIMATIONS_PLUGIN_URL
        ));
        
        // Enhanced sound settings localization
        wp_localize_script('halloween-animations-frontend', 'halloween_animations_ajax', array(
            'pluginUrl' => HALLOWEEN_ANIMATIONS_PLUGIN_URL,
            'soundSettings' => array(
                'enabled' => $this->get_setting('sound_enabled', false),
                'mode' => $this->get_setting('sound_mode', 'ambient'),
                'selectedSounds' => $this->get_setting('selected_sounds', array('spooky-wind.mp3', 'owl-hooting.mp3')),
                'interval' => $this->get_setting('sound_interval', 15),
                'volume' => $this->get_setting('sound_volume', 50)
            )
        ));

        // Always enqueue enhanced sound system for debugging and user interaction
        wp_enqueue_script(
            'halloween-animations-enhanced-sounds',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/enhanced-sounds.js',
            array('halloween-animations-frontend', 'jquery'),
            HALLOWEEN_ANIMATIONS_VERSION,
            true
        );
    }

    /**
     * Output Halloween HTML elements
     */
    public function output_halloween_elements() {
        if (!$this->should_display_effects()) {
            return;
        }

        echo '<div id="halloween-animations-container">';

        // Bats
        if ($this->get_setting('bats_enabled')) {
            $bat_count = $this->get_setting('bats_count', 5);
            echo '<div id="halloween-bats" data-count="' . esc_attr($bat_count) . '" data-speed="' . esc_attr($this->get_setting('bats_speed', 'medium')) . '"></div>';
        }

        // Ghosts
        if ($this->get_setting('ghosts_enabled')) {
            $ghost_count = $this->get_setting('ghosts_count', 3);
            echo '<div id="halloween-ghosts" data-count="' . esc_attr($ghost_count) . '" data-speed="' . esc_attr($this->get_setting('ghosts_speed', 'medium')) . '">';
            for ($i = 0; $i < $ghost_count; $i++) {
                echo '<div class="halloween-ghost" data-ghost="' . esc_attr($i) . '">👻</div>';
            }
            echo '</div>';
        }

        // Running Pumpkin
        if ($this->get_setting('pumpkin_enabled')) {
            echo '<div id="halloween-pumpkin" data-speed="' . esc_attr($this->get_setting('pumpkin_speed', 'medium')) . '">';
            echo '<div class="halloween-pumpkin">🎃</div>';
            echo '</div>';
        }

        // Falling Leaves
        if ($this->get_setting('leaves_enabled')) {
            $leaves_count = $this->get_setting('leaves_count', 10);
            echo '<div id="halloween-leaves" data-count="' . esc_attr($leaves_count) . '">';
            $leaf_types = array('🍂', '🍁', '🌾');
            for ($i = 0; $i < $leaves_count; $i++) {
                $leaf = $leaf_types[array_rand($leaf_types)];
                echo '<div class="halloween-leaf" data-leaf="' . esc_attr($i) . '">' . esc_html($leaf) . '</div>';
            }
            echo '</div>';
        }

        // Crawling Spiders
        if ($this->get_setting('spiders_enabled')) {
            $spider_count = $this->get_setting('spiders_count', 2);
            echo '<div id="halloween-spiders" data-count="' . esc_attr($spider_count) . '">';
            for ($i = 0; $i < $spider_count; $i++) {
                echo '<div class="halloween-spider" data-spider="' . esc_attr($i) . '">🕷️</div>';
            }
            echo '</div>';
        }

        // Spooky Fog
        if ($this->get_setting('fog_enabled')) {
            echo '<div id="halloween-fog">';
            // Create multiple fog particles for more realistic effect
            for ($i = 1; $i <= 8; $i++) {
                echo '<div class="fog-particle fog-particle-' . esc_attr($i) . '"></div>';
            }
            echo '</div>';
        }

        echo '</div>';

        // Enhanced sound system handles all audio loading via JavaScript
        // No need for HTML audio elements as the enhanced system dynamically loads selected sounds
    }

    /**
     * Get animation settings for JavaScript
     *
     * @return array Animation settings
     */
    private function get_animation_settings() {
        return array(
            'bats' => array(
                'enabled' => $this->get_setting('bats_enabled', true),
                'count' => $this->get_setting('bats_count', 5),
                'speed' => $this->get_setting('bats_speed', 'medium')
            ),
            'ghosts' => array(
                'enabled' => $this->get_setting('ghosts_enabled', true),
                'count' => $this->get_setting('ghosts_count', 3),
                'speed' => $this->get_setting('ghosts_speed', 'medium')
            ),
            'pumpkin' => array(
                'enabled' => $this->get_setting('pumpkin_enabled', true),
                'speed' => $this->get_setting('pumpkin_speed', 'medium')
            ),
            'leaves' => array(
                'enabled' => $this->get_setting('leaves_enabled', true),
                'count' => $this->get_setting('leaves_count', 10)
            ),
            'spiders' => array(
                'enabled' => $this->get_setting('spiders_enabled', true),
                'count' => $this->get_setting('spiders_count', 2)
            ),
            'fog' => array(
                'enabled' => $this->get_setting('fog_enabled', true)
            )
        );
    }
}