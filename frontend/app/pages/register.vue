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

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);

const validate = () => {
  let valid = true;
  errors.name = '';
  errors.email = '';
  errors.password = '';
  errors.password_confirmation = '';

  if (!form.name) {
    errors.name = 'お名前は必須です';
    valid = false;
  }

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

const handleRegister = async () => {
  if (!validate()) return;
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
    const data = e?.data;
    if (data?.errors?.email) {
      errors.email = 'このメールアドレスは既に使われております';
    } else {
      errors.name = data?.errors?.name?.[0] || '';
      errors.email = data?.errors?.email?.[0] || '';
      errors.password = data?.errors?.password?.[0] || '';
    }
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

      <!-- Googleログインボタン -->
      <button
        @click="
          () =>
            (window.location.href = `${useRuntimeConfig().public.apiBase}/auth/google`)
        "
        class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition mb-4 font-semibold text-gray-700"
      >
        <img src="https://www.google.com/favicon.ico" class="w-5 h-5" />
        Googleで登録
      </button>

      <div class="flex items-center gap-3 mb-4">
        <hr class="flex-1 border-gray-200" />
        <span class="text-gray-400 text-sm">または</span>
        <hr class="flex-1 border-gray-200" />
      </div>

      <!-- お名前 -->
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          お名前 <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.name"
          type="text"
          :class="[
            'w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-400',
            errors.name ? 'border-red-400' : 'border-gray-300',
          ]"
          placeholder="田中太郎"
        />
        <p v-if="errors.name" class="text-red-500 text-xs mt-1">
          {{ errors.name }}
        </p>
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
      <div class="mb-4">
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
