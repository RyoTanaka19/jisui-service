<script setup lang="ts">
const config = useRuntimeConfig();
const token = useCookie('auth_token');
const { isLoggedIn, logout } = useAuth();
const router = useRouter();
const { setFlash } = useFlash();

const profileAvatar = ref('');
const showDropdown = ref(false);

const fetchCurrentUser = async () => {
  if (token.value) {
    try {
      const user: any = await $fetch(`${config.public.apiBase}/me`, {
        headers: { Authorization: `Bearer ${token.value}` },
      });
      profileAvatar.value = user.avatar_url || '';
    } catch (e) {}
  } else {
    profileAvatar.value = '';
  }
};

onMounted(async () => {
  await fetchCurrentUser();
});

watch(token, async () => {
  await fetchCurrentUser();
});

const handleLogout = async () => {
  await logout();
  showDropdown.value = false;
  setFlash('ログアウトしました');
  router.push('/');
};

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const closeDropdown = () => {
  showDropdown.value = false;
};
</script>

<template>
  <header class="bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <NuxtLink to="/" class="text-xl font-bold text-green-600"
          >🍳 みんなと自炊</NuxtLink
        >
        <NuxtLink
          to="/posts"
          class="text-sm text-gray-500 hover:text-green-600 transition"
        >
          投稿一覧
        </NuxtLink>
      </div>

      <div class="flex items-center gap-3">
        <template v-if="isLoggedIn">
          <NuxtLink
            to="/posts/create"
            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
          >
            ＋ 投稿する
          </NuxtLink>

          <!-- プロフィールアイコン（プルダウン） -->
          <div class="relative">
            <button @click="toggleDropdown">
              <img
                v-if="profileAvatar"
                :src="profileAvatar"
                class="w-9 h-9 rounded-full object-cover border-2 border-green-400"
              />
              <div
                v-else
                class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-lg"
              >
                👤
              </div>
            </button>

            <!-- プルダウンメニュー -->
            <div
              v-if="showDropdown"
              class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50"
            >
              <div class="py-2">
                <NuxtLink
                  to="/profile"
                  @click="closeDropdown"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                >
                  👤 プロフィール
                </NuxtLink>
                <hr class="my-1 border-gray-100" />
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-50"
                >
                  ログアウト
                </button>
              </div>
            </div>
          </div>

          <!-- プルダウン外クリックで閉じる -->
          <div
            v-if="showDropdown"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
          />
        </template>

        <template v-else>
          <NuxtLink to="/login" class="text-sm text-green-500 hover:underline"
            >ログイン</NuxtLink
          >
          <NuxtLink
            to="/register"
            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
          >
            新規登録
          </NuxtLink>
        </template>
      </div>
    </div>
  </header>
</template>
