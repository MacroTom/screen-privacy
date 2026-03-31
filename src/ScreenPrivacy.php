<?php

namespace MacroTom\ScreenPrivacy;

class ScreenPrivacy
{
    /**
     * Enable screen privacy protection.
     */
    public function enable(): bool
    {
        if (function_exists('nativephp_call')) {
            $result = json_decode(nativephp_call('ScreenPrivacy.Enable'));
            $data = $result->data ?? $result;
            return $data->success ?? false;
        }

        return false;
    }

    /**
     * Disable screen privacy protection.
     */
    public function disable(): bool
    {
        if (function_exists('nativephp_call')) {
            $result = json_decode(nativephp_call('ScreenPrivacy.Disable'));
            $data = $result->data ?? $result;
            return $data->success ?? false;
        }

        return false;
    }

    /**
     * Check if screen privacy protection is currently active.
     */
    public function isEnabled(): bool
    {
        if (function_exists('nativephp_call')) {
            $result = json_decode(nativephp_call('ScreenPrivacy.IsEnabled'));
            $data = $result->data ?? $result;
            return $data->enabled ?? false;
        }

        return false;
    }

    /**
     * Check if the screen is currently being recorded.
     */
    public function isRecording(): bool
    {
        if (function_exists('nativephp_call')) {
            $result = json_decode(nativephp_call('ScreenPrivacy.IsRecording'));
            $data = $result->data ?? $result;
            return $data->isRecording ?? false;
        }

        return false;
    }

    /**
     * Initialize screen privacy (registers callbacks on Android 14+).
     */
    public function init(): bool
    {
        if (function_exists('nativephp_call')) {
            $result = json_decode(nativephp_call('ScreenPrivacy.Init'));
            $data = $result->data ?? $result;
            return $data->success ?? false;
        }

        return false;
    }
}