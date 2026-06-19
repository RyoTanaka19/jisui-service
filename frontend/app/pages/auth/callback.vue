<script setup lang="ts">
const route = useRoute();
const router = useRouter();
const token = useCookie('auth_token');
const { setFlash } = useFlash();

onMounted(() => {
  const tokenParam = route.query.token as string;
  const nameParam = route.query.name as string;
  const error = route.query.error;

  if (error) {
    router.push('/login?error=google_auth_failed');
    return;
  }

  if (tokenParam) {
    token.value = tokenParam;
    setFlash('Googleログインしました！');
    router.push('/posts');
  } else {
    router.push('/login');
  }
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center">
      <p class="text-gray-500">ログイン中...</p>
    </div>
  </div>
</template>
