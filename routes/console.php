<?php

use App\Jobs\AutoHadirJob;
use App\Jobs\AutoAlphaGuruJob;
use App\Jobs\ProcessNotificationQueueJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks (Blueprint Section 6 & 9)
|--------------------------------------------------------------------------
|
| AutoHadirJob   : Runs at 07:00 daily – marks all students 'hadir'
| AutoAlphaGuruJob: Runs at 14:15 daily – marks absent teachers 'alpha'
| NotificationQueue: Runs every 5 minutes – sends pending WA notifications
|
*/

Schedule::job(new AutoHadirJob)->dailyAt('07:00')
    ->weekdays()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new AutoAlphaGuruJob)->dailyAt('14:15')
    ->weekdays()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new ProcessNotificationQueueJob)->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
