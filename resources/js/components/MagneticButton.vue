<script setup lang="ts">
import { ref } from 'vue';
import { Motion } from 'motion-v';

const props = defineProps<{
  strength?: number;
}>();

const root = ref<HTMLElement | null>(null);
const x = ref(0);
const y = ref(0);

const handleMouseMove = (e: MouseEvent) => {
  if (!root.value) return;

  const { left, top, width, height } = root.value.getBoundingClientRect();
  const centerX = left + width / 2;
  const centerY = top + height / 2;

  const strength = props.strength || 30;

  const deltaX = e.clientX - centerX;
  const deltaY = e.clientY - centerY;

  x.value = (deltaX / width) * strength;
  y.value = (deltaY / height) * strength;
};

const handleMouseLeave = () => {
  x.value = 0;
  y.value = 0;
};
</script>

<template>
  <Motion ref="root" class="inline-block" @mousemove="handleMouseMove" @mouseleave="handleMouseLeave"
    :animate="{ x: x, y: y }" :transition="{ type: 'spring', stiffness: 150, damping: 15, mass: 0.1 }">
    <slot />
  </Motion>
</template>
