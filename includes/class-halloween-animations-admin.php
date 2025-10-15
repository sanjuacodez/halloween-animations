<?php
/**
 * Admin functionality for WP Halloween plugin
 *
 * @package Halloween_Animations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
class Halloween_Animations_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_halloween_animations_test_animation', array($this, 'test_animation'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Halloween Effects', 'halloween-animations'),
            __('Halloween Effects', 'halloween-animations'),
            'manage_options',
            'halloween-animations',
            array($this, 'admin_page')
        );
    }

    /**
     * Initialize settings
     */
    public function settings_init() {
        register_setting('halloween_animations_settings', 'halloween_animations_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));

        // General Settings Section
        add_settings_section(
            'halloween_animations_general',
            __('General Settings', 'halloween-animations'),
            array($this, 'general_section_callback'),
            'halloween_animations_settings'
        );

        // Animation Settings Section
        add_settings_section(
            'halloween_animations_animations',
            __('Animation Settings', 'halloween-animations'),
            array($this, 'animations_section_callback'),
            'halloween_animations_settings'
        );

        // Display Settings Section
        add_settings_section(
            'halloween_animations_display',
            __('Display Settings', 'halloween-animations'),
            array($this, 'display_section_callback'),
            'halloween_animations_settings'
        );

        $this->add_settings_fields();
    }

    /**
     * Add settings fields
     */
    private function add_settings_fields() {
        $animations = array(
            'bats' => array('label' => __('Flying Bats', 'halloween-animations')),
            'ghosts' => array('label' => __('Floating Ghosts', 'halloween-animations')),
            'pumpkin' => array('label' => __('Running Pumpkin', 'halloween-animations')),
            'leaves' => array('label' => __('Falling Leaves', 'halloween-animations')),
            'spiders' => array('label' => __('Crawling Spiders', 'halloween-animations')),
            'fog' => array('label' => __('Fog Effect', 'halloween-animations'))
        );

        foreach ($animations as $animation => $args) {
            add_settings_field(
                $animation . '_field',
                $args['label'],
                array($this, 'animation_field_callback'),
                'halloween_animations_settings',
                'halloween_animations_animations',
                array_merge($args, array('animation' => $animation))
            );
        }

        // Mobile settings
        add_settings_field(
            'mobile_field',
            __('Mobile Support', 'halloween-animations'),
            array($this, 'mobile_field_callback'),
            'halloween_animations_settings',
            'halloween_animations_general'
        );

        // Sound settings
        add_settings_field(
            'sound_field',
            __('Sound Effects', 'halloween-animations'),
            array($this, 'sound_field_callback'),
            'halloween_animations_settings',
            'halloween_animations_general'
        );

        // Display settings
        add_settings_field(
            'display_options_field',
            __('Display Options', 'halloween-animations'),
            array($this, 'display_options_callback'),
            'halloween_animations_settings',
            'halloween_animations_display'
        );
    }

    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__('Configure general Halloween effects settings.', 'halloween-animations') . '</p>';
    }

    public function animations_section_callback() {
        echo '<p>' . esc_html__('Enable and configure individual Halloween animations.', 'halloween-animations') . '</p>';
    }

    public function display_section_callback() {
        echo '<p>' . esc_html__('Control where Halloween effects are displayed on your site.', 'halloween-animations') . '</p>';
    }

    /**
     * Field callbacks
     */
    public function animation_field_callback($args) {
        $settings = get_option('halloween_animations_settings', array());
        $animation = $args['animation'];
        $enabled = isset($settings[$animation . '_enabled']) ? $settings[$animation . '_enabled'] : false;
        $count = isset($settings[$animation . '_count']) ? $settings[$animation . '_count'] : 5;
        $speed = isset($settings[$animation . '_speed']) ? $settings[$animation . '_speed'] : 'medium';

        echo '<div class="halloween-animation-setting">';
        echo '<label>';
        echo '<input type="checkbox" name="halloween_animations_settings[' . esc_attr($animation) . '_enabled]" value="1" ' . checked(1, $enabled, false) . '>';
        echo ' ' . esc_html__('Enable', 'halloween-animations') . ' ' . esc_html($args['label']);
        echo '</label>';

        if (in_array($animation, array('bats', 'ghosts', 'leaves', 'spiders'))) {
            echo '<div class="halloween-sub-settings" style="margin-left: 20px; margin-top: 10px;">';
            echo '<label>' . esc_html__('Count:', 'halloween-animations') . ' ';
            echo '<input type="number" name="halloween_animations_settings[' . esc_attr($animation) . '_count]" value="' . esc_attr($count) . '" min="1" max="20" style="width: 60px;">';
            echo '</label>';
            echo '</div>';
        }

        if (in_array($animation, array('bats', 'ghosts', 'pumpkin'))) {
            echo '<div class="halloween-sub-settings" style="margin-left: 20px; margin-top: 5px;">';
            echo '<label>' . esc_html__('Speed:', 'halloween-animations') . ' ';
            echo '<select name="halloween_animations_settings[' . esc_attr($animation) . '_speed]">';
            echo '<option value="slow"' . selected('slow', $speed, false) . '>' . esc_html__('Slow', 'halloween-animations') . '</option>';
            echo '<option value="medium"' . selected('medium', $speed, false) . '>' . esc_html__('Medium', 'halloween-animations') . '</option>';
            echo '<option value="fast"' . selected('fast', $speed, false) . '>' . esc_html__('Fast', 'halloween-animations') . '</option>';
            echo '</select>';
            echo '</label>';
            echo '</div>';
        }

        echo '<button type="button" class="button button-secondary test-animation" data-animation="' . esc_attr($animation) . '" style="margin-left: 20px; margin-top: 10px;display:block;">';
        echo esc_html__('Test Animation', 'halloween-animations');
        echo '</button>';

        echo '</div>';
    }

    public function display_options_callback() {
        $settings = get_option('halloween_animations_settings', array());
        $display_pages = isset($settings['display_pages']) ? $settings['display_pages'] : array('all');
        $excluded_pages = isset($settings['excluded_pages']) ? $settings['excluded_pages'] : array();

        echo '<div class="halloween-display-options">';
        
        // Display on options
        echo '<h4>' . esc_html__('Display On', 'halloween-animations') . '</h4>';
        echo '<label><input type="radio" name="halloween_animations_settings[display_pages][]" value="all" ' . (in_array('all', $display_pages) ? 'checked' : '') . '> ' . esc_html__('Entire Website', 'halloween-animations') . '</label><br>';
        echo '<label><input type="radio" name="halloween_animations_settings[display_pages][]" value="home" ' . (in_array('home', $display_pages) ? 'checked' : '') . '> ' . esc_html__('Homepage Only', 'halloween-animations') . '</label><br>';
        echo '<label><input type="radio" name="halloween_animations_settings[display_pages][]" value="posts" ' . (in_array('posts', $display_pages) ? 'checked' : '') . '> ' . esc_html__('Posts Only', 'halloween-animations') . '</label><br>';
        echo '<label><input type="radio" name="halloween_animations_settings[display_pages][]" value="pages" ' . (in_array('pages', $display_pages) ? 'checked' : '') . '> ' . esc_html__('Pages Only', 'halloween-animations') . '</label><br>';
        echo '<label><input type="radio" name="halloween_animations_settings[display_pages][]" value="custom" ' . (in_array('custom', $display_pages) ? 'checked' : '') . '> ' . esc_html__('Custom Selection', 'halloween-animations') . '</label><br>';

        // Custom post types
        $post_types = get_post_types(array('public' => true), 'objects');
        echo '<div id="custom-post-types" style="margin-left: 20px; margin-top: 10px;">';
        echo '<h5>' . esc_html__('Post Types:', 'halloween-animations') . '</h5>';
        foreach ($post_types as $post_type) {
            if ($post_type->name !== 'attachment') {
                $checked = in_array($post_type->name, $display_pages) ? 'checked' : '';
                echo '<label><input type="checkbox" name="halloween_animations_settings[post_types][]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . '> ' . esc_html($post_type->label) . '</label><br>';
            }
        }
        echo '</div>';

        // Categories
        $categories = get_categories();
        if (!empty($categories)) {
            echo '<div id="categories-selection" style="margin-left: 20px; margin-top: 10px;">';
            echo '<h5>' . esc_html__('Categories:', 'halloween-animations') . '</h5>';
            foreach ($categories as $category) {
                $checked = in_array('cat_' . $category->term_id, $display_pages) ? 'checked' : '';
                echo '<label><input type="checkbox" name="halloween_animations_settings[categories][]" value="cat_' . esc_attr($category->term_id) . '" ' . esc_attr($checked) . '> ' . esc_html($category->name) . '</label><br>';
            }
            echo '</div>';
        }

        echo '</div>';
    }

    public function mobile_field_callback() {
        $settings = get_option('halloween_animations_settings', array());
        $mobile_enabled = isset($settings['mobile_enabled']) ? $settings['mobile_enabled'] : true;
        
        echo '<label>';
        echo '<input type="checkbox" name="halloween_animations_settings[mobile_enabled]" value="1" ' . checked(1, $mobile_enabled, false) . '>';
        echo ' ' . esc_html__('Enable Halloween effects on mobile devices', 'halloween-animations');
        echo '</label>';
    }

    public function sound_field_callback() {
        $settings = get_option('halloween_animations_settings', array());
        $sound_enabled = isset($settings['sound_enabled']) ? $settings['sound_enabled'] : false;
        $sound_mode = isset($settings['sound_mode']) ? $settings['sound_mode'] : 'ambient';
        $selected_sounds = isset($settings['selected_sounds']) ? $settings['selected_sounds'] : array('spooky-wind.mp3', 'owl-hooting.mp3');
        $sound_interval = isset($settings['sound_interval']) ? $settings['sound_interval'] : 15;
        $sound_volume = isset($settings['sound_volume']) ? $settings['sound_volume'] : 50;
        
        echo '<div class="halloween-sound-settings">';
        
        // Enable sounds checkbox
        echo '<label style="font-weight: bold;">';
        echo '<input type="checkbox" name="halloween_animations_settings[sound_enabled]" value="1" ' . checked(1, $sound_enabled, false) . '>';
        echo ' ' . esc_html__('Enable Spooky Background Sounds', 'halloween-animations');
        echo '</label>';
        
        echo '<div class="halloween-sub-settings" style="margin-left: 20px; margin-top: 15px; display: ' . ($sound_enabled ? 'block' : 'none') . ';">';
        
        // Sound mode selection
        echo '<h4>' . esc_html__('Playback Mode', 'halloween-animations') . '</h4>';
        echo '<div style="margin-bottom: 15px;">';
        
        echo '<label style="display: block; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">';
        echo '<input type="radio" name="halloween_animations_settings[sound_mode]" value="ambient" ' . checked('ambient', $sound_mode, false) . ' style="margin-right: 8px;"> ';
        echo '<strong>' . esc_html__('🌬️ Ambient Mode', 'halloween-animations') . '</strong><br>';
        echo '<small style="color: #666; margin-left: 20px;">Continuous wind background with random spooky effects overlaid. Creates a subtle, atmospheric Halloween mood perfect for websites.</small>';
        echo '</label>';
        
        echo '<label style="display: block; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">';
        echo '<input type="radio" name="halloween_animations_settings[sound_mode]" value="random" ' . checked('random', $sound_mode, false) . ' style="margin-right: 8px;"> ';
        echo '<strong>' . esc_html__('🎲 Random Mode', 'halloween-animations') . '</strong><br>';
        echo '<small style="color: #666; margin-left: 20px;">Plays selected sounds in random order with gaps of silence between. Each visit creates a unique spooky experience.</small>';
        echo '</label>';
        
        echo '<label style="display: block; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">';
        echo '<input type="radio" name="halloween_animations_settings[sound_mode]" value="playlist" ' . checked('playlist', $sound_mode, false) . ' style="margin-right: 8px;"> ';
        echo '<strong>' . esc_html__('📃 Playlist Mode', 'halloween-animations') . '</strong><br>';
        echo '<small style="color: #666; margin-left: 20px;">Plays all selected sounds in sequence, one after another, then repeats. Creates a structured, story-like Halloween audio experience.</small>';
        echo '</label>';
        
        echo '<label style="display: block; margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">';
        echo '<input type="radio" name="halloween_animations_settings[sound_mode]" value="chaos" ' . checked('chaos', $sound_mode, false) . ' style="margin-right: 8px;"> ';
        echo '<strong>' . esc_html__('💀 Chaos Mode', 'halloween-animations') . '</strong><br>';
        echo '<small style="color: #666; margin-left: 20px;">Multiple sounds can play simultaneously with unpredictable timing. Creates an intense, haunted house atmosphere.</small>';
        echo '</label>';
        
        echo '</div>';
        
        // Available sounds
        $this->display_sound_selection($selected_sounds);
        
        // Timing controls
        echo '<h4>' . esc_html__('⏱️ Timing Controls', 'halloween-animations') . '</h4>';
        echo '<div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;">';
        echo '<label style="display: block; margin-bottom: 10px;">' . esc_html__('Sound Interval (seconds):', 'halloween-animations') . ' ';
        echo '<input type="number" name="halloween_animations_settings[sound_interval]" value="' . esc_attr($sound_interval) . '" min="2" max="120" style="width: 70px; margin-left: 5px;">';
        echo '</label>';
        echo '<small style="color: #555; line-height: 1.4;">📝 <strong>How this works:</strong><br>';
        echo '• <strong>Ambient Mode:</strong> Time between random effect sounds (wind plays continuously)<br>';
        echo '• <strong>Random Mode:</strong> Time between each random sound (with silence gaps)<br>';
        echo '• <strong>Playlist Mode:</strong> Time between sequential tracks<br>';
        echo '• <strong>Chaos Mode:</strong> Base interval for overlapping sounds</small>';
        echo '</div>';
        
        // Volume control
        echo '<h4>' . esc_html__('🔊 Audio Settings', 'halloween-animations') . '</h4>';
        echo '<div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0;">';
        echo '<label style="display: block; margin-bottom: 10px;">' . esc_html__('Master Volume:', 'halloween-animations') . ' ';
        echo '<input type="range" name="halloween_animations_settings[sound_volume]" value="' . esc_attr($sound_volume) . '" min="0" max="100" style="width: 150px; vertical-align: middle; margin: 0 10px;">';
        echo ' <span id="volume-display" style="font-weight: bold; color: #ff6b35;">' . esc_html($sound_volume) . '%</span>';
        echo '</label>';
        echo '<small style="color: #555;">🎚️ Controls the overall volume of all Halloween sounds. Recommended: 30-60% for background ambiance.</small>';
        echo '</div>';
        
        // Test button
        echo '<button type="button" class="button button-secondary test-sounds" style="margin-top: 10px;display:block;">';
        echo esc_html__('🔊 Test Sounds', 'halloween-animations');
        echo '</button>';
        
        echo '</div>';
        echo '</div>';
    }

    /**
     * Display sound file selection checkboxes
     */
    private function display_sound_selection($selected_sounds) {
        // Get available sound files
        $sounds_dir = plugin_dir_path(dirname(__FILE__)) . 'assets/sounds/';
        $available_sounds = array();
        
        if (is_dir($sounds_dir)) {
            $files = scandir($sounds_dir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                    $available_sounds[] = $file;
                }
            }
        }
        
        if (empty($available_sounds)) {
            echo '<p style="color: #d63384;">' . esc_html__('No MP3 sound files found in assets/sounds/ directory.', 'halloween-animations') . '</p>';
            return;
        }
        
        echo '<h4>' . esc_html__('🎵 Available Sounds', 'halloween-animations') . '</h4>';
        echo '<p style="color: #666; margin-bottom: 15px;">Select which sounds to include in your Halloween experience. Different modes use these sounds in different ways:</p>';
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 15px;">';
        
        foreach ($available_sounds as $sound_file) {
            $is_selected = in_array($sound_file, $selected_sounds);
            $display_name = $this->format_sound_name($sound_file);
            
            echo '<label style="display: flex; align-items: center; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: ' . ($is_selected ? '#e8f4f8' : '#fff') . ';">';
            echo '<input type="checkbox" name="halloween_animations_settings[selected_sounds][]" value="' . esc_attr($sound_file) . '" ' . checked(true, $is_selected, false) . ' style="margin-right: 8px;">';
            echo '<span style="font-size: 12px;">' . esc_html($display_name) . '</span>';
            echo '</label>';
        }
        
        echo '</div>';
    }

    /**
     * Format sound file name for display
     */
    private function format_sound_name($filename) {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(array('-', '_'), ' ', $name);
        $name = ucwords($name);
        
        // Add emojis for visual appeal
        $emoji_map = array(
            'wind' => '💨',
            'owl' => '🦉',
            'wolf' => '🐺',
            'ghost' => '👻',
            'bat' => '🦇',
            'howl' => '🌙',
            'whisper' => '💭',
            'voice' => '🗣️'
        );
        
        foreach ($emoji_map as $keyword => $emoji) {
            if (strpos(strtolower($name), $keyword) !== false) {
                return $emoji . ' ' . $name;
            }
        }
        
        return '🎃 ' . $name;
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (!is_array($input)) {
            return $sanitized;
        }

        // Boolean settings
        $boolean_fields = array('bats_enabled', 'ghosts_enabled', 'pumpkin_enabled', 'leaves_enabled', 'spiders_enabled', 'fog_enabled', 'mobile_enabled', 'sound_enabled');
        foreach ($boolean_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? (bool)$input[$field] : false;
        }

        // Numeric settings
        $numeric_fields = array(
            'bats_count' => array('min' => 1, 'max' => 20, 'default' => 5),
            'ghosts_count' => array('min' => 1, 'max' => 20, 'default' => 3),
            'leaves_count' => array('min' => 1, 'max' => 50, 'default' => 10),
            'spiders_count' => array('min' => 1, 'max' => 10, 'default' => 2),
            'sound_interval' => array('min' => 2, 'max' => 120, 'default' => 15),
            'sound_volume' => array('min' => 0, 'max' => 100, 'default' => 50)
        );
        
        foreach ($numeric_fields as $field => $limits) {
            if (isset($input[$field])) {
                $value = intval($input[$field]);
                $sanitized[$field] = max($limits['min'], min($limits['max'], $value));
            } else {
                $sanitized[$field] = $limits['default'];
            }
        }

        // Speed settings
        $speed_fields = array('bats_speed', 'ghosts_speed', 'pumpkin_speed');
        $valid_speeds = array('slow', 'medium', 'fast');
        foreach ($speed_fields as $field) {
            $sanitized[$field] = isset($input[$field]) && in_array($input[$field], $valid_speeds) ? $input[$field] : 'medium';
        }

        // Sound mode
        $valid_modes = array('ambient', 'random', 'playlist', 'chaos');
        $sanitized['sound_mode'] = isset($input['sound_mode']) && in_array($input['sound_mode'], $valid_modes) ? $input['sound_mode'] : 'ambient';

        // Arrays
        $sanitized['display_pages'] = isset($input['display_pages']) && is_array($input['display_pages']) ? array_map('sanitize_text_field', $input['display_pages']) : array('all');
        $sanitized['excluded_pages'] = isset($input['excluded_pages']) && is_array($input['excluded_pages']) ? array_map('sanitize_text_field', $input['excluded_pages']) : array();
        $sanitized['selected_sounds'] = isset($input['selected_sounds']) && is_array($input['selected_sounds']) ? array_map('sanitize_file_name', $input['selected_sounds']) : array();
        $sanitized['post_types'] = isset($input['post_types']) && is_array($input['post_types']) ? array_map('sanitize_text_field', $input['post_types']) : array();
        $sanitized['categories'] = isset($input['categories']) && is_array($input['categories']) ? array_map('sanitize_text_field', $input['categories']) : array();

        // Add success message using WordPress settings API
        add_settings_error(
            'halloween_animations_settings',
            'settings_updated',
            __('Halloween settings saved successfully! 🎃', 'halloween-animations'),
            'updated'
        );

        return $sanitized;
    }

    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="halloween-admin-header">
                <p><?php esc_html_e('Transform your WordPress site into a spooky Halloween experience! Configure various Halloween animations and effects to delight your visitors.', 'halloween-animations'); ?></p>
            </div>

            <form action="options.php" method="post">
                <?php
                settings_fields('halloween_animations_settings');
                do_settings_sections('halloween_animations_settings');
                submit_button(esc_html__('Save Halloween Settings', 'halloween-animations'));
                ?>
            </form>

            <div class="halloween-preview-section">
                <h2><?php esc_html_e('Preview Effects', 'halloween-animations'); ?></h2>
                <p><?php esc_html_e('Click the buttons below to preview animations on this admin page:', 'halloween-animations'); ?></p>
                <div class="halloween-preview-buttons">
                    <button type="button" class="button button-secondary preview-animation" data-animation="bats"><?php esc_html_e('Preview Bats', 'halloween-animations'); ?></button>
                    <button type="button" class="button button-secondary preview-animation" data-animation="ghosts"><?php esc_html_e('Preview Ghosts', 'halloween-animations'); ?></button>
                    <button type="button" class="button button-secondary preview-animation" data-animation="pumpkin"><?php esc_html_e('Preview Pumpkin', 'halloween-animations'); ?></button>
                    <button type="button" class="button button-secondary preview-animation" data-animation="leaves"><?php esc_html_e('Preview Leaves', 'halloween-animations'); ?></button>
                    <button type="button" class="button button-secondary preview-animation" data-animation="spiders"><?php esc_html_e('Preview Spiders', 'halloween-animations'); ?></button>
                    <button type="button" class="button button-secondary preview-animation" data-animation="fog"><?php esc_html_e('Preview Fog', 'halloween-animations'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_halloween-animations' !== $hook) {
            return;
        }

        wp_enqueue_script(
            'halloween-animations-admin',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/halloween-admin.js',
            array('jquery'),
            defined('HALLOWEEN_ANIMATIONS_VERSION') ? HALLOWEEN_ANIMATIONS_VERSION : '1.0.0',
            true
        );

        // Add inline script for admin functionality
        $inline_script = '
        jQuery(document).ready(function($) {
            $("input[name=\"halloween_animations_settings[sound_enabled]\"]").change(function() {
                $(".halloween-sub-settings").toggle(this.checked);
            });
            $("input[name=\"halloween_animations_settings[sound_volume]\"]").on("input", function() {
                $("#volume-display").text(this.value + "%");
            });
        });';
        
        wp_add_inline_script('halloween-animations-admin', $inline_script);

        wp_enqueue_script(
            'halloween-animations-enhanced-sounds',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/enhanced-sounds.js',
            array('jquery'),
            defined('HALLOWEEN_ANIMATIONS_VERSION') ? HALLOWEEN_ANIMATIONS_VERSION : '1.0.0',
            true
        );

        wp_enqueue_style(
            'halloween-animations-admin',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/halloween-admin.css',
            array(),
            defined('HALLOWEEN_ANIMATIONS_VERSION') ? HALLOWEEN_ANIMATIONS_VERSION : '1.0.0'
        );

        wp_enqueue_style(
            'halloween-animations-frontend',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/halloween-animations.css',
            array(),
            defined('HALLOWEEN_ANIMATIONS_VERSION') ? HALLOWEEN_ANIMATIONS_VERSION : '1.0.0'
        );

        // Localize script for AJAX
        wp_localize_script('halloween-animations-admin', 'halloween_animations_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('halloween_animations_admin'),
            'sounds_url' => HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/sounds/',
            'plugin_url' => HALLOWEEN_ANIMATIONS_PLUGIN_URL,
            'debug' => defined('WP_DEBUG') && WP_DEBUG
        ));

        // Also localize for enhanced-sounds script with the expected object name
        wp_localize_script('halloween-animations-enhanced-sounds', 'halloween_animations_ajax', array(
            'pluginUrl' => HALLOWEEN_ANIMATIONS_PLUGIN_URL,
            'soundSettings' => array(
                'enabled' => false, // Admin doesn't need sound effects by default
                'mode' => 'ambient',
                'volume' => 30,
                'interval' => 15,
                'selected_sounds' => array()
            ),
            'debug' => defined('WP_DEBUG') && WP_DEBUG
        ));
    }

    /**
     * Test animation AJAX handler
     */
    public function test_animation() {
        // Verify nonce for security
        if (!check_ajax_referer('halloween_animations_admin', 'nonce', false)) {
            wp_send_json_error(__('Security check failed', 'halloween-animations'));
        }

        // Check capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have sufficient permissions to access this page.', 'halloween-animations'));
        }

        $animation = isset($_POST['animation']) ? sanitize_text_field(wp_unslash($_POST['animation'])) : '';
        
        if (empty($animation)) {
            wp_send_json_error(__('No animation specified', 'halloween-animations'));
        }
        
        // Validate animation type
        $valid_animations = array('bats', 'ghosts', 'pumpkin', 'leaves', 'spiders', 'fog');
        if (!in_array($animation, $valid_animations)) {
            wp_send_json_error(__('Invalid animation type', 'halloween-animations'));
        }

        // Get current settings to provide contextual information
        $settings = get_option('halloween_animations_settings', array());
        $is_enabled = isset($settings[$animation . '_enabled']) ? $settings[$animation . '_enabled'] : false;
        
        $response_data = array(
            /* translators: %s: animation name */
            'message' => sprintf(esc_html__('%s animation test completed successfully! 🎃', 'halloween-animations'), ucfirst($animation)),
            'animation' => $animation,
            'enabled' => $is_enabled,
            'timestamp' => current_time('timestamp')
        );
        
        // Add animation-specific information
        switch ($animation) {
            case 'bats':
                $count = isset($settings['bats_count']) ? $settings['bats_count'] : 5;
                $speed = isset($settings['bats_speed']) ? $settings['bats_speed'] : 'medium';
                /* translators: %1$d: number of bats, %2$s: animation speed */
                $response_data['details'] = sprintf(esc_html__('Bat animation with %1$d bats at %2$s speed', 'halloween-animations'), $count, $speed);
                break;
                
            case 'ghosts':
                $count = isset($settings['ghosts_count']) ? $settings['ghosts_count'] : 3;
                $speed = isset($settings['ghosts_speed']) ? $settings['ghosts_speed'] : 'medium';
                /* translators: %1$d: number of ghosts, %2$s: animation speed */
                $response_data['details'] = sprintf(esc_html__('Ghost animation with %1$d ghosts at %2$s speed', 'halloween-animations'), $count, $speed);
                break;
                
            case 'pumpkin':
                $speed = isset($settings['pumpkin_speed']) ? $settings['pumpkin_speed'] : 'medium';
                /* translators: %s: animation speed */
                $response_data['details'] = sprintf(esc_html__('Pumpkin animation at %s speed', 'halloween-animations'), $speed);
                break;
                
            case 'leaves':
                $count = isset($settings['leaves_count']) ? $settings['leaves_count'] : 10;
                /* translators: %d: number of falling leaves */
                $response_data['details'] = sprintf(esc_html__('Leaves animation with %d falling leaves', 'halloween-animations'), $count);
                break;
                
            case 'spiders':
                $count = isset($settings['spiders_count']) ? $settings['spiders_count'] : 2;
                /* translators: %d: number of crawling spiders */
                $response_data['details'] = sprintf(esc_html__('Spider animation with %d crawling spiders', 'halloween-animations'), $count);
                break;
                
            case 'fog':
                $response_data['details'] = esc_html__('Fog effect with multiple drifting particles', 'halloween-animations');
                break;
        }
        
        // Add status message if animation is disabled
        if (!$is_enabled) {
            /* translators: %s: animation name */
            $response_data['warning'] = sprintf(esc_html__('Note: %s animation is currently disabled in settings.', 'halloween-animations'), ucfirst($animation));
        }
        
        wp_send_json_success($response_data);
    }

    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Check if we're on the Halloween settings page
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'settings_page_halloween-animations') {
            return;
        }
        
        // Use WordPress's built-in settings notices instead of checking GET parameters
        // This automatically handles the settings-updated parameter properly
        settings_errors('halloween_animations_settings');
    }
}