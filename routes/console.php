<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:send-reminders')->everyMinute();
Schedule::command('attendance:daily-summary 8')->dailyAt('08:05');
Schedule::command('attendance:daily-summary 15')->dailyAt('15:05');
Schedule::command('attendance:daily-summary 22')->dailyAt('22:05');
