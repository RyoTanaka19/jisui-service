<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const config = useRuntimeConfig();
const token = useCookie('auth_token');
const route = useRoute();
const router = useRouter();

const id = computed(() => route.params.id);
const form = reactive({
  title: '',
  description: '',
  image_url: '',
  starts_at: '',
  ends_at: '',
  status: 'upcoming',
});
const error = ref('');
const loading = ref(false);
const imageLoading = ref(false);
const previewUrl = ref('');

onMounted(async () => {
  try {
    const currentUser: any = await $fetch(`${config.public.apiBase}/me`, {
      headers: { Authorization: `Bearer ${token.value}` },
    });
    if (!currentUser?.is_admin) {
      router.push('/');
      return;
    }

    const response: any = await $fetch(
      `${config.public.apiBase}/events/${id.value}`,
      {
        headers: { Authorization: `Bearer ${token.value}` },
      },
    );
    const event = response.event;
    form.title = event.title;
    form.description = event.description || '';
    form.image_url = event.image_url || '';
    form.starts_at = event.starts_at?.slice(0, 16) || '';
    form.ends_at = event.ends_at?.slice(0, 16) || '';
    form.status = event.status;
    previewUrl.value = event.image_url || '';
  } catch (e) {
    router.push('/events');
  }
});

const handleImageUpload = async (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;

  imageLoading.value = true;
  try {
    const formData = new FormData();
    formData.append('image', file);

    const response: any = await $fetch(`${config.public.apiBase}/upload`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: formData,
    });

    form.image_url = response.url;
    previewUrl.value = response.url;
  } catch (e) {
    error.value = '画像のアップロードに失敗しました';
  } finally {
    imageLoading.value = false;
  }
};

const handleSubmit = async () => {
  error.value = '';
  loading.value = true;
  try {
    await $fetch(`${config.public.apiBase}/events/${id.value}`, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
      },
      body: form,
    });
    router.push(`/events/${id.value}`);
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
        <NuxtLink to="/events" class="text-xl font-bold text-green-600"
          >🍳 自炊サービス</NuxtLink
        >
      </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
          イベントを編集する
        </h1>

        <div
          v-if="error"
          class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
        >
          {{ error }}
        </div>

        <!-- 画像アップロード -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >画像</label
          >
          <div
            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-green-400 transition"
            @click="($refs.imageInput as HTMLInputElement).click()"
          >
            <img
              v-if="previewUrl"
              :src="previewUrl"
              class="mx-auto max-h-48 rounded-lg mb-2"
            />
            <p v-else class="text-gray-400 text-sm">
              クリックして画像をアップロード
            </p>
            <p v-if="imageLoading" class="text-green-500 text-sm">
              アップロード中...
            </p>
          </div>
          <input
            ref="imageInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleImageUpload"
          />
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
            >開始日時</label
          >
          <input
            v-model="form.starts_at"
            type="datetime-local"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >終了日時</label
          >
          <input
            v-model="form.ends_at"
            type="datetime-local"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >ステータス</label
          >
          <select
            v-model="form.status"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          >
            <option value="upcoming">開催予定</option>
            <option value="ongoing">開催中</option>
            <option value="finished">終了</option>
          </select>
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
          :to="`/events/${id}`"
          class="text-green-500 hover:underline text-sm"
          >← 詳細に戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
