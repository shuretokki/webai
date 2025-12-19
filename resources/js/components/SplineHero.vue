<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
import { Application } from '@splinetool/runtime';
import { useIntersectionObserver } from '@vueuse/core';

const canvasRef = ref<HTMLCanvasElement | null>(null);
const containerRef = ref<HTMLElement | null>(null);
const isLoading = ref(true);
const isVisible = ref(false);
let splineApp: Application | null = null;

useIntersectionObserver(
  containerRef,
  ([{ isIntersecting }]) => {
    isVisible.value = isIntersecting;
  },
  { threshold: 0 }
);

onMounted(async () => {
  if (canvasRef.value) {
    // Production hardening: Explicitly set high-performance mode
    splineApp = new Application(canvasRef.value);

    try {
      await splineApp.load('https://prod.spline.design/CCJpn9mqbF1JOqmZ/scene.splinecode');
      isLoading.value = false;
    } catch (error) {
      console.error('Spline failed to load:', error);
    }
  }
});

onBeforeUnmount(() => {
  if (splineApp) {
    splineApp = null;
  }
});
</script>

<template>
  <div ref="containerRef" class="relative w-full h-full overflow-hidden bg-black pointer-events-none">
    <img v-if="isLoading" src="/images/heroSection.jpg"
      class="absolute inset-0 w-full h-full object-cover opacity-60 z-0 transition-opacity duration-1000"
      :class="{ 'opacity-0': !isLoading }" aria-hidden="true" />

    <div v-if="isLoading"
      class="absolute inset-0 z-20 flex items-center justify-center transition-opacity duration-1000"
      :class="{ 'opacity-0 pointer-events-none': !isLoading }">
      <div class="w-12 h-12 border-2 border-white/10 border-t-white/60 rounded-full animate-spin"></div>
    </div>

    <canvas v-show="isVisible || isLoading" ref="canvasRef" id="canvas3d"
      class="w-full h-full object-cover outline-none z-10 relative pointer-events-none"></canvas>
  </div>
</template>

<style scoped>
canvas {
  -webkit-tap-highlight-color: transparent;
  user-select: none;
  backface-visibility: hidden;
  transform: translateZ(0);
}
</style>
