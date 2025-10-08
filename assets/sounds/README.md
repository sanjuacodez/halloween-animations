# Halloween Sound Files

This directory contains the audio files used for spooky atmospheric effects.

## Files Required

- `wind.mp3` - Gentle wind sound for atmospheric background
- `owl.mp3` - Owl hoot sound effect

## Usage

These audio files are loaded when the sound effects option is enabled in the plugin settings. The sounds are:

- **Wind**: Plays continuously in a loop at low volume (30%)
- **Owl**: Plays randomly every 10-30 seconds at medium volume (50%)

## Audio Specifications

- **Format**: MP3 (for broad browser compatibility)
- **Quality**: Compressed for web delivery (128kbps recommended)
- **Duration**: 
  - Wind: 10-30 seconds (seamless loop)
  - Owl: 2-5 seconds (single hoot)

## Browser Compatibility

Modern browsers require user interaction before playing audio. The plugin handles this by:
1. Showing a sound enable button on first visit
2. Starting audio only after user clicks or interacts with the page
3. Providing mute/unmute controls
4. Auto-pausing when tab is inactive

## Adding Custom Sounds

To add your own sound files:
1. Replace the MP3 files in this directory
2. Ensure files maintain the same names (`wind.mp3`, `owl.mp3`)
3. Test across different browsers and devices
4. Keep file sizes reasonable for web delivery

## File Sources

You can source appropriate Halloween sound effects from:
- Freesound.org (Creative Commons)
- Zapsplat.com (with account)
- YouTube Audio Library
- Custom recordings

Remember to check licensing requirements for any audio files you use.