<?php

use App\Console\Commands\CheckTicketSlaCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// FASE 4: revisa SLAs próximos a vencer / vencidos y notifica por correo.
Schedule::command(CheckTicketSlaCommand::class)->everyFifteenMinutes();
