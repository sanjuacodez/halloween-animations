<?php
/**
 * Admin functionality for Seasonal Effects plugin
 * 
 * @package Halloween_Animations
 * @author Sanjay Shankar <me@sanjayshankar.me>
 * @link https://sanjayshankar.me
 */

if (!defined('ABSPATH')) {
    exit;
}

class Halloween_Animations_Admin {

    private $active_tab = 'general';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);
        add_action('admin_menu', array($this, 'add_site_animations_menu'), 30);
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_apply_seasonal_template', array($this, 'ajax_apply_template'));
        add_action('admin_notices', array($this, 'show_settings_saved_notice'));
    }
    
    public function show_settings_saved_notice() {
        // Check if we're on the plugin page and settings were just updated
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, no action taken
        if (isset($_GET['page']) && $_GET['page'] === 'halloween-animations' && isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
            $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✅ Settings saved successfully!</strong> The changes have been applied. You can see the effects on your site's frontend now.</p>
            </div>
            <?php
        }
    }

    public function add_admin_menu() {
        // Add main Seasonal Effects menu
        add_menu_page(
            'Seasonal Effects',
            'Seasonal Effects',
            'manage_options',
            'seasonal-effects',
            array($this, 'main_dashboard_page'),
            'dashicons-admin-appearance',
            30
        );

        // Dashboard Submenu
        add_submenu_page(
            'seasonal-effects',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'seasonal-effects',
            array($this, 'main_dashboard_page')
        );
    }

    public function add_site_animations_menu() {
        // Site Animations Submenu
        add_submenu_page(
            'seasonal-effects',
            'Site Animations',
            'Site Animations',
            'manage_options',
            'halloween-animations',
            array($this, 'admin_page')
        );
    }

    public function main_dashboard_page() {
        $settings = get_option('halloween_animations_settings', array());
        $notice_bar_options = get_option('ha_notice_bar_settings', array());
        
        $active_effects = 0;
        $effects_list = array();
        
        // Count active effects
        $all_effects = array(
            'sale_tags', 'shopping_icons', 'matrix_rain', 'glitch_effect',
            'snowflakes', 'christmas_lights', 'ornaments',
            'fireworks', 'confetti', 'balloons', 'hearts', 'heart_confetti', 'pulsating_hearts', 
            'easter_eggs', 'bunny', 'bats', 'ghosts', 'pumpkin', 'leaves', 'spiders', 'fog'
        );
        
        foreach ($all_effects as $effect) {
            if (!empty($settings[$effect . '_enabled'])) {
                $active_effects++;
                $effects_list[] = ucwords(str_replace('_', ' ', $effect));
            }
        }
        
        $plugin_enabled = isset($settings['plugin_enabled']) ? $settings['plugin_enabled'] : true;
        $notice_bar_enabled = isset($notice_bar_options['enable']) && $notice_bar_options['enable'] === 'yes';
        ?>
        <div class="wrap seasonal-dashboard">
            <div class="seasonal-header-hero">
                <div class="hero-content">
                    <h1>✨ Seasonal Effects & Notice Bar</h1>
                    <p>Transform your website for any occasion with immersive animations and powerful announcements.</p>
                </div>
                <div class="hero-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations&tab=templates')); ?>" class="button button-primary hero-button">
                        <span class="dashicons dashicons-layout"></span> Browse Templates
                    </a>
                </div>
            </div>
            
            <div class="dashboard-status-bar">
                <div class="status-item <?php echo $plugin_enabled ? 'active' : 'inactive'; ?>">
                    <span class="status-icon dashicons dashicons-<?php echo $plugin_enabled ? 'yes' : 'no'; ?>"></span>
                    <div class="status-details">
                        <strong>Seasonal Effects</strong>
                        <span><?php echo $plugin_enabled ? 'Active' : 'Disabled'; ?></span>
                    </div>
                </div>
                <div class="status-item <?php echo $notice_bar_enabled ? 'active' : 'inactive'; ?>">
                    <span class="status-icon dashicons dashicons-<?php echo $notice_bar_enabled ? 'yes' : 'no'; ?>"></span>
                    <div class="status-details">
                        <strong>Notice Bar</strong>
                        <span><?php echo $notice_bar_enabled ? 'Active' : 'Disabled'; ?></span>
                    </div>
                </div>
                <div class="status-item info">
                    <span class="status-icon dashicons dashicons-chart-bar"></span>
                    <div class="status-details">
                        <strong>Active Animations</strong>
                        <span><?php echo esc_html($active_effects); ?> Running</span>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid-layout">
                <!-- Main Cards -->
                <div class="dashboard-main-col">
                    <div class="dashboard-card feature-card">
                        <div class="card-icon" style="background: #e3f2fd; color: #2196f3;">
                            <span class="dashicons dashicons-star-filled"></span>
                        </div>
                        <div class="card-content">
                            <h2>Site Animations</h2>
                            <p>Add falling snow, fireworks, spooky effects, and more to your site. Choose from over 20+ high-quality animations.</p>
                            <div class="card-actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations')); ?>" class="button button-primary">Manage Animations</a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations&tab=sounds')); ?>" class="button button-secondary">Sound Effects</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-card feature-card">
                        <div class="card-icon" style="background: #fff3e0; color: #ff9800;">
                            <span class="dashicons dashicons-megaphone"></span>
                        </div>
                        <div class="card-content">
                            <h2>Notice Bar</h2>
                            <p>Create eye-catching announcements, sales banners, and countdown timers. Boost your conversions instantly.</p>
                            <div class="card-actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ha-notice-bar')); ?>" class="button button-primary">Manage Notice Bar</a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ha-notice-bar&tab=templates')); ?>" class="button button-secondary">Banner Templates</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="dashboard-sidebar-col">
                    <div class="dashboard-card info-card">
                        <h3>🚀 Quick Actions</h3>
                        <ul class="quick-links">
                            <li>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations&tab=general')); ?>">
                                    <span class="dashicons dashicons-admin-settings"></span> General Settings
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=halloween-animations&tab=templates')); ?>">
                                    <span class="dashicons dashicons-layout"></span> Apply a Template
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ha-notice-bar&tab=schedule')); ?>">
                                    <span class="dashicons dashicons-calendar-alt"></span> Schedule a Sale
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <?php if ($active_effects > 0): ?>
                    <div class="dashboard-card info-card">
                        <h3>Currently Running</h3>
                        <div class="active-tags">
                            <?php 
                            $display_limit = 5;
                            $count = 0;
                            foreach ($effects_list as $effect_name) {
                                if ($count >= $display_limit) break;
                                echo '<span class="effect-tag">' . esc_html($effect_name) . '</span>';
                                $count++;
                            }
                            if (count($effects_list) > $display_limit) {
                                echo '<span class="effect-tag more">+' . (count($effects_list) - $display_limit) . ' more</span>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="dashboard-card info-card support-card">
                        <h3>Need Help?</h3>
                        <p>Check out our documentation or get support.</p>
                        <a href="https://wordpress.org/support/plugin/halloween-animations/" target="_blank" class="button button-link">Get Support &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_init() {
        register_setting('halloween_animations_settings', 'halloween_animations_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings')
        ));

        // General Settings Section
        add_settings_section('general_section', '', '__return_false', 'seasonal_general');
        add_settings_field('enable_plugin', 'Enable Seasonal Effects', array($this, 'enable_plugin_field'), 'seasonal_general', 'general_section');
        add_settings_field('mobile_support', 'Mobile Support', array($this, 'mobile_field'), 'seasonal_general', 'general_section');
        add_settings_field('display_pages', 'Display On Pages', array($this, 'display_pages_field'), 'seasonal_general', 'general_section');
        
        // Templates Section - no fields needed, handled in render
        add_settings_section('templates_section', '', '__return_false', 'seasonal_templates');
        
        // Animations Section
        add_settings_section('animations_section', '', '__return_false', 'seasonal_animations');
    }

    public function enable_plugin_field() {
        $settings = get_option('halloween_animations_settings', array());
        $enabled = isset($settings['plugin_enabled']) ? $settings['plugin_enabled'] : true;
        echo '<label><input type="checkbox" name="halloween_animations_settings[plugin_enabled]" value="1" ' . checked(1, $enabled, false) . '> Enable all seasonal effects</label>';
    }

    public function mobile_field() {
        $settings = get_option('halloween_animations_settings', array());
        $enabled = isset($settings['mobile_enabled']) ? $settings['mobile_enabled'] : true;
        echo '<label><input type="checkbox" name="halloween_animations_settings[mobile_enabled]" value="1" ' . checked(1, $enabled, false) . '> Enable effects on mobile devices</label>';
    }
    
    public function display_pages_field() {
        $settings = get_option('halloween_animations_settings', array());
        $display_pages = isset($settings['display_pages']) ? $settings['display_pages'] : array('all');
        ?>
        <label style="display:block;"><input type="checkbox" name="halloween_animations_settings[display_pages][]" value="all" <?php checked(in_array('all', $display_pages), true); ?>> All Pages</label>
        <label style="display:block;"><input type="checkbox" name="halloween_animations_settings[display_pages][]" value="home" <?php checked(in_array('home', $display_pages), true); ?>> Homepage</label>
        <label style="display:block;"><input type="checkbox" name="halloween_animations_settings[display_pages][]" value="posts" <?php checked(in_array('posts', $display_pages), true); ?>> Blog Posts</label>
        <label style="display:block;"><input type="checkbox" name="halloween_animations_settings[display_pages][]" value="pages" <?php checked(in_array('pages', $display_pages), true); ?>> Pages</label>
        <?php
    }

    public function animation_field($args) {
        $settings = get_option('halloween_animations_settings', array());
        $animation = $args['animation'];
        $enabled = isset($settings[$animation . '_enabled']) ? $settings[$animation . '_enabled'] : false;
        $count = isset($settings[$animation . '_count']) ? intval($settings[$animation . '_count']) : 5;
        $speed = isset($settings[$animation . '_speed']) ? $settings[$animation . '_speed'] : 'medium';

        echo '<div class="animation-setting">';
        echo '<label><input type="checkbox" name="halloween_animations_settings[' . esc_attr($animation) . '_enabled]" value="1" ' . checked(1, $enabled, false) . ' class="anim-toggle"> Enable</label>';
        echo '<div class="sub-settings" style="' . ($enabled ? 'display:block;' : 'display:none;') . '">';

        $count_animations = array('bats', 'ghosts', 'leaves', 'spiders');
        if (in_array($animation, $count_animations)) {
            echo '<label>Count: <input type="number" name="halloween_animations_settings[' . esc_attr($animation) . '_count]" value="' . esc_attr($count) . '" min="1" max="20" style="width:80px;"></label>';
        }

        $speed_animations = array('bats', 'ghosts', 'pumpkin', 'fog');
        if (in_array($animation, $speed_animations)) {
            echo '<label style="display:block;margin-top:10px;">Speed: ';
            echo '<select name="halloween_animations_settings[' . esc_attr($animation) . '_speed]">';
            echo '<option value="slow"' . selected('slow', $speed, false) . '>Slow</option>';
            echo '<option value="medium"' . selected('medium', $speed, false) . '>Medium</option>';
            echo '<option value="fast"' . selected('fast', $speed, false) . '>Fast</option>';
            echo '</select></label>';
        }

        echo '</div></div>';
    }

    public function seasonal_effect_field($args) {
        $settings = get_option('halloween_animations_settings', array());
        $effect = $args['effect'];
        $enabled = isset($settings[$effect . '_enabled']) ? $settings[$effect . '_enabled'] : false;
        $count = isset($settings[$effect . '_count']) ? intval($settings[$effect . '_count']) : (isset($args['count_default']) ? $args['count_default'] : 10);
        $speed = isset($settings[$effect . '_speed']) ? $settings[$effect . '_speed'] : 'medium';
        $frequency = isset($settings[$effect . '_frequency']) ? $settings[$effect . '_frequency'] : 'medium';

        echo '<div class="animation-setting">';
        echo '<label><input type="checkbox" name="halloween_animations_settings[' . esc_attr($effect) . '_enabled]" value="1" ' . checked(1, $enabled, false) . ' class="anim-toggle"> Enable</label>';
        echo '<div class="sub-settings" style="' . ($enabled ? 'display:block;' : 'display:none;') . '">';

        if (isset($args['has_count']) && $args['has_count']) {
            $max = isset($args['count_max']) ? $args['count_max'] : 20;
            echo '<label>Count: <input type="number" name="halloween_animations_settings[' . esc_attr($effect) . '_count]" value="' . esc_attr($count) . '" min="1" max="' . esc_attr($max) . '" style="width:80px;"></label>';
        }

        if (isset($args['has_speed']) && $args['has_speed']) {
            echo '<label style="display:block;margin-top:10px;">Speed: ';
            echo '<select name="halloween_animations_settings[' . esc_attr($effect) . '_speed]">';
            echo '<option value="slow"' . selected('slow', $speed, false) . '>Slow</option>';
            echo '<option value="medium"' . selected('medium', $speed, false) . '>Medium</option>';
            echo '<option value="fast"' . selected('fast', $speed, false) . '>Fast</option>';
            echo '</select></label>';
        }

        if (isset($args['has_frequency']) && $args['has_frequency']) {
            echo '<label style="display:block;margin-top:10px;">Frequency: ';
            echo '<select name="halloween_animations_settings[' . esc_attr($effect) . '_frequency]">';
            echo '<option value="low"' . selected('low', $frequency, false) . '>Low</option>';
            echo '<option value="medium"' . selected('medium', $frequency, false) . '>Medium</option>';
            echo '<option value="high"' . selected('high', $frequency, false) . '>High</option>';
            echo '</select></label>';
        }

        echo '</div></div>';
    }

    public function sanitize_settings($input) {
        // Get existing settings first to preserve values from other tabs
        $existing_settings = get_option('halloween_animations_settings', array());
        $sanitized = $existing_settings;
        
        if (!is_array($input)) {
            return $sanitized;
        }

        // Determine which tab is being saved
        $active_tab = isset($input['_active_tab']) ? $input['_active_tab'] : '';
        unset($input['_active_tab']); // Remove from saved settings

        // Define which fields belong to which tab
        $general_fields = array('plugin_enabled', 'mobile_enabled');
        $animation_fields = array(
            'sale_tags_enabled', 'shopping_icons_enabled', 'matrix_rain_enabled', 'glitch_effect_enabled',
            'snowflakes_enabled', 'christmas_lights_enabled', 'ornaments_enabled',
            'fireworks_enabled', 'confetti_enabled', 'balloons_enabled',
            'hearts_enabled', 'heart_confetti_enabled', 'pulsating_hearts_enabled', 'easter_eggs_enabled', 'bunny_enabled',
            'bats_enabled', 'ghosts_enabled', 'pumpkin_enabled', 'leaves_enabled', 'spiders_enabled', 'fog_enabled'
        );
        $sound_fields = array('sound_enabled');

        $all_boolean_fields = array_merge($general_fields, $animation_fields, $sound_fields);

        // Only process boolean fields from the active tab
        // This prevents unchecked boxes from other tabs being reset to false
        foreach ($all_boolean_fields as $field) {
            $is_general_field = in_array($field, $general_fields);
            $is_animation_field = in_array($field, $animation_fields);
            $is_sound_field = in_array($field, $sound_fields);
            
            // Only update if this field belongs to the active tab
            if (($active_tab === 'general' && $is_general_field) ||
                ($active_tab === 'animations' && $is_animation_field) ||
                ($active_tab === 'sounds' && $is_sound_field)) {
                $sanitized[$field] = isset($input[$field]) ? 1 : 0;
            }
        }
        
        // Handle display_pages array
        if (isset($input['display_pages']) && is_array($input['display_pages'])) {
            $sanitized['display_pages'] = array_map('sanitize_text_field', $input['display_pages']);
        }
        
        // Handle selected_sounds array
        if (isset($input['selected_sounds']) && is_array($input['selected_sounds'])) {
            $sanitized['selected_sounds'] = array_map('sanitize_file_name', $input['selected_sounds']);
        }
        
        // Handle sound_mode
        if (isset($input['sound_mode'])) {
            $valid_modes = array('ambient', 'random', 'sequential', 'mix');
            $sanitized['sound_mode'] = in_array($input['sound_mode'], $valid_modes) ? $input['sound_mode'] : 'ambient';
        }
        
        // Handle sound_volume
        if (isset($input['sound_volume'])) {
            $sanitized['sound_volume'] = max(0, min(100, intval($input['sound_volume'])));
        }
        
        // Handle sound_interval
        if (isset($input['sound_interval'])) {
            $sanitized['sound_interval'] = max(5, min(60, intval($input['sound_interval'])));
        }

        $number_fields = array(
            'bats_count',
            'ghosts_count',
            'leaves_count',
            'spiders_count',
            // Seasonal effects counts
            'sale_tags_count',
            'shopping_icons_count',
            'matrix_rain_count',
            'snowflakes_count',
            'ornaments_count',
            'confetti_count',
            'balloons_count',
            'hearts_count',
            'heart_confetti_count',
            'pulsating_hearts_count',
            'easter_eggs_count'
        );

        foreach ($number_fields as $field) {
            if (isset($input[$field])) {
                $value = intval($input[$field]);
                // Allow higher counts for seasonal effects
                $max = 60;
                if (in_array($field, array('bats_count', 'ghosts_count', 'leaves_count', 'spiders_count'))) {
                    $max = 20;
                }
                $sanitized[$field] = max(1, min($max, $value));
            }
        }

        // Handle size fields (for snowflakes, etc.)
        if (isset($input['snowflakes_size'])) {
            $sanitized['snowflakes_size'] = max(8, min(30, intval($input['snowflakes_size'])));
        }

        $speed_fields = array(
            'bats_speed',
            'ghosts_speed',
            'pumpkin_speed',
            'fog_speed',
            'snowflakes_speed',
            'hearts_speed'
        );

        $valid_speeds = array('slow', 'medium', 'fast');

        foreach ($speed_fields as $field) {
            if (isset($input[$field])) {
                if (in_array($input[$field], $valid_speeds)) {
                    $sanitized[$field] = $input[$field];
                } else {
                    $sanitized[$field] = 'medium';
                }
            }
            // If not in input, keep existing value (don't reset)
        }

        // Frequency fields
        if (isset($input['fireworks_frequency'])) {
            $valid_frequencies = array('low', 'medium', 'high');
            if (in_array($input['fireworks_frequency'], $valid_frequencies)) {
                $sanitized['fireworks_frequency'] = $input['fireworks_frequency'];
            }
        }

        // Handle custom text for sale tags (textarea with line breaks)
        if (isset($input['sale_tags_text'])) {
            $sanitized['sale_tags_text'] = sanitize_textarea_field($input['sale_tags_text']);
        }

        // Handle color fields for sale tags
        if (isset($input['sale_tags_bg_color'])) {
            $sanitized['sale_tags_bg_color'] = sanitize_hex_color($input['sale_tags_bg_color']);
        }
        if (isset($input['sale_tags_text_color'])) {
            $sanitized['sale_tags_text_color'] = sanitize_hex_color($input['sale_tags_text_color']);
        }

        return $sanitized;
    }

    public function admin_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, no action taken
        $tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
        $this->active_tab = $tab;
        ?>
        <div class="wrap seasonal-effects-admin">
            <h1>✨ Site Animations</h1>
            <p>Create amazing seasonal animations for any occasion - Halloween, Black Friday, Christmas, New Year, Valentine's Day, Easter, and more!</p>
            
            <?php settings_errors('halloween_animations_settings'); ?>
            
            <?php 
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
            if (isset($_GET['applied'])): ?>
                <div class="notice notice-success is-dismissible" style="margin: 15px 0;">
                    <p><strong>✅ Template applied successfully!</strong> The changes have been applied. You can see the animations and effects on your site's frontend now. Visit your website to view the changes in action!</p>
                </div>
            <?php endif; ?>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=halloween-animations&tab=general" class="nav-tab <?php echo $tab === 'general' ? 'nav-tab-active' : ''; ?>">⚙️ General</a>
                <a href="?page=halloween-animations&tab=templates" class="nav-tab <?php echo $tab === 'templates' ? 'nav-tab-active' : ''; ?>">📋 Templates</a>
                <a href="?page=halloween-animations&tab=animations" class="nav-tab <?php echo $tab === 'animations' ? 'nav-tab-active' : ''; ?>">✨ Animations</a>
                <a href="?page=halloween-animations&tab=sounds" class="nav-tab <?php echo $tab === 'sounds' ? 'nav-tab-active' : ''; ?>">🔊 Sound Effects</a>
            </nav>

            <div class="tab-content" style="margin-top: 20px;">
                <?php
                if ($tab === 'general') {
                    $this->render_general_tab();
                } elseif ($tab === 'templates') {
                    $this->render_templates_tab();
                } elseif ($tab === 'animations') {
                    $this->render_animations_tab();
                } elseif ($tab === 'sounds') {
                    $this->render_sounds_tab();
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render General Settings Tab
     */
    private function render_general_tab() {
        $settings = get_option('halloween_animations_settings', array());
        ?>
        <form action="options.php" method="post">
            <?php settings_fields('halloween_animations_settings'); ?>
            <input type="hidden" name="halloween_animations_settings[_active_tab]" value="general">
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Seasonal Effects</th>
                    <td>
                        <label>
                            <input type="checkbox" name="halloween_animations_settings[plugin_enabled]" value="1" <?php checked(isset($settings['plugin_enabled']) ? $settings['plugin_enabled'] : true, true); ?>>
                            Enable seasonal effects plugin
                        </label>
                        <p class="description"><strong>Master switch:</strong> Turn this OFF to disable all effects on your entire website. When enabled, you can control individual effects in the Animations tab.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mobile Support</th>
                    <td>
                        <label>
                            <input type="checkbox" name="halloween_animations_settings[mobile_enabled]" value="1" <?php checked(isset($settings['mobile_enabled']) ? $settings['mobile_enabled'] : true, true); ?>>
                            Enable effects on mobile devices
                        </label>
                        <p class="description">Effects are automatically optimized for mobile (fewer elements, better performance).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Display On Pages</th>
                    <td>
                        <?php
                        $display_pages = isset($settings['display_pages']) ? $settings['display_pages'] : array('all');
                        ?>
                        <label style="display:block;margin-bottom:5px;">
                            <input type="checkbox" name="halloween_animations_settings[display_pages][]" value="all" <?php checked(in_array('all', $display_pages), true); ?>>
                            All Pages (Recommended)
                        </label>
                        <label style="display:block;margin-bottom:5px;">
                            <input type="checkbox" name="halloween_animations_settings[display_pages][]" value="home" <?php checked(in_array('home', $display_pages), true); ?>>
                            Homepage Only
                        </label>
                        <label style="display:block;margin-bottom:5px;">
                            <input type="checkbox" name="halloween_animations_settings[display_pages][]" value="posts" <?php checked(in_array('posts', $display_pages), true); ?>>
                            Blog Posts
                        </label>
                        <label style="display:block;margin-bottom:5px;">
                            <input type="checkbox" name="halloween_animations_settings[display_pages][]" value="pages" <?php checked(in_array('pages', $display_pages), true); ?>>
                            Pages
                        </label>
                        <p class="description">Choose where to display seasonal effects.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save General Settings'); ?>
        </form>
        <?php
    }

    /**
     * Render Templates Tab
     */
    private function render_templates_tab() {
        $settings = get_option('halloween_animations_settings', array());
        $templates = $this->get_occasion_templates();
        ?>
        <div class="templates-grid">
            <h2>🎨 Quick Templates</h2>
            <p>Apply a pre-configured template for any occasion with one click! You can customize afterwards in the Animations tab.</p>
            
            <div class="templates-container" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                <?php foreach ($templates as $template_id => $template): ?>
                    <div class="template-card" style="border:1px solid #ddd; border-radius:8px; padding:20px; background:#fff;">
                        <h3 style="margin:0 0 10px 0; font-size:18px;"><?php echo esc_html($template['name']); ?></h3>
                        <p style="color:#666; font-size:14px; line-height:1.5;"><?php echo esc_html($template['description']); ?></p>
                        
                        <div style="margin:15px 0; padding:10px; background:#f5f5f5; border-radius:4px; font-size:13px;">
                            <strong>Effects:</strong><br>
                            <?php echo esc_html($template['effects_summary']); ?>
                        </div>
                        
                        <button type="button" class="button button-primary apply-template" data-template="<?php echo esc_attr($template_id); ?>" style="width:100%; margin-top:10px;">
                            Apply Template
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.apply-template').on('click', function() {
                var template = $(this).data('template');
                var nonce = '<?php echo esc_js(wp_create_nonce('apply_seasonal_template')); ?>';
                
                if (!confirm('This will override your current animation settings. Continue?')) {
                    return;
                }
                
                $(this).prop('disabled', true).text('Applying...');
                
                $.post(ajaxurl, {
                    action: 'apply_seasonal_template',
                    template: template,
                    nonce: nonce
                }, function(response) {
                    if (response && response.success) {
                        // Stay on templates tab and show success message
                        // Add timestamp to prevent browser caching
                        window.location.href = '?page=halloween-animations&tab=templates&applied=1&_t=' + new Date().getTime();
                    } else {
                        var errorMsg = (response && response.data) ? response.data : 'Unknown error occurred';
                        alert('❌ Error: ' + errorMsg);
                        $('.apply-template').prop('disabled', false).text('Apply Template');
                    }
                }).fail(function(xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    alert('❌ Failed to apply template. Check console for details.');
                    $('.apply-template').prop('disabled', false).text('Apply Template');
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render Animations Tab
     */
    private function render_animations_tab() {
        $settings = get_option('halloween_animations_settings', array());
        ?>
        <form action="options.php" method="post">
            <?php settings_fields('halloween_animations_settings'); ?>
            <input type="hidden" name="halloween_animations_settings[_active_tab]" value="animations">
            
            <h2>✨ Customize Your Animations</h2>
            <p>Select and customize individual effects for your occasion. You can mix and match effects from different themes!</p>
            
            <div class="animations-grid">
                <?php $this->render_animation_group('Black Friday / Cyber Monday', 'blackfriday', array(
                    'sale_tags' => array('label' => '💰 Sale Tags', 'has_count' => true, 'has_custom_text' => true, 'has_colors' => true, 'max' => 15, 'default' => 8),
                    'shopping_icons' => array('label' => '🛒 Shopping Icons', 'has_count' => true, 'max' => 10, 'default' => 5),
                    'matrix_rain' => array('label' => '🖥️ Matrix Rain', 'has_count' => true, 'max' => 20, 'default' => 10),
                    'glitch_effect' => array('label' => '⚡ Glitch Effect', 'has_count' => false)
                ), '#ff6b35'); ?>
                
                <?php $this->render_animation_group('Christmas', 'christmas', array(
                    'snowflakes' => array('label' => '❄️ Snowflakes', 'has_count' => true, 'has_speed' => true, 'has_size' => true, 'max' => 50, 'default' => 30),
                    'christmas_lights' => array('label' => '💡 Christmas Lights', 'has_count' => false),
                    'ornaments' => array('label' => '🔴 Ornaments', 'has_count' => true, 'max' => 10, 'default' => 5)
                ), '#2ecc71'); ?>
                
                <?php $this->render_animation_group('New Year', 'newyear', array(
                    'fireworks' => array('label' => '🎆 Fireworks', 'has_frequency' => true),
                    'confetti' => array('label' => '🎊 Confetti', 'has_count' => true, 'max' => 60, 'default' => 40),
                    'balloons' => array('label' => '🎈 Balloons', 'has_count' => true, 'max' => 15, 'default' => 8)
                ), '#f39c12'); ?>
                
                <?php $this->render_animation_group('Valentine\'s Day', 'valentines', array(
                    'hearts' => array('label' => '❤️ Falling Hearts', 'has_count' => true, 'max' => 30, 'default' => 15),
                    'heart_confetti' => array('label' => '💕 Heart Confetti', 'has_count' => true, 'max' => 40, 'default' => 20),
                    'pulsating_hearts' => array('label' => '💓 Pulsating Hearts', 'has_count' => true, 'max' => 10, 'default' => 5)
                ), '#e74c3c'); ?>
                
                <?php $this->render_animation_group('Easter', 'easter', array(
                    'easter_eggs' => array('label' => '🥚 Easter Eggs', 'has_count' => true, 'max' => 20, 'default' => 10),
                    'bunny' => array('label' => '🐰 Bunny', 'has_count' => false)
                ), '#9b59b6'); ?>
                
                <?php $this->render_animation_group('Halloween', 'halloween', array(
                    'bats' => array('label' => '🦇 Flying Bats', 'has_count' => true, 'has_speed' => true, 'max' => 20, 'default' => 5),
                    'ghosts' => array('label' => '👻 Floating Ghosts', 'has_count' => true, 'has_speed' => true, 'max' => 20, 'default' => 3),
                    'pumpkin' => array('label' => '🎃 Running Pumpkin', 'has_speed' => true),
                    'leaves' => array('label' => '🍂 Falling Leaves', 'has_count' => true, 'max' => 20, 'default' => 10),
                    'spiders' => array('label' => '🕷️ Crawling Spiders', 'has_count' => true, 'max' => 20, 'default' => 2),
                    'fog' => array('label' => '🌫️ Fog Effect', 'has_speed' => true)
                ), '#ff5722'); ?>
            </div>
            
            <?php submit_button('Save Animation Settings'); ?>
        </form>
        <?php
    }

    /**
     * Render animation group
     */
    private function render_animation_group($title, $group, $effects, $color = '#2271b1') {
        $settings = get_option('halloween_animations_settings', array());
        
        // Count enabled effects
        $enabled_count = 0;
        foreach ($effects as $effect_key => $effect) {
            if (!empty($settings[$effect_key . '_enabled'])) {
                $enabled_count++;
            }
        }
        
        $group_id = sanitize_title($title);
        $is_collapsed = ($enabled_count === 0) ? ' collapsed' : '';
        ?>
        <div class="animation-group-card<?php echo esc_attr($is_collapsed); ?>" data-group="<?php echo esc_attr($group_id); ?>">
            <div class="animation-group-header" style="background: linear-gradient(135deg, <?php echo esc_attr($color); ?> 0%, <?php echo esc_attr($this->adjust_brightness($color, -20)); ?> 100%);">
                <h3 class="group-title">
                    <span class="group-toggle dashicons dashicons-arrow-down-alt2"></span>
                    <?php echo esc_html($title); ?>
                </h3>
                <div class="group-badge">
                    <?php if ($enabled_count > 0): ?>
                        <span class="enabled-count"><?php echo esc_html($enabled_count); ?> Active</span>
                    <?php else: ?>
                        <span class="disabled-count">Disabled</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="animation-group-body" style="<?php echo $is_collapsed ? 'display:none;' : ''; ?>">
            
            <?php foreach ($effects as $effect_key => $effect): 
                $is_enabled = !empty($settings[$effect_key . '_enabled']);
            ?>
                <div class="animation-setting" style="margin-bottom:15px; padding:15px; background:#fff; border:1px solid #ddd; border-radius:4px;">
                    <label style="display:block; margin-bottom:10px; font-weight:600;">
                        <input type="checkbox" name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_enabled]" value="1" class="anim-toggle" <?php checked($is_enabled, true); ?>>
                        <?php echo esc_html($effect['label']); ?>
                        <?php if ($is_enabled): ?>
                            <span style="color:#46b450; font-size:0.9em;">✓ Enabled</span>
                        <?php endif; ?>
                    </label>
                    
                    <div class="sub-settings" style="<?php echo $is_enabled ? 'display:block;' : 'display:none;'; ?>">
                        <div class="settings-row">
                            <?php if (!empty($effect['has_count'])): ?>
                                <div class="setting-field">
                                    <label class="setting-label">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                        Count
                                    </label>
                                    <input type="number" name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_count]" value="<?php echo esc_attr(isset($settings[$effect_key . '_count']) ? $settings[$effect_key . '_count'] : $effect['default']); ?>" min="1" max="<?php echo esc_attr($effect['max']); ?>" class="small-number">
                                    <span class="setting-hint">Max: <?php echo esc_attr($effect['max']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($effect['has_speed'])): ?>
                                <div class="setting-field">
                                    <label class="setting-label">
                                        <span class="dashicons dashicons-controls-forward"></span>
                                        Speed
                                    </label>
                                    <select name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_speed]" class="setting-select">
                                        <option value="slow" <?php selected(isset($settings[$effect_key . '_speed']) ? $settings[$effect_key . '_speed'] : 'medium', 'slow'); ?>>Slow</option>
                                        <option value="medium" <?php selected(isset($settings[$effect_key . '_speed']) ? $settings[$effect_key . '_speed'] : 'medium', 'medium'); ?>>Medium</option>
                                        <option value="fast" <?php selected(isset($settings[$effect_key . '_speed']) ? $settings[$effect_key . '_speed'] : 'medium', 'fast'); ?>>Fast</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($effect['has_frequency'])): ?>
                                <div class="setting-field">
                                    <label class="setting-label">
                                        <span class="dashicons dashicons-performance"></span>
                                        Frequency
                                    </label>
                                    <select name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_frequency]" class="setting-select">
                                        <option value="low" <?php selected(isset($settings[$effect_key . '_frequency']) ? $settings[$effect_key . '_frequency'] : 'medium', 'low'); ?>>Low</option>
                                        <option value="medium" <?php selected(isset($settings[$effect_key . '_frequency']) ? $settings[$effect_key . '_frequency'] : 'medium', 'medium'); ?>>Medium</option>
                                        <option value="high" <?php selected(isset($settings[$effect_key . '_frequency']) ? $settings[$effect_key . '_frequency'] : 'medium', 'high'); ?>>High</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($effect['has_size'])): ?>
                                <div class="setting-field">
                                    <label class="setting-label">
                                        <span class="dashicons dashicons-editor-expand"></span>
                                        Size
                                    </label>
                                    <input type="number" name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_size]" value="<?php echo esc_attr(isset($settings[$effect_key . '_size']) ? $settings[$effect_key . '_size'] : 15); ?>" min="8" max="30" step="1" class="small-number">
                                    <span class="setting-hint">8-30px</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($effect['has_custom_text'])): ?>
                            <div class="setting-field-full">
                                <label class="setting-label">
                                    <span class="dashicons dashicons-edit"></span>
                                    Custom Text (one per line)
                                </label>
                                <textarea name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_text]" rows="4" class="setting-textarea"><?php echo esc_textarea(isset($settings[$effect_key . '_text']) ? $settings[$effect_key . '_text'] : "💰 50% OFF\n🏷️ SALE!\n🛍️ BUY NOW\n💳 75% OFF\n🎁 DEALS\n⚡ FLASH SALE"); ?></textarea>
                                <span class="setting-hint">Enter custom sale messages (emojis supported)</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($effect['has_colors'])): ?>
                            <div class="setting-field-full">
                                <label class="setting-label">
                                    <span class="dashicons dashicons-admin-appearance"></span>
                                    Colors
                                </label>
                                <div class="color-pickers">
                                    <div class="color-picker-field">
                                        <label>Background</label>
                                        <input type="color" name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_bg_color]" value="<?php echo esc_attr(isset($settings[$effect_key . '_bg_color']) ? $settings[$effect_key . '_bg_color'] : '#ff0000'); ?>" class="color-input">
                                    </div>
                                    <div class="color-picker-field">
                                        <label>Text</label>
                                        <input type="color" name="halloween_animations_settings[<?php echo esc_attr($effect_key); ?>_text_color]" value="<?php echo esc_attr(isset($settings[$effect_key . '_text_color']) ? $settings[$effect_key . '_text_color'] : '#ffffff'); ?>" class="color-input">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render Sound Effects Tab
     */
    private function render_sounds_tab() {
        $settings = get_option('halloween_animations_settings', array());
        $sound_dir = HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'assets/sounds/';
        $sound_files = glob($sound_dir . '*.mp3');
        ?>
        <form action="options.php" method="post">
            <?php settings_fields('halloween_animations_settings'); ?>
            <input type="hidden" name="halloween_animations_settings[_active_tab]" value="sounds">
            
            <h2>🔊 Sound Effects</h2>
            <p>Add ambient sounds to enhance your seasonal effects!</p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Sounds</th>
                    <td>
                        <label>
                            <input type="checkbox" name="halloween_animations_settings[sound_enabled]" value="1" <?php checked(isset($settings['sound_enabled']) ? $settings['sound_enabled'] : false, true); ?> id="sound-enabled-toggle">
                            Enable background sounds
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Sound Mode</th>
                    <td>
                        <?php $sound_mode = isset($settings['sound_mode']) ? $settings['sound_mode'] : 'ambient'; ?>
                        <label style="display:block; margin-bottom:8px;">
                            <input type="radio" name="halloween_animations_settings[sound_mode]" value="ambient" <?php checked($sound_mode, 'ambient'); ?>>
                            <strong>Ambient</strong> - Continuous background atmosphere
                        </label>
                        <label style="display:block; margin-bottom:8px;">
                            <input type="radio" name="halloween_animations_settings[sound_mode]" value="random" <?php checked($sound_mode, 'random'); ?>>
                            <strong>Random</strong> - Play random sounds at intervals
                        </label>
                        <label style="display:block; margin-bottom:8px;">
                            <input type="radio" name="halloween_animations_settings[sound_mode]" value="sequential" <?php checked($sound_mode, 'sequential'); ?>>
                            <strong>Sequential</strong> - Play sounds in order
                        </label>
                        <label style="display:block; margin-bottom:8px;">
                            <input type="radio" name="halloween_animations_settings[sound_mode]" value="mix" <?php checked($sound_mode, 'mix'); ?>>
                            <strong>Mix Multiple</strong> - Blend multiple sounds together
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Volume</th>
                    <td>
                        <input type="range" name="halloween_animations_settings[sound_volume]" min="0" max="100" value="<?php echo esc_attr(isset($settings['sound_volume']) ? $settings['sound_volume'] : 50); ?>" style="width:300px;" oninput="this.nextElementSibling.value = this.value + '%'">
                        <output><?php echo esc_attr(isset($settings['sound_volume']) ? $settings['sound_volume'] : 50); ?>%</output>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Play Interval</th>
                    <td>
                        <input type="number" name="halloween_animations_settings[sound_interval]" value="<?php echo esc_attr(isset($settings['sound_interval']) ? $settings['sound_interval'] : 15); ?>" min="5" max="60" style="width:80px;">
                        seconds
                        <p class="description">Time between sounds (for Random mode)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Select Sound Files</th>
                    <td>
                        <?php
                        $selected_sounds = isset($settings['selected_sounds']) ? $settings['selected_sounds'] : array();
                        if (empty($sound_files)) {
                            echo '<p style="color:#d63638;">⚠️ No sound files found in assets/sounds/ directory.</p>';
                        } else {
                            echo '<div style="max-height:500px; overflow-y:auto; border:1px solid #ddd; padding:0; background:#fff; border-radius:4px;">';
                            echo '<table class="widefat striped" style="margin:0; border:none;">';
                            echo '<thead><tr><th style="padding:12px;">Sound File</th><th style="padding:12px; width:100px;">Size</th><th style="padding:12px; width:120px; text-align:center;">Preview</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($sound_files as $sound_file) {
                                $filename = basename($sound_file);
                                $file_size = size_format(filesize($sound_file));
                                $is_checked = in_array($filename, $selected_sounds);
                                
                                echo '<tr>';
                                echo '<td style="padding:12px;">';
                                echo '<label style="cursor:pointer; display:flex; align-items:center;">';
                                echo '<input type="checkbox" name="halloween_animations_settings[selected_sounds][]" value="' . esc_attr($filename) . '" ' . checked($is_checked, true, false) . ' style="margin-right:10px;">';
                                echo '<strong>' . esc_html(str_replace('.mp3', '', $filename)) . '</strong>';
                                echo '</label>';
                                echo '</td>';
                                echo '<td style="padding:12px; color:#666;">' . esc_html($file_size) . '</td>';
                                echo '<td style="padding:12px; text-align:center;">';
                                echo '<button type="button" class="button button-small preview-sound" data-sound="' . esc_attr($filename) . '" style="font-size:12px;">▶ Play</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                            echo '</div>';
                        }
                        ?>
                        <p class="description">Select which sounds to include in your playlist.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save Sound Settings'); ?>
        </form>
        
        <script>
        jQuery(document).ready(function($) {
            var currentAudio = null;
            var currentButton = null;
            
            $('.preview-sound').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var soundFile = $btn.data('sound');
                var soundUrl = '<?php echo esc_url(HALLOWEEN_ANIMATIONS_PLUGIN_URL); ?>assets/sounds/' + soundFile;
                
                // If clicking the same button, toggle play/pause
                if (currentAudio && currentButton && currentButton.is($btn)) {
                    if (currentAudio.paused) {
                        currentAudio.play();
                        $btn.text('⏸ Stop');
                    } else {
                        currentAudio.pause();
                        currentAudio.currentTime = 0;
                        $btn.text('▶ Play');
                        currentAudio = null;
                        currentButton = null;
                    }
                    return;
                }
                
                // Stop previous audio
                if (currentAudio) {
                    currentAudio.pause();
                    currentAudio.currentTime = 0;
                    if (currentButton) {
                        currentButton.text('▶ Play');
                    }
                }
                
                // Play new audio
                currentAudio = new Audio(soundUrl);
                var volume = $('input[name="halloween_animations_settings[sound_volume]"]').val();
                currentAudio.volume = (volume ? volume : 50) / 100;
                
                currentAudio.play().then(function() {
                    $btn.text('⏸ Stop');
                    currentButton = $btn;
                }).catch(function(error) {
                    console.error('Audio playback failed:', error);
                    alert('Failed to play audio. Please check if the file exists.');
                });
                
                currentAudio.onended = function() {
                    $btn.text('▶ Play');
                    currentAudio = null;
                    currentButton = null;
                };
            });
        });
        </script>
        <?php
    }

    /**
     * Helper function to adjust color brightness
     */
    private function adjust_brightness($hex, $percent) {
        // Remove # if present
        $hex = str_replace('#', '', $hex);
        
        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Adjust brightness
        $r = (int) max(0, min(255, $r + ($r * $percent / 100)));
        $g = (int) max(0, min(255, $g + ($g * $percent / 100)));
        $b = (int) max(0, min(255, $b + ($b * $percent / 100)));
        
        // Convert back to hex
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get occasion templates
     */
    private function get_occasion_templates() {
        return array(
            'blackfriday' => array(
                'name' => '🛍️ Black Friday',
                'description' => 'High-energy sales event with animated sale tags and shopping icons.',
                'effects_summary' => 'Sale tags (10), Shopping icons (6) | 🔊 Sounds: Disabled',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'sale_tags_enabled' => 1,
                    'sale_tags_count' => 10,
                    'shopping_icons_enabled' => 1,
                    'shopping_icons_count' => 6,
                    // Sounds
                    'sound_enabled' => 0,
                    'sound_mode' => 'ambient',
                    'sound_volume' => 50,
                    'sound_interval' => 15,
                    'selected_sounds' => array()
                )
            ),
            'cybermonday' => array(
                'name' => '💻 Cyber Monday',
                'description' => 'Tech-focused online sales with Matrix rain and glitch effects.',
                'effects_summary' => 'Matrix rain (10), Glitch effect, Sale tags (10), Shopping icons (6) | 🔊 Sounds: Disabled',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'matrix_rain_enabled' => 1,
                    'matrix_rain_count' => 10,
                    'glitch_effect_enabled' => 1,
                    'sale_tags_enabled' => 1,
                    'sale_tags_count' => 10,
                    'shopping_icons_enabled' => 1,
                    'shopping_icons_count' => 6,
                    // Sounds
                    'sound_enabled' => 0,
                    'sound_mode' => 'ambient',
                    'sound_volume' => 50,
                    'sound_interval' => 15,
                    'selected_sounds' => array()
                )
            ),
            'christmas' => array(
                'name' => '🎄 Christmas',
                'description' => 'Winter wonderland with snowflakes, lights, and ornaments.',
                'effects_summary' => 'Snowflakes (30), Christmas lights, Ornaments (5) | 🔊 Sounds: Jingle Bells, Merry Christmas (Playlist, 40% vol)',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'snowflakes_enabled' => 1,
                    'snowflakes_count' => 30,
                    'snowflakes_speed' => 'medium',
                    'snowflakes_size' => 15,
                    'christmas_lights_enabled' => 1,
                    'ornaments_enabled' => 1,
                    'ornaments_count' => 5,
                    // Sounds
                    'sound_enabled' => 1,
                    'sound_mode' => 'playlist',
                    'sound_volume' => 40,
                    'sound_interval' => 15,
                    'selected_sounds' => array('jingle-bells.mp3', 'piano-merry-christmas.mp3')
                )
            ),
            'newyear' => array(
                'name' => '🎆 New Year',
                'description' => 'Celebration with fireworks, confetti, and balloons.',
                'effects_summary' => 'Fireworks (medium), Confetti (40), Balloons (8) | 🔊 Sounds: Fireworks (Ambient, 45% vol)',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'fireworks_enabled' => 1,
                    'fireworks_frequency' => 'medium',
                    'confetti_enabled' => 1,
                    'confetti_count' => 40,
                    'balloons_enabled' => 1,
                    'balloons_count' => 8,
                    // Sounds
                    'sound_enabled' => 1,
                    'sound_mode' => 'ambient',
                    'sound_volume' => 45,
                    'sound_interval' => 15,
                    'selected_sounds' => array('fireworks.mp3')
                )
            ),
            'valentines' => array(
                'name' => '💝 Valentine\'s Day',
                'description' => 'Romantic atmosphere with falling hearts, heart confetti, and pulsating hearts.',
                'effects_summary' => 'Falling hearts (15), Heart confetti (20), Pulsating hearts (5) | 🔊 Sounds: Disabled',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'hearts_enabled' => 1,
                    'hearts_count' => 15,
                    'heart_confetti_enabled' => 1,
                    'heart_confetti_count' => 20,
                    'pulsating_hearts_enabled' => 1,
                    'pulsating_hearts_count' => 5,
                    // Sounds
                    'sound_enabled' => 0,
                    'sound_mode' => 'ambient',
                    'sound_volume' => 50,
                    'sound_interval' => 15,
                    'selected_sounds' => array()
                )
            ),
            'halloween' => array(
                'name' => '🎃 Halloween',
                'description' => 'Spooky effects with bats, ghosts, and fog.',
                'effects_summary' => 'Bats (5), Ghosts (3), Fog, Spiders (2) | 🔊 Sounds: Spooky Mix (Random, 35% vol, 20s intervals)',
                'preview' => true,
                'settings' => array(
                    // Animations
                    'bats_enabled' => 1,
                    'bats_count' => 5,
                    'bats_speed' => 'medium',
                    'ghosts_enabled' => 1,
                    'ghosts_count' => 3,
                    'fog_enabled' => 1,
                    'fog_speed' => 'medium',
                    'spiders_enabled' => 1,
                    'spiders_count' => 2,
                    'spiders_speed' => 'medium',
                    // Sounds
                    'sound_enabled' => 1,
                    'sound_mode' => 'random',
                    'sound_volume' => 35,
                    'sound_interval' => 20,
                    'selected_sounds' => array('bats.mp3', 'spooky-wind.mp3', 'wolf-howling.mp3', 'evil-witch-laugh.mp3')
                )
            )
        );
    }

    /**
     * AJAX handler for applying templates
     */
    public function ajax_apply_template() {
        try {
            check_ajax_referer('apply_seasonal_template', 'nonce');
            
            if (!current_user_can('manage_options')) {
                wp_send_json_error('Unauthorized');
            }
            
            $template_id = isset($_POST['template']) ? sanitize_text_field(wp_unslash($_POST['template'])) : '';
            $templates = $this->get_occasion_templates();
            
            if (!isset($templates[$template_id])) {
                wp_send_json_error('Invalid template');
            }
        } catch (Exception $e) {
            // error_log('Template application error: ' . $e->getMessage());
            wp_send_json_error('Error: ' . $e->getMessage());
        }
        
        $template = $templates[$template_id];
        $current_settings = get_option('halloween_animations_settings', array());
        
        // Keep only general settings (plugin_enabled, mobile_enabled, display_pages)
        $general_settings_to_keep = array(
            'plugin_enabled' => isset($current_settings['plugin_enabled']) ? $current_settings['plugin_enabled'] : 1,
            'mobile_enabled' => isset($current_settings['mobile_enabled']) ? $current_settings['mobile_enabled'] : 1,
            'display_pages' => isset($current_settings['display_pages']) ? $current_settings['display_pages'] : array('all'),
        );
        
        // Start fresh with only general settings
        $current_settings = $general_settings_to_keep;
        
        // Define ALL possible effect settings to completely disable
        $all_effects = array(
            'sale_tags', 'shopping_icons', 'matrix_rain', 'glitch_effect',
            'snowflakes', 'christmas_lights', 'ornaments',
            'fireworks', 'confetti', 'balloons', 'hearts', 'heart_confetti', 'pulsating_hearts', 
            'roses', 'easter_eggs', 'bunny',
            'bats', 'ghosts', 'pumpkin', 'leaves', 'spiders', 'fog'
        );
        
        // Set all effects to disabled (0) by default
        foreach ($all_effects as $effect) {
            $current_settings[$effect . '_enabled'] = 0;
        }
        
        // Now apply ONLY the template settings (this will enable only the template's effects and configure sounds)
        foreach ($template['settings'] as $key => $value) {
            $current_settings[$key] = $value;
        }
        
        try {
            // Use update_option instead of direct DB query
            $option_name = 'halloween_animations_settings';
            
            // IMPORTANT: Remove sanitization filter temporarily because sanitize_settings()
            // relies on $_POST['_active_tab'] to determine which boolean fields to save.
            // Since we are doing a full programmatic update here, we want to save ALL fields
            // exactly as we prepared them in $current_settings.
            remove_filter('sanitize_option_halloween_animations_settings', array($this, 'sanitize_settings'));
            
            $result = update_option($option_name, $current_settings);
            
            // Re-add filter just in case
            add_filter('sanitize_option_halloween_animations_settings', array($this, 'sanitize_settings'));
            
            // Force cache clear to ensure frontend sees changes immediately
            wp_cache_delete($option_name, 'options');
            
            if ($result === false && $current_settings !== get_option($option_name)) {
                // Only consider it a failure if the value wasn't updated AND it's different
                // update_option returns false if value is unchanged, which is fine
                wp_send_json_error('Database update failed');
            }
            
            wp_send_json_success('Template applied successfully!');
        } catch (Exception $e) {
            // error_log('Template save error: ' . $e->getMessage());
            wp_send_json_error('Save error: ' . $e->getMessage());
        }
    }

    public function enqueue_admin_scripts($hook) {
        // Allow both the main dashboard page and the submenu page
        // toplevel_page_seasonal-effects is the main dashboard
        // seasonal-effects_page_halloween-animations is the animations settings page
        if ('toplevel_page_seasonal-effects' !== $hook && 'seasonal-effects_page_halloween-animations' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'seasonal-admin',
            esc_url(HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/seasonal-admin.css'),
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );

        // Enqueue admin script with jQuery dependency
        wp_enqueue_script(
            'seasonal-admin-script',
            esc_url(HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/admin-animations.js'),
            array('jquery'),
            HALLOWEEN_ANIMATIONS_VERSION,
            true
        );
    }
}