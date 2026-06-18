<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'イベントのステータスを自動更新する';

    public function handle()
    {
        $now = Carbon::now();

        // 開始時間を過ぎたら ongoing に変更
        Event::where('status', 'upcoming')
            ->where('starts_at', '<=', $now)
            ->update(['status' => 'ongoing']);

        // 終了時間を過ぎたら finished に変更
        Event::where('status', 'ongoing')
            ->where('ends_at', '<=', $now)
            ->update(['status' => 'finished']);

        $this->info('イベントのステータスを更新しました');
    }
}