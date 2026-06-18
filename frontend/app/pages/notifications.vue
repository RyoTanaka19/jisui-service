<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const config = useRuntimeConfig();
const token = useCookie('auth_token');
const { fetchUnreadCount } = useNotification();

const notifications = ref(null);
const loading = ref(false);

const fetchNotifications = async () => {
  notifications.value = await $fetch(`${config.public.apiBase}/notifications`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
};

onMounted(async () => {
  await fetchNotifications();
});

const markAsRead = async (notification: any) => {
  if (notification.is_read) return;
  await $fetch(
    `${config.public.apiBase}/notifications/${notification.id}/read`,
    {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
    },
  );
  notification.is_read = true;
  await fetchUnreadCount();
};

const markAllAsRead = async () => {
  loading.value = true;
  await $fetch(`${config.public.apiBase}/notifications/mark-all`, {
    method: 'POST',
    headers: { Authorization: `Bearer ${token.value}` },
  });
  await fetchNotifications();
  await fetchUnreadCount();
  loading.value = false;
};

const notificationIcon = (type: string) => {
  const icons: Record<string, string> = {
    event_started: '🎉',
    event_finished: '✅',
    admin_granted: '⭐',
    admin_revoked: '❌',
    admin_request: '📨',
    message: '✉️',
  };
  return icons[type] || '🔔';
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
      </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">通知</h1>
        <button
          @click="markAllAsRead"
          :disabled="loading"
          class="text-sm text-green-500 hover:underline disabled:opacity-50"
        >
          全て既読にする
        </button>
      </div>

      <div
        v-if="!notifications?.data?.length"
        class="text-center text-gray-400 py-12"
      >
        通知がありません
      </div>
    </main>
  </div>
</template>
