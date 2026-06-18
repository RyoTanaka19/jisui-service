export const useApi = () => {
  const config = useRuntimeConfig();
  const token = useCookie('auth_token');

  const api = async (endpoint: string, options: any = {}) => {
    return await $fetch(`${config.public.apiBase}${endpoint}`, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
        ...options.headers,
      },
    });
  };

  return { api };
};
