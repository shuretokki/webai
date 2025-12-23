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
    <div class="absolute bottom-0 left-0 right-0 w-full h-full overflow-hidden pointer-events-none opacity-100">
      <img
        src="/images/preFooter.webp"
        alt=""
        class="w-full h-full object-cover object-bottom"
        width="1920"
        height="1080"
        loading="lazy"
        decoding="async"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/10 to-transparent"></div>
    </div>

    <Motion
      :initial="variants.background.initial"
      :while-in-view="variants.background.enter"
      :viewport="{ once: true, margin: '-10%' }"
      :transition="transition.background"
      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[150vw] aspect-square bg-white/[0.02] rounded-full border border-white/10"
    />

    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none"></div>
  </section>
</template>