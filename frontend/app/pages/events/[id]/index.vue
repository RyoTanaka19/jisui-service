<script setup lang="ts">
const config = useRuntimeConfig();
const { token, isLoggedIn } = useAuth();
const route = useRoute();
const router = useRouter();
const { setFlash } = useFlash();

const id = computed(() => route.params.id);
const eventData = ref(null);
const currentUser = ref(null);
const joinLoading = ref(false);
const error = ref('');

const fetchEvent = async () => {
  const response: any = await $fetch(
    `${config.public.apiBase}/events/${id.value}`,
    {
      headers: {
        Accept: 'application/json',
        ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
      },
    },
  );
  eventData.value = response;
};

const fetchCurrentUser = async () => {
  if (!token.value) return;
  currentUser.value = await $fetch(`${config.public.apiBase}/me`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
};

onMounted(async () => {
  await fetchEvent();
  await fetchCurrentUser();
});

const handleJoin = async () => {
  if (!isLoggedIn.value) {
    router.push('/login');
    return;
  }
  joinLoading.value = true;
  error.value = '';
  try {
    await $fetch(`${config.public.apiBase}/events/${id.value}/join`, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token.value}`,
      },
    });
    await fetchEvent();
  } catch (e: any) {
    error.value = e?.data?.message || '参加に失敗しました';
  } finally {
    joinLoading.value = false;
  }
};

const handleDelete = async () => {
  if (!confirm('本当に削除しますか？')) return;
  await $fetch(`${config.public.apiBase}/events/${id.value}`, {
    method: 'DELETE',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
  setFlash('イベントを削除しました');
  router.push('/events');
};

const statusLabel = (status: string) => {
  const labels: Record<string, string> = {
    upcoming: '開催予定',
    ongoing: '開催中',
    finished: '終了',
  };
  return labels[status] || status;
};

const statusColor = (status: string) => {
  const colors: Record<string, string> = {
    upcoming: 'bg-blue-100 text-blue-700',
    ongoing: 'bg-green-100 text-green-700',
    finished: 'bg-gray-100 text-gray-500',
  };
  return colors[status] || '';
};

const isAdmin = computed(() => currentUser.value?.is_admin === true);
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow overflow-hidden mb-6">
        <img
          v-if="eventData?.event?.image_url"
          :src="eventData.event.image_url"
          class="w-full h-64 object-cover"
        />
        <div class="p-8">
          <div class="flex items-center gap-2 mb-4">
            <span
              :class="[
                'text-xs font-semibold px-2 py-1 rounded-full',
                statusColor(eventData?.event?.status),
              ]"
            >
              {{ statusLabel(eventData?.event?.status) }}
            </span>
          </div>

          <h1 class="text-2xl font-bold text-gray-800 mb-4">
            {{ eventData?.event?.title }}
          </h1>

          <div class="flex items-center gap-4 text-sm text-gray-400 mb-6">
            <span
              >📅 開始:
              {{
                new Date(eventData?.event?.starts_at).toLocaleString('ja-JP')
              }}</span
            >
          </div>
          <div class="flex items-center gap-4 text-sm text-gray-400 mb-6">
            <span
              >📅 終了:
              {{
                new Date(eventData?.event?.ends_at).toLocaleString('ja-JP')
              }}</span
            >
          </div>

          <p class="text-gray-600 leading-relaxed mb-6">
            {{ eventData?.event?.description }}
          </p>

          <div class="flex items-center gap-4 mb-6">
            <span class="text-sm text-gray-500"
              >参加者数: {{ eventData?.participants_count }}人</span
            >
          </div>

          <!-- エラーメッセージ -->
          <div
            v-if="error"
            class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm"
          >
            {{ error }}
          </div>

          <!-- 参加ボタン -->
          <div class="flex gap-3">
            <button
              v-if="
                !eventData?.joined &&
                eventData?.event?.status !== 'finished' &&
                currentUser?.id !== eventData?.event?.created_by
              "
              @click="handleJoin"
              :disabled="joinLoading"
              class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg transition disabled:opacity-50"
            >
              {{ joinLoading ? '処理中...' : '参加する' }}
            </button>
            <span
              v-if="eventData?.joined"
              class="bg-green-100 text-green-700 font-semibold px-6 py-2 rounded-lg text-sm"
            >
              ✅ 参加済み
            </span>
          </div>

          <!-- 管理者ボタン -->
          <div v-if="isAdmin" class="flex gap-3 mt-4">
            <NuxtLink
              :to="`/events/${id}/edit`"
              class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
            >
              編集
            </NuxtLink>
            <button
              @click="handleDelete"
              class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition text-sm"
            >
              削除
            </button>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <NuxtLink to="/events" class="text-green-500 hover:underline text-sm"
          >← イベント一覧に戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
