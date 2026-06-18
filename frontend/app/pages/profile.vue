<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const { api } = useApi();
const config = useRuntimeConfig();
const token = useCookie('auth_token');

const profile = ref(null);
const loading = ref(false);
const avatarLoading = ref(false);
const success = ref('');
const error = ref('');

const form = reactive({
  name: '',
  email: '',
});

const requestSuccess = ref('');
const requestError = ref('');
const requestLoading = ref(false);

const handleAdminRequest = async () => {
  if (!confirm('スーパー管理者に管理者権限を申請しますか？')) return;
  requestLoading.value = true;
  requestSuccess.value = '';
  requestError.value = '';
  try {
    const response: any = await $fetch(
      `${config.public.apiBase}/admin-request`,
      {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token.value}`,
        },
      },
    );
    requestSuccess.value = response.message;
  } catch (e: any) {
    requestError.value = '申請に失敗しました';
  } finally {
    requestLoading.value = false;
  }
};

const fetchProfile = async () => {
  profile.value = await $fetch(`${config.public.apiBase}/profile`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
  form.name = profile.value.name;
  form.email = profile.value.email;
};

onMounted(async () => {
  await fetchProfile();
});

const handleUpdate = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;
  try {
    await $fetch(`${config.public.apiBase}/profile`, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
      },
      body: form,
    });
    success.value = 'プロフィールを更新しました';
    await fetchProfile();
  } catch (e: any) {
    error.value = '更新に失敗しました';
  } finally {
    loading.value = false;
  }
};

const handleAvatarUpload = async (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;

  avatarLoading.value = true;
  error.value = '';
  try {
    const formData = new FormData();
    formData.append('image', file);

    const response: any = await $fetch(
      `${config.public.apiBase}/profile/avatar`,
      {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
        body: formData,
      },
    );

    profile.value.avatar_url = response.avatar_url;
    success.value = 'プロフィール画像を更新しました';
    await fetchProfile();
  } catch (e) {
    error.value = '画像のアップロードに失敗しました';
  } finally {
    avatarLoading.value = false;
  }
};
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
        <h1 class="text-2xl font-bold text-gray-800 mb-6">プロフィール編集</h1>

        <div
          v-if="success"
          class="bg-green-50 text-green-600 rounded-lg p-3 mb-4 text-sm"
        >
          {{ success }}
        </div>

        <div
          v-if="error"
          class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
        >
          {{ error }}
        </div>

        <!-- プロフィール画像 -->
        <div class="flex flex-col items-center mb-8">
          <div
            class="relative cursor-pointer"
            @click="($refs.avatarInput as HTMLInputElement).click()"
          >
            <img
              v-if="profile?.avatar_url"
              :src="profile.avatar_url"
              class="w-24 h-24 rounded-full object-cover border-4 border-green-100"
            />
            <div
              v-else
              class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-3xl"
            >
              👤
            </div>
            <div
              class="absolute bottom-0 right-0 bg-green-500 rounded-full p-1"
            >
              <span class="text-white text-xs">📷</span>
            </div>
          </div>
          <p v-if="avatarLoading" class="text-green-500 text-sm mt-2">
            アップロード中...
          </p>
          <p v-else class="text-gray-400 text-sm mt-2">
            クリックして画像を変更
          </p>
          <input
            ref="avatarInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleAvatarUpload"
          />
        </div>

        <!-- ユーザー名 -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >ユーザー名</label
          >
          <input
            v-model="form.name"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <!-- メールアドレス -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >メールアドレス</label
          >
          <input
            v-model="form.email"
            type="email"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          />
        </div>

        <button
          @click="handleUpdate"
          :disabled="loading"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? '更新中...' : '更新する' }}
        </button>

        <!-- 管理者申請 -->
        <div
          v-if="!profile?.is_admin"
          class="mt-6 pt-6 border-t border-gray-100"
        >
          <h2 class="text-sm font-semibold text-gray-700 mb-2">
            管理者権限の申請
          </h2>
          <p class="text-xs text-gray-400 mb-3">
            イベントを作成・管理したい場合はスーパー管理者に申請できます。
          </p>

          <div
            v-if="requestSuccess"
            class="bg-green-50 text-green-600 rounded-lg p-3 mb-3 text-sm"
          >
            {{ requestSuccess }}
          </div>
          <div
            v-if="requestError"
            class="bg-red-50 text-red-600 rounded-lg p-3 mb-3 text-sm"
          >
            {{ requestError }}
          </div>

          <button
            @click="handleAdminRequest"
            :disabled="requestLoading"
            class="bg-purple-500 hover:bg-purple-600 text-white font-semibold px-4 py-2 rounded-lg transition text-sm disabled:opacity-50"
          >
            {{ requestLoading ? '申請中...' : '管理者権限を申請する' }}
          </button>
        </div>
        <div v-else class="mt-6 pt-6 border-t border-gray-100">
          <span class="text-sm text-purple-500 font-semibold"
            >✅ 管理者権限あり</span
          >
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
