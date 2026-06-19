<script setup lang="ts">
const config = useRuntimeConfig();
const token = useCookie('auth_token');
const { isLoggedIn, logout } = useAuth();
const router = useRouter();

const profileAvatar = ref('');
const currentUserData = ref(null);
const { unreadMessageCount, fetchUnreadMessageCount } = useNotification();

onMounted(async () => {
  if (token.value) {
    try {
      const user: any = await $fetch(`${config.public.apiBase}/me`, {
        headers: { Authorization: `Bearer ${token.value}` },
      });
      profileAvatar.value = user.avatar_url || '';
      currentUserData.value = user;
      await fetchUnreadMessageCount();
    } catch (e) {}
  }
});

const handleLogout = async () => {
  await logout();
  router.push('/login');
};
</script>

<template>
  <header class="bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <NuxtLink to="/" class="text-xl font-bold text-green-600"
          >🍳 自炊サービス</NuxtLink
        >
        <NuxtLink
          to="/events"
          class="text-sm text-gray-500 hover:text-green-600 transition"
        >
          イベント
        </NuxtLink>
      </div>

      <div class="flex items-center gap-3">
        <template v-if="isLoggedIn">
          <NuxtLink
            to="/posts/create"
            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
          >
            ＋ 投稿する
          </NuxtLink>

          <NuxtLink
            v-if="currentUserData?.is_admin"
            to="/admin"
            class="text-sm text-purple-500 hover:text-purple-700 font-semibold"
          >
            管理者画面
          </NuxtLink>

          <!-- メッセージアイコン -->
          <NuxtLink to="/messages" class="relative">
            <span class="text-xl">✉️</span>
            <span
              v-if="unreadMessageCount > 0"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"
            >
              {{ unreadMessageCount }}
            </span>
          </NuxtLink>

          <!-- プロフィールアイコン -->
          <NuxtLink to="/profile">
            <img
              v-if="profileAvatar"
              :src="profileAvatar"
              class="w-9 h-9 rounded-full object-cover border-2 border-green-400"
            />
            <div
              v-else
              class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-lg"
            >
              👤
            </div>
          </NuxtLink>

          <button
            @click="handleLogout"
            class="text-sm text-gray-500 hover:text-gray-700"
          >
            ログアウト
          </button>
        </template>

        <template v-else>
          <NuxtLink to="/login" class="text-sm text-green-500 hover:underline"
            >ログイン</NuxtLink
          >
          <NuxtLink
            to="/register"
            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
          >
            会員登録
          </NuxtLink>
        </template>
      </div>
    </div>
  </header>
</template>
