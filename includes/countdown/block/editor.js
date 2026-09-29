/* Countdown Timer block — editor UI. Plain ES5 so it needs no build step. */
(function (blocks, element, blockEditor, components, i18n, ServerSideRender) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var __ = i18n.__;
	var InspectorControls = blockEditor.InspectorControls;
	var BlockControls = blockEditor.BlockControls;
	var AlignmentToolbar = blockEditor.AlignmentToolbar;
	var useBlockProps = blockEditor.useBlockProps;
	var PanelColorSettings = blockEditor.PanelColorSettings;
	var PanelBody = components.PanelBody;
	var SelectControl = components.SelectControl;
	var TextControl = components.TextControl;
	var ToggleControl = components.ToggleControl;
	var DateTimePicker = components.DateTimePicker;
	var Placeholder = components.Placeholder;
	var Button = components.Button;
	var Dropdown = components.Dropdown;
	var config = window.haCountdownBlock || { styles: {}, timezone: '' };

	var styleOptions = Object.keys(config.styles).map(function (key) {
		return { value: key, label: config.styles[key] };
	});

	// Local "YYYY-MM-DDTHH:MM" for the picker; stored as "YYYY-MM-DD HH:MM" in site time.
	function toPicker(value) {
		return value ? value.replace(' ', 'T') : undefined;
	}
	function fromPicker(value) {
		return value ? value.slice(0, 16).replace('T', ' ') : '';
	}
	function readable(value) {
		if (!value) {
			return __('Choose end date & time', 'halloween-animations');
		}
		var d = new Date(value.replace(' ', 'T'));
		return isNaN(d.getTime()) ? value : d.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' });
	}

	function EndDatePicker(props) {
		return el(Dropdown, {
			popoverProps: { placement: 'bottom-start' },
			renderToggle: function (toggle) {
				return el(Button, {
					variant: props.value ? 'secondary' : 'primary',
					onClick: toggle.onToggle,
					'aria-expanded': toggle.isOpen
				}, readable(props.value));
			},
			renderContent: function () {
				return el('div', { style: { padding: '8px' } },
					el(DateTimePicker, {
						currentDate: toPicker(props.value),
						onChange: function (v) { props.onChange(fromPicker(v)); },
						is12Hour: false
					})
				);
			}
		});
	}

	blocks.registerBlockType('halloween-animations/countdown', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps();
			var tzNote = config.timezone
				? __('Counts down in your site timezone:', 'halloween-animations') + ' ' + config.timezone
				: '';

			var inspector = el(InspectorControls, {},
				el(PanelBody, { title: __('Timer', 'halloween-animations'), initialOpen: true },
					el('p', { className: 'components-base-control__label' }, __('Ends at', 'halloween-animations')),
					el(EndDatePicker, { value: a.end, onChange: function (v) { set({ end: v }); } }),
					tzNote && el('p', { className: 'components-base-control__help', style: { marginTop: '8px' } }, tzNote),
					el(TextControl, {
						label: __('Heading (optional)', 'halloween-animations'),
						value: a.title,
						placeholder: __('e.g. Black Friday sale ends in', 'halloween-animations'),
						onChange: function (v) { set({ title: v }); }
					}),
					el(TextControl, {
						label: __('Message when finished', 'halloween-animations'),
						value: a.expiredText,
						help: __('Leave empty to hide the timer once it ends.', 'halloween-animations'),
						onChange: function (v) { set({ expiredText: v }); }
					}),
					el(ToggleControl, {
						label: __('Show seconds', 'halloween-animations'),
						checked: a.showSeconds,
						onChange: function (v) { set({ showSeconds: v }); }
					})
				),
				el(PanelBody, { title: __('Appearance', 'halloween-animations'), initialOpen: true },
					el(SelectControl, {
						label: __('Style', 'halloween-animations'),
						value: a.style,
						options: styleOptions,
						onChange: function (v) { set({ style: v }); }
					}),
					el(SelectControl, {
						label: __('Size', 'halloween-animations'),
						value: a.size,
						options: [
							{ value: 'small', label: __('Small', 'halloween-animations') },
							{ value: 'medium', label: __('Medium', 'halloween-animations') },
							{ value: 'large', label: __('Large', 'halloween-animations') }
						],
						onChange: function (v) { set({ size: v }); }
					}),
					el(SelectControl, {
						label: __('Labels', 'halloween-animations'),
						value: a.labels,
						options: [
							{ value: 'long', label: __('Days, Hours, Minutes', 'halloween-animations') },
							{ value: 'short', label: __('d, h, m', 'halloween-animations') }
						],
						onChange: function (v) { set({ labels: v }); }
					})
				),
				a.style !== 'simple' && el(PanelColorSettings, {
					title: __('Colors', 'halloween-animations'),
					initialOpen: false,
					colorSettings: [
						{ label: __('Box background', 'halloween-animations'), value: a.bgColor, onChange: function (v) { set({ bgColor: v || '#1e1e1e' }); } },
						{ label: __('Numbers & labels', 'halloween-animations'), value: a.textColor, onChange: function (v) { set({ textColor: v || '#ffffff' }); } }
					]
				})
			);

			var toolbar = el(BlockControls, {},
				el(AlignmentToolbar, { value: a.align, onChange: function (v) { set({ align: v || 'center' }); } })
			);

			var body = a.end
				? el(ServerSideRender, { block: 'halloween-animations/countdown', attributes: a })
				: el(Placeholder, {
					icon: 'clock',
					label: __('Countdown Timer', 'halloween-animations'),
					instructions: __('Pick when the countdown ends. You can change the style in the block settings.', 'halloween-animations')
				}, el(EndDatePicker, { value: a.end, onChange: function (v) { set({ end: v }); } }));

			return el(Fragment, {}, toolbar, inspector, el('div', blockProps, body));
		},
		save: function () {
			return null; // Rendered by PHP so the shortcode and block always match.
		}
	});
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.serverSideRender);
