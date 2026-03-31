<?php

namespace MacroTom\ScreenPrivacy\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenRecordingDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public bool $isRecording
    ) {
    }
}
