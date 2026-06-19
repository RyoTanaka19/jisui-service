<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const { register } = useAuth();
const router = useRouter();
const { setFlash } = useFlash();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const error = ref('');
const loading = ref(false);

const handleRegister = async () => {
  error.value = '';
  loading.value = true;
  try {
    await register(
      form.name,
      form.email,
      form.password,
      form.password_confirmation,
    );
    setFlash('会員登録が完了しました！');
    router.push('/posts');
  } catch (e: any) {
    error.value = '登録に失敗しました。入力内容を確認してください';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
        🍳 会員登録
      </h1>

      <div
        v-if="error"
        class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
      >
        {{ error }}
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1"
          >お名前</label
        >
        <input
          v-model="form.name"
          type="text"
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          placeholder="田中太郎"
        />
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
          >パスワード</label
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
        @click="handleRegister"
        :disabled="loading"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition disabled:opacity-50"
      >
        {{ loading ? '登録中...' : '会員登録' }}
      </button>

      <p class="text-center text-sm text-gray-500 mt-4">
        すでにアカウントをお持ちの方は
        <NuxtLink to="/login" class="text-green-500 hover:underline"
          >ログイン</NuxtLink
        >
      </p>
    </div>
  </div>
</template>
