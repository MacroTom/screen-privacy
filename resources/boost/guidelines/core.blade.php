## macrotom/screen-privacy

Prevent screenshots and screen recordings of sensitive content using native platform protections (FLAG_SECURE on Android, secure overlay + UIScreen.isCaptured on iOS).

### Installation

```bash
composer require macrotom/screen-privacy
```

### PHP Usage (Livewire/Blade)

Use the `ScreenPrivacy` facade:

@verbatim
<code-snippet name="Initializing Screen Privacy" lang="php">
use MacroTom\ScreenPrivacy\Facades\ScreenPrivacy;

// Initialize screen privacy (call once on app start)
// On Android 14+ (API 34+), this registers the screen capture callback
// On iOS, this sets up the UIScreen.isCaptured observer and secure overlay
ScreenPrivacy::init(); // returns bool
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Enabling and Disabling Protection" lang="php">
use MacroTom\ScreenPrivacy\Facades\ScreenPrivacy;

// Enable screen privacy protection
// Android: sets FLAG_SECURE on the window
// iOS: adds a secure text field overlay that hides content from captures
ScreenPrivacy::enable(); // returns bool

// Disable screen privacy protection
// Android: clears FLAG_SECURE
// iOS: removes the secure overlay
ScreenPrivacy::disable(); // returns bool
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="Checking Status" lang="php">
use MacroTom\ScreenPrivacy\Facades\ScreenPrivacy;

// Check if screen privacy protection is currently active
$isEnabled = ScreenPrivacy::isEnabled(); // returns bool

// Check if the screen is currently being recorded
// NOTE: Always returns false on Android; only works on iOS via UIScreen.isCaptured
$isRecording = ScreenPrivacy::isRecording(); // returns bool
</code-snippet>
@endverbatim

### Handling Events in PHP

@verbatim
<code-snippet name="ScreenCaptureDetected Event" lang="php">
use Native\Mobile\Attributes\OnNative;
use MacroTom\ScreenPrivacy\Events\ScreenCaptureDetected;

#[OnNative(ScreenCaptureDetected::class)]
public function handleScreenCapture()
{
    // A screenshot attempt was detected
    // No payload -- this event has no constructor parameters
    // Android: fired via Activity.ScreenCaptureCallback (API 34+)
    // iOS: fired when a screenshot notification is observed
}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="ScreenRecordingDetected Event" lang="php">
use Native\Mobile\Attributes\OnNative;
use MacroTom\ScreenPrivacy\Events\ScreenRecordingDetected;

#[OnNative(ScreenRecordingDetected::class)]
public function handleScreenRecording(bool $isRecording)
{
    // Screen recording state changed
    // $isRecording is true when recording started, false when recording stopped
    // NOTE: Only fires on iOS (UIScreen.isCaptured changes)
    // On Android this event is never dispatched
    if ($isRecording) {
        // Recording started -- take action (e.g. hide sensitive data)
    } else {
        // Recording stopped -- restore UI
    }
}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="ScreenPrivacyEnabled Event" lang="php">
use Native\Mobile\Attributes\OnNative;
use MacroTom\ScreenPrivacy\Events\ScreenPrivacyEnabled;

#[OnNative(ScreenPrivacyEnabled::class)]
public function handlePrivacyEnabled()
{
    // Screen privacy protection was enabled
    // No payload -- this event has no constructor parameters
    // Fired after a successful call to ScreenPrivacy::enable()
}
</code-snippet>
@endverbatim

@verbatim
<code-snippet name="ScreenPrivacyDisabled Event" lang="php">
use Native\Mobile\Attributes\OnNative;
use MacroTom\ScreenPrivacy\Events\ScreenPrivacyDisabled;

#[OnNative(ScreenPrivacyDisabled::class)]
public function handlePrivacyDisabled()
{
    // Screen privacy protection was disabled
    // No payload -- this event has no constructor parameters
    // Fired after a successful call to ScreenPrivacy::disable()
}
</code-snippet>
@endverbatim

### JavaScript Usage (Vue/React/Inertia)

@verbatim
<code-snippet name="Using ScreenPrivacy in JavaScript" lang="javascript">
import { screenPrivacy } from '@macrotom/screen-privacy';

// Initialize on app start (call once)
await screenPrivacy.init();

// Enable screen privacy protection
await screenPrivacy.enable();

// Disable screen privacy protection
await screenPrivacy.disable();

// Check if screen privacy is currently active
const enabled = await screenPrivacy.isEnabled();  // { enabled: true/false }

// Check if the screen is currently being recorded (iOS only; always false on Android)
const recording = await screenPrivacy.isRecording();  // { isRecording: true/false }
</code-snippet>
@endverbatim

### Handling Events in JavaScript (Vue)

