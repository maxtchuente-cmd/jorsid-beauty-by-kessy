<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('bookings:send-reminders')->dailyAt('08:00')->withoutOverlapping();
