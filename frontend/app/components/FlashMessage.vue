<script setup lang="ts">
const { flash } = useFlash();
</script>

<template>
  <Transition name="flash">
    <div
      v-if="flash"
      :class="[
        'fixed top-20 left-1/2 -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-semibold text-sm',
        flash.type === 'success' ? 'bg-green-500' : 'bg-red-500',
      ]"
    >
      {{ flash.message }}
    </div>
  </Transition>
</template>

<style scoped>
.flash-enter-active,
.flash-leave-active {
  transition: all 0.3s ease;
}
.flash-enter-from,
.flash-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-20px);
}
</style>
