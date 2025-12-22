<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';
import { ArrowRight } from 'lucide-vue-next';

interface Props {
  content: any;
  canHover: boolean;
}

defineProps<Props>();
</script>

<template>
  <section id="developer" class="relative overflow-hidden"
    :class="[ui.layout.sectionVertical, ui.layout.sectionPadding]">
    <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none" :style="ui.patterns.blueprint">
    </div>

    <div :class="ui.layout.clampWidth" class="relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center mb-32">
        <div class="md:col-span-5">
          <Motion :initial="{ opacity: 0, x: -20 }" :while-in-view="{ opacity: 1, x: 0 }" :viewport="{ once: true }"
            :transition="{ duration: 0.8 }">
            <h2 :class="[ui.typography.display, 'mb-8']">
              {{ content.title }}</h2>
            <p :class="ui.typography.body" class="mb-10 text-lg max-w-sm">
              {{ content.description }}</p>
            <Link href="/explore"
              class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-white/60 transition-colors group"
              :class="{ 'hover:text-white': canHover }">
              {{ content.cta }}
              <ArrowRight class="w-4 h-4 transition-transform" :class="{ 'group-hover:translate-x-1': canHover }" />
            </Link>
          </Motion>
        </div>
        <div class="md:col-span-7">
          <Motion :initial="{ opacity: 0, scale: 0.95 }" :while-in-view="{ opacity: 1, scale: 1 }"
            :viewport="{ once: true }" :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }">
            <div
              class="aspect-[4/3] relative rounded-2xl overflow-hidden border border-white/10 bg-black shadow-2xl shimmer-ecnelis">
              <img src="/images/landing/chat.webp" class="w-full h-full object-cover opacity-80" width="800"
                height="600" loading="lazy" decoding="async" />
            </div>
          </Motion>
        </div>
      </div>

      <div :class="ui.developer.grid">
        <Motion v-for="(card, i) in content.cards" :key="card.id" :initial="{ opacity: 0, y: 20 }"
          :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }" :transition="ui.animations.stagger(i)"
          :class="[card.span, ui.developer.card, 'h-[480px] md:h-[540px]']">

          <div class="flex-col h-full flex relative overflow-hidden">
            <Motion :class="ui.developer.glow" :animate="{
              x: [0, 40, 0],
              y: [0, 60, 0],
              scale: [1, 1.2, 1]
            }" :transition="{ duration: 8, repeat: Infinity, ease: 'easeInOut' }" />

            <div class="p-10 relative z-10">
              <h3 :class="ui.typography.cardTitle" class="mb-3">{{ card.title }}</h3>
              <p :class="ui.typography.cardBody" class="max-w-xs">{{ card.description }}</p>
            </div>

            <div :class="ui.developer.imageWrapper">
              <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.02)_0%,transparent_70%)]">
              </div>

              <Motion :animate="{
                y: [0, -15, 0],
                rotateZ: [0, 1, 0]
              }" :transition="{
                duration: 6,
                repeat: Infinity,
                ease: 'easeInOut',
                delay: i * 0.5
              }" class="w-full h-full max-w-[400px] max-h-[300px]">
                <img :src="card.image" class="w-full h-full object-contain filter brightness-90 shadow-2xl" width="600"
                  height="400" loading="lazy" decoding="async" />
              </Motion>
            </div>
          </div>
        </Motion>
      </div>
    </div>
  </section>
</template>
