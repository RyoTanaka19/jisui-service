<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const { login } = useAuth();
const router = useRouter();

const form = reactive({
  email: '',
  password: '',
});
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  error.value = '';
  loading.value = true;
  try {
    await login(form.email, form.password);
    router.push('/');
  } catch (e: any) {
    error.value = 'メールアドレスまたはパスワードが正しくありません';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
        🍳 ログイン
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

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >パスワード</label
        >
        <input
          v-model="form.password"
          type="password"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          placeholder="********"
        />
      </div>

      <button
        @click="handleLogin"
        :disabled="loading"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
      >
        {{ loading ? 'ログイン中...' : 'ログイン' }}
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        アカウントをお持ちでない方は
        <NuxtLink to="/register" class="text-green-500 hover:underline"
          >会員登録</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
