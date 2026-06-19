<template>
  <footer
    class="bg-white border-t border-gray-200 fixed bottom-0 left-0 right-0 z-50"
  >
    <div class="max-w-4xl mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <p class="text-sm text-gray-400">
          © 2026 自炊サービス. All rights reserved.
        </p>
        <div class="flex items-center gap-4">
          <NuxtLink
            to="/"
            class="text-sm text-gray-400 hover:text-green-600 transition"
          >
            トップ
          </NuxtLink>
          <NuxtLink
            to="/events"
            class="text-sm text-gray-400 hover:text-green-600 transition"
          >
            イベント
          </NuxtLink>
          <NuxtLink
            to="/profile"
            class="text-sm text-gray-400 hover:text-green-600 transition"
          >
            プロフィール
          </NuxtLink>
        </div>
      </div>
    </div>
  </footer>
</template>
