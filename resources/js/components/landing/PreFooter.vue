<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';
import { ArrowRight } from 'lucide-vue-next';

interface Props {
  content: any;
  canHover: boolean;
  preFooterImageY: any;
  preFooterImageScale: any;
}

defineProps<Props>();
</script>

<template>
  <section
    class="min-h-dvh h-[80dvh] relative flex items-center justify-center overflow-hidden mb-10 mx-6 rounded-sm group">
    <div class="absolute inset-0 bg-black z-10 w-full h-full"></div>
    <Motion class="absolute inset-0 w-full h-full overflow-hidden opacity-60 mix-blend-screen grayscale"
      :while-hover="{ filter: 'grayscale(0%)' }" :transition="{ duration: 2 }">
      <Motion class="w-full h-full" :style="{ y: preFooterImageY, scale: preFooterImageScale }">
        <img :src="content.image" alt="Pre footer bg" class="w-full h-full object-cover object-center" />
      </Motion>
    </Motion>

    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-transparent to-transparent z-20">
    </div>

    <div class="relative z-30 w-[clamp(320px,70%,1400px)] mx-auto px-6 text-center">
      <Motion :initial="{ opacity: 0 }"
        :while-in-view="{ opacity: 1, transition: { staggerChildren: 0.2, delayChildren: 0.1 } }"
        :viewport="{ once: true, margin: '-20%' }">
        <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
          :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }">
          <h1 :class="[ui.typography.hero, 'mb-8 md:mb-12']">{{ content.title }}</h1>
        </Motion>
        <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
          :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }"
          class="flex flex-col items-center gap-6">
          <Link href="/register" :class="ui.layout.button">
            <Motion class="absolute inset-0 bg-white" :while-hover="{ scale: 1.05 }" />
            <Motion class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent"
              :initial="{ x: '-100%' }" :while-hover="canHover ? { x: '100%' } : {}" :transition="{ duration: 0.7 }" />
            <Motion class="absolute inset-0 rounded-lg" :initial="{ boxShadow: '0 0 0px #ffffff00' }"
              :while-hover="canHover ? { boxShadow: '0 0 40px #ffffff66' } : {}" />
            <span class="relative z-10">{{ content.cta }}</span>
            <ArrowRight class="w-5 h-5 relative z-10 transition-transform duration-300"
              :class="{ 'group-hover:translate-x-1': canHover }" />
          </Link>
          <p class="text-white/40 text-xs md:text-sm">{{ content.subtext }}</p>
        </Motion>
      </Motion>
    </div>
  </section>
</template>
