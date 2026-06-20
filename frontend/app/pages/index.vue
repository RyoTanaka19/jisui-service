<script setup lang="ts">
const { isLoggedIn } = useAuth();
</script>

<template>
  <div
    class="min-h-screen bg-gradient-to-b from-green-50 to-white flex flex-col"
  >
    <!-- ヘッダーなし・フッターなし -->
    <div class="flex-1 flex flex-col items-center justify-center px-4 py-16">
      <!-- ロゴ -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-green-600 mb-4">🍳 みんなと自炊</h1>
        <p class="text-xl text-gray-500 mb-2">毎日の自炊をもっと楽しく</p>
        <p class="text-gray-400">レシピを共有して、料理の輪を広げよう</p>
      </div>

      <!-- ボタン -->
      <div class="flex flex-col items-center gap-4 w-full max-w-xs mb-12">
        <template v-if="isLoggedIn">
          <NuxtLink
            to="/posts"
            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition text-center text-lg"
          >
            投稿一覧を見る
          </NuxtLink>
        </template>
        <template v-else>
          <NuxtLink
            to="/login"
            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition text-center text-lg"
          >
            ログイン
          </NuxtLink>
          <NuxtLink
            to="/register"
            class="w-full bg-white hover:bg-gray-50 text-green-600 font-semibold py-3 rounded-xl transition text-center text-lg border-2 border-green-500"
          >
            新規登録
          </NuxtLink>
          <NuxtLink
            to="/posts"
            class="text-sm text-gray-400 hover:text-gray-600 transition"
          >
            ログインせずに投稿一覧を見る →
          </NuxtLink>
        </template>
      </div>
    </div>
  </div>
</template>
