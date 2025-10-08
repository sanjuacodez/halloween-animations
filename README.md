# 🎃 Halloween Animations

Transform your WordPress site into a spooky Halloween experience with immersive animations and atmospheric sounds!

## Description

Halloween Animations is a comprehensive WordPress plugin that adds various Halloween-themed animations and effects to your website. Create an immersive spooky atmosphere for your visitors with flying bats, floating ghosts, running pumpkins, falling leaves, crawling spiders, advanced fog effects, and an enhanced multi-mode sound system.

## ✨ Features

### 🦇 Advanced Animated Effects
- **Flying Bats** - Realistic sprite-based animations with multiple flight patterns and wing-flapping motion
- **Floating Ghosts** - Ethereal ghost sprites with realistic floating movement and glow effects
- **Running Pumpkin** - Animated pumpkin character with rotation effects running across the screen
- **Falling Leaves** - Autumn leaves with natural drift patterns and rotation effects
- **Crawling Spiders** - Spiders with vertical movement and realistic web-line effects
- **Advanced Fog** - Multi-particle fog system with realistic atmospheric drifting effects

### 🔊 Enhanced Sound System
- **4 Playback Modes**: Ambient, Random, Playlist, and Chaos modes for different atmospheric experiences
- **Ambient Mode**: Continuous wind background with random spooky effects overlaid
- **Random Mode**: Randomly selected sounds with customizable intervals and silence gaps
- **Playlist Mode**: Sequential playback of all selected sounds in order
- **Chaos Mode**: Multiple simultaneous sounds for intense haunted house atmosphere
- **Custom Audio Files**: Add your own MP3 files to the sounds directory
- **Volume Control**: Adjustable master volume with real-time preview
- **Smart Audio**: Respects browser autoplay policies and handles errors gracefully

### 🎮 Comprehensive Admin Controls
- **Individual Animation Toggle** - Enable/disable each effect independently
- **Live Testing** - Preview animations directly in the admin panel with realistic effects
- **Customizable Count** - Set the number of bats (1-20), ghosts (1-20), leaves (1-20), and spiders (1-20)
- **Speed Control** - Adjust animation speed (slow, medium, fast) for dynamic effects
- **Sound Testing** - Test audio files directly in admin with volume preview

### 🎯 Display Options
- **Entire Website** - Show effects across all pages
- **Homepage Only** - Limit effects to the front page
- **Posts Only** - Display on blog posts only
- **Pages Only** - Show on static pages only
- **Custom Selection** - Choose specific post types and categories
- **Custom Post Types** - Support for any custom post type
- **Category Selection** - Target specific post categories

### 🔊 Sound Effects
- **Atmospheric Sounds** - Optional spooky background audio (wind, owl hoots)
- **User-Controlled** - Visitors can enable/disable sounds
- **Mobile Optimized** - Smart audio handling for mobile devices

### 📱 Responsive Design
- **Mobile Support** - Option to enable/disable effects on mobile devices
- **Performance Optimized** - Lightweight animations that don't impact site speed
- **Accessibility** - Respects user preferences for reduced motion
- **Cross-Browser** - Compatible with all modern browsers

## Installation

1. Upload the `halloween-animations` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Halloween Effects** to configure your spooky animations
4. Enable the desired effects and customize their settings
5. Save your settings and visit your site to see the Halloween magic!

## Configuration

### Basic Setup
1. Navigate to **Settings > Halloween Effects** in your WordPress admin
2. Enable the animations you want to use
3. Adjust the count and speed for each effect
4. Choose where to display the effects (entire site, specific pages, etc.)
5. Optionally enable spooky sounds
6. Save your settings

### Animation Settings

#### Flying Bats
- **Count**: 1-20 bats
- **Speed**: Slow, Medium, Fast
- **Behavior**: Sprite-based bats with realistic wing-flapping animation and intelligent flight patterns
- **Technology**: Uses a 4-frame sprite sheet for smooth wing animation

#### Floating Ghosts
- **Count**: 1-20 ghosts
- **Speed**: Slow, Medium, Fast
- **Behavior**: Ghosts float around the page with gentle movement

#### Running Pumpkin
- **Speed**: Slow, Medium, Fast
- **Behavior**: Pumpkin runs across the bottom of the screen periodically

#### Falling Leaves
- **Count**: 1-20 leaves
- **Behavior**: Leaves fall from the top with slight horizontal drift

#### Crawling Spiders
- **Count**: 1-20 spiders
- **Behavior**: Realistic vertical movement - spiders drop down from ceiling (upside down) or climb up from floor (right-side up)
- **Web Lines**: Spiders dropping down create visible web lines that extend as they descend
- **Orientation**: Smart rotation - spiders appear upside down when dropping, normal when climbing

#### Advanced Fog
- **Multi-Particle System**: 8 individual fog particles with different sizes and timing
- **Realistic Movement**: Particles drift across screen with varying speeds and opacity
- **Atmospheric Effects**: Creates realistic fog layers at the bottom of pages
- **Optimized Performance**: Uses CSS transforms and hardware acceleration

