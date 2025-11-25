(function($) {
'use strict';
window.SeasonalEffects = {
settings: {},
elements: [],
animationFrame: null,
isVisible: true,
maxElements: 50, // Performance cap
init: function(settings) {
this.settings = settings;
this.maxElements = this.isMobile() ? 20 : 50;
this.initEffects();
this.handleVisibility();
$(window).on('beforeunload', () => this.destroy());
},
initEffects: function() {
const s = this.settings;
const isEnabled = (value) => {
return value === true || value === 1 || value === '1';
};
console.log('Seasonal Effects: Checking enabled effects...');
if (isEnabled(s.sale_tags_enabled)) {
console.log('Creating sale tags:', s.sale_tags_count);
this.createSaleTags(s.sale_tags_count || 8);
}
if (isEnabled(s.shopping_icons_enabled)) {
console.log('Creating shopping icons:', s.shopping_icons_count);
this.createShoppingIcons(s.shopping_icons_count || 5);
}
if (isEnabled(s.matrix_rain_enabled)) {
console.log('Creating Matrix rain:', s.matrix_rain_count);
this.createMatrixRain(s.matrix_rain_count || 10);
}
if (isEnabled(s.glitch_effect_enabled)) {
console.log('Creating glitch effect');
this.createGlitchEffect();
}
if (isEnabled(s.snowflakes_enabled)) {
console.log('Creating snowflakes:', s.snowflakes_count);
this.createSnowflakes(s.snowflakes_count || 30);
}
if (isEnabled(s.christmas_lights_enabled)) {
console.log('Creating Christmas lights');
this.createChristmasLights();
}
if (isEnabled(s.ornaments_enabled)) {
console.log('Creating ornaments:', s.ornaments_count);
this.createOrnaments(s.ornaments_count || 5);
}
if (isEnabled(s.fireworks_enabled)) {
console.log('Starting fireworks');
this.startFireworks();
}
if (isEnabled(s.confetti_enabled)) {
console.log('Creating confetti:', s.confetti_count);
this.createConfetti(s.confetti_count || 40);
}
if (isEnabled(s.balloons_enabled)) {
console.log('Creating balloons:', s.balloons_count);
this.createBalloons(s.balloons_count || 8);
}
if (isEnabled(s.hearts_enabled)) {
console.log('Creating falling hearts:', s.hearts_count);
this.createHearts(s.hearts_count || 15);
}
if (isEnabled(s.heart_confetti_enabled)) {
console.log('Creating heart confetti:', s.heart_confetti_count);
this.createHeartConfetti(s.heart_confetti_count || 20);
}
if (isEnabled(s.pulsating_hearts_enabled)) {
console.log('Creating pulsating hearts:', s.pulsating_hearts_count);
this.createPulsatingHearts(s.pulsating_hearts_count || 5);
}
if (isEnabled(s.easter_eggs_enabled)) {
console.log('Creating Easter eggs:', s.easter_eggs_count);
this.createEasterEggs(s.easter_eggs_count || 10);
}
if (isEnabled(s.bunny_enabled)) {
console.log('Creating bunny');
this.createBunny();
}
console.log('Seasonal Effects: Total elements created:', this.elements.length);
},
createSaleTags: function(count) {
const customText = this.settings.sale_tags_text || "💰 50% OFF\n🏷️ SALE!\n🛍️ BUY NOW\n💳 75% OFF\n🎁 DEALS\n⚡ FLASH SALE";
const tags = customText.split('\n').filter(tag => tag.trim() !== '');
const bgColor = this.settings.sale_tags_bg_color || '#ff0000';
const textColor = this.settings.sale_tags_text_color || '#ffffff';
const isCyberMonday = this.settings.occasion === 'cybermonday';
for (let i = 0; i < Math.min(count, this.maxElements); i++) {
const $tag = $('<div class="seasonal-element seasonal-sale-tag"></div>');
if (isCyberMonday && !this.settings.sale_tags_bg_color) {
$tag.addClass('cyber-monday');
} else {
$tag.css({
'background': 'linear-gradient(135deg, ' + bgColor + ', ' + this.adjustColor(bgColor, -20) + ')',
'color': textColor,
'box-shadow': '0 4px 15px ' + this.hexToRgba(bgColor, 0.5)
});
}
$tag.html(tags[i % tags.length]);
const randomizePosition = () => {
$tag.css({
top: Math.random() * 70 + 10 + '%',
left: Math.random() * 80 + 10 + '%'
});
};
randomizePosition();
$tag.css({
'animation-delay': (i * 0.8) + 's',
'animation-duration': '4s'
});
setInterval(() => {
if (this.isVisible) randomizePosition();
}, 4000 + (i * 800));
$('body').append($tag);
this.elements.push($tag);
}
},
adjustColor: function(color, percent) {
const num = parseInt(color.replace('#', ''), 16);
const amt = Math.round(2.55 * percent);
const R = Math.max(0, Math.min(255, (num >> 16) + amt));
const G = Math.max(0, Math.min(255, (num >> 8 & 0x00FF) + amt));
const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
},
hexToRgba: function(hex, alpha) {
const num = parseInt(hex.replace('#', ''), 16);
const r = (num >> 16) & 255;
const g = (num >> 8) & 255;
const b = num & 255;
return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
},
createShoppingIcons: function(count) {
const icons = ['🛒', '🎁', '💸', '🪙', '📦'];
for (let i = 0; i < Math.min(count, 10); i++) {
const $icon = $('<div class="seasonal-element seasonal-shopping-icon"></div>');
$icon.text(icons[i % icons.length]);
const randomizePosition = () => {
$icon.css({
top: Math.random() * 70 + 10 + '%',
left: Math.random() * 80 + 10 + '%'
});
};
randomizePosition();
$icon.css({
'animation-delay': (i * 0.6) + 's',
'animation-duration': '3s'
});
setInterval(() => {
if (this.isVisible) randomizePosition();
}, 3000 + (i * 600));
$('body').append($icon);
this.elements.push($icon);
}
},
createMatrixRain: function(count) {
const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
const columnCount = Math.min(count || 10, 20);
for (let i = 0; i < columnCount; i++) {
const $rain = $('<div class="seasonal-element matrix-rain"></div>');
let text = '';
const length = 15 + Math.floor(Math.random() * 10);
for (let j = 0; j < length; j++) {
text += chars.charAt(Math.floor(Math.random() * chars.length)) + '<br>';
}
$rain.html(text);
$rain.css({
left: Math.random() * 100 + '%',
'animation-delay': Math.random() * 5 + 's',
'animation-duration': (8 + Math.random() * 4) + 's'
});
$('body').append($rain);
this.elements.push($rain);
}
},
createGlitchEffect: function() {
const $glitch = $('<div class="glitch-overlay"></div>');
$('body').append($glitch);
this.elements.push($glitch);
},
createSnowflakes: function(count) {
const snowflakes = ['❄', '❅', '❆'];
const size = this.settings.snowflakes_size || 15; // Default 15px
console.log('Creating', count, 'snowflakes with size:', size + 'px');
for (let i = 0; i < Math.min(count, this.maxElements); i++) {
const $snowflake = $('<div class="seasonal-element seasonal-snowflake"></div>');
$snowflake.text(snowflakes[Math.floor(Math.random() * 3)]);
const leftPos = Math.random() * 100;
const fontSize = size + 'px'; // Use the size setting
const duration = this.getSnowfallSpeed();
$snowflake.css({
left: leftPos + '%',
'font-size': fontSize,
'animation-delay': Math.random() * 5 + 's',
'animation-duration': duration + 's'
});
$('body').append($snowflake);
this.elements.push($snowflake);
if (i === 0) {
console.log('First snowflake:', {
element: $snowflake[0],
left: leftPos + '%',
fontSize: fontSize,
duration: duration + 's',
visible: $snowflake.is(':visible'),
inDOM: document.body.contains($snowflake[0])
});
}
}
},
getSnowfallSpeed: function() {
const speed = this.settings.snowflakes_speed || 'medium';
switch(speed) {
case 'slow': return 15 + Math.random() * 5;
case 'fast': return 6 + Math.random() * 2;
default: return 10 + Math.random() * 3;
}
},
createChristmasLights: function() {
const $container = $('<div class="christmas-lights-container"></div>');
const colors = ['red', 'green', 'blue', 'yellow', 'white'];
const lightCount = this.isMobile() ? 15 : 25;
for (let i = 0; i < lightCount; i++) {
const $light = $('<div class="christmas-light"></div>');
$light.addClass(colors[i % colors.length]);
$light.css('animation-delay', (Math.random() * 2) + 's');
$container.append($light);
}
$('body').append($container);
this.elements.push($container);
},
createOrnaments: function(count) {
const ornaments = ['🔴', '🟢', '🔵', '🟡', '⚪'];
console.log('Creating', count, 'ornaments');
for (let i = 0; i < Math.min(count, 8); i++) {
const $ornament = $('<div class="seasonal-element seasonal-ornament"></div>');
$ornament.text(ornaments[i % ornaments.length]);
const leftPos = (10 + i * (80 / count)) + '%';
$ornament.css({
top: '20px',
left: leftPos,
'animation-delay': Math.random() * 2 + 's'
});
$('body').append($ornament);
this.elements.push($ornament);
if (i === 0) {
console.log('First ornament:', {
element: $ornament[0],
top: '20px',
left: leftPos,
visible: $ornament.is(':visible'),
inDOM: document.body.contains($ornament[0]),
computedStyle: window.getComputedStyle($ornament[0])
});
}
}
},
startFireworks: function() {
const frequency = this.settings.fireworks_frequency || 'medium';
const delays = { low: 5000, medium: 3000, high: 1500 };
const launchFirework = () => {
if (!this.isVisible) return;
const x = Math.random() * window.innerWidth;
const y = Math.random() * window.innerHeight * 0.6;
this.createFirework(x, y);
setTimeout(launchFirework, delays[frequency] + Math.random() * 2000);
};
launchFirework();
},
createFirework: function(x, y) {
const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ffa500', '#ff1493'];
const particles = this.isMobile() ? 30 : 50;
for (let i = 0; i < particles; i++) {
const $particle = $('<div class="firework-particle"></div>');
const angle = (Math.PI * 2 * i) / particles;
const velocity = 150 + Math.random() * 100;
const tx = Math.cos(angle) * velocity;
const ty = Math.sin(angle) * velocity;
$particle.css({
left: x + 'px',
top: y + 'px',
backgroundColor: colors[Math.floor(Math.random() * colors.length)],
'--tx': tx + 'px',
'--ty': ty + 'px'
});
$('body').append($particle);
setTimeout(() => $particle.remove(), 1800);
}
},
createConfetti: function(count) {
const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'];
for (let i = 0; i < Math.min(count, this.maxElements); i++) {
const $confetti = $('<div class="seasonal-element seasonal-confetti"></div>');
$confetti.css({
left: Math.random() * 100 + '%',
backgroundColor: colors[Math.floor(Math.random() * colors.length)],
'animation-delay': Math.random() * 3 + 's',
'animation-duration': (4 + Math.random() * 2) + 's'
});
$('body').append($confetti);
this.elements.push($confetti);
}
},
createBalloons: function(count) {
const balloons = ['🎈', '🎉', '🎊'];
for (let i = 0; i < Math.min(count, 12); i++) {
const $balloon = $('<div class="seasonal-element seasonal-balloon"></div>');
$balloon.text(balloons[i % balloons.length]);
$balloon.css({
left: Math.random() * 90 + 5 + '%',
'animation-delay': Math.random() * 3 + 's',
'animation-duration': (12 + Math.random() * 6) + 's'
});
$('body').append($balloon);
this.elements.push($balloon);
}
},
createHearts: function(count) {
const hearts = ['❤️', '💕', '💖', '💗', '💓', '💝', '💘'];
for (let i = 0; i < Math.min(count, this.maxElements); i++) {
const $heart = $('<div class="seasonal-element seasonal-heart"></div>');
$heart.text(hearts[Math.floor(Math.random() * hearts.length)]);
const leftPos = Math.random() * 100;
const fontSize = (1 + Math.random() * 1) + 'em';
const duration = 8 + Math.random() * 6;
$heart.css({
left: leftPos + '%',
'font-size': fontSize,
'animation-delay': Math.random() * 5 + 's',
'animation-duration': duration + 's'
});
$('body').append($heart);
this.elements.push($heart);
}
},
createHeartConfetti: function(count) {
const colors = [
'linear-gradient(135deg, #ff1493, #ff69b4)',
'linear-gradient(135deg, #ff69b4, #ffb6c1)',
'linear-gradient(135deg, #ff1493, #c71585)',
'linear-gradient(135deg, #db7093, #ff69b4)'
];
for (let i = 0; i < Math.min(count, this.maxElements); i++) {
const $confetti = $('<div class="seasonal-element seasonal-heart-confetti"></div>');
$confetti.css({
left: Math.random() * 100 + '%',
background: colors[Math.floor(Math.random() * colors.length)],
'animation-delay': Math.random() * 4 + 's',
'animation-duration': (5 + Math.random() * 3) + 's'
});
$('body').append($confetti);
this.elements.push($confetti);
}
},
createPulsatingHearts: function(count) {
const hearts = ['❤️', '💕', '💖', '💗'];
for (let i = 0; i < Math.min(count, 8); i++) {
const $heart = $('<div class="seasonal-element seasonal-pulsating-heart"></div>');
$heart.text(hearts[i % hearts.length]);
$heart.css({
top: Math.random() * 70 + 10 + '%',
left: Math.random() * 80 + 10 + '%',
'animation-delay': (i * 0.2) + 's'
});
$('body').append($heart);
this.elements.push($heart);
}
},
createEasterEggs: function(count) {
const eggs = ['🥚', '🐣', '🌷', '🌼'];
for (let i = 0; i < Math.min(count, 15); i++) {
const $egg = $('<div class="seasonal-element seasonal-easter-egg"></div>');
$egg.text(eggs[i % eggs.length]);
$egg.css({
bottom: '0',
left: Math.random() * 90 + 5 + '%',
'animation-delay': Math.random() * 2 + 's'
});
$('body').append($egg);
this.elements.push($egg);
}
},
createBunny: function() {
const sides = ['bottom', 'top', 'left', 'right'];
sides.forEach((side, index) => {
const $bunny = $('<div class="seasonal-element seasonal-bunny"></div>');
$bunny.text('🐰');
$bunny.addClass(side);
$('body').append($bunny);
this.elements.push($bunny);
const startPeek = () => {
if (!this.isVisible) return;
setTimeout(() => {
$bunny.addClass('peeking');
}, 500);
setTimeout(() => {
$bunny.removeClass('peeking');
}, 2500); // Shows for 2 seconds
setTimeout(startPeek, 8000 + Math.random() * 7000);
};
setTimeout(startPeek, index * 3000);
});
},
isMobile: function() {
return window.innerWidth <= 768;
},
handleVisibility: function() {
const self = this;
document.addEventListener('visibilitychange', function() {
self.isVisible = !document.hidden;
if (document.hidden) {
self.pauseAnimations();
} else {
self.resumeAnimations();
}
});
},
pauseAnimations: function() {
$('.seasonal-element, .christmas-lights-container, .christmas-light, .firework-particle')
.css('animation-play-state', 'paused');
},
resumeAnimations: function() {
$('.seasonal-element, .christmas-lights-container, .christmas-light')
.css('animation-play-state', 'running');
},
destroy: function() {
this.elements.forEach($el => $el.remove());
this.elements = [];
$('.seasonal-element, .christmas-lights-container, .firework-particle').remove();
}
};
$(document).ready(function() {
if (typeof seasonalEffectsSettings !== 'undefined') {
console.log('Seasonal Effects: Initializing...', seasonalEffectsSettings);
window.SeasonalEffects.init(seasonalEffectsSettings);
console.log('Seasonal Effects: Initialized successfully');
} else {
console.warn('Seasonal Effects: seasonalEffectsSettings not found');
}
});
})(jQuery);