<script setup lang="ts">
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

interface Props {
  content: any;
  heroImageScale: any;
  headerY: any;
  headerScale: any;
  headerBlur: any;
  headerOpacity: any;
  headerLineHeight: any;
}

defineProps<Props>();
</script>

<template>
  <section class="relative h-dvh flex items-center justify-center group overflow-hidden">
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
      <Motion class="absolute inset-0 will-change-transform"
        :style="{ scale: heroImageScale, filter: headerBlur, opacity: headerOpacity }">
        <div class="absolute inset-0 bg-black/40 z-10 bg-noise mix-blend-overlay"></div>
        <img :src="content.image" alt="Hero background" class="w-full h-full object-cover" width="1920" height="1080"
          decoding="async" />
        <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-black via-black/90 to-transparent z-20">
        </div>
      </Motion>
    </div>

    <div :class="ui.layout.hero" class="relative z-10 flex flex-col items-center justify-center pt-0 pb-0">
      <Motion initial="initial" animate="enter" :variants="{ enter: { transition: { staggerChildren: 0.1 } } }">
        <Motion class="mb-8 md:mb-12" :initial="ui.animations.pageTransition.initial"
          :animate="ui.animations.pageTransition.enter">
          <Motion is="h1" :class="[ui.typography.hero, 'origin-center will-change-transform']"
            :style="{ y: headerY, scale: headerScale, filter: headerBlur, opacity: headerOpacity, lineHeight: headerLineHeight }">
            {{ content.title.line1 }} <span :class="[ui.typography.accentHero, 'mx-2']">{{
              content.title.line2 }}</span> <br class="md:hidden" />
            {{ content.title.line3 }} <span :class="[ui.typography.accentHero, 'mx-2']">{{
              content.title.line4 }}</span>.
          </Motion>
        </Motion>
      </Motion>
    </div>

    <Motion
      class="absolute bottom-12 left-1/2 -translate-x-1/2 flex flex-col items-center gap-6 z-20 pointer-events-none"
      :style="{ opacity: headerOpacity }">
      <span class="text-[10px] uppercase tracking-[0.4em] text-white/20 font-medium">{{ content.cta }}</span>
      <div class="w-5 h-9 rounded-full border border-white/10 flex justify-center p-1.5">
        <div class="w-1 h-1.5 rounded-full bg-white opacity-20 animate-indicator-pulse" />
      </div>
    </Motion>
  </section>
</template>
