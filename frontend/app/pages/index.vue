<script setup lang="ts">
const { isLoggedIn, logout } = useAuth();
const router = useRouter();
const config = useRuntimeConfig();

const posts = ref(null);

const fetchPosts = async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/posts`, {
      headers: { Accept: 'application/json' },
    });
    posts.value = response;
  } catch (e) {
    console.error('API接続エラー', e);
  }
};

await fetchPosts();

const handleLogout = async () => {
  await logout();
  router.push('/login');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div
        class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between"
      >
        <h1 class="text-xl font-bold text-green-600">🍳 自炊サービス</h1>
        <div class="flex items-center gap-3">
          <template v-if="isLoggedIn">
            <NuxtLink
              to="/posts/create"
              class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
            >
              ＋ 投稿する
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
              >会員登録</NuxtLink
            >
          </template>
        </div>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">みんなのレシピ</h2>

      <div v-if="!posts?.data?.length" class="text-center text-gray-400 py-12">
        まだ投稿がありません
      </div>

      <div class="grid gap-4">
        <NuxtLink
          v-for="post in posts?.data"
          :key="post.id"
          :to="`/posts/${post.id}`"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 block"
        >
          <h3 class="text-lg font-semibold text-gray-800 mb-2">
            {{ post.title }}
          </h3>
          <p class="text-gray-500 text-sm mb-3 line-clamp-2">
            {{ post.description }}
          </p>
          <div class="flex items-center gap-4 text-xs text-gray-400">
            <span>⏱ {{ post.cooking_time }}分</span>
            <span>👥 {{ post.servings }}人前</span>
            <span>👤 {{ post.user?.name }}</span>
          </div>
        </NuxtLink>
      </div>
    </main>
  </div>
</template>
