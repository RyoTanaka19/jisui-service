<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
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
        $startingEvents = Event::where('status', 'upcoming')
            ->where('starts_at', '<=', $now)
            ->get();

        foreach ($startingEvents as $event) {
            $event->update(['status' => 'ongoing']);

            // 通知を送るユーザーIDを収集（重複なし）
            $notifyUserIds = collect();

            // イベント作成者に通知
            $notifyUserIds->push($event->created_by);

            // 参加者に通知
            $participants = EventParticipant::where('event_id', $event->id)->get();
            foreach ($participants as $participant) {
                $notifyUserIds->push($participant->user_id);
            }

            // 重複を除いて通知送信
            foreach ($notifyUserIds->unique() as $userId) {
                $user = User::find($userId);
                if (!$user) continue;

                // システムユーザーからメッセージを送信
                $systemUser = User::where('email', env('SUPER_ADMIN_EMAIL'))->first();
                if ($systemUser) {
                    Message::create([
                        'sender_id'   => $systemUser->id,
                        'receiver_id' => $userId,
                        'subject'     => "🎉 イベント「{$event->title}」が開始しました",
                        'body'        => "「{$event->title}」が開始しました！\n\n開始時間: " . $event->starts_at->format('Y年m月d日 H:i') . "\n終了時間: " . $event->ends_at->format('Y年m月d日 H:i'),
                        'type'        => 'event_started',
                    ]);
                }
            }
        }

        // 終了時間を過ぎたら finished に変更
        $endingEvents = Event::where('status', 'ongoing')
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($endingEvents as $event) {
            $event->update(['status' => 'finished']);

            // 通知を送るユーザーIDを収集（重複なし）
            $notifyUserIds = collect();

            // イベント作成者に通知
            $notifyUserIds->push($event->created_by);

            // 参加者に通知
            $participants = EventParticipant::where('event_id', $event->id)->get();
            foreach ($participants as $participant) {
                $notifyUserIds->push($participant->user_id);
            }

            // 重複を除いて通知送信
            foreach ($notifyUserIds->unique() as $userId) {
                $user = User::find($userId);
                if (!$user) continue;

                $systemUser = User::where('email', env('SUPER_ADMIN_EMAIL'))->first();
                if ($systemUser) {
                    Message::create([
                        'sender_id'   => $systemUser->id,
                        'receiver_id' => $userId,
                        'subject'     => "✅ イベント「{$event->title}」が終了しました",
                        'body'        => "「{$event->title}」が終了しました。ご参加ありがとうございました！\n\n終了時間: " . $event->ends_at->format('Y年m月d日 H:i'),
                        'type'        => 'event_finished',
                    ]);
                }
            }
        }

        $this->info('イベントのステータスを更新しました');
    }
}