<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('tickets:notify-overdue')->daily();
