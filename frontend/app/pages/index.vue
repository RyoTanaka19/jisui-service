<script setup lang="ts">
const { isLoggedIn, logout } = useAuth();
const router = useRouter();
const config = useRuntimeConfig();

const posts = ref(null);
const searchQuery = ref('');

const fetchPosts = async (search = '') => {
  try {
    const params = search ? `?search=${encodeURIComponent(search)}` : '';
    const response = await $fetch(`${config.public.apiBase}/posts${params}`, {
      headers: { Accept: 'application/json' },
      cache: 'no-cache',
    });
    posts.value = response;
  } catch (e) {
    console.error('API接続エラー', e);
  }
};

onMounted(async () => {
  await fetchPosts();
});

const handleSearch = async () => {
  await fetchPosts(searchQuery.value);
};

const clearSearch = async () => {
  searchQuery.value = '';
  await fetchPosts();
};

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

      <!-- 検索フォーム -->
      <div class="flex gap-2 mb-6">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="レシピを検索..."
          class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          @keyup.enter="handleSearch"
        />
        <button
          @click="handleSearch"
          class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg transition"
        >
          検索
        </button>
        <button
          v-if="searchQuery"
          @click="clearSearch"
          class="bg-gray-200 hover:bg-gray-300 text-gray-600 font-semibold px-4 py-2 rounded-lg transition"
        >
          クリア
        </button>
      </div>

      <div v-if="!posts?.data?.length" class="text-center text-gray-400 py-12">
        <p v-if="searchQuery">
          「{{ searchQuery }}」に一致するレシピが見つかりませんでした
        </p>
        <p v-else>まだ投稿がありません</p>
      </div>

      <div class="grid gap-4">
        <NuxtLink
          v-for="post in posts?.data"
          :key="post.id"
          :to="`/posts/${post.id}`"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition block overflow-hidden"
        >
          <img
            v-if="post.image_url"
            :src="post.image_url"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
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
          </div>
        </NuxtLink>
      </div>
    </main>
  </div>
</template>
