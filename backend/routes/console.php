<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Clean up old read notifications daily
Schedule::command('model:prune', ['--model' => 'Illuminate\Notifications\DatabaseNotification'])
    ->daily()
    ->description('Prune read notifications older than 30 days');
