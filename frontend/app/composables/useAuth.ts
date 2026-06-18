export const useAuth = () => {
  const token = useCookie('auth_token');
  const user = useState('user', () => null);
  const config = useRuntimeConfig();

  const login = async (email: string, password: string) => {
    const data: any = await $fetch(`${config.public.apiBase}/login`, {
      method: 'POST',
      body: { email, password },
    });
    token.value = data.token;
    user.value = data.user;
  };

  const register = async (
    name: string,
    email: string,
    password: string,
    password_confirmation: string,
  ) => {
    const data: any = await $fetch(`${config.public.apiBase}/register`, {
      method: 'POST',
      body: { name, email, password, password_confirmation },
    });
    token.value = data.token;
    user.value = data.user;
  };

  const logout = async () => {
    await $fetch(`${config.public.apiBase}/logout`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
    });
    token.value = null;
    user.value = null;
  };

  const isLoggedIn = computed(() => !!token.value);

  return { token, user, login, register, logout, isLoggedIn };
};
