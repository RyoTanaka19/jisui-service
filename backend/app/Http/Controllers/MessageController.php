<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // 受信メッセージ一覧
    public function index(Request $request)
    {
        $messages = $request->user()->receivedMessages()->with('sender')->paginate(20);
        return response()->json($messages);
    }

    // メッセージ送信
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
            'type'        => 'sometimes|string',
        ]);

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $validated['receiver_id'],
            'subject'     => $validated['subject'],
            'body'        => $validated['body'],
            'type'        => $validated['type'] ?? 'general',
        ]);

        // 受信者に通知を作成
        Notification::create([
            'user_id' => $validated['receiver_id'],
            'type'    => 'message',
            'title'   => '新しいメッセージが届きました',
            'message' => "{$request->user()->name} からメッセージが届きました: {$validated['subject']}",
            'data'    => ['message_id' => $message->id],
        ]);

        return response()->json($message, 201);
    }

    // メッセージを既読にする
    public function markAsRead(Request $request, Message $message)
    {
        if ($message->receiver_id !== $request->user()->id) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $message->update(['is_read' => true]);
        return response()->json(['message' => '既読にしました']);
    }

    // 未読数取得
    public function unreadCount(Request $request)
    {
        $count = $request->user()->receivedMessages()->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    // 管理者申請
    public function requestAdmin(Request $request)
    {
        $superAdmin = User::where('email', env('SUPER_ADMIN_EMAIL'))->first();

        if (!$superAdmin) {
            return response()->json(['message' => 'スーパー管理者が見つかりません'], 404);
        }

        // メッセージ送信
        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $superAdmin->id,
            'subject'     => '管理者権限の申請',
            'body'        => "{$request->user()->name}（{$request->user()->email}）が管理者権限を申請しています。",
            'type'        => 'admin_request',
        ]);

        // スーパー管理者に通知
        Notification::create([
            'user_id' => $superAdmin->id,
            'type'    => 'admin_request',
            'title'   => '管理者権限の申請',
            'message' => "{$request->user()->name} が管理者権限を申請しています",
            'data'    => ['user_id' => $request->user()->id, 'message_id' => $message->id],
        ]);

        return response()->json(['message' => '申請を送信しました']);
    }
}