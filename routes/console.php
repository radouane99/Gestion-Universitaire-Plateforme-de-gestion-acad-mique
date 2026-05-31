<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::command('convocations:send-reminders')->dailyAt('08:00');
\Illuminate\Support\Facades\Schedule::command('appointments:send-reminders')->hourly();
\Illuminate\Support\Facades\Schedule::command('db:backup')->dailyAt('03:00');
