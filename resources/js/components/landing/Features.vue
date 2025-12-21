<script setup lang="ts">
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';
import { Sparkles, Apple, Monitor } from 'lucide-vue-next';

interface Props {
  content: any;
  canHover: boolean;
}

defineProps<Props>();
</script>

<template>
  <section id="features" :class="[ui.layout.sectionVertical, ui.layout.sectionPadding]">
    <div :class="ui.layout.clampWidth">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <Motion v-for="(feature, idx) in content" :key="feature.id" :initial="{ opacity: 0, y: 20 }"
          :while-in-view="{ opacity: 1, y: 0 }" :transition="ui.animations.stagger(idx)" :viewport="{ once: true }"
          :class="[ui.layout.card.base, 'p-8']">
          <div
            class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 transition-opacity"
            :class="{ 'group-hover:opacity-100': canHover }">
          </div>
          <div class="h-full flex flex-col relative z-10">
            <div>
              <div
                class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center mb-10 transition-transform duration-500"
                :class="{ 'group-hover:scale-110': canHover }">
                <component :is="idx === 0 ? Sparkles : (idx === 1 ? Apple : Monitor)"
                  class="w-6 h-6 text-white/40 transition-colors" :class="{ 'group-hover:text-white': canHover }" />
              </div>
              <h3 :class="ui.typography.cardTitle" class="mb-4">{{ feature.label }}</h3>
              <p :class="ui.typography.cardBody" class="mb-10">
                {{ feature.description }}
              </p>
            </div>
            <div
              class="mt-auto aspect-video bg-black/40 rounded-lg border border-white/5 overflow-hidden relative transition-colors"
              :class="{ 'group-hover:border-white/10': canHover }">
              <div
                class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-transparent to-transparent opacity-50">
              </div>
              <img :src="feature.image"
                class="w-full h-full object-cover grayscale opacity-30 transition-all duration-1000"
                :class="{ 'group-hover:grayscale-0 group-hover:opacity-80': canHover }" width="800" height="450"
                loading="lazy" decoding="async" />
            </div>
          </div>
        </Motion>
      </div>
    </div>
  </section>
</template>
