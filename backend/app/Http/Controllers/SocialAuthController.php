<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Googleログインページにリダイレクト
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    // Googleからのコールバック処理
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // 既存ユーザーを検索、なければ作成
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'              => $googleUser->getName(),
                    'google_id'         => $googleUser->getId(),
                    'password'          => bcrypt(str()->random(24)),
                    'email_verified_at' => now(),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // フロントエンドにトークンを渡してリダイレクト
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect("{$frontendUrl}/auth/callback?token={$token}&name={$user->name}");

        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect("{$frontendUrl}/login?error=google_auth_failed");
        }
    }
}