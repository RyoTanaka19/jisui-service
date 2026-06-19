<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // イベント一覧
    public function index(Request $request)
    {
        $query = Event::with('creator')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(10);
        return response()->json($events);
    }

    // イベント詳細
    public function show(Request $request, Event $event)
    {
        $event->load('creator');
        $participantsCount = $event->participants()->count() + 1;
        $joined = false;

        if ($request->user()) {
            $joined = $event->participants()
                ->where('user_id', $request->user()->id)
                ->exists();
        }

        return response()->json([
            'event'              => $event,
            'participants_count' => $participantsCount,
            'joined'             => $joined,
        ]);
    }

    // イベント作成（管理者のみ）
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|url',
            'starts_at'   => 'required|date',
            'ends_at'     => 'required|date|after:starts_at',
            'status'      => 'sometimes|in:upcoming,ongoing,finished',
        ]);

        $event = Event::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($event, 201);
    }

    // イベント更新（管理者のみ）
    public function update(Request $request, Event $event)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image_url'   => 'nullable|url',
            'starts_at'   => 'sometimes|date',
            'ends_at'     => 'sometimes|date|after:starts_at',
            'status'      => 'sometimes|in:upcoming,ongoing,finished',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    // イベント削除（管理者のみ）
    public function destroy(Request $request, Event $event)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $event->delete();

        return response()->json(['message' => '削除しました']);
    }

    // イベント参加
    public function join(Request $request, Event $event)
    {
        $userId = $request->user()->id;
        $already = $event->participants()->where('user_id', $userId)->exists();

        if ($already) {
            return response()->json(['message' => 'すでに参加済みです'], 409);
        }

        $event->participants()->create(['user_id' => $userId]);

        return response()->json(['message' => '参加しました']);
    }

    // 参加済みイベント一覧
    public function joined(Request $request)
    {
        $events = $request->user()->events()->with('creator')->latest()->paginate(10);
        return response()->json($events);
    }
}