/**
 * Halloween Effects Frontend JavaScript
 * Handles all Halloween animations and effects on the frontend
 * 
 * @package Halloween_Animations
 */

(function($) {
    'use strict';

    class HalloweenEffects {
        constructor() {
            this.settings = halloween_animations_frontend.settings || {};
            this.container = $('#halloween-animations-container');
            this.animationTimers = [];
            this.isVisible = true;
            
            // Add custom easing for spider animations
            this.addCustomEasing();
            
            this.init();
        }

        addCustomEasing() {
            // Add custom easing functions for more realistic spider movement
            if (typeof $.easing !== 'undefined') {
                $.easing.easeInQuad = function (x, t, b, c, d) {
                    return c*(t/=d)*t + b;
                };
                $.easing.easeOutBounce = function (x, t, b, c, d) {
                    if ((t/=d) < (1/2.75)) {
                        return c*(7.5625*t*t) + b;
                    } else if (t < (2/2.75)) {
                        return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
                    } else if (t < (2.5/2.75)) {
                        return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
                    } else {
                        return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
                    }
                };
            }
        }

        init() {
            if (!this.container.length) {
                return;
            }

            // Initialize animations based on settings
            this.initializeBats();
            this.initializeGhosts();
            this.initializePumpkin();
            this.initializeLeaves();
            this.initializeSpiders();
            this.initializeFog();

            // Handle visibility changes
            this.handleVisibilityChange();
            
            // Handle window resize
            $(window).on('resize', this.handleResize.bind(this));
        }

        initializeBats() {
            if (!this.settings.bats || !this.settings.bats.enabled) return;

            const batsContainer = $('#halloween-bats');
            if (!batsContainer.length) return;

            const batCount = parseInt(this.settings.bats.count) || 5;
            const speed = this.getSpeedValue('bats');
            
            // Initialize the sophisticated bat animation system
            this.batSystem = new HalloweenBatSystem(batsContainer, {
                image: halloween_animations_frontend.pluginUrl + 'assets/images/bats.png',
                amount: batCount,
                speed: speed,
                width: 35,
                height: 20,
                frames: 4,
                zIndex: 9999,
                flickering: 15
            });
        }

        getSpeedValue(type) {
            const speed = this.settings[type] && this.settings[type].speed || 'medium';
            switch (speed) {
                case 'slow': return 10;
                case 'fast': return 35;
                case 'medium':
                default: return 20;
            }
        }

        initializeGhosts() {
            if (!this.settings.ghosts || !this.settings.ghosts.enabled) return;

            const ghostsContainer = $('#halloween-ghosts');
            if (!ghostsContainer.length) return;

            const ghostCount = parseInt(this.settings.ghosts.count) || 3;
            const speed = this.settings.ghosts.speed || 'medium';
            
            ghostsContainer.addClass('speed-' + speed);

            // Position ghosts randomly and start floating
            for (let i = 0; i < ghostCount; i++) {
                const ghost = ghostsContainer.find(`.halloween-ghost[data-ghost="${i}"]`);
                if (ghost.length) {
                    this.positionGhost(ghost, i);
                }
            }
        }

        positionGhost(ghost, index) {
            const x = Math.random() * (window.innerWidth - 100);
            const y = Math.random() * (window.innerHeight - 200) + 100;
            const delay = index * 2000;
            
            setTimeout(() => {
                ghost.css({
                    left: x + 'px',
                    top: y + 'px',
                    display: 'block'
                });
                
                // Add floating movement
                this.floatGhost(ghost);
            }, delay);
        }

        floatGhost(ghost) {
            if (!this.isVisible) return;
            
            const currentX = parseInt(ghost.css('left'));
            const currentY = parseInt(ghost.css('top'));
            
            const newX = currentX + (Math.random() - 0.5) * 100;
            const newY = currentY + (Math.random() - 0.5) * 50;
            
            // Keep within bounds
            const boundedX = Math.max(0, Math.min(window.innerWidth - 50, newX));
            const boundedY = Math.max(50, Math.min(window.innerHeight - 100, newY));
            
            ghost.animate({
                left: boundedX,
                top: boundedY
            }, {
                duration: 3000 + Math.random() * 2000,
                easing: 'swing',
                complete: () => {
                    setTimeout(() => {
                        this.floatGhost(ghost);
                    }, 1000 + Math.random() * 2000);
                }
            });
        }

        initializePumpkin() {
            if (!this.settings.pumpkin || !this.settings.pumpkin.enabled) return;

            const pumpkinContainer = $('#halloween-pumpkin');
            if (!pumpkinContainer.length) return;

            const speed = this.settings.pumpkin.speed || 'medium';
            pumpkinContainer.addClass('speed-' + speed);

            const pumpkin = pumpkinContainer.find('.halloween-pumpkin');
            if (pumpkin.length) {
                this.animatePumpkin(pumpkin);
            }
        }

        animatePumpkin(pumpkin) {
            const duration = this.getAnimationDuration('pumpkin');
            
            // Start from left side
            pumpkin.css({
                left: '-80px',
                display: 'block'
            });

            // Animate across screen
            pumpkin.animate({
                left: window.innerWidth + 80
            }, {
                duration: duration,
                easing: 'linear',
                complete: () => {
                    // Restart after delay
                    setTimeout(() => {
                        this.animatePumpkin(pumpkin);
                    }, Math.random() * 5000 + 3000);
                }
            });
        }

        initializeLeaves() {
            if (!this.settings.leaves || !this.settings.leaves.enabled) return;

            const leavesContainer = $('#halloween-leaves');
            if (!leavesContainer.length) return;

            const leafCount = parseInt(this.settings.leaves.count) || 10;

            // Start leaf animations with random delays
            for (let i = 0; i < leafCount; i++) {
                const leaf = leavesContainer.find(`.halloween-leaf[data-leaf="${i}"]`);
                if (leaf.length) {
                    this.animateLeaf(leaf, i);
                }
            }
        }

        animateLeaf(leaf, index) {
            const delay = Math.random() * 3000 + (index * 500);
            
            setTimeout(() => {
                if (!this.isVisible) return;
                
                const startX = Math.random() * window.innerWidth;
                const endX = startX + (Math.random() - 0.5) * 200;
                const duration = 8000 + Math.random() * 4000;
                
                leaf.css({
                    left: startX + 'px',
                    top: '-50px',
                    display: 'block'
                });

                // Animate falling with slight horizontal drift
                leaf.animate({
                    top: window.innerHeight + 50,
                    left: endX
                }, {
                    duration: duration,
                    easing: 'linear',
                    complete: () => {
                        // Restart animation
                        setTimeout(() => {
                            this.animateLeaf(leaf, index);
                        }, Math.random() * 2000 + 1000);
                    }
                });
            }, delay);
        }

        initializeSpiders() {
            if (!this.settings.spiders || !this.settings.spiders.enabled) return;

            const spidersContainer = $('#halloween-spiders');
            if (!spidersContainer.length) return;

            const spiderCount = parseInt(this.settings.spiders.count) || 2;

            // Start spider animations with vertical crawling
            for (let i = 0; i < spiderCount; i++) {
                const spider = spidersContainer.find(`.halloween-spider[data-spider="${i}"]`);
                if (spider.length) {
                    this.animateSpiderVertical(spider, i);
                }
            }
        }

        animateSpiderVertical(spider, index) {
            const delay = Math.random() * 3000 + (index * 2000);
            
            setTimeout(() => {
                if (!this.isVisible) return;
                
                // Randomly choose to crawl down from top or up from bottom
                const crawlDown = Math.random() > 0.5;
                const horizontalPosition = Math.random() * (window.innerWidth - 100) + 50;
                
                let startY, endY, duration;
                
                // Reset spider classes
                spider.removeClass('spider-dropping spider-climbing');
                
                if (crawlDown) {
                    // Spider drops down from top (upside down)
                    startY = -30;
                    endY = window.innerHeight + 30;
                    duration = 8000 + Math.random() * 4000; // Slower for realism
                    spider.addClass('spider-dropping');
                } else {
                    // Spider climbs up from bottom (right side up)
                    startY = window.innerHeight + 30;
                    endY = -30;
                    duration = 10000 + Math.random() * 5000; // Even slower climbing up
                    spider.addClass('spider-climbing');
                }
                
                spider.css({
                    left: horizontalPosition + 'px',
                    top: startY + 'px',
                    display: 'block'
                });

                // Add web line effect for spiders dropping down
                if (crawlDown) {
                    this.createSpiderWeb(spider, horizontalPosition);
                }

                // Animate vertical movement
                spider.animate({
                    top: endY
                }, {
                    duration: duration,
                    easing: crawlDown ? 'easeInQuad' : 'linear',
                    complete: () => {
                        // Remove web line if it exists
                        spider.siblings('.spider-web-line').remove();
                        
                        // Reset classes
                        spider.removeClass('spider-dropping spider-climbing');
                        
                        // Restart animation after delay
                        setTimeout(() => {
                            this.animateSpiderVertical(spider, index);
                        }, Math.random() * 5000 + 3000);
                    }
                });
            }, delay);
        }

        createSpiderWeb(spider, x) {
            // Create a web line for spiders dropping down
            const webLine = $('<div class="spider-web-line"></div>');
            webLine.css({
                position: 'absolute',
                left: (x + 9) + 'px', // Center on spider
                top: '0px',
                width: '1px',
                height: '0px',
                background: 'rgba(200, 200, 200, 0.6)',
                boxShadow: '0 0 2px rgba(255, 255, 255, 0.3)',
                zIndex: 9998,
                pointerEvents: 'none'
            });
            
            spider.parent().append(webLine);
            
            // Animate web line extending as spider drops
            spider.on('animateStep', function(e, fx) {
                if (fx.prop === 'top') {
                    webLine.css('height', Math.max(0, fx.now + 30) + 'px');
                }
            });
        }

        initializeFog() {
            if (!this.settings.fog || !this.settings.fog.enabled) {
                return;
            }

            const fogContainer = $('#halloween-fog');
            if (!fogContainer.length) {
                return;
            }
            
            // Ensure fog container is visible and properly positioned
            fogContainer.show();
            
            // Check for fog particles
            const fogParticles = fogContainer.find('.fog-particle');
            
            if (fogParticles.length === 0) {
                return;
            }
            
            // Add debug mode if URL parameter is present
            if (window.location.search.includes('halloween_debug')) {
                fogContainer.css({
                    'border': '3px solid lime',
                    'background': 'rgba(0, 255, 0, 0.1)'
                });
                fogParticles.css({
                    'border': '1px solid red',
                    'filter': 'none',
                    'background': 'rgba(255, 255, 255, 0.8) !important'
                });
            }
            
            // Log particle info for debugging
            if (window.location.search.includes('halloween_debug')) {
                fogParticles.each(function(index) {
                    const particle = $(this);
                    console.log(`🌫️ Fog particle ${index + 1}:`, {
                        class: particle.attr('class'),
                        computed: {
                            width: particle.css('width'),
                            height: particle.css('height'),
                            background: particle.css('background'),
                            opacity: particle.css('opacity'),
                            animation: particle.css('animation')
                        }
                    });
                });
            }
        }

        getAnimationDuration(type) {
            const speed = this.settings[type] && this.settings[type].speed || 'medium';
            
            switch (speed) {
                case 'slow':
                    return type === 'bats' ? 25000 : type === 'pumpkin' ? 20000 : 15000;
                case 'fast':
                    return type === 'bats' ? 8000 : type === 'pumpkin' ? 6000 : 5000;
                case 'medium':
                default:
                    return type === 'bats' ? 15000 : type === 'pumpkin' ? 12000 : 8000;
            }
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
                    this.isVisible = !document[hidden];
                    
                    if (!this.isVisible) {
                        // Pause animations when tab is not visible
                        this.container.find('*').stop();
                    }
                }, false);
            }
        }

        handleResize() {
            // Debounce resize events
            clearTimeout(this.resizeTimer);
            this.resizeTimer = setTimeout(() => {
                // Restart animations on resize to adjust to new dimensions
                this.container.find('*').stop();
                
                setTimeout(() => {
                    this.init();
                }, 100);
            }, 250);
        }

        // Public method to stop all animations
        stop() {
            this.isVisible = false;
            this.container.find('*').stop();
            this.animationTimers.forEach(timer => clearTimeout(timer));
            this.animationTimers = [];
        }

        // Public method to start animations
        start() {
            this.isVisible = true;
            this.init();
        }

        // Public method to stop bat system
        stopBats() {
            if (this.batSystem) {
                this.batSystem.stop();
            }
        }

        // Public method to start bat system
        startBats() {
            if (this.batSystem) {
                this.batSystem.start();
            }
        }
    }

    /**
     * Sophisticated Bat Animation System
     * Based on the original jquery-halloween-bats script
     */
    class HalloweenBatSystem {
        constructor($container, options) {
            this.container = $container;
            this.options = $.extend({
                image: 'bats.png',
                zIndex: 10000,
                amount: 5,
                width: 35,
                height: 20,
                frames: 4,
                speed: 20,
                flickering: 15
            }, options);

            this.isRunning = false;
            this.isActiveWindow = true;
            this.bats = [];
            this.innerWidth = this.container.width();
            this.innerHeight = this.container.height();

            this.init();
        }

        init() {
            this.setupWindowEvents();
            this.createBats();
            this.start();
        }

        setupWindowEvents() {
            $(window).on('resize.halloweenBats', () => {
                this.innerWidth = this.container.width();
                this.innerHeight = this.container.height();
            });

            $(window).on('focus.halloweenBats', () => {
                this.isActiveWindow = true;
            });

            $(window).on('blur.halloweenBats', () => {
                this.isActiveWindow = false;
            });
        }

        createBats() {
            while (this.bats.length < this.options.amount) {
                this.bats.push(new HalloweenBat(this.container, this.options, this));
            }
        }

        start() {
            if (this.isRunning) return;
            
            this.isRunning = true;
            let lastTime = Date.now();

            const animate = () => {
                const time = Date.now();
                const deltaTime = (time - lastTime) / 1000;
                lastTime = time;

                if (this.isActiveWindow) {
                    this.bats.forEach(bat => {
                        bat.move(deltaTime);
                        bat.animate(deltaTime);
                    });
                }

                if (this.isRunning) {
                    requestAnimationFrame(animate);
                }
            };

            animate();
        }

        stop() {
            this.isRunning = false;
            $(window).off('.halloweenBats');
            this.bats.forEach(bat => bat.remove());
            this.bats = [];
        }
    }

    /**
     * Individual Bat Class
     */
    class HalloweenBat {
        constructor($container, options, system) {
            this.container = $container;
            this.options = options;
            this.system = system;
            
            this.initialize();
        }

        initialize() {
            this.$bat = $('<div class="halloween-bat-sprite"/>');
            
            this.x = this.randomPosition('horizontal');
            this.y = this.randomPosition('vertical');
            this.tx = this.randomPosition('horizontal');
            this.ty = this.randomPosition('vertical');
            this.dx = -5 + Math.random() * 10;
            this.dy = -5 + Math.random() * 10;
            this.positionUpdateTimer = this.getPositionUpdateTime();
            
            this.frame = Math.random() * this.options.frames;
            this.frame = Math.round(this.frame);
            
            this.$bat.css({
                position: 'absolute',
                left: this.x + 'px',
                top: this.y + 'px',
                zIndex: this.options.zIndex,
                width: this.options.width + 'px',
                height: this.options.height + 'px',
                backgroundImage: 'url(' + this.options.image + ')',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: '0 ' + (this.frame * -this.options.height) + 'px'
            });
            
            this.container.append(this.$bat);
        }

        getPositionUpdateTime() {
            return 0.5 + Math.random();
        }

        randomPosition(direction) {
            let screenLength, imageLength;
            
            if (direction === 'horizontal') {
                screenLength = this.system.innerWidth;
                imageLength = this.options.width;
            } else {
                screenLength = this.system.innerHeight;
                imageLength = this.options.height;
            }
            
            return Math.random() * (screenLength - imageLength);
        }

        move(deltaTime) {
            const left = this.tx - this.x;
            const top = this.ty - this.y;
            
            let length = Math.sqrt(left * left + top * top);
            length = Math.max(1, length);
            
            const dLeft = this.options.speed * (left / length);
            const dTop = this.options.speed * (top / length);
            
            const ddLeft = (dLeft - this.dx) / this.options.flickering;
            const ddTop = (dTop - this.dy) / this.options.flickering;
            
            this.dx += ddLeft * deltaTime * 25;
            this.dy += ddTop * deltaTime * 25;
            
            this.x += this.dx * deltaTime * 25;
            this.y += this.dy * deltaTime * 25;
            
            this.x = Math.max(0, Math.min(this.x, this.system.innerWidth - this.options.width));
            this.y = Math.max(0, Math.min(this.y, this.system.innerHeight - this.options.height));
            
            this.applyPosition();
            
            this.positionUpdateTimer -= deltaTime;
            if (this.positionUpdateTimer < 0) {
                this.tx = this.randomPosition('horizontal');
                this.ty = this.randomPosition('vertical');
                this.positionUpdateTimer = this.getPositionUpdateTime();
            }
        }

        applyPosition() {
            this.$bat.css({
                left: this.x + 'px',
                top: this.y + 'px'
            });
        }

        animate(deltaTime) {
            this.frame += 5 * deltaTime;
            
            if (this.frame >= this.options.frames) {
                this.frame -= this.options.frames;
            }
            
            const frame = Math.floor(this.frame);
            
            this.$bat.css(
                'backgroundPosition',
                '0 ' + (frame * -this.options.height) + 'px'
            );
        }

        remove() {
            if (this.$bat) {
                this.$bat.remove();
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Check if reduced motion is preferred
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return; // Skip animations for users who prefer reduced motion
        }

        // Initialize Halloween effects
        window.halloweenEffects = new HalloweenEffects();
    });

})(jQuery);