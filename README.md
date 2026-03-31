# Screen Privacy Plugin for NativePHP Mobile

Prevent screenshots and screen recordings of sensitive content using native platform protections (FLAG_SECURE on Android, secure overlay on iOS).

## Installation

```bash
composer require macrotom/screen-privacy
```

## Usage

### PHP (Livewire/Blade)

```php
use MacroTom\ScreenPrivacy\Facades\ScreenPrivacy;

// Initialize screen privacy (registers callbacks on Android 14+)
ScreenPrivacy::init();

// Enable screen privacy protection
ScreenPrivacy::enable();

// Disable screen privacy protection
ScreenPrivacy::disable();

// Check if screen privacy is currently active
$isEnabled = ScreenPrivacy::isEnabled(); // returns bool

// Check if the screen is being recorded (iOS only)
$isRecording = ScreenPrivacy::isRecording(); // returns bool
```

### JavaScript (Vue/React/Inertia)

```javascript
import { screenPrivacy } from '@macrotom/screen-privacy';

// Initialize on mount
await screenPrivacy.init();

// Enable/disable protection
await screenPrivacy.enable();
await screenPrivacy.disable();

// Check status
const enabled = await screenPrivacy.isEnabled();
const recording = await screenPrivacy.isRecording();
```

## Events

```php
use Native\Mobile\Attributes\OnNative;
use MacroTom\ScreenPrivacy\Events\ScreenCaptureDetected;
use MacroTom\ScreenPrivacy\Events\ScreenRecordingDetected;
use MacroTom\ScreenPrivacy\Events\ScreenPrivacyEnabled;
use MacroTom\ScreenPrivacy\Events\ScreenPrivacyDisabled;

#[OnNative(ScreenCaptureDetected::class)]
public function handleScreenCapture()
{
    // A screenshot attempt was detected
}

#[OnNative(ScreenRecordingDetected::class)]
public function handleScreenRecording(bool $isRecording)
{
    // Screen recording started or stopped
}

#[OnNative(ScreenPrivacyEnabled::class)]
public function handlePrivacyEnabled()
{
    // Screen privacy was enabled
}

#[OnNative(ScreenPrivacyDisabled::class)]
public function handlePrivacyDisabled()
{
    // Screen privacy was disabled
}
```

## Platform Behavior

| Feature | Android | iOS |
|---|---|---|
| Screenshot blocking | FLAG_SECURE (hardware-level) | Secure overlay via UITextField |
| Screenshot detection | Android 14+ (API 34) callback | `userDidTakeScreenshotNotification` |
| Recording detection | Not supported (returns `false`) | `UIScreen.isCaptured` |
| Minimum version | API 26 | iOS 13.0 |

## Important Notes

- Call `init()` once during app initialization before using other methods.
- Android uses `FLAG_SECURE` which hardware-blocks all screen captures while enabled.
- iOS detects screenshots after they occur and applies a secure overlay to block recordings.
- Screen recording detection (`isRecording()`) is only available on iOS. On Android it always returns `false`.

## License

MIT
