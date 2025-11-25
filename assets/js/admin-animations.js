jQuery(document).ready(function($) {
'use strict';
$('.anim-toggle').on('change', function() {
$(this).closest('.animation-setting').find('.sub-settings').slideToggle(200);
});
$('.animation-group-header').on('click', function() {
var $card = $(this).closest('.animation-group-card');
$card.toggleClass('collapsed');
$card.find('.animation-group-body').slideToggle(300);
});
$('.anim-toggle').on('change', function() {
var $card = $(this).closest('.animation-group-card');
var enabledCount = $card.find('.anim-toggle:checked').length;
var $badge = $card.find('.group-badge');
if (enabledCount > 0) {
$badge.html('<span class="enabled-count">' + enabledCount + ' Active</span>');
} else {
$badge.html('<span class="disabled-count">Disabled</span>');
}
});
});