@verbatim
<code-snippet name="Listening to All ScreenPrivacy Events in Vue" lang="javascript">
import { on, off, Events } from '#nativephp';
import { onMounted, onUnmounted, ref } from 'vue';

const isProtected = ref(false);
const isBeingRecorded = ref(false);

const handleCaptureDetected = () => {
    // Screenshot attempt detected (no payload)
    console.warn('Screenshot detected!');
};

const handleRecordingDetected = (payload) => {
    // payload.isRecording: boolean (true = started, false = stopped)
    // iOS only; never fires on Android
    isBeingRecorded.value = payload.isRecording;
};

const handlePrivacyEnabled = () => {
    // Screen privacy was enabled (no payload)
    isProtected.value = true;
};

const handlePrivacyDisabled = () => {
    // Screen privacy was disabled (no payload)
    isProtected.value = false;
};

onMounted(() => {
    on(Events.ScreenPrivacy.ScreenCaptureDetected, handleCaptureDetected);
    on(Events.ScreenPrivacy.ScreenRecordingDetected, handleRecordingDetected);
    on(Events.ScreenPrivacy.ScreenPrivacyEnabled, handlePrivacyEnabled);
    on(Events.ScreenPrivacy.ScreenPrivacyDisabled, handlePrivacyDisabled);
});

onUnmounted(() => {
    off(Events.ScreenPrivacy.ScreenCaptureDetected, handleCaptureDetected);
    off(Events.ScreenPrivacy.ScreenRecordingDetected, handleRecordingDetected);
    off(Events.ScreenPrivacy.ScreenPrivacyEnabled, handlePrivacyEnabled);
    off(Events.ScreenPrivacy.ScreenPrivacyDisabled, handlePrivacyDisabled);
});
</code-snippet>
@endverbatim

### Methods

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `init()` | - | `bool` | Initialize screen privacy. Registers platform callbacks. Call once on app start. |
| `enable()` | - | `bool` | Enable screen privacy protection. Blocks screenshots and hides content from recordings. |
| `disable()` | - | `bool` | Disable screen privacy protection. Restores normal screenshot and recording behavior. |
| `isEnabled()` | - | `bool` | Check if screen privacy protection is currently active. |
| `isRecording()` | - | `bool` | Check if the screen is currently being recorded. **Always returns `false` on Android.** |

### Events

| Event | Payload Fields | Description |
|-------|---------------|-------------|
| `ScreenCaptureDetected` | _(none)_ | A screenshot attempt was detected. On Android, requires API 34+ and fires via `Activity.ScreenCaptureCallback`. On iOS, fires via screenshot notification observer. |
| `ScreenRecordingDetected` | `bool $isRecording` | Screen recording state changed. `true` = recording started, `false` = recording stopped. **iOS only** -- never fires on Android. |
| `ScreenPrivacyEnabled` | _(none)_ | Screen privacy protection was successfully enabled. Fired after `enable()` completes. |
| `ScreenPrivacyDisabled` | _(none)_ | Screen privacy protection was successfully disabled. Fired after `disable()` completes. |

### Platform Behavior

- **Android:** Uses `FLAG_SECURE` on the window, which hardware-blocks all screen captures (screenshots appear black, screen recordings show a black screen). Screenshot detection via `Activity.ScreenCaptureCallback` requires API 34+ (Android 14+). `isRecording()` always returns `false` because Android does not expose screen recording state to apps. Minimum API 26.
- **iOS:** Uses a secure `UITextField` overlay technique that hides content from screenshots and screen recordings. Screenshot detection fires via `UIApplication.userDidTakeScreenshotNotification`. Screen recording detection uses `UIScreen.main.isCaptured` KVO observer. Minimum iOS 13.0.
- Both platforms dispatch `ScreenPrivacyEnabled` and `ScreenPrivacyDisabled` events when protection state changes.

### Important Notes

- Call `init()` once during app initialization (e.g. in a Livewire `mount()` or Vue `onMounted`) before calling any other methods. On Android 14+, this registers the screen capture callback. On iOS, this sets up the `UIScreen.isCaptured` observer.
- `enable()` and `disable()` return `false` when the native bridge is unavailable (e.g. during local development in a browser).
- `isRecording()` is only meaningful on iOS. On Android it always returns `false` -- do not rely on it for cross-platform recording detection.
- `ScreenCaptureDetected` fires **after** the screenshot is taken -- it cannot prevent the screenshot on iOS (only detect it). On Android, `FLAG_SECURE` prevents the screenshot content from being captured, so the user gets a black image.
- `ScreenRecordingDetected` is iOS-only. It fires when recording starts (`isRecording = true`) and again when recording stops (`isRecording = false`).
- No additional permissions are required on either platform.
