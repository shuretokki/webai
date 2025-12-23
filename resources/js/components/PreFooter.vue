<script setup lang="ts">
import { Motion } from 'motion-v';

const variants = {
  background: {
    initial: { y: '100%', opacity: 0, scale: 0.9 },
    enter: { y: '50%', opacity: 1, scale: 1 }
  },
  line: {
    initial: { scaleY: 0, opacity: 0 },
    enter: { scaleY: 1, opacity: 1 }
  }
} as const;

const transition = {
  background: {
    duration: 2,
    ease: [0.16, 1, 0.3, 1],
    clamp: true
  },
  line: {
    duration: 1.5,
    delay: 0.5,
    clamp: true
  }
} as const;
</script>

<template>
  <section class="relative h-[60dvh] bg-black overflow-hidden flex items-center justify-center pb-[env(safe-area-inset-bottom)]">
    <!-- The "50% up, 50% cut out" background element -->
    <Motion
      :initial="variants.background.initial"
      :while-in-view="variants.background.enter"
      :viewport="{ once: true, margin: '-10%' }"
      :transition="transition.background"
      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[150vw] aspect-square bg-white/[0.02] rounded-full border border-white/10"
    />

    <!-- Subtle glow -->
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-full bg-[radial-gradient(circle_at_50%_100%,#ffffff08,transparent_70%)] pointer-events-none"></div>

    <!-- Minimalist center line -->
    <Motion
      :initial="variants.line.initial"
      :while-in-view="variants.line.enter"
      :viewport="{ once: true }"
      :transition="transition.line"
      class="relative z-10 w-px h-32 bg-gradient-to-b from-transparent via-white/20 to-transparent origin-bottom"
    />
  </section>
</template>