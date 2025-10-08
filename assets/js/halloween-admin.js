/**
 * Halloween Effects Admin JavaScript
 * Handles admin interface interactions and animation previews
 * 
 * @package Halloween_Animations
 */

(function($) {
    'use strict';

    class HalloweenAdmin {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeConditionalFields();
            this.addGeneralAnimations();
        }

        bindEvents() {
            // Test animation buttons
            $('.test-animation').on('click', this.testAnimation.bind(this));
            
            // Preview animation buttons
            $('.preview-animation').on('click', this.previewAnimation.bind(this));
            
            // Display option changes
            $('input[name="halloween_animations_settings[display_pages][]"]').on('change', this.handleDisplayOptionChange.bind(this));
            
            // Animation enable/disable toggles
            $('input[type="checkbox"][name*="_enabled"]').on('change', this.handleAnimationToggle.bind(this));
            
            // Sound-related events
            this.bindSoundEvents();
            
            // Form submission
            $('form').on('submit', this.handleFormSubmit.bind(this));
        }

        initializeConditionalFields() {
            // Show/hide custom post types and categories based on display selection
            this.handleDisplayOptionChange();
            
            // Show/hide sub-settings based on animation toggles
            $('input[type="checkbox"][name*="_enabled"]').each((index, element) => {
                this.toggleSubSettings($(element));
            });
        }

        testAnimation(event) {
            event.preventDefault();
            
            const button = $(event.target);
            const animation = button.data('animation');
            
            if (button.hasClass('testing')) {
                return;
            }

            const originalText = button.text();
            
            button.addClass('testing')
                  .text('Testing...')
                  .prop('disabled', true);

            // Create test animation element
            this.createTestElement(animation);

            // Send AJAX request - check if halloween_animations_admin object exists
            const ajaxData = {
                action: 'halloween_animations_test_animation',
                animation: animation,
                nonce: (window.halloween_animations_admin && window.halloween_animations_admin.nonce) || ''
            };

            $.ajax({
                url: (window.halloween_animations_admin && window.halloween_animations_admin.ajax_url) || window.ajaxurl || '/wp-admin/admin-ajax.php',
                method: 'POST',
                data: ajaxData,
                success: (response) => {
                    if (response.success) {
                        let message = response.data.message;
                        
                        // Add details if available
                        if (response.data.details) {
                            message += '<br><small>' + response.data.details + '</small>';
                        }
                        
                        // Add warning if animation is disabled
                        if (response.data.warning) {
                            message += '<br><span style="color: #ff6b35;"><strong>' + response.data.warning + '</strong></span>';
                        }
                        
                        this.showNotice('success', message);
                    } else {
                        this.showNotice('error', response.data ? response.data.message || 'Test failed' : 'Test failed');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX Error:', xhr, status, error);
                    let errorMessage = 'Test failed: ' + error;
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        errorMessage = xhr.responseJSON.data.message;
                    }
                    this.showNotice('error', errorMessage);
                },
                complete: () => {
                    setTimeout(() => {
                        button.removeClass('testing')
                              .text(originalText)
                              .prop('disabled', false);
                    }, 2000);
                }
            });
        }

        previewAnimation(event) {
            event.preventDefault();
            
            const animation = $(event.target).data('animation');
            this.createTestElement(animation);
        }

        createTestElement(animation) {
            // Remove any existing test elements
            $('.admin-preview-' + animation).remove();
            
            let element;
            
            switch (animation) {
                case 'bats':
                    element = this.createBatPreview();
                    break;
                case 'ghosts':
                    element = this.createGhostPreview();
                    break;
                case 'pumpkin':
                    element = this.createPumpkinPreview();
                    break;
                case 'leaves':
                    element = this.createLeavesPreview();
                    break;
                case 'spiders':
                    element = this.createSpiderPreview();
                    break;
                case 'fog':
                    element = this.createFogPreview();
                    break;
                default:
                    element = this.createDefaultPreview(animation);
            }

            $('body').append(element);
            this.showNotice('success', `🎃 Testing ${animation} animation! Look for the preview on your screen.`);

            // Remove element after animation
            setTimeout(() => {
                element.fadeOut(500, () => element.remove());
            }, 4000);
        }

        createBatPreview() {
            const batContainer = $('<div>').addClass('admin-preview-bats').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                zIndex: 999999,
                pointerEvents: 'none',
                overflow: 'hidden'
            });

            // Get plugin URL for bat sprite image - try different methods
            let pluginUrl = '';
            if (window.halloween_animations_admin && window.halloween_animations_admin.plugin_url) {
                pluginUrl = window.halloween_animations_admin.plugin_url;
            } else {
                // Fallback - try to determine from current URL
                pluginUrl = window.location.origin + '/wp-content/plugins/halloween-animations/';
            }

            // Create multiple bats using the same sprite system as frontend
            for (let i = 0; i < 3; i++) {
                const batWrapper = $('<div>').css({
                    position: 'absolute',
                    top: Math.random() * 300 + 100 + 'px',
                    left: '-50px',
                    animation: `admin-bat-fly-${i} 4s linear infinite`
                });

                const bat = $('<div>').addClass('halloween-bat-sprite admin-bat-sprite').css({
                    position: 'absolute',
                    width: '35px',
                    height: '20px',
                    backgroundImage: 'url(' + pluginUrl + 'assets/images/bats.png)',
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: '0 ' + (Math.floor(Math.random() * 4) * -20) + 'px', // Random frame from 4 frames
                    opacity: 0.9,
                    animation: `admin-bat-sprite-animation-${i} 0.4s steps(4) infinite`
                });
                
                batWrapper.append(bat);
                batContainer.append(batWrapper);
            }

            // Add CSS animations matching frontend behavior
            this.addBatAnimations();
            return batContainer;
        }

        createGhostPreview() {
            const ghost = $('<div>').addClass('admin-preview-ghost').text('👻').css({
                position: 'fixed',
                top: '30%',
                left: '20%',
                fontSize: '48px',
                zIndex: 999999,
                pointerEvents: 'none',
                textShadow: '0 0 20px rgba(255, 255, 255, 0.8)',
                animation: 'admin-ghost-float 4s ease-in-out'
            });
            return ghost;
        }

        createPumpkinPreview() {
            const pumpkin = $('<div>').addClass('admin-preview-pumpkin').text('🎃').css({
                position: 'fixed',
                bottom: '50px',
                left: '-80px',
                fontSize: '60px',
                zIndex: 999999,
                pointerEvents: 'none',
                animation: 'admin-pumpkin-run 4s linear'
            });
            return pumpkin;
        }

        createLeavesPreview() {
            const leavesContainer = $('<div>').addClass('admin-preview-leaves').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                zIndex: 999999,
                pointerEvents: 'none'
            });

            // Create multiple falling leaves
            const leafEmojis = ['🍂', '🍁', '🍃'];
            for (let i = 0; i < 5; i++) {
                const leaf = $('<div>').text(leafEmojis[i % leafEmojis.length]).css({
                    position: 'absolute',
                    top: '-30px',
                    left: Math.random() * 80 + 10 + '%',
                    fontSize: '24px',
                    animation: `admin-leaf-fall-${i} ${3 + Math.random() * 2}s linear infinite`,
                    animationDelay: Math.random() * 2 + 's'
                });
                leavesContainer.append(leaf);
            }

            this.addLeavesAnimations();
            return leavesContainer;
        }

        createSpiderPreview() {
            const spider = $('<div>').addClass('admin-preview-spider').text('🕷️').css({
                position: 'fixed',
                top: '-30px',
                left: '70%',
                fontSize: '28px',
                zIndex: 999999,
                pointerEvents: 'none',
                animation: 'admin-spider-crawl 4s linear'
            });
            return spider;
        }

        createFogPreview() {
            const fogContainer = $('<div>').addClass('admin-preview-fog').css({
                position: 'fixed',
                bottom: 0,
                left: 0,
                width: '100%',
                height: '200px',
                zIndex: 999999,
                pointerEvents: 'none',
                overflow: 'hidden'
            });

            // Create multiple fog particles like the frontend
            for (let i = 1; i <= 4; i++) {
                const fogParticle = $('<div>').css({
                    position: 'absolute',
                    bottom: Math.random() * 60 - 20 + 'px',
                    left: '-200px',
                    width: (200 + Math.random() * 200) + 'px',
                    height: (60 + Math.random() * 40) + 'px',
                    background: `radial-gradient(ellipse, rgba(${100 + Math.random() * 50}, ${100 + Math.random() * 50}, ${100 + Math.random() * 50}, 0.8) 0%, transparent 70%)`,
                    borderRadius: '50%',
                    filter: 'blur(25px)',
                    animation: `admin-fog-drift-${i} ${4 + i}s linear infinite`
                });
                fogContainer.append(fogParticle);
            }

            this.addFogAnimations();
            return fogContainer;
        }

        createDefaultPreview(animation) {
            const emoji = this.getAnimationEmoji(animation);
            return $('<div>').addClass('admin-preview-' + animation).text(emoji).css({
                position: 'fixed',
                top: '50%',
                left: '50%',
                fontSize: '48px',
                zIndex: 999999,
                pointerEvents: 'none',
                animation: 'admin-default-bounce 2s ease-in-out 2'
            });
        }

        getAnimationEmoji(animation) {
            const emojiMap = {
                'bats': '🦇',
                'ghosts': '👻',
                'pumpkin': '🎃',
                'leaves': '🍂',
                'spiders': '🕷️',
                'fog': '🌫️'
            };
            return emojiMap[animation] || '🎃';
        }

        addBatAnimations() {
            if ($('#admin-bat-animations').length) return;
            
            const batAnimations = `
                <style id="admin-bat-animations">
                    /* Sprite-based bat flight animations - matching frontend */
                    @keyframes admin-bat-fly-0 {
                        0% { 
                            transform: translateX(-50px) translateY(0px) rotateY(0deg); 
                            opacity: 0; 
                        }
                        5% { opacity: 0.9; }
                        25% { 
                            transform: translateX(25vw) translateY(-20px) rotateY(10deg); 
                        }
                        50% { 
                            transform: translateX(50vw) translateY(20px) rotateY(-10deg); 
                        }
                        75% { 
                            transform: translateX(75vw) translateY(-10px) rotateY(5deg); 
                        }
                        95% { opacity: 0.9; }
                        100% { 
                            transform: translateX(calc(100vw + 50px)) translateY(10px) rotateY(0deg); 
                            opacity: 0; 
                        }
                    }
                    
                    @keyframes admin-bat-fly-1 {
                        0% { 
                            transform: translateX(-50px) translateY(0px) rotateY(0deg); 
                            opacity: 0; 
                        }
                        5% { opacity: 0.9; }
                        30% { 
                            transform: translateX(30vw) translateY(30px) rotateY(-15deg); 
                        }
                        60% { 
                            transform: translateX(60vw) translateY(-15px) rotateY(15deg); 
                        }
                        95% { opacity: 0.9; }
                        100% { 
                            transform: translateX(calc(100vw + 50px)) translateY(5px) rotateY(0deg); 
                            opacity: 0; 
                        }
                    }
                    
                    @keyframes admin-bat-fly-2 {
                        0% { 
                            transform: translateX(-50px) translateY(0px) rotateY(0deg); 
                            opacity: 0; 
                        }
                        5% { opacity: 0.9; }
                        20% { 
                            transform: translateX(20vw) translateY(-25px) rotateY(8deg); 
                        }
                        40% { 
                            transform: translateX(40vw) translateY(25px) rotateY(-12deg); 
                        }
                        80% { 
                            transform: translateX(80vw) translateY(-5px) rotateY(6deg); 
                        }
                        95% { opacity: 0.9; }
                        100% { 
                            transform: translateX(calc(100vw + 50px)) translateY(15px) rotateY(0deg); 
                            opacity: 0; 
                        }
                    }
                    
                    /* Sprite frame animations - matching frontend */
                    @keyframes admin-bat-sprite-animation-0 {
                        0% { background-position: 0 0; }
                        25% { background-position: -35px 0; }
                        50% { background-position: -70px 0; }
                        75% { background-position: -105px 0; }
                        100% { background-position: 0 0; }
                    }
                    
                    @keyframes admin-bat-sprite-animation-1 {
                        0% { background-position: 0 -20px; }
                        25% { background-position: -35px -20px; }
                        50% { background-position: -70px -20px; }
                        75% { background-position: -105px -20px; }
                        100% { background-position: 0 -20px; }
                    }
                    
                    @keyframes admin-bat-sprite-animation-2 {
                        0% { background-position: 0 -40px; }
                        25% { background-position: -35px -40px; }
                        50% { background-position: -70px -40px; }
                        75% { background-position: -105px -40px; }
                        100% { background-position: 0 -40px; }
                    }
                    
                    /* Sprite styling */
                    .admin-bat-sprite {
                        filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
                    }
                    
                    .admin-preview-bats {
                        z-index: 999999 !important;
                    }
                </style>
            `;
            $('head').append(batAnimations);
        }

        addLeavesAnimations() {
            if ($('#admin-leaves-animations').length) return;
            
            const leavesAnimations = `
                <style id="admin-leaves-animations">
                    @keyframes admin-leaf-fall-0 {
                        0% { transform: translateY(-30px) rotate(0deg); opacity: 0; }
                        10% { opacity: 1; }
                        100% { transform: translateY(calc(100vh + 50px)) rotate(360deg); opacity: 0; }
                    }
                    @keyframes admin-leaf-fall-1 {
                        0% { transform: translateY(-30px) rotate(0deg) translateX(0); opacity: 0; }
                        10% { opacity: 1; }
                        50% { transform: translateY(50vh) rotate(180deg) translateX(20px); }
                        100% { transform: translateY(calc(100vh + 50px)) rotate(360deg) translateX(-10px); opacity: 0; }
                    }
                    @keyframes admin-leaf-fall-2 {
                        0% { transform: translateY(-30px) rotate(0deg) translateX(0); opacity: 0; }
                        10% { opacity: 1; }
                        30% { transform: translateY(30vh) rotate(90deg) translateX(-15px); }
                        70% { transform: translateY(70vh) rotate(270deg) translateX(25px); }
                        100% { transform: translateY(calc(100vh + 50px)) rotate(360deg) translateX(0); opacity: 0; }
                    }
                    @keyframes admin-leaf-fall-3 {
                        0% { transform: translateY(-30px) rotate(0deg); opacity: 0; }
                        10% { opacity: 1; }
                        100% { transform: translateY(calc(100vh + 50px)) rotate(-360deg); opacity: 0; }
                    }
                    @keyframes admin-leaf-fall-4 {
                        0% { transform: translateY(-30px) rotate(0deg) translateX(0); opacity: 0; }
                        10% { opacity: 1; }
                        25% { transform: translateY(25vh) rotate(45deg) translateX(15px); }
                        75% { transform: translateY(75vh) rotate(315deg) translateX(-20px); }
                        100% { transform: translateY(calc(100vh + 50px)) rotate(360deg) translateX(5px); opacity: 0; }
                    }
                </style>
            `;
            $('head').append(leavesAnimations);
        }

        addFogAnimations() {
            if ($('#admin-fog-animations').length) return;
            
            const fogAnimations = `
                <style id="admin-fog-animations">
                    @keyframes admin-fog-drift-1 {
                        0% { transform: translateX(-200px) scale(1); opacity: 0; }
                        10% { opacity: 0.8; }
                        90% { opacity: 0.8; }
                        100% { transform: translateX(calc(100vw + 200px)) scale(1.2); opacity: 0; }
                    }
                    @keyframes admin-fog-drift-2 {
                        0% { transform: translateX(-250px) scale(0.8); opacity: 0; }
                        15% { opacity: 0.6; }
                        85% { opacity: 0.6; }
                        100% { transform: translateX(calc(100vw + 250px)) scale(1.1); opacity: 0; }
                    }
                    @keyframes admin-fog-drift-3 {
                        0% { transform: translateX(-300px) scale(1.1); opacity: 0; }
                        20% { opacity: 0.7; }
                        80% { opacity: 0.7; }
                        100% { transform: translateX(calc(100vw + 300px)) scale(1.3); opacity: 0; }
                    }
                    @keyframes admin-fog-drift-4 {
                        0% { transform: translateX(-180px) scale(0.9); opacity: 0; }
                        12% { opacity: 0.5; }
                        88% { opacity: 0.5; }
                        100% { transform: translateX(calc(100vw + 180px)) scale(1.0); opacity: 0; }
                    }
                </style>
            `;
            $('head').append(fogAnimations);
        }

        // Add general admin preview animations
        addGeneralAnimations() {
            if ($('#admin-general-animations').length) return;
            
            const generalAnimations = `
                <style id="admin-general-animations">
                    @keyframes admin-ghost-float {
                        0%, 100% { transform: translateY(0) rotate(-5deg) scale(1); opacity: 0.8; }
                        25% { transform: translateY(-30px) rotate(2deg) scale(1.1); opacity: 1; }
                        50% { transform: translateY(-15px) rotate(5deg) scale(1.05); opacity: 0.9; }
                        75% { transform: translateY(-25px) rotate(-2deg) scale(1.08); opacity: 1; }
                    }
                    
                    @keyframes admin-pumpkin-run {
                        0% { transform: translateX(-80px) rotate(0deg) scale(1); }
                        25% { transform: translateX(25vw) rotate(90deg) scale(1.1); }
                        50% { transform: translateX(50vw) rotate(180deg) scale(1); }
                        75% { transform: translateX(75vw) rotate(270deg) scale(1.1); }
                        100% { transform: translateX(calc(100vw + 80px)) rotate(360deg) scale(1); }
                    }
                    
                    @keyframes admin-spider-crawl {
                        0% { transform: translateY(-30px) rotate(0deg); }
                        25% { transform: translateY(25vh) rotate(90deg); }
                        50% { transform: translateY(50vh) rotate(180deg); }
                        75% { transform: translateY(75vh) rotate(270deg); }
                        100% { transform: translateY(calc(100vh + 30px)) rotate(360deg); }
                    }
                    
                    @keyframes admin-default-bounce {
                        0%, 100% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
                        50% { transform: translate(-50%, -50%) scale(1.2) rotate(180deg); }
                    }
                </style>
            `;
            $('head').append(generalAnimations);
        }

        handleDisplayOptionChange() {
            const selectedValue = $('input[name="halloween_animations_settings[display_pages][]"]:checked').val();
            const customSection = $('#custom-post-types, #categories-selection');
            
            if (selectedValue === 'custom') {
                customSection.show();
            } else {
                customSection.hide();
            }
        }

        handleAnimationToggle(event) {
            const checkbox = $(event.target);
            this.toggleSubSettings(checkbox);
        }

        toggleSubSettings(checkbox) {
            const subSettings = checkbox.closest('.halloween-animation-setting').find('.halloween-sub-settings');
            
            if (checkbox.is(':checked')) {
                subSettings.slideDown(200);
            } else {
                subSettings.slideUp(200);
            }
        }

        // Sound-related methods
        bindSoundEvents() {
            // Sound enabled checkbox toggle
            $('input[name="halloween_animations_settings[sound_enabled]"]').on('change', function() {
                $('.halloween-sub-settings').toggle(this.checked);
            });
            
            // Volume range input
            $('input[name="halloween_animations_settings[sound_volume]"]').on('input', function() {
                $('#volume-display').text(this.value + '%');
            });
            
            // Sound selection checkboxes
            $('input[name="halloween_animations_settings[selected_sounds][]"]').on('change', this.handleSoundSelection.bind(this));
            
            // Sound mode radio buttons
            $('input[name="halloween_animations_settings[sound_mode]"]').on('change', this.handleSoundModeChange.bind(this));
            
            // Test sounds button
            $('.test-sounds').on('click', this.handleTestSounds.bind(this));
        }

        handleSoundSelection() {
            const selectedSounds = $('input[name="halloween_animations_settings[selected_sounds][]"]:checked');
            
            if (selectedSounds.length === 0) {
                this.showNotice('error', 'Please select at least one sound file.');
                return;
            }
            
            // Update visual feedback
            selectedSounds.each(function() {
                $(this).closest('label').css('background', '#e8f4f8');
            });
            
            $('input[name="halloween_animations_settings[selected_sounds][]"]:not(:checked)').each(function() {
                $(this).closest('label').css('background', '#fff');
            });
        }

        handleSoundModeChange(event) {
            const mode = event.target.value;
            const intervalLabel = $('label:contains("Sound Interval")');
            const intervalInput = $('input[name="halloween_animations_settings[sound_interval]"]');
            
            switch(mode) {
                case 'ambient':
                    intervalLabel.find('small').text('Time between random sound effects');
                    intervalInput.attr('min', 5).attr('max', 60);
                    break;
                case 'random':
                    intervalLabel.find('small').text('Time between random sounds');
                    intervalInput.attr('min', 3).attr('max', 120);
                    break;
                case 'playlist':
                    intervalLabel.find('small').text('Time between playlist tracks');
                    intervalInput.attr('min', 2).attr('max', 60);
                    break;
            }
            
            // Show helpful tooltip
            this.showSoundModeTooltip(mode);
        }

        showSoundModeTooltip(mode) {
            const descriptions = {
                'ambient': 'Continuous wind with random sound effects overlaid',
                'random': 'Randomly selected sounds play at intervals',
                'playlist': 'All selected sounds play in sequence repeatedly',
                'chaos': 'Multiple sounds can play simultaneously with unpredictable timing'
            };
            
            const tooltip = $('<div>')
                .addClass('halloween-tooltip')
                .text(descriptions[mode])
                .css({
                    'position': 'absolute',
                    'background': '#333',
                    'color': 'white',
                    'padding': '8px 12px',
                    'border-radius': '4px',
                    'font-size': '12px',
                    'z-index': 1000,
                    'max-width': '250px',
                    'box-shadow': '0 2px 8px rgba(0,0,0,0.2)'
                });
            
            const modeRadio = $('input[name="halloween_animations_settings[sound_mode]"][value="' + mode + '"]');
            const offset = modeRadio.offset();
            
            tooltip.css({
                'top': offset.top - 40,
                'left': offset.left + 200
            });
            
            $('body').append(tooltip);
            
            setTimeout(() => {
                tooltip.fadeOut(300, () => tooltip.remove());
            }, 3000);
        }

        handleTestSounds() {
            const button = $('.test-sounds');
            const originalText = button.text();
            
            button.text('🔊 Testing Sounds...').prop('disabled', true);
            
            // Get selected sounds
            const selectedSounds = [];
            $('input[name="halloween_animations_settings[selected_sounds][]"]:checked').each(function() {
                selectedSounds.push($(this).val());
            });
            
            if (selectedSounds.length === 0) {
                button.text('❌ No Sounds Selected').prop('disabled', false);
                this.showNotice('error', 'Please select at least one sound to test.');
                setTimeout(() => {
                    button.text(originalText);
                }, 2000);
                return;
            }
            
            // Test a random selected sound
            const randomSound = selectedSounds[Math.floor(Math.random() * selectedSounds.length)];
            
            // Get plugin URL - try different methods
            let pluginUrl = '';
            if (window.halloween_animations_admin && window.halloween_animations_admin.sounds_url) {
                pluginUrl = window.halloween_animations_admin.sounds_url;
            } else {
                // Fallback - try to determine from current URL
                pluginUrl = window.location.origin + '/wp-content/plugins/halloween-animations/assets/sounds/';
            }
            
            const soundUrl = pluginUrl + randomSound;
            
            const audio = new Audio(soundUrl);
            audio.volume = 0.3; // Keep it quiet for testing
            
            audio.play().then(() => {
                button.text('✅ Sound Playing').prop('disabled', false);
                this.showNotice('success', 'Testing sound: ' + randomSound.replace(/\.(mp3|wav|ogg)$/i, ''));
                
                // Stop after 3 seconds
                setTimeout(() => {
                    audio.pause();
                    audio.currentTime = 0;
                    button.text(originalText);
                }, 3000);
            }).catch((error) => {
                console.error('Audio play error:', error);
                button.text('❌ Test Failed').prop('disabled', false);
                this.showNotice('error', 'Failed to play sound: ' + randomSound + '. Check if the file exists.');
                
                setTimeout(() => {
                    button.text(originalText);
                }, 2000);
            });
        }

        handleFormSubmit(event) {
            // Add loading state to submit button
            const submitButton = $(event.target).find('input[type="submit"]');
            submitButton.val('Saving...').prop('disabled', true);
            
            // Re-enable button after a delay (form will redirect/reload)
            setTimeout(() => {
                submitButton.val('Save Halloween Settings').prop('disabled', false);
            }, 3000);
        }

        showNotice(type, message) {
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            const notice = $('<div>')
                .addClass('notice ' + noticeClass + ' is-dismissible')
                .html('<p>' + message + '</p>')
                .hide();

            $('.wrap h1').after(notice);
            notice.slideDown(300);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                notice.slideUp(300, () => notice.remove());
            }, 5000);

            // Add dismiss button functionality
            notice.on('click', '.notice-dismiss', function() {
                notice.slideUp(300, () => notice.remove());
            });
        }

        // Method to import/export settings (for future enhancement)
        exportSettings() {
            const form = $('form');
            const formData = form.serializeArray();
            const settings = {};
            
            formData.forEach(item => {
                if (item.name.startsWith('halloween_animations_settings[')) {
                    const key = item.name.replace('halloween_animations_settings[', '').replace(']', '');
                    settings[key] = item.value;
                }
            });
            
            const dataStr = JSON.stringify(settings, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
            
            const exportFileDefaultName = 'halloween-settings.json';
            
            const linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();
        }

        // Keyboard shortcuts
        bindKeyboardShortcuts() {
            $(document).on('keydown', (event) => {
                // Ctrl+S or Cmd+S to save
                if ((event.ctrlKey || event.metaKey) && event.key === 's') {
                    event.preventDefault();
                    $('form').submit();
                }
                
                // Escape to close any open modals or notices
                if (event.key === 'Escape') {
                    $('.notice-dismiss').click();
                }
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Debug: Check if localization object exists
        console.log('WP Halloween Admin Debug Info:');
        console.log('halloween_animations_admin object:', window.halloween_animations_admin);
        console.log('ajaxurl:', window.ajaxurl);
        
        // Add fallback for missing localization
        if (!window.halloween_animations_admin) {
            console.warn('halloween_animations_admin object not found, creating fallback');
            window.halloween_animations_admin = {
                ajax_url: window.ajaxurl || '/wp-admin/admin-ajax.php',
                nonce: '', // Will be empty but AJAX might still work
                sounds_url: '/wp-content/plugins/halloween-animations/assets/sounds/'
            };
        }
        
        window.halloweenAdmin = new HalloweenAdmin();
        
        // Add some spooky styling to the admin page
        const adminStyles = `
            <style>
                @keyframes admin-preview-fog {
                    0% { opacity: 0; }
                    50% { opacity: 0.6; }
                    100% { opacity: 0; }
                }
                
                .halloween-admin-header {
                    position: relative;
                    overflow: hidden;
                }
                
                .halloween-admin-header::after {
                    content: '';
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
                    animation: shine 3s infinite;
                    pointer-events: none;
                }
                
                @keyframes shine {
                    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
                }
            </style>
        `;
        
        $('head').append(adminStyles);
    });

})(jQuery);