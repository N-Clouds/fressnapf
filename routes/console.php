<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:import-orders')->everyFiveMinutes();
Schedule::command('app:import-order-data')->everyFiveMinutes();
Schedule::command('app:ship-order')->everyTenMinutes();
