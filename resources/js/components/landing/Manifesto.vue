<script setup lang="ts">
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

interface Props {
  content: any;
  isMobile: boolean;
}

defineProps<Props>();
</script>

<template>
  <section class="py-48 md:py-64 relative overflow-hidden" :class="ui.layout.sectionPadding">
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
      <div class="w-[600px] h-[600px] bg-white/5 rounded-full animate-pulse transition-all duration-1000"
        :class="isMobile ? 'blur-[60px] opacity-20' : 'blur-[120px] opacity-100'">
      </div>
    </div>

    <div :class="ui.layout.sectionContainer" class="relative z-10 flex justify-center">
      <Motion initial="initial" :while-in-view="'enter'" :viewport="{ once: true, margin: '-20%' }" class="max-w-6xl">

        <Motion v-if="isMobile" :initial="{ opacity: 0, y: 30 }" :while-in-view="{ opacity: 1, y: 0 }"
          :transition="{ duration: 1, ease: ui.animations.easing.default }" :class="ui.typography.manifesto">
          {{ content.title }}
        </Motion>

        <h2 v-else :class="[ui.typography.manifesto]">
          <template v-for="(word, i) in content.title.split(' ')" :key="i">
            <Motion :variants="ui.animations.wordReveal(i)" class="inline-block"
              :class="{ 'opacity-30': ['invisible', 'power'].includes(word.toLowerCase().replace(/[.,]/g, '')) }">
              {{ word }}&nbsp;
            </Motion>
          </template>
        </h2>
      </Motion>
    </div>
  </section>
</template>
