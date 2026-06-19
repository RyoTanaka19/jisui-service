<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const { api } = useApi();
const router = useRouter();
const config = useRuntimeConfig();
const token = useCookie('auth_token');
const { setFlash } = useFlash();

const form = reactive({
  title: '',
  description: '',
  cooking_time: '',
  image_url: '',
});

const errors = reactive({
  title: '',
  description: '',
});

const loading = ref(false);
const imageLoading = ref(false);
const previewUrl = ref('');

const validate = () => {
  let valid = true;
  errors.title = '';
  errors.description = '';

  if (!form.title) {
    errors.title = 'タイトルは必須です';
    valid = false;
  }

  if (!form.description) {
    errors.description = '説明は必須です';
    valid = false;
  }

  return valid;
};

const handleImageUpload = async (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;

  imageLoading.value = true;
  try {
    const formData = new FormData();
    formData.append('image', file);

    const response: any = await $fetch(`${config.public.apiBase}/upload`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
      body: formData,
    });

    form.image_url = response.url;
    previewUrl.value = response.url;
  } catch (e) {
    errors.title = '画像のアップロードに失敗しました';
  } finally {
    imageLoading.value = false;
  }
};

const handleSubmit = async () => {
  if (!validate()) return;
  loading.value = true;
  try {
    await api('/posts', {
      method: 'POST',
      body: form,
    });
    setFlash('投稿しました！');
    router.push('/posts');
  } catch (e: any) {
    errors.title = '投稿に失敗しました。入力内容を確認してください';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">自炊を投稿する</h1>

        <!-- 画像アップロード -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            画像 <span class="text-gray-400 text-xs">（任意）</span>
          </label>
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

        <!-- タイトル -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            タイトル <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.title"
            type="text"
            :class="[
              'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
              errors.title ? 'border-red-400' : 'border-gray-300',
            ]"
            placeholder="例：簡単チャーハン"
          />
          <p v-if="errors.title" class="text-red-500 text-xs mt-1">
            {{ errors.title }}
          </p>
        </div>

        <!-- 説明 -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            説明 <span class="text-red-500">*</span>
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            :class="[
              'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
              errors.description ? 'border-red-400' : 'border-gray-300',
            ]"
            placeholder="レシピの説明を入力してください"
          />
          <p v-if="errors.description" class="text-red-500 text-xs mt-1">
            {{ errors.description }}
          </p>
        </div>

        <!-- 調理時間 -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            調理時間（分） <span class="text-gray-400 text-xs">（任意）</span>
          </label>
          <input
            v-model="form.cooking_time"
            type="number"
            min="1"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
            placeholder="例：30"
          />
        </div>

        <button
          @click="handleSubmit"
          :disabled="loading"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? '投稿中...' : '投稿する' }}
        </button>
      </div>
    </main>
  </div>
</template>
