<?php

use Illuminate\Support\Facades\Schedule;

// 1分ごとにイベントのステータスを自動更新
Schedule::command('events:update-status')->everyMinute();