### Display Controls

#### Site-wide Options
- **Entire Website**: Shows effects on all pages and posts
- **Homepage Only**: Limits effects to your site's front page
- **Posts Only**: Shows effects only on blog post pages
- **Pages Only**: Shows effects only on static pages

#### Custom Selection
- **Post Types**: Choose specific post types (posts, pages, products, etc.)
- **Categories**: Target specific post categories
- **Excluded Pages**: Option to exclude specific pages (coming in future update)

#### Device Options
- **Mobile Support**: Toggle effects on mobile devices
- **Performance**: Automatic optimization for slower devices

### Sound Settings
- **Enhanced Sound System**: Four distinct playback modes for different experiences
- **Ambient Mode**: Continuous atmospheric wind with random spooky sound effects
- **Random Mode**: Unpredictable sound selection with customizable intervals
- **Playlist Mode**: Sequential story-like audio experience 
- **Chaos Mode**: Intense multi-layered audio for haunted house atmosphere
- **Custom Sounds**: Add your own MP3 files to the plugin's sounds directory
- **Volume Control**: Master volume slider with real-time adjustment
- **Smart Loading**: Preloads audio files for smooth playback
- **User Control**: Visitors can enable/disable sounds with a toggle button
- **Auto-pause**: Sounds pause when browser tab is not active

## Customization

### CSS Customization
The plugin includes comprehensive CSS that can be customized:

```css
/* Customize bat appearance */
.halloween-bat {
    font-size: 32px !important;
    color: #your-color !important;
}

/* Adjust ghost opacity */
.halloween-ghost {
    opacity: 0.5 !important;
}

/* Modify fog intensity */
.fog-layer {
    opacity: 0.8 !important;
}
```

### JavaScript Hooks
The plugin provides JavaScript hooks for developers:

```javascript
// Access the Halloween effects instance
if (window.halloweenEffects) {
    // Stop all animations
    window.halloweenEffects.stop();
    
    // Start animations
    window.halloweenEffects.start();
}

// Control sounds
if (window.halloweenSounds) {
    // Mute all sounds
    window.halloweenSounds.mute();
    
    // Set volume (0-1)
    window.halloweenSounds.setVolume(0.5);
}
```

## Performance

The plugin is designed with performance in mind:

- **Lightweight**: Minimal impact on page load times
- **Optimized Animations**: CSS-based animations where possible
- **Smart Loading**: Effects only load when enabled
- **Memory Management**: Proper cleanup of animation timers
- **Mobile Optimization**: Reduced effects on mobile devices
- **Visibility API**: Pauses animations when tab is not visible

## Browser Support

- **Chrome**: Full support (recommended)
- **Firefox**: Full support
- **Safari**: Full support
- **Edge**: Full support
- **Mobile Browsers**: Optimized support
- **Internet Explorer**: Not supported (modern browsers only)

## Accessibility

The plugin respects user accessibility preferences:

- **Reduced Motion**: Automatically disables animations for users who prefer reduced motion
- **High Contrast**: Adjusts colors for high contrast mode
- **Screen Readers**: Effects don't interfere with screen readers
- **Keyboard Navigation**: No interference with keyboard navigation

## Troubleshooting

### Effects Not Showing
1. Check that the effects are enabled in Settings > Halloween Effects
2. Verify the display settings match your current page type
3. Ensure your theme doesn't have conflicting CSS
4. Check browser console for JavaScript errors

### Performance Issues
1. Reduce the number of animated elements
2. Disable effects on mobile devices
3. Use slower animation speeds
4. Disable sound effects if not needed

### Sound Not Working
1. Click the sound enable button when it appears
2. Check that sounds are enabled in plugin settings
3. Ensure browser allows audio playback
4. Check that audio files are accessible

## Changelog

### Version 1.0.0
- Initial release
- Flying bats with sprite-based animation system and multiple flight patterns  
- Floating ghosts with realistic ethereal movement and glow effects
- Running pumpkin animation with rotation effects
- Falling leaves with natural drift and rotation patterns
- Crawling spiders with vertical movement and web-line effects
- Advanced multi-particle fog system with realistic atmospheric effects
- Enhanced sound system with 4 distinct playback modes (Ambient, Random, Playlist, Chaos)
- Comprehensive admin controls with live animation testing
- Complete display targeting options for precise control
- Mobile optimization with device-specific controls
- Accessibility features including reduced motion support
- Cross-browser compatibility and performance optimizations
- Developer-friendly code with hooks and filters

## Credits

- Inspired by the [jquery-halloween-bats](https://github.com/Artimon/jquery-halloween-bats) project
- Halloween emoji characters for visual effects
- Sound effects optimized for web delivery

## Support

For support, feature requests, or bug reports, please visit the plugin's support forum or contact the developer.

## License

This plugin is licensed under the GPL v2 or later.

---

**Transform your WordPress site into a spooky Halloween destination! 🎃👻🦇**