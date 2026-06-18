<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const config = useRuntimeConfig();
const email = ref('');
const loading = ref(false);
const success = ref(false);
const error = ref('');

const handleSubmit = async () => {
  error.value = '';
  loading.value = true;
  try {
    await $fetch(`${config.public.apiBase}/forgot-password`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { email: email.value },
    });
    success.value = true;
  } catch (e: any) {
    error.value = 'メールアドレスが見つかりませんでした';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
        🔑 パスワードリセット
      </h1>

      <div
        v-if="success"
        class="bg-green-50 text-green-600 rounded-lg p-4 mb-4 text-sm text-center"
      >
        パスワードリセットメールを送信しました。メールをご確認ください。
      </div>

      <div v-else>
        <div
          v-if="error"
          class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
        >
          {{ error }}
        </div>

        <p class="text-gray-500 text-sm mb-6">
          登録済みのメールアドレスを入力してください。パスワードリセット用のリンクをお送りします。
        </p>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1"
            >メールアドレス</label
          >
          <input
            v-model="email"
            type="email"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
            placeholder="example@email.com"
          />
        </div>

        <button
          @click="handleSubmit"
          :disabled="loading"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
        >
          {{ loading ? '送信中...' : 'リセットメールを送信' }}
        </button>
      </div>

      <p class="text-center text-sm text-gray-500 mt-4">
        <NuxtLink to="/login" class="text-green-500 hover:underline"
          >ログインに戻る</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
