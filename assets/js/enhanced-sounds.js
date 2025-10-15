/**
 * Enhanced Halloween Sound System
 * Supports multiple playback modes, sound selection, and admin controls
 */

(function($) {
    'use strict';

    class HalloweenSoundManager {
        constructor() {
            // Initialize properties
            this.sounds = {};
            this.audioElements = new Map();
            this.loadedSounds = 0;
            this.allSoundsLoaded = false;
            this.isPlaying = false;
            this.userInteracted = false;
            this.errorShown = false;
            this.currentPlaylist = [];
            this.currentTrackIndex = 0;
            this.ambientSound = null;
            
            // Timer management for different modes
            this.ambientTimer = null;
            this.randomTimer = null;
            this.playlistTimer = null;
            this.chaosTimer1 = null;
            this.chaosTimer2 = null;
            this.chaosTimer3 = null;
            this.chaosTimer4 = null;
            
            // Get settings from localized script with better fallbacks
            this.settings = (typeof halloween_animations_ajax !== 'undefined' && halloween_animations_ajax.soundSettings) ? halloween_animations_ajax.soundSettings : {};
            this.pluginUrl = (typeof halloween_animations_ajax !== 'undefined') ? halloween_animations_ajax.pluginUrl : '';
            this.mode = this.settings.mode || 'ambient';
            this.selectedSounds = this.settings.selectedSounds || ['spooky-wind.mp3', 'owl-hooting.mp3'];
            this.interval = (this.settings.interval || 15) * 1000; // Convert to milliseconds
            this.volume = (this.settings.volume || 50) / 100; // Convert to decimal
            this.soundEnabled = this.settings.enabled || false;
            
            // Initialize sound system
            this.init();
        }

        init() {
            if (!this.soundEnabled) {
                return;
            }

            this.initializeAudioContext();
            this.preloadSounds();
            this.bindEvents();
            this.waitForUserInteraction();
        }

        initializeAudioContext() {
            // Initialize Web Audio API context for better sound control
            try {
                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                if (typeof AudioContext !== 'undefined') {
                    this.audioContext = new AudioContext();
                } else {
                    // Fallback to HTML5 audio
                }
            } catch (error) {
                // Fallback to HTML5 audio
            }
        }

        preloadSounds() {
            this.selectedSounds.forEach(soundFile => {
                const audio = new Audio(this.pluginUrl + 'assets/sounds/' + soundFile);
                audio.preload = 'auto';
                audio.volume = this.volume;
                
                audio.addEventListener('canplaythrough', () => {
                    this.loadedSounds++;
                    if (this.loadedSounds === this.selectedSounds.length) {
                        this.allSoundsLoaded = true;
                    }
                });
                
                audio.addEventListener('error', (e) => {
                    // Silently handle audio loading errors
                });
                
                // Store in both Map and object for compatibility
                this.audioElements.set(soundFile, audio);
                this.sounds[soundFile.replace('.mp3', '')] = audio;
            });
        }

        createPlaylist() {
            this.currentPlaylist = [];
            
            // Add all loaded sounds except ambient wind
            Object.keys(this.sounds).forEach(soundKey => {
                if (soundKey !== 'spooky-wind' || this.mode !== 'ambient') {
                    this.currentPlaylist.push(soundKey);
                }
            });

            // Shuffle for random mode
            if (this.mode === 'random') {
                this.shuffleArray(this.currentPlaylist);
            }
        }        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }

        bindEvents() {
            // Handle visibility changes
            this.handleVisibilityChange();
            
            // Handle page unload
            $(window).on('beforeunload', () => {
                this.stopAllSounds();
            });
        }

        waitForUserInteraction() {
            if (this.userInteracted) {
                return;
            }

            // Create start button with enhanced styling
            const modeEmojis = {
                'ambient': '🌬️',
                'random': '🎲', 
                'playlist': '📜',
                'chaos': '💀'
            };
            
            const modeNames = {
                'ambient': 'Ambient',
                'random': 'Random',
                'playlist': 'Playlist', 
                'chaos': 'Chaos'
            };
            
            const startButton = $(`
                <div style="
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    background: linear-gradient(135deg, #ff6b35, #f7931e);
                    color: white;
                    padding: 15px 20px;
                    border-radius: 25px;
                    font-family: 'Creepster', cursive, Arial, sans-serif;
                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                    z-index: 999999;
                    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
                    border: 2px solid #ff8c5a;
                    transition: all 0.3s ease;
                    user-select: none;
                    text-align: center;
                    min-width: 200px;
                ">
                    🎃 Start Spooky Sounds<br>
                    <small style="font-size: 12px; opacity: 0.9;">${modeEmojis[this.mode]} ${modeNames[this.mode]} Mode</small>
                </div>
            `);

            // Add hover effects
            startButton.hover(
                function() { 
                    $(this).css({
                        'transform': 'scale(1.05) translateY(-2px)',
                        'box-shadow': '0 6px 20px rgba(255, 107, 53, 0.6)'
                    }); 
                },
                function() { 
                    $(this).css({
                        'transform': 'scale(1) translateY(0)',
                        'box-shadow': '0 4px 15px rgba(255, 107, 53, 0.4)'
                    }); 
                }
            );

            startButton.on('click', () => {
                this.userInteracted = true;
                startButton.html('🔊 Activating Sounds...<br><small>Loading...</small>');
                
                // Test if sounds can play
                this.testSounds().then(() => {
                    this.startSoundSystem();
                    startButton.fadeOut(300, () => startButton.remove());
                }).catch((error) => {
                    console.error('Sound test failed:', error);
                    startButton.html('🔇 Sound Error<br><small>Check console</small>');
                    setTimeout(() => {
                        startButton.fadeOut(300, () => startButton.remove());
                    }, 3000);
                });
            });

            // Only show if sounds are enabled and not already interacted
            if (!this.userInteracted) {
                $('body').append(startButton);
                
                // Auto-hide after 15 seconds
                setTimeout(() => {
                    if (startButton.is(':visible')) {
                        startButton.fadeOut(300, () => startButton.remove());
                    }
                }, 15000);
            }
        }

        testSounds() {
            return new Promise((resolve, reject) => {
                const testResults = {
                    loaded: [],
                    failed: [],
                    errors: []
                };

                const soundKeys = Object.keys(this.sounds);
                if (soundKeys.length === 0) {
                    reject(new Error('No sounds loaded'));
                    return;
                }

                let testsCompleted = 0;
                const totalTests = soundKeys.length;

                soundKeys.forEach(soundKey => {
                    const sound = this.sounds[soundKey];
                    if (sound) {
                        const originalVolume = sound.volume;
                        sound.volume = 0.05; // Very quiet for test
                        
                        sound.play().then(() => {
                            testResults.loaded.push(soundKey);
                            sound.pause();
                            sound.currentTime = 0;
                            sound.volume = originalVolume;
                            
                            testsCompleted++;
                            if (testsCompleted === totalTests) {
                                this.handleTestResults(testResults, resolve, reject);
                            }
                        }).catch((error) => {
                            testResults.failed.push(soundKey);
                            testResults.errors.push(`${soundKey}: ${error.message}`);
                            
                            testsCompleted++;
                            if (testsCompleted === totalTests) {
                                this.handleTestResults(testResults, resolve, reject);
                            }
                        });
                    } else {
                        testResults.failed.push(soundKey);
                        testResults.errors.push(`${soundKey}: Sound object not found`);
                        
                        testsCompleted++;
                        if (testsCompleted === totalTests) {
                            this.handleTestResults(testResults, resolve, reject);
                        }
                    }
                });

                // Timeout after 3 seconds
                setTimeout(() => {
                    if (testsCompleted < totalTests) {
                        reject(new Error('Sound test timeout'));
                    }
                }, 3000);
            });
        }

        handleTestResults(testResults, resolve, reject) {
            if (testResults.loaded.length > 0) {
                resolve(testResults);
            } else {
                this.showSoundErrorNotification(testResults.errors);
                reject(new Error(`No sounds could be loaded: ${testResults.errors.join(', ')}`));
            }
        }

        showSoundErrorNotification(errors) {
            const notification = $(`
                <div style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #2c1810;
                    border: 2px solid #ff6b35;
                    border-radius: 8px;
                    padding: 15px;
                    color: #ff6b35;
                    font-family: 'Creepster', cursive, Arial, sans-serif;
                    font-size: 14px;
                    max-width: 320px;
                    z-index: 999999;
                    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
                    animation: fadeInSlide 0.5s ease-out;
                ">
                    <div style="font-weight: bold; margin-bottom: 8px;">🎃 Halloween Sounds Issue</div>
                    <div style="font-size: 12px; line-height: 1.4;">
                        ${errors.length > 0 ? errors.join('<br>') : 'Unknown sound error'}
                        <br><small style="color: #cc5529; margin-top: 5px; display: block;">
                            Check browser console for details.
                        </small>
                    </div>
                </div>
            `);

            // Add animation keyframes
            if (!$('#halloween-notification-styles').length) {
                $('head').append(`
                    <style id="halloween-notification-styles">
                        @keyframes fadeInSlide {
                            from { opacity: 0; transform: translateX(100%); }
                            to { opacity: 1; transform: translateX(0); }
                        }
                    </style>
                `);
            }

            $('body').append(notification);

            // Auto-remove after 8 seconds
            setTimeout(() => {
                notification.fadeOut(500, () => notification.remove());
            }, 8000);
        }

        startSoundSystem() {
            if (!this.userInteracted || this.isPlaying) {
                return;
            }

            this.isPlaying = true;

            // Stop any previous timers
            this.clearAllTimers();

            switch (this.mode) {
                case 'ambient':
                    this.startAmbientMode();
                    break;
                case 'random':
                    this.startRandomMode();
                    break;
                case 'playlist':
                    this.startPlaylistMode();
                    break;
                case 'chaos':
                    this.startChaosMode();
                    break;
                default:
                    console.warn('Unknown sound mode:', this.mode);
                    this.startAmbientMode();
            }
        }

        startAmbientMode() {
            // Start continuous wind sound
            const windSound = this.sounds['spooky-wind'];
            if (windSound) {
                windSound.loop = true;
                windSound.play().catch(e => {
                    // Silently handle play error
                });
            }

            // Schedule random effects from other sounds (excluding wind)
            this.scheduleAmbientEffects();
        }

        startRandomMode() {
            // Create and shuffle playlist for random mode
            this.createPlaylist();
            
            // No continuous background sound - just random individual sounds
            this.scheduleNextRandomSound();
        }

        startPlaylistMode() {
            // Create playlist for sequential playback
            this.createPlaylist();
            
            if (this.currentPlaylist.length === 0) {
                return;
            }

            this.currentTrackIndex = 0;
            this.playNextInSequence();
        }

        startChaosMode() {
            // Four overlapping timers with different intervals for chaotic effect
            this.scheduleRandomChaosEffects();
        }

        // AMBIENT MODE METHODS
        scheduleAmbientEffects() {
            if (!this.isPlaying) return;

            // Get non-wind sounds for effects
            const effectSounds = this.currentPlaylist.filter(key => key !== 'spooky-wind');
            
            if (effectSounds.length === 0) {
                setTimeout(() => this.scheduleAmbientEffects(), this.interval);
                return;
            }

            // More variation in ambient mode - 50% to 150% of interval
            const minDelay = this.interval * 0.5;
            const maxDelay = this.interval * 1.5;
            const delay = minDelay + Math.random() * (maxDelay - minDelay);

            this.ambientTimer = setTimeout(() => {
                if (this.isPlaying && this.userInteracted) {
                    const randomSound = effectSounds[Math.floor(Math.random() * effectSounds.length)];
                    this.playSound(randomSound, this.volume * 0.7); // Quieter for ambient
                }
                
                this.scheduleAmbientEffects();
            }, delay);
        }

        // RANDOM MODE METHODS  
        scheduleNextRandomSound() {
            if (!this.isPlaying) return;

            // Shuffle playlist if we've played all sounds
            if (this.currentTrackIndex >= this.currentPlaylist.length) {
                this.shuffleArray(this.currentPlaylist);
                this.currentTrackIndex = 0;
            }

            const soundKey = this.currentPlaylist[this.currentTrackIndex];
            this.playSound(soundKey, this.volume);
            this.currentTrackIndex++;

            // Schedule next sound with exact interval (no variation for predictability)
            this.randomTimer = setTimeout(() => {
                this.scheduleNextRandomSound();
            }, this.interval);
        }

        // PLAYLIST MODE METHODS
        playNextInSequence() {
            if (!this.isPlaying || this.currentPlaylist.length === 0) return;

            const soundKey = this.currentPlaylist[this.currentTrackIndex];
            this.playSound(soundKey, this.volume);

            this.currentTrackIndex++;
            if (this.currentTrackIndex >= this.currentPlaylist.length) {
                this.currentTrackIndex = 0;
            }

            // Wait for current sound to potentially finish (or use interval)
            this.playlistTimer = setTimeout(() => {
                this.playNextInSequence();
            }, this.interval);
        }

        // CHAOS MODE METHODS
        startChaosTimers() {
            if (!this.isPlaying) return;

            // Create multiple overlapping timers with different behaviors
            this.startShortChaosTimer();    // Quick sounds
            this.startMediumChaosTimer();   // Medium interval
            this.startLongChaosTimer();     // Long interval sounds
            this.startWildcardTimer();      // Completely random
        }

        startShortChaosTimer() {
            if (!this.isPlaying) return;
            
            const shortInterval = this.interval * 0.3; // Much faster
            const randomSound = this.currentPlaylist[Math.floor(Math.random() * this.currentPlaylist.length)];
            
            if (Math.random() > 0.3) { // 70% chance to play
                this.playSound(randomSound, this.volume * 0.5); // Quieter since overlapping
            }

            this.chaosTimer1 = setTimeout(() => this.startShortChaosTimer(), shortInterval + Math.random() * shortInterval);
        }

        startMediumChaosTimer() {
            if (!this.isPlaying) return;
            
            const mediumInterval = this.interval * 0.8;
            const randomSound = this.currentPlaylist[Math.floor(Math.random() * this.currentPlaylist.length)];
            
            if (Math.random() > 0.4) { // 60% chance to play
                this.playSound(randomSound, this.volume * 0.7);
            }

            this.chaosTimer2 = setTimeout(() => this.startMediumChaosTimer(), mediumInterval + Math.random() * mediumInterval);
        }

        startLongChaosTimer() {
            if (!this.isPlaying) return;
            
            const longInterval = this.interval * 1.5;
            const randomSound = this.currentPlaylist[Math.floor(Math.random() * this.currentPlaylist.length)];
            
            this.playSound(randomSound, this.volume);

            this.chaosTimer3 = setTimeout(() => this.startLongChaosTimer(), longInterval + Math.random() * longInterval);
        }

        startWildcardTimer() {
            if (!this.isPlaying) return;
            
            // Completely unpredictable
            const wildInterval = Math.random() * this.interval * 2;
            
            if (Math.random() > 0.8) { // 20% chance for extra chaos
                const sound1 = this.currentPlaylist[Math.floor(Math.random() * this.currentPlaylist.length)];
                const sound2 = this.currentPlaylist[Math.floor(Math.random() * this.currentPlaylist.length)];
                
                this.playSound(sound1, this.volume * 0.4);
                setTimeout(() => this.playSound(sound2, this.volume * 0.4), 200);
            }

            this.chaosTimer4 = setTimeout(() => this.startWildcardTimer(), wildInterval);
        }

        clearAllTimers() {
            if (this.ambientTimer) {
                clearTimeout(this.ambientTimer);
                this.ambientTimer = null;
            }
            if (this.randomTimer) {
                clearTimeout(this.randomTimer);
                this.randomTimer = null;
            }
            if (this.playlistTimer) {
                clearTimeout(this.playlistTimer);
                this.playlistTimer = null;
            }
            if (this.chaosTimer1) {
                clearTimeout(this.chaosTimer1);
                this.chaosTimer1 = null;
            }
            if (this.chaosTimer2) {
                clearTimeout(this.chaosTimer2);
                this.chaosTimer2 = null;
            }
            if (this.chaosTimer3) {
                clearTimeout(this.chaosTimer3);
                this.chaosTimer3 = null;
            }
            if (this.chaosTimer4) {
                clearTimeout(this.chaosTimer4);
                this.chaosTimer4 = null;
            }
        }

        playSound(soundKey, volume = null) {
            const sound = this.sounds[soundKey];
            if (sound) {
                sound.currentTime = 0;
                sound.volume = volume !== null ? volume : this.volume;
                
                sound.play().catch(e => {
                    // Silently handle play error
                });
            }
        }

        stopAllSounds() {
            this.isPlaying = false;
            
            // Clear all timers
            this.clearAllTimers();
            
            // Stop all sounds
            Object.values(this.sounds).forEach(sound => {
                if (sound) {
                    sound.pause();
                    sound.currentTime = 0;
                }
            });
        }

        pauseAllSounds() {
            Object.values(this.sounds).forEach(sound => {
                if (sound && !sound.paused) {
                    sound.pause();
                }
            });
        }

        resumeAllSounds() {
            if (!this.userInteracted || !this.isPlaying) {
                return;
            }

            Object.values(this.sounds).forEach(sound => {
                if (sound && sound.paused) {
                    sound.play().catch(e => {
                        // Silently handle resume error
                    });
                }
            });
        }

        handleVisibilityChange() {
            let hidden, visibilityChange;
            
            if (typeof document.hidden !== "undefined") {
                hidden = "hidden";
                visibilityChange = "visibilitychange";
            } else if (typeof document.msHidden !== "undefined") {
                hidden = "msHidden";
                visibilityChange = "msvisibilitychange";
            } else if (typeof document.webkitHidden !== "undefined") {
                hidden = "webkitHidden";
                visibilityChange = "webkitvisibilitychange";
            }

            if (typeof document[hidden] !== "undefined") {
                document.addEventListener(visibilityChange, () => {
                    if (document[hidden]) {
                        this.pauseAllSounds();
                    } else {
                        this.resumeAllSounds();
                    }
                }, false);
            }
        }

        // Public methods for external control
        mute() {
            Object.values(this.sounds).forEach(sound => {
                if (sound) {
                    sound.muted = true;
                }
            });
        }

        unmute() {
            Object.values(this.sounds).forEach(sound => {
                if (sound) {
                    sound.muted = false;
                }
            });
        }

        setVolume(volume) {
            this.volume = Math.max(0, Math.min(1, volume)); // Clamp between 0 and 1
            
            Object.values(this.sounds).forEach(sound => {
                if (sound) {
                    sound.volume = this.volume;
                }
            });
            
            // Ambient sound should be quieter
            if (this.ambientSound) {
                this.ambientSound.volume = this.volume * 0.6;
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Only initialize if sound settings are available and properly configured
        if (typeof halloween_animations_ajax !== 'undefined' && 
            halloween_animations_ajax.soundSettings && 
            halloween_animations_ajax.soundSettings.enabled &&
            halloween_animations_ajax.pluginUrl) {
            
            window.halloweenSoundManager = new HalloweenSoundManager();
            
            // Admin test button functionality
            $('.test-sounds').on('click', function() {
                const button = $(this);
                const originalText = button.text();
                
                button.text('🔊 Testing...').prop('disabled', true);
                
                if (window.halloweenSoundManager) {
                    // Test a random sound
                    const sounds = Object.keys(window.halloweenSoundManager.sounds);
                    if (sounds.length > 0) {
                        const randomSound = sounds[Math.floor(Math.random() * sounds.length)];
                        window.halloweenSoundManager.playSound(randomSound);
                        
                        setTimeout(() => {
                            button.text('✅ Test Complete').prop('disabled', false);
                            setTimeout(() => {
                                button.text(originalText);
                            }, 2000);
                        }, 2000);
                    } else {
                        button.text('❌ No Sounds').prop('disabled', false);
                        setTimeout(() => {
                            button.text(originalText);
                        }, 2000);
                    }
                }
            });
        }
    });

})(jQuery);