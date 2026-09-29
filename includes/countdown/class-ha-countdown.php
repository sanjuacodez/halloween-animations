<?php
/**
 * Countdown timer: [seasonal_effects_countdown] shortcode and block.
 *
 * Both entry points share one renderer, so a timer looks and behaves the same
 * whether it was added with the block editor, a shortcode, or a page builder's
 * shortcode widget.
 *
 * @package Halloween_Animations
 */

if (!defined('ABSPATH')) {
    exit;
}

class HA_Countdown {

    const SHORTCODE = 'seasonal_effects_countdown';
    const HANDLE    = 'ha-countdown-timer';

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', array($this, 'register'));
    }

    /** Timer styles, shared with the notice bar so the names match across the plugin. */
    public static function styles() {
        return array(
            'simple'  => __('Simple', 'halloween-animations'),
            'rounded' => __('Rounded', 'halloween-animations'),
            'square'  => __('Square', 'halloween-animations'),
            'filled'  => __('Filled', 'halloween-animations'),
            'circle'  => __('Circle', 'halloween-animations'),
        );
    }

    public static function defaults() {
        return array(
            'end'          => '',
            'style'        => 'rounded',
            'size'         => 'medium',
            'labels'       => 'long',
            'align'        => 'center',
            'bg_color'     => '#1e1e1e',
            'text_color'   => '#ffffff',
            'show_seconds' => 'yes',
            'expired_text' => __('This offer has ended.', 'halloween-animations'),
            'title'        => '',
        );
    }

    public function register() {
        wp_register_style(
            self::HANDLE,
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/css/countdown.css',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION
        );
        wp_register_script(
            self::HANDLE,
            HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'assets/js/countdown.js',
            array(),
            HALLOWEEN_ANIMATIONS_VERSION,
            true
        );

        add_shortcode(self::SHORTCODE, array($this, 'shortcode'));

        // Block registration from block.json needs WordPress 5.5+.
        if (function_exists('register_block_type_from_metadata')) {
            wp_register_script(
                'ha-countdown-block-editor',
                HALLOWEEN_ANIMATIONS_PLUGIN_URL . 'includes/countdown/block/editor.js',
                array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render'),
                HALLOWEEN_ANIMATIONS_VERSION,
                true
            );
            wp_localize_script(
                'ha-countdown-block-editor',
                'haCountdownBlock',
                array(
                    'styles'   => self::styles(),
                    'defaults' => self::defaults(),
                    'timezone' => wp_timezone_string(),
                )
            );
            register_block_type(
                HALLOWEEN_ANIMATIONS_PLUGIN_DIR . 'includes/countdown/block',
                array(
                    'render_callback' => array($this, 'render_block'),
                )
            );
        }
    }

    public function shortcode($atts) {
        $atts = shortcode_atts(self::defaults(), $atts, self::SHORTCODE);
        return self::render($atts);
    }

    public function render_block($attributes) {
        $map = array(
            'end'         => 'end',
            'style'       => 'style',
            'size'        => 'size',
            'labels'      => 'labels',
            'align'       => 'align',
            'bgColor'     => 'bg_color',
            'textColor'   => 'text_color',
            'expiredText' => 'expired_text',
            'title'       => 'title',
        );
        $atts = self::defaults();
        foreach ($map as $from => $to) {
            if (isset($attributes[$from]) && '' !== $attributes[$from]) {
                $atts[$to] = $attributes[$from];
            }
        }
        // An empty message is meaningful (hide the timer when it ends), so pass it through as-is.
        foreach (array('expiredText' => 'expired_text', 'title' => 'title') as $from => $to) {
            if (isset($attributes[$from])) {
                $atts[$to] = (string) $attributes[$from];
            }
        }
        if (isset($attributes['showSeconds'])) {
            $atts['show_seconds'] = $attributes['showSeconds'] ? 'yes' : 'no';
        }
        return self::render($atts);
    }

    /**
     * Parse the end date in the site's timezone and return a UTC timestamp.
     * Using the site's timezone means every visitor counts down to the same
     * moment, wherever they are.
     */
    public static function end_timestamp($end) {
        $end = trim((string) $end);
        if ('' === $end) {
            return 0;
        }
        try {
            $date = new DateTimeImmutable($end, wp_timezone());
        } catch (Exception $e) {
            return 0;
        }
        return $date->getTimestamp();
    }

    public static function render($atts) {
        $styles = self::styles();
        $style  = isset($styles[$atts['style']]) ? $atts['style'] : 'rounded';
        $size   = in_array($atts['size'], array('small', 'medium', 'large'), true) ? $atts['size'] : 'medium';
        $align  = in_array($atts['align'], array('left', 'center', 'right'), true) ? $atts['align'] : 'center';
        $long   = 'short' !== $atts['labels'];
        $secs   = !in_array(strtolower((string) $atts['show_seconds']), array('no', '0', 'false'), true);
        $bg     = sanitize_hex_color($atts['bg_color']) ?: '#1e1e1e';
        $fg     = sanitize_hex_color($atts['text_color']) ?: '#ffffff';
        $end    = self::end_timestamp($atts['end']);

        if (!$end) {
            // Only editors see the hint; visitors see nothing rather than a broken timer.
            if (current_user_can('edit_posts')) {
                return '<p class="ha-countdown-timer-notice">' . esc_html__('Countdown timer: set an end date to display it.', 'halloween-animations') . '</p>';
            }
            return '';
        }

        wp_enqueue_style(self::HANDLE);
        wp_enqueue_script(self::HANDLE);

        $units = array(
            'days'    => $long ? __('Days', 'halloween-animations') : __('d', 'halloween-animations'),
            'hours'   => $long ? __('Hours', 'halloween-animations') : __('h', 'halloween-animations'),
            'minutes' => $long ? __('Minutes', 'halloween-animations') : __('m', 'halloween-animations'),
            'seconds' => $long ? __('Seconds', 'halloween-animations') : __('s', 'halloween-animations'),
        );
        if (!$secs) {
            unset($units['seconds']);
        }

        // Render the real remaining time server-side so there is no "00:00" flash before JS runs.
        $left   = max(0, $end - time());
        $values = array(
            'days'    => (int) floor($left / DAY_IN_SECONDS),
            'hours'   => (int) floor(($left % DAY_IN_SECONDS) / HOUR_IN_SECONDS),
            'minutes' => (int) floor(($left % HOUR_IN_SECONDS) / MINUTE_IN_SECONDS),
            'seconds' => (int) ($left % MINUTE_IN_SECONDS),
        );

        $classes = array(
            'ha-countdown-timer',
            'ha-ct-style-' . $style,
            'ha-ct-size-' . $size,
            'ha-ct-align-' . $align,
            $long ? 'ha-ct-labels-long' : 'ha-ct-labels-short',
        );
        if (!$left) {
            $classes[] = 'is-expired';
        }

        $html  = '<div class="' . esc_attr(implode(' ', $classes)) . '"';
        $html .= ' data-end="' . esc_attr($end * 1000) . '"';
        $html .= ' style="--ha-ct-bg:' . esc_attr($bg) . ';--ha-ct-fg:' . esc_attr($fg) . ';">';
        if ('' !== trim((string) $atts['title'])) {
            $html .= '<p class="ha-ct-title">' . esc_html($atts['title']) . '</p>';
        }
        $html .= '<div class="ha-ct-units" role="timer" aria-live="off">';
        $i = 0;
        foreach ($units as $unit => $label) {
            if ($i++ && 'simple' === $style) {
                $html .= '<span class="ha-ct-sep" aria-hidden="true">:</span>';
            }
            $html .= '<div class="ha-ct-unit ha-ct-' . esc_attr($unit) . '">';
            $html .= '<span class="ha-ct-value">' . esc_html(str_pad((string) $values[$unit], 2, '0', STR_PAD_LEFT)) . '</span>';
            $html .= '<span class="ha-ct-label">' . esc_html($label) . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<p class="ha-ct-expired">' . esc_html($atts['expired_text']) . '</p>';
        $html .= '</div>';

        return $html;
    }
}
