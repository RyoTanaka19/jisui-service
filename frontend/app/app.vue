<script setup lang="ts">
const route = useRoute();
const isTopPage = computed(() => route.path === '/');

useHead({
  title: '自炊サービス',
  meta: [
    {
      name: 'description',
      content: '毎日の自炊をもっと楽しく。レシピを共有して料理の輪を広げよう。',
    },
    { property: 'og:title', content: '自炊サービス' },
    {
      property: 'og:description',
      content: '毎日の自炊をもっと楽しく。レシピを共有して料理の輪を広げよう。',
    },
    { property: 'og:type', content: 'website' },
    { property: 'og:url', content: 'https://jisui-service-15eo.vercel.app' },
    {
      property: 'og:image',
      content: 'https://jisui-service-15eo.vercel.app/ogp.png',
    },
    { name: 'twitter:card', content: 'summary_large_image' },
    { name: 'twitter:title', content: '自炊サービス' },
    {
      name: 'twitter:description',
      content: '毎日の自炊をもっと楽しく。レシピを共有して料理の輪を広げよう。',
    },
    {
      name: 'twitter:image',
      content: 'https://jisui-service-15eo.vercel.app/ogp.png',
    },
  ],
});
</script>

<template>
  <div>
    <NuxtRouteAnnouncer />
    <AppHeader v-if="!isTopPage" />
    <FlashMessage />
    <div :class="isTopPage ? '' : 'pt-16 pb-16'">
      <NuxtPage />
    </div>
    <AppFooter v-if="!isTopPage" />
  </div>
</template>
