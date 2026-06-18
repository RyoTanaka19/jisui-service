<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'イベントのステータスを自動更新する';

    public function handle()
    {
        $now = Carbon::now();

        // 開始時間を過ぎたら ongoing に変更して参加者に通知
        $startingEvents = Event::where('status', 'upcoming')
            ->where('starts_at', '<=', $now)
            ->get();

        foreach ($startingEvents as $event) {
            $event->update(['status' => 'ongoing']);

            // 参加者全員に通知
            $participants = EventParticipant::where('event_id', $event->id)->get();
            foreach ($participants as $participant) {
                Notification::create([
                    'user_id' => $participant->user_id,
                    'type'    => 'event_started',
                    'title'   => 'イベントが開始しました',
                    'message' => "「{$event->title}」が開始しました！",
                    'data'    => ['event_id' => $event->id],
                ]);
            }
        }

        // 終了時間を過ぎたら finished に変更して参加者に通知
        $endingEvents = Event::where('status', 'ongoing')
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($endingEvents as $event) {
            $event->update(['status' => 'finished']);

            // 参加者全員に通知
            $participants = EventParticipant::where('event_id', $event->id)->get();
            foreach ($participants as $participant) {
                Notification::create([
                    'user_id' => $participant->user_id,
                    'type'    => 'event_finished',
                    'title'   => 'イベントが終了しました',
                    'message' => "「{$event->title}」が終了しました。ありがとうございました！",
                    'data'    => ['event_id' => $event->id],
                ]);
            }
        }

        $this->info('イベントのステータスを更新しました');
    }
}