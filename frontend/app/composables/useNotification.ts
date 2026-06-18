export const useNotification = () => {
  const config = useRuntimeConfig();
  const token = useCookie('auth_token');

  const unreadCount = ref(0);
  const unreadMessageCount = ref(0);

  const fetchUnreadCount = async () => {
    if (!token.value) return;
    try {
      const response: any = await $fetch(
        `${config.public.apiBase}/notifications/unread-count`,
        {
          headers: { Authorization: `Bearer ${token.value}` },
        },
      );
      unreadCount.value = response.count;
    } catch (e) {}
  };

  const fetchUnreadMessageCount = async () => {
    if (!token.value) return;
    try {
      const response: any = await $fetch(
        `${config.public.apiBase}/messages/unread-count`,
        {
          headers: { Authorization: `Bearer ${token.value}` },
        },
      );
      unreadMessageCount.value = response.count;
    } catch (e) {}
  };

  return {
    unreadCount,
    unreadMessageCount,
    fetchUnreadCount,
    fetchUnreadMessageCount,
  };
};
