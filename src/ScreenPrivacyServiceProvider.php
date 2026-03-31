<?php

namespace MacroTom\ScreenPrivacy;

use Illuminate\Support\ServiceProvider;

class ScreenPrivacyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('screen-privacy', function () {
            return new ScreenPrivacy();
        });
    }
}