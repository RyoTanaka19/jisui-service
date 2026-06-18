<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // コメント一覧
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->latest()->get();
        return response()->json($comments);
    }

    // コメント作成
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);

        return response()->json($comment->load('user'), 201);
    }

    // コメント削除
    public function destroy(Request $request, Post $post, Comment $comment)
    {
        if ($request->user()->id !== $comment->user_id) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $comment->delete();

        return response()->json(['message' => '削除しました']);
    }
}