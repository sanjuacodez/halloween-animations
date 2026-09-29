/* Countdown timer — ticks every [data-end] timer on the page with one interval. */
(function () {
	'use strict';

	function pad(n) {
		return n < 10 ? '0' + n : String(n);
	}

	function tick(timers) {
		var now = Date.now();
		timers.forEach(function (el) {
			var left = Math.max(0, Math.floor((el._haEnd - now) / 1000));
			var parts = {
				days: Math.floor(left / 86400),
				hours: Math.floor((left % 86400) / 3600),
				minutes: Math.floor((left % 3600) / 60),
				seconds: left % 60
			};
			Object.keys(parts).forEach(function (unit) {
				var node = el.querySelector('.ha-ct-' + unit + ' .ha-ct-value');
				if (node) {
					var text = pad(parts[unit]);
					if (node.textContent !== text) {
						node.textContent = text;
					}
				}
			});
			if (!left) {
				el.classList.add('is-expired');
			}
		});
	}

	function init() {
		var timers = Array.prototype.slice.call(document.querySelectorAll('.ha-countdown-timer[data-end]'));
		timers.forEach(function (el) {
			el._haEnd = parseInt(el.getAttribute('data-end'), 10) || 0;
		});
		timers = timers.filter(function (el) {
			return el._haEnd > 0;
		});
		if (!timers.length) {
			return;
		}
		tick(timers);
		var id = setInterval(function () {
			tick(timers);
			if (timers.every(function (el) { return el.classList.contains('is-expired'); })) {
				clearInterval(id);
			}
		}, 1000);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
