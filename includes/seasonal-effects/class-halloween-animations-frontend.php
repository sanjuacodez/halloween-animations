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
        // Check if plugin is disabled globally
        if (!$this->get_setting('plugin_enabled', true)) {
            return false;
        }
        
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
     * Check if any seasonal effect is enabled
     */
    private function check_seasonal_effects_enabled() {
        $effects = array(
            'sale_tags', 'shopping_icons', 'matrix_rain', 'glitch_effect',
            'snowflakes', 'christmas_lights', 'ornaments',
            'fireworks', 'confetti', 'balloons',
            'hearts', 'heart_confetti', 'pulsating_hearts',
            'easter_eggs', 'bunny'
        );
        
        foreach ($effects as $effect) {
            if ($this->get_setting($effect . '_enabled')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        if (!$this->should_display_effects()) {
            return;
        }

        // Check if any animation is enabled (Halloween)
        $animations_enabled = false;
        $animations = array('bats', 'ghosts', 'pumpkin', 'leaves', 'spiders', 'fog');
        
        foreach ($animations as $animation) {
            if ($this->get_setting($animation . '_enabled')) {
                $animations_enabled = true;
                break;
            }
        }

        // Check if any seasonal effect is enabled
        $seasonal_effects_enabled = $this->check_seasonal_effects_enabled();

        if (!$animations_enabled && !$seasonal_effects_enabled) {
            return;
        }

        // Enqueue seasonal effects CSS (consolidated) - needed for both Halloween and seasonal effects
        wp_enqueue_style(
            'seasonal-effects-all',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/seasonal-effects-all.css',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );

        // Enqueue Halloween animations if enabled
        if ($animations_enabled) {
            wp_enqueue_script(
                'halloween-animations-frontend',
                HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/halloween-animations.js',
                array('jquery'),
                HALLOWEEN_ANIMATIONS_VERSION,
                true
            );
        }

        // Enqueue seasonal effects JS if any seasonal effect is enabled
        if ($seasonal_effects_enabled) {
            wp_enqueue_script(
                'seasonal-effects-all',
                HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/seasonal-effects-all.js',
                array('jquery'),
                HALLOWEEN_ANIMATIONS_VERSION,
                true
            );

            // Localize seasonal effects settings
            wp_localize_script('seasonal-effects-all', 'seasonalEffectsSettings', array(
                // Black Friday / Cyber Monday
                'sale_tags_enabled' => $this->get_setting('sale_tags_enabled', false),
                'sale_tags_count' => $this->get_setting('sale_tags_count', 8),
                'sale_tags_text' => $this->get_setting('sale_tags_text', "💰 50% OFF\n🏷️ SALE!\n🛍️ BUY NOW\n💳 75% OFF\n🎁 DEALS\n⚡ FLASH SALE"),
                'sale_tags_bg_color' => $this->get_setting('sale_tags_bg_color', '#ff0000'),
                'sale_tags_text_color' => $this->get_setting('sale_tags_text_color', '#ffffff'),
                'shopping_icons_enabled' => $this->get_setting('shopping_icons_enabled', false),
                'shopping_icons_count' => $this->get_setting('shopping_icons_count', 5),
                'matrix_rain_enabled' => $this->get_setting('matrix_rain_enabled', false),
                'matrix_rain_count' => $this->get_setting('matrix_rain_count', 10),
                'glitch_effect_enabled' => $this->get_setting('glitch_effect_enabled', false),
                
                // Christmas
                'snowflakes_enabled' => $this->get_setting('snowflakes_enabled', false),
                'snowflakes_count' => $this->get_setting('snowflakes_count', 30),
                'snowflakes_speed' => $this->get_setting('snowflakes_speed', 'medium'),
                'snowflakes_size' => $this->get_setting('snowflakes_size', 15),
                'christmas_lights_enabled' => $this->get_setting('christmas_lights_enabled', false),
                'ornaments_enabled' => $this->get_setting('ornaments_enabled', false),
                'ornaments_count' => $this->get_setting('ornaments_count', 5),
                
                // New Year
                'fireworks_enabled' => $this->get_setting('fireworks_enabled', false),
                'fireworks_frequency' => $this->get_setting('fireworks_frequency', 'medium'),
                'confetti_enabled' => $this->get_setting('confetti_enabled', false),
                'confetti_count' => $this->get_setting('confetti_count', 40),
                'balloons_enabled' => $this->get_setting('balloons_enabled', false),
                'balloons_count' => $this->get_setting('balloons_count', 8),
                
                // Valentine's Day
                'hearts_enabled' => $this->get_setting('hearts_enabled', false),
                'hearts_count' => $this->get_setting('hearts_count', 15),
                'heart_confetti_enabled' => $this->get_setting('heart_confetti_enabled', false),
                'heart_confetti_count' => $this->get_setting('heart_confetti_count', 20),
                'pulsating_hearts_enabled' => $this->get_setting('pulsating_hearts_enabled', false),
                'pulsating_hearts_count' => $this->get_setting('pulsating_hearts_count', 5),
                
                // Easter
                'easter_eggs_enabled' => $this->get_setting('easter_eggs_enabled', false),
                'easter_eggs_count' => $this->get_setting('easter_eggs_count', 10),
                'bunny_enabled' => $this->get_setting('bunny_enabled', false),

                // Mobile detection
                'isMobile' => wp_is_mobile()
            ));
        }

        // Localize Halloween animations script with settings including notice bar position awareness
        if ($animations_enabled) {
            wp_localize_script('halloween-animations-frontend', 'halloween_animations_frontend', array(
                'settings' => $this->get_animation_settings(),
                'pluginUrl' => HALLOWEEN_ANIMATIONS_PLUGIN_URL,
                'noticeBarSettings' => $this->get_notice_bar_position_settings()
            ));
        }
        
        // Enqueue sound system if sound is enabled
        if ($this->get_setting('sound_enabled', false)) {
            // Enqueue enhanced sound system
            wp_enqueue_script(
                'halloween-animations-enhanced-sounds',
                HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/enhanced-sounds.js',
                array('jquery'),
                HALLOWEEN_ANIMATIONS_VERSION,
                true
            );
            
            // Enhanced sound settings localization
            wp_localize_script('halloween-animations-enhanced-sounds', 'halloween_animations_ajax', array(
                'pluginUrl' => HALLOWEEN_ANIMATIONS_PLUGIN_URL,
                'soundSettings' => array(
                    'enabled' => $this->get_setting('sound_enabled', false),
                    'mode' => $this->get_setting('sound_mode', 'ambient'),
                    'selectedSounds' => $this->get_setting('selected_sounds', array('spooky-wind.mp3', 'owl-hooting.mp3')),
                    'interval' => $this->get_setting('sound_interval', 15),
                    'volume' => $this->get_setting('sound_volume', 50)
                )
            ));
        }
    }

    /**
     * Get notice bar position settings to adjust animations accordingly
     *
     * @return array Notice bar position settings
     */
    private function get_notice_bar_position_settings() {
        $notice_bar_options = get_option('notice_bar_options', array());
        
        return array(
            'enabled' => isset($notice_bar_options['enable_notice_bar']) ? $notice_bar_options['enable_notice_bar'] : false,
            'position' => isset($notice_bar_options['position']) ? $notice_bar_options['position'] : 'top',
            'position_type' => isset($notice_bar_options['position_type']) ? $notice_bar_options['position_type'] : 'fixed'
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

    /**
     * Render spiders with animation
     */
    private function render_spiders() {
        $count = isset($this->settings['spiders_count']) ? intval($this->settings['spiders_count']) : 2;
        $count = max(1, min(10, $count)); // Limit between 1-10
        $speed = isset($this->settings['spiders_speed']) ? $this->settings['spiders_speed'] : 'medium';
        
        $output = '<div id="halloween-spiders">';
        
        for ($i = 1; $i <= $count; $i++) {
            $speed_class = 'speed-' . esc_attr($speed);
            $output .= sprintf(
                '<div class="halloween-spider %s">🕷️</div>',
                $speed_class
            );
        }
        
        $output .= '</div>';
        
        return $output;
    }

    private function render_fog() {
        $speed = isset($this->settings['fog_speed']) ? $this->settings['fog_speed'] : 'medium';
        $speed_class = 'speed-' . esc_attr($speed);
        
        $output = '<div id="halloween-fog" class="' . $speed_class . '">';
        
        // Add multiple fog layers for depth
        $output .= '<div class="fog-layer"></div>';
        $output .= '<div class="fog-layer"></div>';
        $output .= '<div class="fog-layer"></div>';
        
        // Add fog particles
        for ($i = 1; $i <= 6; $i++) {
            $output .= '<div class="fog-particle fog-particle-' . $i . '"></div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
}