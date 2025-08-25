# Audio Conversion Setup Guide

## Overview
The chat orders system now supports automatic conversion of 3gp audio files to mp3 format for better browser compatibility. This allows Android apps to send 3gp files while web browsers can play mp3 files.

## Features
- **Automatic Conversion**: 3gp files are automatically converted to mp3 when uploaded as voice messages
- **Platform-Specific URLs**: Different audio URLs are provided for Android apps vs web browsers
- **Fallback Support**: If conversion fails, the original 3gp file is still accessible
- **Dual Storage**: Both 3gp and mp3 files are stored for maximum compatibility

## Installation Requirements

### 1. Install FFmpeg

#### Windows
1. Download FFmpeg from https://ffmpeg.org/download.html
2. Extract to a folder (e.g., `C:\ffmpeg`)
3. Add FFmpeg to your system PATH:
   - Open System Properties > Advanced > Environment Variables
   - Edit the PATH variable and add `C:\ffmpeg\bin`
4. Restart your terminal/command prompt

#### macOS
```bash
# Using Homebrew
brew install ffmpeg

# Or using MacPorts
sudo port install ffmpeg
```

#### Ubuntu/Debian
```bash
sudo apt update
sudo apt install ffmpeg
```

#### CentOS/RHEL
```bash
sudo yum install epel-release
sudo yum install ffmpeg ffmpeg-devel
```

### 2. Verify Installation
Run the test command to verify FFmpeg is working:
```bash
php artisan test:audio-conversion
```

## Database Changes
A new column `mp3_path` has been added to the `chat_messages` table to store the converted mp3 file paths.

## API Changes

### 1. Upload Voice Message
**Endpoint**: `POST /api/chat-orders/{chatOrderId}/messages`

**Request**:
```json
{
    "type": "voice",
    "media": "3gp_file",
    "duration": 30
}
```

**Response**:
```json
{
    "status": "success",
    "message": "Message sent successfully",
    "data": {
        "id": 1,
        "chat_order_id": 1,
        "sender_id": 1,
        "type": "voice",
        "media_path": "uploads/chat-media/1/abc123.3gp",
        "mp3_path": "uploads/chat-media/1/def456.mp3",
        "duration": 30,
        "created_at": "2025-08-23T12:00:00.000000Z"
    }
}
```

### 2. Get Messages with Platform-Specific URLs
**Endpoint**: `GET /api/chat-orders/{chatOrderId}/messages?platform=web`

**Query Parameters**:
- `platform`: `web` (default) or `android`

**Response for Web**:
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "type": "voice",
            "audio_url": "http://example.com/uploads/chat-media/1/def456.mp3",
            "audio_path": "uploads/chat-media/1/def456.mp3",
            "has_mp3_version": true
        }
    ]
}
```

**Response for Android**:
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "type": "voice",
            "audio_url": "http://example.com/uploads/chat-media/1/abc123.3gp",
            "audio_path": "uploads/chat-media/1/abc123.3gp",
            "has_mp3_version": true
        }
    ]
}
```

### 3. Platform-Specific Messages Endpoint
**Endpoint**: `GET /api/chat-orders/{chatOrderId}/messages/platform?platform=web`

This endpoint returns all messages with platform-specific audio URLs and additional metadata.

## Frontend Integration

### Web Browser (Blade Template)
```php
@if($message->type === 'voice')
    <audio controls class="w-full">
        @if($message->mp3_path && file_exists(public_path($message->mp3_path)))
            <source src="{{ asset($message->mp3_path) }}" type="audio/mpeg">
        @else
            <source src="{{ asset($message->media_path) }}" type="audio/mpeg">
        @endif
        Your browser does not support the audio element.
    </audio>
@endif
```

### JavaScript
```javascript
if (message.type === 'voice') {
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.className = 'w-full';
    const source = document.createElement('source');
    
    // Use mp3 file if available, otherwise fallback to original
    if (message.mp3_path) {
        source.src = `${baseUrl}/${message.mp3_path}`;
    } else {
        source.src = `${baseUrl}/${message.media_path}`;
    }
    
    source.type = 'audio/mpeg';
    audio.appendChild(source);
    messageContent.appendChild(audio);
}
```

## Android App Integration

### Upload Voice Message
```java
// Use the existing API endpoint
// The system will automatically convert 3gp to mp3
```

### Play Voice Messages
```java
// Use the platform-specific endpoint
String url = "https://api.example.com/chat-orders/" + chatOrderId + "/messages/platform?platform=android";

// The response will contain 3gp URLs for Android
String audioUrl = message.getAudioUrl(); // Returns 3gp file URL
```

## Error Handling

### Conversion Failures
If audio conversion fails:
1. The original 3gp file is still saved and accessible
2. The `mp3_path` field will be `null`
3. Web browsers will fallback to the original 3gp file
4. Error logs are recorded for debugging

### FFmpeg Not Available
If FFmpeg is not installed:
1. Voice messages are still saved with the original 3gp file
2. No mp3 conversion occurs
3. Web browsers may not be able to play the audio
4. The system continues to function normally

## File Storage Structure
```
public/uploads/chat-media/
├── {chatOrderId}/
│   ├── {random_id}.3gp    # Original 3gp file
│   └── {random_id}.mp3    # Converted mp3 file
```

## Monitoring and Logs
Audio conversion activities are logged in Laravel's log files:
- Successful conversions: `storage/logs/laravel.log`
- Failed conversions: `storage/logs/laravel.log` (with error details)
- FFmpeg availability checks: `storage/logs/laravel.log`

## Testing
Use the provided test command to verify the setup:
```bash
# Test FFmpeg availability
php artisan test:audio-conversion

# Test with a sample file
php artisan test:audio-conversion --test-file=path/to/sample.3gp
```

## Troubleshooting

### FFmpeg Not Found
1. Verify FFmpeg is installed: `ffmpeg -version`
2. Check if FFmpeg is in your system PATH
3. Restart your web server after installing FFmpeg

### Conversion Failures
1. Check the Laravel logs for detailed error messages
2. Verify the input file is a valid 3gp file
3. Ensure sufficient disk space for conversion
4. Check file permissions on the upload directory

### Audio Not Playing in Browser
1. Verify the mp3 file was created successfully
2. Check if the file path is correct
3. Ensure the web server can serve the mp3 files
4. Check browser console for any JavaScript errors 