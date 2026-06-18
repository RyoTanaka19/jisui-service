<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const { api } = useApi();
const route = useRoute();
const router = useRouter();

const id = computed(() => route.params.id);

const { data: post } = await useAsyncData(`post-${id.value}`, () =>
  api(`/posts/${id.value}`),
);

const form = reactive({
  title: post.value?.title || '',
  description: post.value?.description || '',
  cooking_time: post.value?.cooking_time || '',
  servings: post.value?.servings || 1,
});

const error = ref('');
const loading = ref(false);

const handleSubmit = async () => {
  error.value = '';
  loading.value = true;
  try {
    await api(`/posts/${id.value}`, {
      method: 'PUT',
      body: form,
    });
    await clearNuxtData(`post-${id.value}`);
    router.push(`/posts/${id.value}`);
  } catch (e: any) {
    error.value = '更新に失敗しました。入力内容を確認してください';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-4">
        <NuxtLink to="/" class="text-xl font-bold text-green-600"
          >🍳 自炊サービス</NuxtLink
        >
      </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">レシピを編集する</h1>

        <div
          v-if="error"
          class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
        >
          {{ error }}
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >タイトル</label
          >
          <input
            v-model="form.title"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >説明</label
          >
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >調理時間（分）</label
          >
          <input
            v-model="form.cooking_time"
            type="number"
            min="1"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >何人前</label
          >
          <input
            v-model="form.servings"
            type="number"
            min="1"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <button
          @click="handleSubmit"
          :disabled="loading"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? '更新中...' : '更新する' }}
        </button>
      </div>

      <div class="mt-4">
        <NuxtLink
          :to="`/posts/${id}`"
          class="text-green-500 hover:underline text-sm"
          >← 詳細に戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
