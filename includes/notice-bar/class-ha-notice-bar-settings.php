<?php
/**
 * Settings Class
 *
 * @package HalloweenAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Notice_Bar_Settings {

    /**
     * Settings sections.
     *
     * @var array
     */
    private $sections;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init_sections();
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Initialize settings sections.
     */
    private function init_sections() {
        $this->sections = array(
            'general' => array(
                'title' => __('General Settings', 'halloween-animations'),
                'fields' => array(
                    'enable' => array(
                        'title' => __('Enable Notice Bar', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'yes' => __('Yes', 'halloween-animations'),
                            'no' => __('No', 'halloween-animations'),
                        ),
                        'default' => 'yes',
                        'description' => __('Enable or disable the notice bar.', 'halloween-animations'),
                    ),
                    'position' => array(
                        'title' => __('Position', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'top' => __('Top', 'halloween-animations'),
                            'bottom' => __('Bottom', 'halloween-animations'),
                        ),
                        'default' => 'top',
                        'description' => __('Choose where to display the notice bar.', 'halloween-animations'),
                    ),
                    'type' => array(
                        'title' => __('Display Type', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'fixed' => __('Fixed (Sticky)', 'halloween-animations'),
                            'relative' => __('Relative (Scrolls with page)', 'halloween-animations'),
                        ),
                        'default' => 'fixed',
                        'description' => __('Fixed bars stay visible while scrolling. Relative bars scroll with the page.', 'halloween-animations'),
                    ),
                ),
            ),
            'design' => array(
                'title' => __('Design Settings', 'halloween-animations'),
                'fields' => array(
                    'background_type' => array(
                        'title' => __('Background Type', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'color' => __('Solid Color', 'halloween-animations'),
                            'gradient' => __('Gradient', 'halloween-animations'),
                            'image' => __('Image + Color', 'halloween-animations'),
                        ),
                        'default' => 'color',
                        'description' => __('Choose the background style.', 'halloween-animations'),
                    ),
                    'background_color' => array(
                        'title' => __('Background Color', 'halloween-animations'),
                        'type' => 'color',
                        'default' => '#000000',
                        'description' => __('Background color of the notice bar (visible behind transparent images).', 'halloween-animations'),
                        'class' => 'ha-background-field ha-background-color',
                        'depends' => array('background_type' => array('color', 'image')),
                    ),
                    'gradient_start_color' => array(
                        'title' => __('Gradient Start Color', 'halloween-animations'),
                        'type' => 'color',
                        'default' => '#ff6b35',
                        'description' => __('Starting color for gradient.', 'halloween-animations'),
                        'class' => 'ha-background-field ha-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    'gradient_end_color' => array(
                        'title' => __('Gradient End Color', 'halloween-animations'),
                        'type' => 'color',
                        'default' => '#ff8c42',
                        'description' => __('Ending color for gradient.', 'halloween-animations'),
                        'class' => 'ha-background-field ha-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    'gradient_direction' => array(
                        'title' => __('Gradient Direction', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'to right' => __('Left to Right', 'halloween-animations'),
                            'to left' => __('Right to Left', 'halloween-animations'),
                            'to bottom' => __('Top to Bottom', 'halloween-animations'),
                            'to top' => __('Bottom to Top', 'halloween-animations'),
                            '135deg' => __('Diagonal', 'halloween-animations'),
                        ),
                        'default' => 'to right',
                        'class' => 'ha-background-field ha-gradient',
                        'depends' => array('background_type' => 'gradient'),
                    ),
                    'background_image' => array(
                        'title' => __('Background Image', 'halloween-animations'),
                        'type' => 'image',
                        'default' => '',
                        'description' => __('Upload or select a background image.', 'halloween-animations'),
                        'class' => 'ha-background-field ha-image',
                        'depends' => array('background_type' => 'image'),
                    ),
                    'text_color' => array(
                        'title' => __('Text Color', 'halloween-animations'),
                        'type' => 'color',
                        'default' => '#ffffff',
                        'description' => __('Color of the text.', 'halloween-animations'),
                    ),
                    'padding' => array(
                        'title' => __('Padding', 'halloween-animations'),
                        'type' => 'text',
                        'default' => '15px',
                        'description' => __('Padding inside the notice bar (e.g., 15px).', 'halloween-animations'),
                    ),
                ),
            ),
            'content' => array(
                'title' => __('Content Settings', 'halloween-animations'),
                'fields' => array(
                    'content' => array(
                        'title' => __('Notice Content', 'halloween-animations'),
                        'type' => 'editor',
                        'default' => '🎃 Spooky Halloween Sale! Get 50% off!',
                        'description' => __('The content to display.', 'halloween-animations'),
                    ),
                ),
            ),
            'schedule' => array(
                'title' => __('Schedule Settings', 'halloween-animations'),
                'fields' => array(
                    'enable_schedule' => array(
                        'title' => __('Enable Scheduling', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'no' => __('No', 'halloween-animations'),
                            'yes' => __('Yes', 'halloween-animations'),
                        ),
                        'default' => 'no',
                    ),
                    'schedule_start' => array(
                        'title' => __('Start Date', 'halloween-animations'),
                        'type' => 'datetime',
                        'default' => '',
                        'depends' => array('enable_schedule' => 'yes'),
                    ),
                    'schedule_end' => array(
                        'title' => __('End Date', 'halloween-animations'),
                        'type' => 'datetime',
                        'default' => '',
                        'depends' => array('enable_schedule' => 'yes'),
                    ),
                    'enable_timer' => array(
                        'title' => __('Show Countdown Timer', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'no' => __('No', 'halloween-animations'),
                            'yes' => __('Yes', 'halloween-animations'),
                        ),
                        'default' => 'no',
                        'description' => __('Display a countdown timer to the end date.', 'halloween-animations'),
                        'depends' => array('enable_schedule' => 'yes'),
                    ),
                    'timer_style' => array(
                        'title' => __('Timer Style', 'halloween-animations'),
                        'type' => 'visual_select',
                        'options' => array(
                            'simple' => __('Simple', 'halloween-animations'),
                            'rounded' => __('Rounded', 'halloween-animations'),
                            'square' => __('Square', 'halloween-animations'),
                            'filled' => __('Filled', 'halloween-animations'),
                            'circle' => __('Circle', 'halloween-animations'),
                        ),
                        'default' => 'simple',
                        'depends' => array('enable_schedule' => 'yes', 'enable_timer' => 'yes'),
                    ),
                    'timer_position' => array(
                        'title' => __('Timer Position', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'inline-right' => __('Right of Content', 'halloween-animations'),
                            'inline-left' => __('Left of Content', 'halloween-animations'),
                            'below' => __('Below Content', 'halloween-animations'),
                            'above' => __('Above Content', 'halloween-animations'),
                        ),
                        'default' => 'inline-right',
                        'depends' => array('enable_schedule' => 'yes', 'enable_timer' => 'yes'),
                    ),
                    'timer_appearance' => array(
                        'title' => __('Timer Appearance', 'halloween-animations'),
                        'type' => 'composite',
                        'depends' => array('enable_schedule' => 'yes', 'enable_timer' => 'yes'),
                        'sub_fields' => array(
                            'timer_bg_color' => array(
                                'label' => __('Background', 'halloween-animations'),
                                'type' => 'color',
                                'default' => '#ffffff',
                                'class' => 'ha-color-picker',
                            ),
                            'timer_text_color' => array(
                                'label' => __('Text Color', 'halloween-animations'),
                                'type' => 'color',
                                'default' => '#000000',
                                'class' => 'ha-color-picker',
                            ),
                            'timer_font_size' => array(
                                'label' => __('Font Size', 'halloween-animations'),
                                'type' => 'text',
                                'default' => '14px',
                                'width' => '80px',
                            ),
                            'timer_border_radius' => array(
                                'label' => __('Radius', 'halloween-animations'),
                                'type' => 'text',
                                'default' => '4px',
                                'width' => '80px',
                            ),
                        )
                    ),
                ),
            ),
            'visibility' => array(
                'title' => __('Visibility Settings', 'halloween-animations'),
                'fields' => array(
                    'show_close_button' => array(
                        'title' => __('Show Close Button', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'yes' => __('Yes', 'halloween-animations'),
                            'no' => __('No', 'halloween-animations'),
                        ),
                        'default' => 'yes',
                        'description' => __('Allow users to dismiss the notice bar.', 'halloween-animations'),
                    ),
                    'close_behavior' => array(
                        'title' => __('Closing Behavior', 'halloween-animations'),
                        'type' => 'select',
                        'options' => array(
                            'session' => __('Hide for Session (Until Browser Close)', 'halloween-animations'),
                            '1_hour' => __('Hide for 1 Hour', 'halloween-animations'),
                            '1_day' => __('Hide for 1 Day', 'halloween-animations'),
                            '7_days' => __('Hide for 7 Days', 'halloween-animations'),
                            'forever' => __('Hide Forever', 'halloween-animations'),
                            'reload' => __('Show on Every Reload (No Cookie)', 'halloween-animations'),
                        ),
                        'default' => 'session',
                        'description' => __('How long the notice should stay hidden after closing.', 'halloween-animations'),
                        'depends' => array('show_close_button' => 'yes'),
                    ),
                    'display_pages' => array(
                        'title' => __('Display On', 'halloween-animations'),
                        'type' => 'multicheck',
                        'options' => $this->get_display_options(),
                        'default' => array('all'),
                        'description' => __('Select where the notice bar should appear.', 'halloween-animations'),
                    ),
                    'reset_visibility' => array(
                        'title' => __('Reset Visibility', 'halloween-animations'),
                        'type' => 'button',
                        'label' => __('Reset for All Users', 'halloween-animations'),
                        'description' => __('Clicking this will clear the hidden status for all users, making the notice appear again.', 'halloween-animations'),
                    ),
                ),
            ),
        );
    }

    public function register_settings() {
        register_setting(
            'ha_notice_bar_settings',
            'ha_notice_bar_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => $this->get_default_settings()
            )
        );

        foreach ($this->sections as $section_id => $section) {
            add_settings_section(
                $section_id,
                $section['title'],
                null,
                'ha_notice_bar_settings'
            );

            foreach ($section['fields'] as $field_id => $field) {
                add_settings_field(
                    $field_id,
                    $field['title'],
                    array($this, 'render_field'),
                    'ha_notice_bar_settings',
                    $section_id,
                    array_merge($field, array('id' => $field_id))
                );
            }
        }
    }

    public function render_field($args) {
        $options = get_option('ha_notice_bar_settings', array());
        if (!is_array($options)) {
            $options = array();
        }
        
        // For composite fields, we don't need a single value
        $value = isset($options[$args['id']]) ? $options[$args['id']] : (isset($args['default']) ? $args['default'] : '');
        
        $class = isset($args['class']) ? $args['class'] : '';
        
        $data_depends = '';
        if (isset($args['depends'])) {
            $data_depends = json_encode($args['depends']);
        }
        
        echo '<div class="ha-field-wrap ' . esc_attr($class) . '"';
        if ( ! empty( $data_depends ) ) {
            echo ' data-depends="' . esc_attr( $data_depends ) . '"';
        }
        echo '>';
        
        switch ($args['type']) {
            case 'text':
            case 'datetime':
                $type = $args['type'] === 'datetime' ? 'datetime-local' : 'text';
                $width_style = isset($args['width']) ? 'width:' . esc_attr($args['width']) . ';' : '';
                printf(
                    '<input type="%s" id="%s" name="ha_notice_bar_settings[%s]" value="%s" class="regular-text" style="%s">',
                    esc_attr($type),
                    esc_attr($args['id']),
                    esc_attr($args['id']),
                    esc_attr($value),
                    esc_attr($width_style)
                );
                break;
                
            case 'select':
                printf('<select id="%s" name="ha_notice_bar_settings[%s]">', esc_attr($args['id']), esc_attr($args['id']));
                foreach ($args['options'] as $key => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($key),
                        selected($value, $key, false),
                        esc_html($label)
                    );
                }
                echo '</select>';
                break;
                
            case 'color':
                printf(
                    '<input type="text" id="%s" name="ha_notice_bar_settings[%s]" value="%s" class="ha-color-picker" data-default-color="%s">',
                    esc_attr($args['id']),
                    esc_attr($args['id']),
                    esc_attr($value),
                    esc_attr($args['default'])
                );
                break;
                
            case 'visual_select':
                echo '<div class="ha-visual-select-wrapper">';
                foreach ($args['options'] as $key => $label) {
                    $selected_class = $value === $key ? 'selected' : '';
                    echo '<label class="ha-visual-option ' . esc_attr($selected_class) . '">';
                    echo '<input type="radio" name="ha_notice_bar_settings[' . esc_attr($args['id']) . ']" value="' . esc_attr($key) . '"';
                    checked($value, $key);
                    echo '>';
                    echo '<span class="ha-visual-preview preview-' . esc_attr($key) . '"></span>';
                    echo '<span class="ha-visual-label">' . esc_html($label) . '</span>';
                    echo '</label>';
                }
                echo '</div>';
                break;

            case 'composite':
                echo '<div class="ha-composite-field">';
                foreach ($args['sub_fields'] as $sub_id => $sub_field) {
                    $sub_value = isset($options[$sub_id]) ? $options[$sub_id] : $sub_field['default'];
                    echo '<div class="ha-sub-field">';
                    echo '<label>' . esc_html($sub_field['label']) . '</label>';
                    
                    if ($sub_field['type'] === 'color') {
                        printf(
                            '<input type="text" id="%s" name="ha_notice_bar_settings[%s]" value="%s" class="ha-color-picker" data-default-color="%s">',
                            esc_attr($sub_id),
                            esc_attr($sub_id),
                            esc_attr($sub_value),
                            esc_attr($sub_field['default'])
                        );
                    } elseif ($sub_field['type'] === 'text') {
                        $width_style = isset($sub_field['width']) ? 'width:' . esc_attr($sub_field['width']) . ';' : '';
                        printf(
                            '<input type="text" id="%s" name="ha_notice_bar_settings[%s]" value="%s" class="regular-text" style="%s">',
                            esc_attr($sub_id),
                            esc_attr($sub_id),
                            esc_attr($sub_value),
                            esc_attr($width_style)
                        );
                    }
                    echo '</div>';
                }
                echo '</div>';
                break;
                
            case 'editor':
                wp_editor($value, 'ha_notice_bar_settings_' . $args['id'], array(
                    'textarea_name' => 'ha_notice_bar_settings[' . esc_attr($args['id']) . ']',
                    'textarea_rows' => 10,
                    'media_buttons' => false,
                    'teeny' => false,
                    'quicktags' => true,
                    'tinymce' => array(
                        'toolbar1' => 'formatselect,fontsizeselect,forecolor,bold,italic,underline,strikethrough,alignleft,aligncenter,alignright,link,unlink,undo,redo',
                        'toolbar2' => '',
                    ),
                ));
                break;
                
            case 'image':
                $image_url = '';
                if ($value) {
                    $image_url = wp_get_attachment_url($value);
                }
                ?>
                <div class="ha-image-upload-wrap">
                    <input type="hidden" name="ha_notice_bar_settings[<?php echo esc_attr($args['id']); ?>]" value="<?php echo esc_attr($value); ?>">
                    <div class="ha-image-preview" style="<?php echo $image_url ? '' : 'display:none;'; ?>">
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto;">
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button ha-upload-image"><?php esc_html_e('Upload Image', 'halloween-animations'); ?></button>
                    <button type="button" class="button ha-remove-image" style="<?php echo $image_url ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove', 'halloween-animations'); ?></button>
                </div>
                <?php
                break;

            case 'multicheck':
                echo '<div class="ha-multicheck-wrapper">';
                $current_values = is_array($value) ? $value : array();
                foreach ($args['options'] as $key => $label) {
                    echo '<label style="display:block; margin-bottom: 5px;">';
                    echo '<input type="checkbox" name="ha_notice_bar_settings[' . esc_attr($args['id']) . '][]" value="' . esc_attr($key) . '"';
                    if (in_array($key, $current_values)) {
                        echo ' checked="checked"';
                    }
                    echo '> ' . esc_html($label) . '</label>';
                }
                echo '</div>';
                break;

            case 'button':
                printf(
                    '<button type="button" id="%s" class="button button-secondary ha-action-button" data-action="%s">%s</button>',
                    esc_attr($args['id']),
                    esc_attr($args['id']),
                    esc_html($args['label'])
                );
                break;
        }
        
        if (!empty($args['description'])) {
            echo '<p class="description">' . wp_kses_post($args['description']) . '</p>';
        }
        
        echo '</div>';
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        foreach ($this->sections as $section) {
            foreach ($section['fields'] as $field_id => $field) {
                // Handle Composite Fields
                if ($field['type'] === 'composite' && isset($field['sub_fields'])) {
                    foreach ($field['sub_fields'] as $sub_id => $sub_field) {
                        if (isset($input[$sub_id])) {
                            $sanitized[$sub_id] = sanitize_text_field($input[$sub_id]);
                        } else {
                            $sanitized[$sub_id] = $sub_field['default'];
                        }
                    }
                    continue;
                }

                // Handle Standard Fields
                if (isset($input[$field_id])) {
                    if ($field['type'] === 'editor') {
                        $sanitized[$field_id] = wp_kses_post($input[$field_id]);
                    } elseif ($field['type'] === 'multicheck') {
                        $sanitized[$field_id] = array_map('sanitize_text_field', $input[$field_id]);
                    } else {
                        $sanitized[$field_id] = sanitize_text_field($input[$field_id]);
                    }
                } else {
                    // Only set default if it's not a composite container itself
                    if ($field['type'] !== 'composite') {
                        $sanitized[$field_id] = $field['default'];
                    }
                }
            }
        }
        return $sanitized;
    }

    public function get_default_settings() {
        $defaults = array();
        foreach ($this->sections as $section) {
            foreach ($section['fields'] as $field_id => $field) {
                if ($field['type'] === 'composite' && isset($field['sub_fields'])) {
                    foreach ($field['sub_fields'] as $sub_id => $sub_field) {
                        $defaults[$sub_id] = $sub_field['default'];
                    }
                } else {
                    $defaults[$field_id] = $field['default'];
                }
            }
        }
        return $defaults;
    }
    
    public function get_sections() {
        return $this->sections;
    }

    /**
     * Get display options for the notice bar.
     *
     * @return array
     */
    private function get_display_options() {
        $options = array(
            'all' => __('Everywhere', 'halloween-animations'),
            'home' => __('Homepage', 'halloween-animations'),
            'blog' => __('Blog / Posts Page', 'halloween-animations'),
            'page' => __('All Pages', 'halloween-animations'),
            'post' => __('All Posts', 'halloween-animations'),
        );

        // Add public post types
        $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
        foreach ($post_types as $post_type) {
            /* translators: %s: Post type label */
            $options[$post_type->name] = sprintf(__('All %s', 'halloween-animations'), $post_type->label);
        }

        return $options;
    }

    /**
     * Get available templates.
     *
     * @return array
     */
    public function get_templates() {
        return array(
            'black_friday' => array(
                'name' => __('Black Friday', 'halloween-animations'),
                'description' => __('Dark theme with red accents and timer.', 'halloween-animations'),
                'settings' => array(
                    'background_type' => 'color',
                    'background_color' => '#000000',
                    'text_color' => '#ffffff',
                    'content' => '⚫ <strong>BLACK FRIDAY SALE</strong> - Up to 80% OFF! Shop Now ->',
                    'enable_schedule' => 'yes',
                    'enable_timer' => 'yes',
                    'timer_style' => 'filled',
                    'timer_bg_color' => '#cc0000',
                    'timer_text_color' => '#ffffff',
                    'timer_font_size' => '16px',
                    'timer_border_radius' => '4px',
                )
            ),
            'cyber_monday' => array(
                'name' => __('Cyber Monday', 'halloween-animations'),
                'description' => __('Neon blue tech theme.', 'halloween-animations'),
                'settings' => array(
                    'background_type' => 'gradient',
                    'gradient_start_color' => '#0f0c29',
                    'gradient_end_color' => '#302b63',
                    'gradient_direction' => 'to right',
                    'text_color' => '#00ffff',
                    'content' => '💻 <strong>CYBER MONDAY</strong> - Tech Deals Live Now!',
                    'enable_schedule' => 'yes',
                    'enable_timer' => 'yes',
                    'timer_style' => 'simple',
                    'timer_bg_color' => '#000000',
                    'timer_text_color' => '#00ffff',
                    'timer_font_size' => '18px',
                )
            ),
            'christmas_sale' => array(
                'name' => __('Christmas Sale', 'halloween-animations'),
                'description' => __('Festive red and green theme.', 'halloween-animations'),
                'settings' => array(
                    'background_type' => 'color',
                    'background_color' => '#c41e3a',
                    'text_color' => '#ffffff',
                    'content' => '🎄 <strong>Merry Christmas!</strong> Holiday Savings are here!',
                    'enable_schedule' => 'yes',
                    'enable_timer' => 'yes',
                    'timer_style' => 'circle',
                    'timer_bg_color' => '#165b33',
                    'timer_text_color' => '#ffffff',
                    'timer_font_size' => '14px',
                )
            ),
            'special_offer' => array(
                'name' => __('Special Offer', 'halloween-animations'),
                'description' => __('Bright yellow attention grabber.', 'halloween-animations'),
                'settings' => array(
                    'background_type' => 'color',
                    'background_color' => '#ffd700',
                    'text_color' => '#000000',
                    'content' => '⭐ <strong>SPECIAL OFFER</strong> - Limited Time Only!',
                    'enable_schedule' => 'no',
                    'enable_timer' => 'no',
                )
            ),
            'flash_sale' => array(
                'name' => __('Flash Sale', 'halloween-animations'),
                'description' => __('Urgent orange gradient.', 'halloween-animations'),
                'settings' => array(
                    'background_type' => 'gradient',
                    'gradient_start_color' => '#ff416c',
                    'gradient_end_color' => '#ff4b2b',
                    'gradient_direction' => 'to right',
                    'text_color' => '#ffffff',
                    'content' => '⚡ <strong>FLASH SALE</strong> - Ends Soon!',
                    'enable_schedule' => 'yes',
                    'enable_timer' => 'yes',
                    'timer_style' => 'rounded',
                    'timer_bg_color' => '#ffffff',
                    'timer_text_color' => '#ff4b2b',
                    'timer_font_size' => '15px',
                    'timer_border_radius' => '20px',
                )
            ),
        );
    }
}
