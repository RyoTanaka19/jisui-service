<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const config = useRuntimeConfig();
const route = useRoute();
const router = useRouter();
const { setFlash } = useFlash();

const form = reactive({
  email: (route.query.email as string) || '',
  token: (route.query.token as string) || '',
  password: '',
  password_confirmation: '',
});
const loading = ref(false);
const error = ref('');

const handleSubmit = async () => {
  error.value = '';
  loading.value = true;
  try {
    await $fetch(`${config.public.apiBase}/reset-password`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: form,
    });
    setFlash(
      'パスワードをリセットしました！新しいパスワードでログインしてください。',
    );
    router.push('/login');
  } catch (e: any) {
    error.value = 'パスワードのリセットに失敗しました。もう一度お試しください';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
        🔑 新しいパスワードを設定
      </h1>

      <div
        v-if="error"
        class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
      >
        {{ error }}
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >メールアドレス</label
        >
        <input
          v-model="form.email"
          type="email"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          placeholder="example@email.com"
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >新しいパスワード</label
        >
        <input
          v-model="form.password"
          type="password"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          placeholder="8文字以上"
        />
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >パスワード確認</label
        >
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          placeholder="8文字以上"
        />
      </div>

      <button
        @click="handleSubmit"
        :disabled="loading"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
      >
        {{ loading ? 'リセット中...' : 'パスワードをリセット' }}
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        <NuxtLink to="/login" class="text-green-500 hover:underline"
          >ログインに戻る</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
