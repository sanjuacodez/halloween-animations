jQuery(document).ready(function($) {
    'use strict';

    // Initialize Color Picker
    $('.ha-color-picker').wpColorPicker();

    // Tab Navigation
    $('.ha-tab-nav .nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');
        
        $('.ha-tab-nav .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        $('.ha-tab-content').hide();
        $('#tab-' + tab).show();
        
        // Update URL
        var url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    });

    // Initial Tab State
    var urlParams = new URLSearchParams(window.location.search);
    var activeTab = urlParams.get('tab') || 'general';
    $('.ha-tab-nav .nav-tab[data-tab="' + activeTab + '"]').click();

    // Dependency Handling
    function checkDependencies() {
        $('.ha-field-wrap[data-depends]').each(function() {
            var $wrap = $(this);
            var depends = $wrap.data('depends');
            var show = true;

            $.each(depends, function(field, value) {
                var $field = $('#' + field);
                var currentValue = $field.val();
                
                // Handle array of values (OR condition)
                if (Array.isArray(value)) {
                    if ($.inArray(currentValue, value) === -1) {
                        show = false;
                        return false; // break
                    }
                } else {
                    if (currentValue !== value) {
                        show = false;
                        return false; // break
                    }
                }
            });

            var $row = $wrap.closest('tr');
            
            if (show) {
                $wrap.show();
                if ($row.length) {
                    $row.show();
                }
            } else {
                $wrap.hide();
                if ($row.length) {
                    $row.hide();
                }
            }
        });
    }

    // Bind change events for dependencies
    $('select, input[type="radio"], input[type="checkbox"]').on('change', checkDependencies);
    
    // Initial check
    checkDependencies();

    // Image Upload
    var mediaUploader;
    
    $('.ha-upload-image').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $wrap = $button.closest('.ha-image-upload-wrap');
        var $input = $wrap.find('input[type="hidden"]');
        var $preview = $wrap.find('.ha-image-preview');
        var $removeBtn = $wrap.find('.ha-remove-image');

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            
            var imgUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $preview.html('<img src="' + imgUrl + '" style="max-width: 200px; height: auto;">').show();
            $removeBtn.show();
        });

        mediaUploader.open();
    });

    $('.ha-remove-image').on('click', function(e) {
        e.preventDefault();
        var $wrap = $(this).closest('.ha-image-upload-wrap');
        $wrap.find('input[type="hidden"]').val('');
        $wrap.find('.ha-image-preview').hide().empty();
        $(this).hide();
    });

    // Visual Select Handling
    $('.ha-visual-option input[type="radio"]').on('change', function() {
        var $wrapper = $(this).closest('.ha-visual-select-wrapper');
        $wrapper.find('.ha-visual-option').removeClass('selected');
        $(this).closest('.ha-visual-option').addClass('selected');
    });

    // Reset Visibility Button
    $('.ha-action-button[data-action="reset_visibility"]').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalText = $btn.text();
        
        $btn.prop('disabled', true).text('Resetting...');
        
        $.ajax({
            url: ha_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'ha_reset_visibility',
                nonce: ha_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Template Application
    $('.ha-apply-template').on('click', function(e) {
        e.preventDefault();
        var $card = $(this).closest('.ha-template-card');
        var settings = $card.data('template');
        
        if (!confirm('Are you sure you want to apply this template? This will overwrite your current settings.')) {
            return;
        }

        $.each(settings, function(key, value) {
            var $field = $('#' + key);
            
            // Handle different field types
            if ($field.length) {
                if ($field.is(':checkbox')) {
                    // Not handling multicheck for now as templates don't use it yet
                } else if ($field.is(':radio')) {
                    // Radio buttons (visual select)
                    $('input[name="ha_notice_bar_settings[' + key + ']"][value="' + value + '"]').prop('checked', true).trigger('change');
                } else if ($field.hasClass('ha-color-picker')) {
                    // Color picker
                    $field.wpColorPicker('color', value);
                } else {
                    // Text, Select, etc.
                    $field.val(value).trigger('change');
                }
            } else {
                // Try finding by name for radios if ID didn't work (though visual select uses ID in our code)
                var $radio = $('input[name="ha_notice_bar_settings[' + key + ']"][value="' + value + '"]');
                if ($radio.length) {
                    $radio.prop('checked', true).trigger('change');
                }
            }

            // Handle TinyMCE Editor
            if (key === 'content' && typeof tinymce !== 'undefined') {
                var editor = tinymce.get('ha_notice_bar_settings_content');
                if (editor) {
                    editor.setContent(value);
                } else {
                    $('#ha_notice_bar_settings_content').val(value);
                }
            }
        });

        // Switch to General tab and show success
        $('.ha-tab-nav .nav-tab[data-tab="general"]').click();
        alert('Template applied successfully! Please save your changes.');
    });

});
