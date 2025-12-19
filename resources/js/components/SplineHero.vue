<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref } from 'vue';
import { Application } from '@splinetool/runtime';

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isLoading = ref(true);
let splineApp: Application | null = null;

onMounted(async () => {
  if (canvasRef.value) {
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
  <div class="relative w-full h-full overflow-hidden">
    <div v-if="isLoading"
      class="absolute inset-0 bg-black z-10 flex items-center justify-center transition-opacity duration-1000"
      :class="{ 'opacity-0 pointer-events-none': !isLoading }">
      <div class="w-12 h-12 border-2 border-white/10 border-t-white/60 rounded-full animate-spin"></div>
    </div>

    <canvas ref="canvasRef" id="canvas3d" class="w-full h-full object-cover outline-none"></canvas>
  </div>
</template>

<style scoped>
canvas {
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}
</style>
