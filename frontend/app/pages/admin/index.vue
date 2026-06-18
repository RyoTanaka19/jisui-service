<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const config = useRuntimeConfig();
const token = useCookie('auth_token');
const router = useRouter();

const users = ref(null);
const loading = ref(false);
const message = ref('');
const currentUser = ref(null);
const superAdminEmail = 'ryo98tana@gmail.com';

onMounted(async () => {
  try {
    currentUser.value = await $fetch(`${config.public.apiBase}/me`, {
      headers: { Authorization: `Bearer ${token.value}` },
    });
    if (!currentUser.value?.is_admin) {
      router.push('/');
      return;
    }
    await fetchUsers();
  } catch (e) {
    router.push('/');
  }
});

const fetchUsers = async () => {
  users.value = await $fetch(`${config.public.apiBase}/admin/users`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
};

const isSuperAdmin = computed(() => {
  return currentUser.value?.email === superAdminEmail;
});

const toggleAdmin = async (user: any) => {
  const action = user.is_admin ? '管理者権限を剥奪' : '管理者権限を付与';
  if (!confirm(`${user.name} の${action}しますか？`)) return;

  loading.value = true;
  message.value = '';
  try {
    const response: any = await $fetch(
      `${config.public.apiBase}/admin/users/${user.id}/toggle-admin`,
      {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token.value}`,
        },
      },
    );
    message.value = response.message;
    await fetchUsers();
  } catch (e: any) {
    message.value = e?.data?.message || '操作に失敗しました';
  } finally {
    loading.value = false;
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
        <span class="text-sm text-gray-500">管理者画面</span>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">ユーザー管理</h1>
      <p v-if="isSuperAdmin" class="text-sm text-purple-500 mb-6">
        スーパー管理者としてログイン中です
      </p>

      <div
        v-if="message"
        class="bg-green-50 text-green-600 rounded-lg p-3 mb-4 text-sm"
      >
        {{ message }}
      </div>

      <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500"
              >
                ID
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500"
              >
                名前
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500"
              >
                メールアドレス
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500"
              >
                権限
              </th>
              <th
                v-if="isSuperAdmin"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500"
              >
                操作
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="user in users?.data"
              :key="user.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 text-sm text-gray-500">{{ user.id }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    class="w-8 h-8 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-sm"
                  >
                    👤
                  </div>
                  <span class="text-sm font-medium text-gray-800">{{
                    user.name
                  }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'text-xs font-semibold px-2 py-1 rounded-full',
                    user.email === superAdminEmail
                      ? 'bg-yellow-100 text-yellow-700'
                      : user.is_admin
                        ? 'bg-purple-100 text-purple-700'
                        : 'bg-gray-100 text-gray-500',
                  ]"
                >
                  {{
                    user.email === superAdminEmail
                      ? 'スーパー管理者'
                      : user.is_admin
                        ? '管理者'
                        : '一般ユーザー'
                  }}
                </span>
              </td>
              <td v-if="isSuperAdmin" class="px-6 py-4">
                <button
                  v-if="user.email !== superAdminEmail"
                  @click="toggleAdmin(user)"
                  :disabled="loading"
                  :class="[
                    'text-xs font-semibold px-3 py-1 rounded-lg transition disabled:opacity-50',
                    user.is_admin
                      ? 'bg-red-100 hover:bg-red-200 text-red-600'
                      : 'bg-green-100 hover:bg-green-200 text-green-600',
                  ]"
                >
                  {{ user.is_admin ? '権限を剥奪' : '管理者にする' }}
                </button>
                <span v-else class="text-xs text-gray-400">変更不可</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        <NuxtLink to="/" class="text-green-500 hover:underline text-sm"
          >← トップに戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
