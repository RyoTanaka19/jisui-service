<script setup lang="ts">
const { api } = useApi();
const { token, isLoggedIn } = useAuth();
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();

const id = computed(() => route.params.id);

const post = ref(null);
const currentUser = ref(null);
const comments = ref([]);
const commentBody = ref('');
const commentLoading = ref(false);
const commentError = ref('');

const fetchPost = async () => {
  post.value = await $fetch(`${config.public.apiBase}/posts/${id.value}`, {
    headers: { Accept: 'application/json' },
  });
};

const fetchCurrentUser = async () => {
  if (!token.value) return;
  currentUser.value = await $fetch(`${config.public.apiBase}/me`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
};

const fetchComments = async () => {
  comments.value = await $fetch(
    `${config.public.apiBase}/posts/${id.value}/comments`,
    {
      headers: { Accept: 'application/json' },
    },
  );
};

await fetchPost();
await fetchCurrentUser();
await fetchComments();

const handleDelete = async () => {
  if (!confirm('本当に削除しますか？')) return;
  await api(`/posts/${id.value}`, { method: 'DELETE' });
  router.push('/');
};

const handleCommentSubmit = async () => {
  if (!commentBody.value.trim()) return;
  commentError.value = '';
  commentLoading.value = true;
  try {
    await api(`/posts/${id.value}/comments`, {
      method: 'POST',
      body: { body: commentBody.value },
    });
    commentBody.value = '';
    await fetchComments();
  } catch (e) {
    commentError.value = 'コメントの投稿に失敗しました';
  } finally {
    commentLoading.value = false;
  }
};

const handleCommentDelete = async (commentId: number) => {
  if (!confirm('コメントを削除しますか？')) return;
  await api(`/posts/${id.value}/comments/${commentId}`, { method: 'DELETE' });
  await fetchComments();
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
      <!-- 投稿詳細 -->
      <div class="bg-white rounded-2xl shadow overflow-hidden mb-6">
        <img
          v-if="post?.image_url"
          :src="post.image_url"
          class="w-full h-64 object-cover"
        />
        <div class="p-8">
          <h1 class="text-2xl font-bold text-gray-800 mb-4">
            {{ post?.title }}
          </h1>

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
      </div>

      <!-- コメントセクション -->
      <div class="bg-white rounded-2xl shadow p-6 mb-4">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
          コメント ({{ comments.length }})
        </h2>

        <!-- コメント投稿フォーム -->
        <div v-if="isLoggedIn" class="mb-6">
          <div
            v-if="commentError"
            class="bg-red-50 text-red-600 rounded-lg p-3 mb-3 text-sm"
          >
            {{ commentError }}
          </div>
          <textarea
            v-model="commentBody"
            rows="3"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 mb-2"
            placeholder="コメントを入力してください"
          />
          <button
            @click="handleCommentSubmit"
            :disabled="commentLoading || !commentBody.trim()"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg transition disabled:opacity-50"
          >
            {{ commentLoading ? '投稿中...' : 'コメントする' }}
          </button>
        </div>
        <div v-else class="mb-6 text-center">
          <p class="text-gray-500 text-sm">
            コメントするには
            <NuxtLink to="/login" class="text-green-500 hover:underline"
              >ログイン</NuxtLink
            >
            してください
          </p>
        </div>

        <!-- コメント一覧 -->
        <div
          v-if="comments.length === 0"
          class="text-center text-gray-400 py-4"
        >
          まだコメントがありません
        </div>

        <div class="space-y-4">
          <div
            v-for="comment in comments"
            :key="comment.id"
            class="border-b border-gray-100 pb-4 last:border-0"
          >
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-2">
                <span class="font-semibold text-gray-700 text-sm">{{
                  comment.user?.name
                }}</span>
                <span class="text-gray-400 text-xs">{{
                  new Date(comment.created_at).toLocaleDateString('ja-JP')
                }}</span>
              </div>
              <button
                v-if="currentUser?.id === comment.user_id"
                @click="handleCommentDelete(comment.id)"
                class="text-red-400 hover:text-red-600 text-xs transition"
              >
                削除
              </button>
            </div>
            <p class="text-gray-600 text-sm leading-relaxed">
              {{ comment.body }}
            </p>
          </div>
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
