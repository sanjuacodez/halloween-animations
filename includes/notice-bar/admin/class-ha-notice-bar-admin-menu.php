<?php
/**
 * Admin Menu Class
 *
 * @package HalloweenAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Notice_Bar_Admin_Menu {

    private $settings;

    public function __construct($settings) {
        $this->settings = $settings;
        // Priority 25 to appear after Dashboard (20) and before Site Animations (30)
        add_action('admin_menu', array($this, 'add_menu_page'), 25);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_ha_reset_visibility', array($this, 'reset_visibility_cookie'));
    }

    public function reset_visibility_cookie() {
        check_ajax_referer('ha_notice_bar_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'halloween-animations'));
        }

        // We can't actually delete cookies for other users.
        // Instead, we'll update a version option. The frontend will check this version
        // against a local storage or cookie value. If the server version is newer,
        // it will show the bar again.
        $current_version = get_option('ha_notice_bar_visibility_version', 1);
        update_option('ha_notice_bar_visibility_version', $current_version + 1);

        wp_send_json_success(__('Visibility reset successfully. All users will see the notice bar again.', 'halloween-animations'));
    }

    public function add_menu_page() {
        // Add as submenu to Seasonal Effects if it exists, otherwise main menu
        add_submenu_page(
            'seasonal-effects',
            __('Notice Bar', 'halloween-animations'),
            __('Notice Bar', 'halloween-animations'),
            'manage_options',
            'ha-notice-bar',
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'ha-notice-bar') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();

        wp_enqueue_script(
            'ha-notice-bar-admin',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'includes/notice-bar/assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            HALLOWEEN_ANIMATIONS_VERSION,
            true
        );

        wp_localize_script('ha-notice-bar-admin', 'ha_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ha_notice_bar_nonce')
        ));
        
        wp_enqueue_style(
            'ha-notice-bar-admin',
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'includes/notice-bar/assets/css/admin.css',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, no action taken
        $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'general';
        $tabs = array(
            'general' => __('General', 'halloween-animations'),
            'design' => __('Design', 'halloween-animations'),
            'content' => __('Content', 'halloween-animations'),
            'schedule' => __('Schedule', 'halloween-animations'),
            'visibility' => __('Visibility', 'halloween-animations'),
            'templates' => __('Templates', 'halloween-animations'),
        );

        ?>
        <div class="wrap ha-settings-wrap">
            <h1><?php esc_html_e('Notice Bar Settings', 'halloween-animations'); ?></h1>

            <nav class="nav-tab-wrapper ha-tab-nav">
                <?php foreach ($tabs as $tab_id => $tab_name) : ?>
                    <a href="?page=ha-notice-bar&tab=<?php echo esc_attr($tab_id); ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>" 
                       data-tab="<?php echo esc_attr($tab_id); ?>">
                        <?php echo esc_html($tab_name); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" action="options.php" class="ha-settings-form">
                <?php
                settings_fields('ha_notice_bar_settings');
                
                $sections = $this->settings->get_sections();
                
                foreach ($tabs as $tab_id => $tab_name) {
                    printf(
                        '<div class="ha-tab-content" id="tab-%s" style="%s">',
                        esc_attr($tab_id),
                        $tab_id === $current_tab ? 'display: block;' : 'display: none;'
                    );

                    if ($tab_id === 'templates') {
                        $templates = $this->settings->get_templates();
                        echo '<h2>' . esc_html__('Choose a Template', 'halloween-animations') . '</h2>';
                        echo '<p>' . esc_html__('Select a template to automatically configure your notice bar settings. You can customize them afterwards.', 'halloween-animations') . '</p>';
                        echo '<div class="ha-templates-grid">';
                        foreach ($templates as $key => $template) {
                            echo '<div class="ha-template-card" data-template="' . esc_attr(json_encode($template['settings'])) . '">';
                            echo '<div class="ha-template-preview preview-' . esc_attr($key) . '"></div>';
                            echo '<div class="ha-template-info">';
                            echo '<h3>' . esc_html($template['name']) . '</h3>';
                            echo '<p>' . esc_html($template['description']) . '</p>';
                            echo '<button type="button" class="button button-primary ha-apply-template">' . esc_html__('Apply Template', 'halloween-animations') . '</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } elseif (isset($sections[$tab_id])) {
                        echo '<h2>' . esc_html($sections[$tab_id]['title']) . '</h2>';
                        echo '<table class="form-table" role="presentation">';
                        foreach ($sections[$tab_id]['fields'] as $field_id => $field) {
                            echo '<tr>';
                            echo '<th scope="row">' . esc_html($field['title']) . '</th>';
                            echo '<td>';
                            $this->settings->render_field(array_merge($field, array('id' => $field_id)));
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    
                    echo '</div>';
                }
                
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
