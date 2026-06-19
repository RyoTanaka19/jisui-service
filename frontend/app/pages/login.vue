<script setup lang="ts">
definePageMeta({ middleware: 'guest' });

const { login } = useAuth();
const router = useRouter();
const { setFlash } = useFlash();
const config = useRuntimeConfig();

const form = reactive({
  email: '',
  password: '',
});

const errors = reactive({
  email: '',
  password: '',
});

const loading = ref(false);

const validate = () => {
  let valid = true;
  errors.email = '';
  errors.password = '';

  if (!form.email) {
    errors.email = 'メールアドレスは必須です';
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = '正しいメールアドレスの形式で入力してください';
    valid = false;
  }

  if (!form.password) {
    errors.password = 'パスワードは必須です';
    valid = false;
  }

  return valid;
};

const handleLogin = async () => {
  if (!validate()) return;
  loading.value = true;
  try {
    await login(form.email, form.password);
    setFlash('ログインしました！');
    router.push('/posts');
  } catch (e: any) {
    errors.email = 'メールアドレスまたはパスワードが正しくありません';
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

      <!-- メールアドレス -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          メールアドレス <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.email"
          type="email"
          :class="[
            'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
            errors.email ? 'border-red-400' : 'border-gray-300',
          ]"
          placeholder="example@email.com"
        />
        <p v-if="errors.email" class="text-red-500 text-xs mt-1">
          {{ errors.email }}
        </p>
      </div>

      <!-- パスワード -->
      <div class="mb-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          パスワード <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.password"
          type="password"
          :class="[
            'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
            errors.password ? 'border-red-400' : 'border-gray-300',
          ]"
          placeholder="********"
        />
        <p v-if="errors.password" class="text-red-500 text-xs mt-1">
          {{ errors.password }}
        </p>
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
