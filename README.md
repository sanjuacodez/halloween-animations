# 🎃 Halloween Animations

Transform your WordPress site into a spooky Halloween experience with immersive animations and atmospheric sounds!

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-green)](https://www.gnu.org/licenses/gpl-2.0.html)

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
- **Smart Error Handling**: Robust audio loading with fallback mechanisms

### ⚙️ Admin Control Panel

- **Full Animation Control**: Enable/disable individual effects with count and speed settings
- **Display Targeting**: Choose where animations appear (homepage, all pages, specific pages)
- **Sound Management**: Complete audio system configuration with mode selection
- **Live Preview**: Test animations and sounds directly from the admin panel
- **Mobile Controls**: Device-specific settings and optimizations
- **Performance Settings**: Animation timing and resource management options

### 🎯 Smart Features

- **Performance Optimized**: Animations pause when tab is inactive to save resources
- **Accessibility Friendly**: Respects `prefers-reduced-motion` settings for accessibility
- **Mobile Responsive**: Adapts animations and controls to different screen sizes
- **Cross-Browser Compatible**: Works on all modern browsers with graceful degradation
- **Clean Uninstall**: Removes all data and settings when plugin is deleted
- **Security First**: All output properly escaped and sanitized, nonce verification for AJAX

## 🚀 Installation

### From WordPress Admin
1. Go to **Plugins > Add New**
2. Search for "Halloween Animations"
3. Click **Install Now** and then **Activate**
4. Navigate to **Settings > Halloween Animations** to configure

### Manual Installation
1. Download the plugin ZIP file
2. Upload to `/wp-content/plugins/` directory
3. Extract the files
4. Activate through the WordPress **Plugins** menu
5. Configure settings in **Settings > Halloween Animations**

### From GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/sanjuacodez/halloween-animations.git
```

## ⚙️ Configuration

### Basic Setup
1. **Activate the Plugin**: Enable Halloween Animations from your WordPress admin
2. **Choose Display Pages**: Select where animations should appear (homepage, all pages, or specific pages)
3. **Configure Animations**: Enable/disable individual effects and adjust their settings
4. **Set Up Sounds**: Choose sound mode and configure audio preferences

### Animation Settings
- **Bats**: Adjust count (1-20) and speed (slow/medium/fast) for realistic flight patterns
- **Ghosts**: Control quantity (1-10) and floating speed for ethereal movement
- **Pumpkin**: Set running speed and appearance frequency
- **Leaves**: Configure fall count (1-20) and natural drift patterns
- **Spiders**: Adjust crawling count (1-5) and vertical movement style
- **Fog**: Enable atmospheric fog effects with particle density control

### Sound Configuration
- **Ambient Mode**: Continuous background atmosphere with occasional effects
- **Random Mode**: Sounds play at configurable random intervals (5-60 seconds)
- **Playlist Mode**: Sequential audio playback of selected sounds
- **Chaos Mode**: Multiple overlapping sounds for intense atmosphere
- **Volume Control**: Master volume (0-100%) with real-time adjustment
- **Sound Selection**: Choose from 6 built-in spooky sounds or add custom MP3 files

## 🛠️ Technical Details

### Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Browser Support**: Modern browsers with CSS3 and HTML5 audio support
- **jQuery**: Included with WordPress

### Performance
- **Lightweight**: Optimized JavaScript and CSS with conditional loading
- **Battery Friendly**: Animations pause when tab is inactive
- **Mobile Optimized**: Responsive animations that adapt to screen size
- **Resource Efficient**: Animations stop during page visibility changes

### Security
- **Nonce Verification**: All AJAX requests are properly secured
- **Data Sanitization**: All inputs sanitized and escaped using WordPress functions
- **Permission Checks**: Proper capability checks for admin functions
- **SQL Injection Safe**: Uses WordPress database API exclusively

## 🎨 Customization

### CSS Customization
Add custom styles to your theme's `style.css`:

```css
/* Customize bat colors */
.halloween-bat-sprite {
    filter: hue-rotate(45deg);
}

/* Adjust ghost transparency */
.halloween-ghost {
    opacity: 0.8;
}

/* Change pumpkin size */
.halloween-pumpkin {
    font-size: 2em;
}

/* Modify fog opacity */
.fog-particle {
    opacity: 0.5;
}
```

### JavaScript Hooks
Access the animation system programmatically:

```javascript
// Stop all animations
if (window.halloweenEffects) {
    window.halloweenEffects.stop();
}

// Start animations
if (window.halloweenEffects) {
    window.halloweenEffects.start();
}

// Control individual elements
if (window.halloweenEffects) {
    window.halloweenEffects.stopBats();
    window.halloweenEffects.startBats();
}
```

## 🤝 Contributing

We welcome contributions! Please feel free to submit issues, feature requests, or pull requests.

### Development Setup
1. Clone the repository
2. Set up a WordPress development environment
3. Activate the plugin in your test site
4. Make your changes and test thoroughly

### Coding Standards
- Follow WordPress Coding Standards
- Use proper escaping for all output
- Include PHPDoc comments for functions
- Test on multiple WordPress versions
- Ensure mobile compatibility

## 📞 Support

### Getting Help
- **Documentation**: Check this README and plugin settings
- **WordPress Support**: Visit the plugin's WordPress.org support forum
- **GitHub Issues**: Report bugs or request features on GitHub
- **Direct Contact**: Reach out through [sanjayshankar.me](https://sanjayshankar.me)

### Common Issues

**Animations not showing:**
1. Check if animations are enabled in plugin settings
2. Verify display page settings are correct
3. Ensure theme is compatible with jQuery animations
4. Check browser console for JavaScript errors

**Sound not playing:**
1. Click the sound enable button when it appears
2. Check that sounds are enabled in plugin settings
3. Ensure browser allows audio playback
4. Check that audio files are accessible

## 📝 Changelog

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

## 👨‍💻 Author

**Sanjay Shankar**
- Website: [sanjayshankar.me](https://sanjayshankar.me)
- GitHub: [@sanjuacodez](https://github.com/sanjuacodez)
- WordPress.org: [sanju-shankar](https://profiles.wordpress.org/sanju-shankar/)

### About the Developer
Sanjay Shankar is a passionate WordPress developer with expertise in creating engaging user experiences through innovative plugins and themes. With a focus on performance, security, and user-friendly design, Sanjay brings creative solutions to the WordPress community.

**Specialties:**
- WordPress Plugin Development
- JavaScript Animation Systems
- User Interface Design
- Performance Optimization
- Security Best Practices

## Credits

- Inspired by the [jquery-halloween-bats](https://github.com/Artimon/jquery-halloween-bats) project
- Halloween emoji characters for visual effects
- Sound effects optimized for web delivery

## 📄 License

This plugin is licensed under the **GPL v2 or later**.

```
Halloween Animations is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Halloween Animations is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 🎃 Happy Halloween!

Transform your WordPress site into a spooky spectacular experience. Whether you're running a Halloween event, seasonal business, or just want to add some fun to your site, Halloween Animations brings the perfect blend of spook and delight to your visitors.

**Made with 💜 by [Sanjay Shankar](https://sanjayshankar.me)**

## ☕ Support the Developer

If you find this plugin helpful and want to support continued development, consider buying me a coffee! Your support helps me create more awesome WordPress plugins and keep them updated.

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-Support%20Development-orange?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/sanjayshankar)

[**☕ Buy me a coffee**](https://buymeacoffee.com/sanjayshankar)

Every coffee helps fuel late-night coding sessions and new feature development! 🚀

---