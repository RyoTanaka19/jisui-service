<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // スーパー管理者チェック
    private function isSuperAdmin(Request $request): bool
    {
        return $request->user()->email === env('SUPER_ADMIN_EMAIL');
    }

    // ユーザー一覧取得（管理者のみ）
    public function users(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $users = User::latest()->paginate(20);
        return response()->json($users);
    }

    // 管理者権限の付与・剥奪（スーパー管理者のみ）
    public function toggleAdmin(Request $request, User $user)
    {
        if (!$this->isSuperAdmin($request)) {
            return response()->json(['message' => 'この操作はスーパー管理者のみ実行できます'], 403);
        }

        if ($request->user()->id === $user->id) {
            return response()->json(['message' => '自分自身の権限は変更できません'], 400);
        }

        $user->update(['is_admin' => !$user->is_admin]);

        // ユーザーに通知を送る
        Notification::create([
            'user_id' => $user->id,
            'type'    => $user->is_admin ? 'admin_granted' : 'admin_revoked',
            'title'   => $user->is_admin ? '管理者権限が付与されました' : '管理者権限が剥奪されました',
            'message' => $user->is_admin
                ? 'スーパー管理者によって管理者権限が付与されました。イベントの作成・管理が可能になりました。'
                : 'スーパー管理者によって管理者権限が剥奪されました。',
            'data'    => ['is_admin' => $user->is_admin],
        ]);

        return response()->json([
            'message'  => $user->is_admin ? '管理者権限を付与しました' : '管理者権限を剥奪しました',
            'is_admin' => $user->is_admin,
        ]);
    }
}