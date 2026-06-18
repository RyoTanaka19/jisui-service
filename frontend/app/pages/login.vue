<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const { login } = useAuth();
const router = useRouter();
const route = useRoute();
const config = useRuntimeConfig();

const form = reactive({
  email: '',
  password: '',
});
const error = ref('');
const success = ref('');
const loading = ref(false);

// パスワードリセット成功時のメッセージ
onMounted(() => {
  if (route.query.reset === 'success') {
    success.value =
      'パスワードをリセットしました。新しいパスワードでログインしてください。';
  }
  if (route.query.error === 'google_auth_failed') {
    error.value = 'Googleログインに失敗しました。もう一度お試しください。';
  }
});

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

const handleGoogleLogin = () => {
  window.location.href = `${config.public.apiBase}/auth/google`;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
        🍳 ログイン
      </h1>

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

      <!-- Googleログインボタン -->
      <button
        @click="handleGoogleLogin"
        class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition mb-4 font-semibold text-gray-700"
      >
        <img src="https://www.google.com/favicon.ico" class="w-5 h-5" />
        Googleでログイン
      </button>

      <div class="flex items-center gap-3 mb-4">
        <hr class="flex-1 border-gray-200" />
        <span class="text-gray-400 text-sm">または</span>
        <hr class="flex-1 border-gray-200" />
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

      <div class="mb-2">
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

      <div class="text-right mb-6">
        <NuxtLink
          to="/forgot-password"
          class="text-sm text-green-500 hover:underline"
        >
          パスワードをお忘れですか？
        </NuxtLink>
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
