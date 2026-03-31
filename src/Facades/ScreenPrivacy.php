<?php

namespace MacroTom\ScreenPrivacy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool init()
 * @method static bool enable()
 * @method static bool disable()
 * @method static bool isEnabled()
 * @method static bool isRecording()
 *
 * @see \MacroTom\ScreenPrivacy\ScreenPrivacy
 */
class ScreenPrivacy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'screen-privacy';
    }
}