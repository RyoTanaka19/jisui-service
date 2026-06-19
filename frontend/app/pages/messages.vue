<script setup lang="ts">
definePageMeta({ middleware: 'auth' });

const config = useRuntimeConfig();
const token = useCookie('auth_token');
const { fetchUnreadMessageCount } = useNotification();

const messages = ref(null);
const selectedMessage = ref(null);

const fetchMessages = async () => {
  messages.value = await $fetch(`${config.public.apiBase}/messages`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token.value}`,
    },
  });
};

onMounted(async () => {
  await fetchMessages();
});

const selectMessage = async (message: any) => {
  selectedMessage.value = message;
  if (!message.is_read) {
    await $fetch(`${config.public.apiBase}/messages/${message.id}/read`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
    });
    message.is_read = true;
    await fetchUnreadMessageCount();
  }
};

const messageTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    admin_request: '管理者申請',
    general: '一般',
  };
  return labels[type] || type;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">メッセージボックス</h1>

      <div class="grid grid-cols-3 gap-4">
        <!-- メッセージ一覧 -->
        <div class="col-span-1 bg-white rounded-2xl shadow overflow-hidden">
          <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">受信メッセージ</h2>
          </div>
          <div
            v-if="!messages?.data?.length"
            class="text-center text-gray-400 py-8 text-sm"
          >
            メッセージがありません
          </div>
          <div
            v-for="message in messages?.data"
            :key="message.id"
            @click="selectMessage(message)"
            :class="[
              'p-4 cursor-pointer hover:bg-gray-50 border-b border-gray-100 transition',
              selectedMessage?.id === message.id ? 'bg-green-50' : '',
              !message.is_read ? 'font-semibold' : '',
            ]"
          >
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs text-gray-500">{{
                message.sender?.name
              }}</span>
              <span
                :class="[
                  'text-xs px-2 py-0.5 rounded-full',
                  message.type === 'admin_request'
                    ? 'bg-purple-100 text-purple-600'
                    : 'bg-gray-100 text-gray-500',
                ]"
              >
                {{ messageTypeLabel(message.type) }}
              </span>
            </div>
            <p class="text-sm text-gray-800 truncate">{{ message.subject }}</p>
            <p class="text-xs text-gray-400 mt-1">
              {{ new Date(message.created_at).toLocaleDateString('ja-JP') }}
            </p>
            <span v-if="!message.is_read" class="text-xs text-green-500"
              >未読</span
            >
          </div>
        </div>

        <!-- メッセージ詳細 -->
        <div class="col-span-2 bg-white rounded-2xl shadow p-6">
          <div v-if="!selectedMessage" class="text-center text-gray-400 py-12">
            メッセージを選択してください
          </div>
          <div v-else>
            <div class="mb-4 pb-4 border-b border-gray-100">
              <h2 class="text-lg font-bold text-gray-800 mb-2">
                {{ selectedMessage.subject }}
              </h2>
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>送信者: {{ selectedMessage.sender?.name }}</span>
                <span>{{
                  new Date(selectedMessage.created_at).toLocaleString('ja-JP')
                }}</span>
              </div>
            </div>
            <p class="text-gray-600 leading-relaxed">
              {{ selectedMessage.body }}
            </p>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <NuxtLink to="/" class="text-green-500 hover:underline text-sm"
          >← トップに戻る</NuxtLink
        >
      </div>
    </main>
  </div>
</template>
