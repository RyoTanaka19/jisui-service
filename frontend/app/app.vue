<script setup lang="ts">
const route = useRoute();
const isTopPage = computed(() => route.path === '/');
</script>

<template>
  <div>
    <NuxtRouteAnnouncer />
    <AppHeader v-if="!isTopPage" />
    <div :class="isTopPage ? '' : 'pt-16 pb-16'">
      <NuxtPage />
    </div>
    <AppFooter v-if="!isTopPage" />
  </div>
</template>
