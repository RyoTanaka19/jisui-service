<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Cloudinary;

class ProfileController extends Controller
{
    // プロフィール取得
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    // プロフィール更新
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json($request->user());
    }

    // プロフィール画像アップロード
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        $result = $cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'jisui-service/avatars']
        );

        $request->user()->update([
            'avatar_url' => $result['secure_url'],
        ]);

        return response()->json([
            'avatar_url' => $result['secure_url'],
        ]);
    }
}