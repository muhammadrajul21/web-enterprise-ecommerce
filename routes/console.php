<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('orders:auto-complete')
    ->dailyAt('00:10')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
