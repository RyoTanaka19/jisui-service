<script setup lang="ts">
const { api } = useApi();
const { token, isLoggedIn } = useAuth();
const route = useRoute();
const router = useRouter();

const id = computed(() => route.params.id);

const post = ref(null);
const currentUser = ref(null);

const fetchPost = async () => {
  try {
    post.value = await $fetch(`http://nginx/api/posts/${id.value}`, {
      headers: { Accept: 'application/json' },
    });
  } catch (e) {
    post.value = await $fetch(`http://localhost:8080/api/posts/${id.value}`, {
      headers: { Accept: 'application/json' },
    });
  }
};

const fetchCurrentUser = async () => {
  if (!token.value) return;
  try {
    currentUser.value = await $fetch('http://nginx/api/me', {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
      },
    });
  } catch (e) {
    currentUser.value = await $fetch('http://localhost:8080/api/me', {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
      },
    });
  }
};

await fetchPost();
await fetchCurrentUser();

const handleDelete = async () => {
  if (!confirm('本当に削除しますか？')) return;
  await api(`/posts/${id.value}`, { method: 'DELETE' });
  router.push('/');
};

const isOwner = computed(() => {
  return isLoggedIn.value && currentUser.value?.id === post.value?.user_id;
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm">
      <div
        class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between"
      >
        <NuxtLink to="/" class="text-xl font-bold text-green-600"
          >🍳 自炊サービス</NuxtLink
        >
      </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ post?.title }}</h1>

        <div class="flex items-center gap-4 text-sm text-gray-400 mb-6">
          <span>⏱ {{ post?.cooking_time }}分</span>
          <span>👥 {{ post?.servings }}人前</span>
          <span>👤 {{ post?.user?.name }}</span>
        </div>

        <p class="text-gray-600 leading-relaxed mb-8">
          {{ post?.description }}
        </p>

        <div v-if="isOwner" class="flex gap-3">
          <NuxtLink
            :to="`/posts/${id}/edit`"
            class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
          >
            編集
          </NuxtLink>
          <button
            @click="handleDelete"
            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
          >
            削除
          </button>
        </div>
      </div>

      <div class="mt-4">
        <NuxtLink to="/" class="text-green-500 hover:underline text-sm"
          >← 一覧に戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
