<script setup lang="ts">
const config = useRuntimeConfig();

const posts = ref(null);
const searchQuery = ref('');
const currentPage = ref(1);

const fetchPosts = async (search = '', page = 1) => {
  try {
    let url = `${config.public.apiBase}/posts?page=${page}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    const response = await $fetch(url, {
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
  currentPage.value = 1;
  await fetchPosts(searchQuery.value, 1);
};

const clearSearch = async () => {
  searchQuery.value = '';
  currentPage.value = 1;
  await fetchPosts();
};

const goToPage = async (page: number) => {
  currentPage.value = page;
  await fetchPosts(searchQuery.value, page);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-2xl mx-auto px-4 py-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">みんなのレシピ</h2>

      <!-- 検索フォーム -->
      <div class="flex gap-2 mb-6">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="レシピを検索..."
          class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
          @keyup.enter="handleSearch"
        />
        <button
          @click="handleSearch"
          class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
        >
          検索
        </button>
        <button
          v-if="searchQuery"
          @click="clearSearch"
          class="bg-gray-200 hover:bg-gray-300 text-gray-600 font-semibold px-3 py-2 rounded-lg transition text-sm"
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

      <div class="grid grid-cols-2 gap-3">
        <NuxtLink
          v-for="post in posts?.data"
          :key="post.id"
          :to="`/posts/${post.id}`"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition block overflow-hidden"
        >
          <img
            v-if="post.image_url"
            :src="post.image_url"
            class="w-full h-40 object-cover"
          />
          <div class="p-4">
            <h3 class="text-base font-semibold text-gray-800 mb-1">
              {{ post.title }}
            </h3>
            <p class="text-gray-500 text-sm mb-2 line-clamp-2">
              {{ post.description }}
            </p>
            <div class="flex items-center gap-3 text-xs text-gray-400">
              <span v-if="post.cooking_time">⏱ {{ post.cooking_time }}分</span>
              <span>👤 {{ post.user?.name }}</span>
            </div>
          </div>
        </NuxtLink>
      </div>

      <!-- ページネーション -->
      <div
        v-if="posts?.last_page"
        class="flex justify-center items-center gap-2 mt-6"
      >
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-1 rounded-lg bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm"
        >
          ← 前へ
        </button>

        <button
          v-for="page in posts?.last_page"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-3 py-1 rounded-lg border transition text-sm',
            currentPage === page
              ? 'bg-green-500 border-green-500 text-white'
              : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50',
          ]"
        >
          {{ page }}
        </button>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === posts?.last_page"
          class="px-3 py-1 rounded-lg bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm"
        >
          次へ →
        </button>
      </div>
    </main>
  </div>
</template>
