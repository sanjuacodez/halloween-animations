# Halloween Images

This directory contains the image assets used for Halloween animations.

## Files

### bats.png
- **Source**: https://www.jqueryscript.net/demo/halloween-bats-flying-around/bats.png
- **Description**: Sprite sheet containing 4 frames of bat wing animation
- **Dimensions**: 35px width × 80px height (4 frames of 20px height each)
- **Usage**: Used for the flying bats animation with frame-by-frame animation
- **Format**: PNG with transparency

## Sprite Sheet Layout

The bats.png file contains 4 animation frames stacked vertically:
```
Frame 0: 0px to 20px (wings up)
Frame 1: 20px to 40px (wings mid-up)
Frame 2: 40px to 60px (wings down)
Frame 3: 60px to 80px (wings mid-down)
```

## Animation System

The bat animation uses CSS background-position to cycle through the frames:
- Each frame is displayed for a calculated duration
- The background-position shifts vertically to show different frames
- Combined with movement animation for realistic bat flight

## Browser Compatibility

- Supports all modern browsers with CSS background-position animation
- PNG transparency works across all target browsers
- Optimized file size for web delivery

## Usage in Code

The sprite is loaded via JavaScript:
```javascript
backgroundImage: 'url(' + options.image + ')'
backgroundPosition: '0 ' + (frame * -height) + 'px'
```

## Adding Custom Sprites

To use custom bat sprites:
1. Create a sprite sheet with frames stacked vertically
2. Update the frame count and dimensions in the JavaScript options
3. Replace bats.png with your custom sprite
4. Ensure proper transparency and web optimization