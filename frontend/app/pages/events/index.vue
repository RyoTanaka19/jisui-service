<script setup lang="ts">
const config = useRuntimeConfig();
const { isLoggedIn, token } = useAuth();
const { token: authToken } = useAuth();

const events = ref(null);
const currentStatus = ref('');

const fetchEvents = async (status = '') => {
  try {
    const params = status ? `?status=${status}` : '';
    const response = await $fetch(`${config.public.apiBase}/events${params}`, {
      headers: { Accept: 'application/json' },
    });
    events.value = response;
    currentStatus.value = status;
  } catch (e) {
    console.error('API接続エラー', e);
  }
};

onMounted(async () => {
  await fetchEvents();
});

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
        <NuxtLink
          v-if="isLoggedIn"
          to="/events/create"
          class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
        >
          ＋ イベント作成
        </NuxtLink>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">イベント一覧</h2>

      <!-- フィルタータブ -->
      <div class="flex gap-2 mb-6">
        <button
          @click="fetchEvents()"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition',
            currentStatus === ''
              ? 'bg-green-500 text-white'
              : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50',
          ]"
        >
          すべて
        </button>
        <button
          @click="fetchEvents('upcoming')"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition',
            currentStatus === 'upcoming'
              ? 'bg-blue-500 text-white'
              : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50',
          ]"
        >
          開催予定
        </button>
        <button
          @click="fetchEvents('ongoing')"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition',
            currentStatus === 'ongoing'
              ? 'bg-green-500 text-white'
              : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50',
          ]"
        >
          開催中
        </button>
        <button
          @click="fetchEvents('finished')"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition',
            currentStatus === 'finished'
              ? 'bg-gray-500 text-white'
              : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50',
          ]"
        >
          終了済み
        </button>
      </div>

      <div v-if="!events?.data?.length" class="text-center text-gray-400 py-12">
        イベントがありません
      </div>

      <div class="grid gap-4">
        <NuxtLink
          v-for="event in events?.data"
          :key="event.id"
          :to="`/events/${event.id}`"
          class="bg-white rounded-xl shadow-sm hover:shadow-md transition block overflow-hidden"
        >
          <img
            v-if="event.image_url"
            :src="event.image_url"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <div class="flex items-center gap-2 mb-2">
              <span
                :class="[
                  'text-xs font-semibold px-2 py-1 rounded-full',
                  statusColor(event.status),
                ]"
              >
                {{ statusLabel(event.status) }}
              </span>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
              {{ event.title }}
            </h3>
            <p class="text-gray-500 text-sm mb-3 line-clamp-2">
              {{ event.description }}
            </p>
            <div class="flex items-center gap-4 text-xs text-gray-400">
              <span
                >📅
                {{
                  new Date(event.starts_at).toLocaleDateString('ja-JP')
                }}</span
              >
              <span
                >🕐
                {{
                  new Date(event.starts_at).toLocaleTimeString('ja-JP', {
                    hour: '2-digit',
                    minute: '2-digit',
                  })
                }}</span
              >
              <span>👤 {{ event.creator?.name }}</span>
            </div>
          </div>
        </NuxtLink>
      </div>
    </main>
  </div>
</template>
