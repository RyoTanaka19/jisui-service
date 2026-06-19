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

const errors = reactive({
  email: '',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);

const validate = () => {
  let valid = true;
  errors.email = '';
  errors.password = '';
  errors.password_confirmation = '';

  if (!form.email) {
    errors.email = 'メールアドレスは必須です';
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = '正しいメールアドレスの形式で入力してください';
    valid = false;
  }

  if (!form.password) {
    errors.password = '新しいパスワードは必須です';
    valid = false;
  } else if (form.password.length < 8) {
    errors.password = 'パスワードは8文字以上で入力してください';
    valid = false;
  }

  if (!form.password_confirmation) {
    errors.password_confirmation = 'パスワード確認は必須です';
    valid = false;
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'パスワードが一致しません';
    valid = false;
  }

  return valid;
};

const handleSubmit = async () => {
  if (!validate()) return;
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
    errors.password =
      'パスワードのリセットに失敗しました。もう一度お試しください';
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

      <!-- 新しいパスワード -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          新しいパスワード <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.password"
          type="password"
          :class="[
            'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
            errors.password ? 'border-red-400' : 'border-gray-300',
          ]"
          placeholder="8文字以上"
        />
        <p v-if="errors.password" class="text-red-500 text-xs mt-1">
          {{ errors.password }}
        </p>
      </div>

      <!-- パスワード確認 -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          パスワード確認 <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.password_confirmation"
          type="password"
          :class="[
            'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
            errors.password_confirmation ? 'border-red-400' : 'border-gray-300',
          ]"
          placeholder="8文字以上"
        />
        <p
          v-if="errors.password_confirmation"
          class="text-red-500 text-xs mt-1"
        >
          {{ errors.password_confirmation }}
        </p>
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
