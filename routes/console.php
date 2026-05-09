<?php

use App\Models\UrgentTodo;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    UrgentTodo::where('done', true)
        ->where('updated_at', '<', now()->subDays(7))
        ->delete();
})->daily()->description('Clean up completed urgent todos older than 7 days');

Schedule::call(function () {
    UrgentTodo::where('created_at', '<', now()->subDays(30))
        ->delete();
})->daily()->description('Clean up all urgent todos older than 30 days');
