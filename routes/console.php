<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('kedr:import-employees')
    ->dailyAt('01:00')
    ->onSuccess(fn() => Artisan::call('kedr:import'))
    ->after(fn() => Artisan::call('attendance:fill-missing'));

Schedule::command('plan:import')->monthlyOn(7);
