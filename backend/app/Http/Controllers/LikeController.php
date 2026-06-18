<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // いいね状態の取得
    public function index(Request $request, Post $post)
    {
        $liked = false;
        if ($request->user()) {
            $liked = $post->likes()->where('user_id', $request->user()->id)->exists();
        }

        return response()->json([
            'likes_count' => $post->likes()->count(),
            'liked'       => $liked,
        ]);
    }

    // いいね・いいね解除
    public function toggle(Request $request, Post $post)
    {
        $userId = $request->user()->id;
        $like = $post->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $liked = true;
        }

        return response()->json([
            'likes_count' => $post->likes()->count(),
            'liked'       => $liked,
        ]);
    }
}
