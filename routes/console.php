<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Tasks
Schedule::command('subscriptions:check-expired')->daily();
Schedule::command('subscriptions:check-expiring')->daily();
Schedule::command('notifications:cleanup')->weekly();
Schedule::command('sitemap:generate')->daily();
Schedule::command('analytics:sync')->dailyAt('23:55');
