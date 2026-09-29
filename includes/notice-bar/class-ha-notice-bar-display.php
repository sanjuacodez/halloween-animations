<?php
/**
 * Display Class
 *
 * @package HalloweenAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Notice_Bar_Display {

    private $settings;

    public function __construct($settings) {
        $this->settings = $settings;
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('wp_footer', array($this, 'render_banner'), 10);
        add_action('wp_body_open', array($this, 'maybe_render_banner_top'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_filter('body_class', array($this, 'add_body_classes'));
    }

    public function add_body_classes($classes) {
        $options = get_option('ha_notice_bar_settings', array());
        
        if (isset($options['enable']) && $options['enable'] === 'yes') {
            $classes[] = 'has-ha-notice-bar';
            
            if (isset($options['position']) && $options['position'] === 'top') {
                $classes[] = 'has-ha-notice-bar-top';
            } else {
                $classes[] = 'has-ha-notice-bar-bottom';
            }
        }
        
        return $classes;
    }

    public function enqueue_styles() {
        $options = get_option('ha_notice_bar_settings', array());
        $css = $this->generate_dynamic_css($options);
        wp_add_inline_style('ha-notice-bar', $css);
    }

    private function generate_dynamic_css($options) {
        $position = isset($options['position']) ? $options['position'] : 'top';
        $type = isset($options['type']) ? $options['type'] : 'fixed';
        $padding = isset($options['padding']) ? $options['padding'] : '15px';
        $text_color = isset($options['text_color']) ? $options['text_color'] : '#ffffff';

        $background_type = isset($options['background_type']) ? $options['background_type'] : 'color';
        $background_style = '';

        switch ($background_type) {
            case 'color':
                $color = isset($options['background_color']) ? $options['background_color'] : '#000000';
                $background_style = "background-color: {$color};";
                break;
            case 'gradient':
                $start = isset($options['gradient_start_color']) ? $options['gradient_start_color'] : '#ff6b35';
                $end = isset($options['gradient_end_color']) ? $options['gradient_end_color'] : '#ff8c42';
                $direction = isset($options['gradient_direction']) ? $options['gradient_direction'] : 'to right';
                $background_style = "background: linear-gradient({$direction}, {$start}, {$end});";
                break;
            case 'image':
                // Always apply background color as base layer
                $color = isset($options['background_color']) ? $options['background_color'] : '#000000';
                $background_style = "background-color: {$color};";

                if (!empty($options['background_image'])) {
                    $image_url = wp_get_attachment_url($options['background_image']);
                    $background_style .= "
                        background-image: url('{$image_url}');
                        background-size: cover;
                        background-position: center;
                        background-repeat: no-repeat;
                    ";
                }
                break;
        }

        $timer_style = isset($options['timer_style']) ? $options['timer_style'] : 'simple';
        $timer_bg_color = isset($options['timer_bg_color']) ? $options['timer_bg_color'] : '#ffffff';
        $timer_text_color = isset($options['timer_text_color']) ? $options['timer_text_color'] : '#000000';
        $timer_font_size = isset($options['timer_font_size']) ? $options['timer_font_size'] : '14px';
        $timer_border_radius = isset($options['timer_border_radius']) ? $options['timer_border_radius'] : '4px';
        $timer_position = isset($options['timer_position']) ? $options['timer_position'] : 'inline-right';

        $css = "
            .ha-notice-bar {
                {$background_style}
                color: {$text_color};
                padding: {$padding};
                position: {$type};
                width: 100%;
                z-index: 99999;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 50px;
                flex-wrap: wrap;
            }
            .ha-notice-bar a {
                color: inherit;
                text-decoration: underline;
            }
            .ha-notice-bar-content {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
                width: 100%;
                max-width: 1200px;
            }
            .ha-countdown {
                display: inline-flex;
                gap: 10px;
                font-weight: bold;
                font-size: {$timer_font_size};
                color: {$timer_text_color};
            }
            .ha-countdown-item {
                display: inline-flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }
            .ha-countdown-item .value {
                font-weight: bold;
            }
            .ha-countdown-item .label {
                font-size: 0.7em;
                opacity: 0.8;
                margin-top: 2px;
                text-transform: uppercase;
            }
        ";

        // Timer Styles
        if ($timer_style !== 'simple') {
            $css .= "
                .ha-countdown-item {
                    background-color: {$timer_bg_color};
                    padding: 5px 10px;
                    min-width: 40px;
                }
            ";
            
            if ($timer_style === 'rounded') {
                $css .= ".ha-countdown-item { border-radius: {$timer_border_radius}; }";
            } elseif ($timer_style === 'circle') {
                $css .= "
                    .ha-countdown-item { 
                        border-radius: 50%; 
                        width: 50px; 
                        height: 50px; 
                        padding: 0;
                    }
                ";
            } elseif ($timer_style === 'square') {
                $css .= ".ha-countdown-item { border-radius: 0; }";
            } elseif ($timer_style === 'filled') {
                $css .= ".ha-countdown-item { border-radius: {$timer_border_radius}; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }";
            }
        }

        // Timer Position Logic via Flexbox Order
        if ($timer_position === 'inline-left') {
            $css .= ".ha-countdown { order: -1; margin-right: 15px; margin-left: 0; }";
        } elseif ($timer_position === 'inline-right') {
            $css .= ".ha-countdown { order: 1; margin-left: 15px; margin-right: 0; }";
        } elseif ($timer_position === 'above') {
            $css .= "
                .ha-notice-bar-content { flex-direction: column; text-align: center; }
                .ha-countdown { order: -1; margin-bottom: 10px; margin-left: 0; }
            ";
        } elseif ($timer_position === 'below') {
            $css .= "
                .ha-notice-bar-content { flex-direction: column; text-align: center; }
                .ha-countdown { order: 1; margin-top: 10px; margin-left: 0; }
            ";
        }

        // Close Button
        $css .= "
            .ha-close-button {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                opacity: 0.8;
                font-size: 24px;
                line-height: 1;
                padding: 5px;
                z-index: 100000;
                color: inherit;
            }
            .ha-close-button:hover {
                opacity: 1;
            }
            @media screen and (max-width: 600px) {
                .ha-notice-bar { padding-right: 40px; }
            }
        ";

        if ($type === 'fixed') {
            if ($position === 'top') {
                $css .= ".ha-notice-bar { top: 0; left: 0; }";
                // Adjust for admin bar
                $css .= "body.admin-bar .ha-notice-bar { top: 32px; }";
                $css .= "@media screen and (max-width: 782px) { body.admin-bar .ha-notice-bar { top: 46px; } }";
            } else {
                $css .= ".ha-notice-bar { bottom: 0; left: 0; }";
            }
        } else {
            // Relative
            $css .= ".ha-notice-bar { position: relative; }";
        }

        return $css;
    }

    public function maybe_render_banner_top() {
        $options = get_option('ha_notice_bar_settings', array());
        
        // Only render in wp_body_open if it's relative/static and positioned at top
        // This ensures it pushes content down naturally
        if (isset($options['type']) && $options['type'] === 'relative' && 
            isset($options['position']) && $options['position'] === 'top') {
            $this->render_banner_html($options);
            
            // Mark as rendered so footer doesn't render it again
            if (!defined('HA_NOTICE_BAR_RENDERED')) {
                define('HA_NOTICE_BAR_RENDERED', true);
            }
        }
    }

    public function render_banner() {
        if (defined('HA_NOTICE_BAR_RENDERED')) {
            return;
        }

        $options = get_option('ha_notice_bar_settings', array());
        
        // If it's relative top but we are here in footer, it means wp_body_open didn't fire.
        // We should render it and maybe move it with JS if needed, or just let it be.
        // But typically fixed bars are rendered in footer.
        
        $this->render_banner_html($options);
    }

    private function should_display($options) {
        if (!isset($options['enable']) || $options['enable'] !== 'yes') {
            return false;
        }

        // Check schedule
        if (isset($options['enable_schedule']) && $options['enable_schedule'] === 'yes') {
            $start = isset($options['schedule_start']) ? strtotime($options['schedule_start']) : 0;
            $end = isset($options['schedule_end']) ? strtotime($options['schedule_end']) : 0;
            $now = current_time('timestamp');

            if ($start && $now < $start) return false;
            if ($end && $now > $end) return false;
        }

        // Check Page Visibility
        $display_pages = isset($options['display_pages']) ? $options['display_pages'] : array('all');
        if (empty($display_pages)) $display_pages = array('all');

        if (in_array('all', $display_pages)) return true;

        if (is_front_page() && in_array('home', $display_pages)) return true;
        if (is_home() && in_array('blog', $display_pages)) return true;
        if (is_page() && in_array('page', $display_pages)) return true;
        if (is_single() && get_post_type() === 'post' && in_array('post', $display_pages)) return true;
        
        // Check CPTs
        if (is_singular()) {
            $post_type = get_post_type();
            if (in_array($post_type, $display_pages)) return true;
        }

        return false;
    }

    private function render_banner_html($options) {
        if (!$this->should_display($options)) {
            return;
        }

        $content = isset($options['content']) ? $options['content'] : '';
        if (empty($content)) return;

        $classes = array('ha-notice-bar');
        if (isset($options['position'])) $classes[] = 'ha-' . $options['position'];
        if (isset($options['type'])) $classes[] = 'ha-' . $options['type'];

        $timer_style = isset($options['timer_style']) ? $options['timer_style'] : 'simple';
        $show_close = isset($options['show_close_button']) ? $options['show_close_button'] : 'yes';
        $close_behavior = isset($options['close_behavior']) ? $options['close_behavior'] : 'session';
        $server_version = get_option('ha_notice_bar_visibility_version', 1);

        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" id="ha-notice-bar" style="display:none;">
            <div class="ha-notice-bar-content">
                <?php echo wp_kses_post($content); ?>
                
                <?php if (isset($options['enable_timer']) && $options['enable_timer'] === 'yes' && !empty($options['schedule_end'])): ?>
                    <div class="ha-countdown" data-end="<?php echo esc_attr($options['schedule_end']); ?>">
                        <div class="ha-countdown-item ha-days"><span class="value">00</span><span class="label">d</span></div>
                        <?php if ($timer_style === 'simple'): ?><span>:</span><?php endif; ?>
                        <div class="ha-countdown-item ha-hours"><span class="value">00</span><span class="label">h</span></div>
                        <?php if ($timer_style === 'simple'): ?><span>:</span><?php endif; ?>
                        <div class="ha-countdown-item ha-minutes"><span class="value">00</span><span class="label">m</span></div>
                        <?php if ($timer_style === 'simple'): ?><span>:</span><?php endif; ?>
                        <div class="ha-countdown-item ha-seconds"><span class="value">00</span><span class="label">s</span></div>
                    </div>
                    <script>
                    (function() {
                        var countdowns = document.querySelectorAll('.ha-countdown');
                        countdowns.forEach(function(el) {
                            var end = new Date(el.getAttribute('data-end')).getTime();
                            
                            var update = function() {
                                var now = new Date().getTime();
                                var distance = end - now;
                                
                                if (distance < 0) {
                                    el.innerHTML = "EXPIRED";
                                    return;
                                }
                                
                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                
                                el.querySelector('.ha-days .value').innerText = String(days).padStart(2, '0');
                                el.querySelector('.ha-hours .value').innerText = String(hours).padStart(2, '0');
                                el.querySelector('.ha-minutes .value').innerText = String(minutes).padStart(2, '0');
                                el.querySelector('.ha-seconds .value').innerText = String(seconds).padStart(2, '0');
                            };
                            
                            setInterval(update, 1000);
                            update();
                        });
                    })();
                    </script>
                <?php endif; ?>
            </div>

            <?php if ($show_close === 'yes'): ?>
                <div class="ha-close-button" role="button" aria-label="Close">&times;</div>
            <?php endif; ?>
        </div>

        <script>
        (function() {
            var bar = document.getElementById('ha-notice-bar');
            if (!bar) return;

            var behavior = '<?php echo esc_js($close_behavior); ?>';
            var serverVersion = <?php echo intval($server_version); ?>;
            var storageKey = 'ha_notice_bar_hidden';
            var versionKey = 'ha_notice_bar_version';
            
            // Check version reset
            var storedVersion = localStorage.getItem(versionKey);
            if (storedVersion && parseInt(storedVersion) < serverVersion) {
                localStorage.removeItem(storageKey);
                localStorage.setItem(versionKey, serverVersion);
            } else if (!storedVersion) {
                localStorage.setItem(versionKey, serverVersion);
            }

            // Check visibility
            var isHidden = false;
            if (behavior === 'reload') {
                isHidden = false;
            } else if (behavior === 'session') {
                isHidden = sessionStorage.getItem(storageKey);
            } else {
                var expiry = localStorage.getItem(storageKey);
                if (expiry && new Date().getTime() < parseInt(expiry)) {
                    isHidden = true;
                } else {
                    localStorage.removeItem(storageKey);
                }
            }

            if (!isHidden) {
                bar.style.display = 'flex';
                
                <?php if (isset($options['type']) && $options['type'] === 'fixed' && isset($options['position']) && $options['position'] === 'top'): ?>
                var height = bar.offsetHeight;
                document.body.style.paddingTop = (parseInt(getComputedStyle(document.body).paddingTop || 0) + height) + 'px';
                <?php endif; ?>
            }

            // Close Handler
            var closeBtn = bar.querySelector('.ha-close-button');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    bar.style.display = 'none';
                    
                    <?php if (isset($options['type']) && $options['type'] === 'fixed' && isset($options['position']) && $options['position'] === 'top'): ?>
                    document.body.style.paddingTop = '';
                    <?php endif; ?>

                    if (behavior === 'reload') return;

                    if (behavior === 'session') {
                        sessionStorage.setItem(storageKey, 'true');
                    } else {
                        var now = new Date().getTime();
                        var ttl = 0;
                        switch(behavior) {
                            case '1_hour': ttl = 60 * 60 * 1000; break;
                            case '1_day': ttl = 24 * 60 * 60 * 1000; break;
                            case '7_days': ttl = 7 * 24 * 60 * 60 * 1000; break;
                            case 'forever': ttl = 365 * 24 * 60 * 60 * 1000; break;
                        }
                        if (ttl > 0) {
                            localStorage.setItem(storageKey, now + ttl);
                        }
                    }
                });
            }
        })();
        </script>
        <?php
    }
}
