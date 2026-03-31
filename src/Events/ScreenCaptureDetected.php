<?php

namespace MacroTom\ScreenPrivacy\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenCaptureDetected
{
    use Dispatchable, SerializesModels;
}
