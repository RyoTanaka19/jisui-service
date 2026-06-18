<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 一覧
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return response()->json($posts);
    }

    // 作成
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image_url'    => 'nullable|url',
            'cooking_time' => 'nullable|integer|min:1',
            'servings'     => 'required|integer|min:1',
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    // 詳細
    public function show(Post $post)
    {
        return response()->json($post->load('user'));
    }

    // 更新
    public function update(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validated = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'image_url'    => 'nullable|url',
            'cooking_time' => 'nullable|integer|min:1',
            'servings'     => 'sometimes|integer|min:1',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    // 削除
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $post->delete();

        return response()->json(['message' => '削除しました']);
    }
}
