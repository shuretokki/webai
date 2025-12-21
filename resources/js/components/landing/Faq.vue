<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion, AnimatePresence } from 'motion-v';
import { ui } from '@/config/ui';
import { ArrowRight } from 'lucide-vue-next';

interface Props {
  content: any;
  canHover: boolean;
  faqOpen: number | null;
}

defineProps<Props>();
defineEmits(['update:faqOpen']);
</script>

<template>
  <section id="faq" class="py-section border-t border-white/5 bg-[#050505]" :class="ui.layout.sectionPadding">
    <div :class="ui.layout.clampWidth">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-12 xl:gap-24">
        <div class="md:col-span-4">
          <Motion :initial="{ opacity: 0, y: 20 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
            class="sticky top-32">
            <h2 :class="[ui.typography.display, 'text-white mb-6 leading-[0.9]']">
              {{ content.title.split(' ')[0] }}<br />
              <span class="text-white/40">{{ content.title.split(' ').slice(1).join(' ') }}</span>
            </h2>
            <Link href="/contact"
              class="inline-flex items-center gap-2 text-sm text-white border-b border-white/20 pb-0.5 hover:border-white transition-colors mt-4">
              Still have questions?
              <ArrowRight class="w-4 h-4 ml-1" />
            </Link>
          </Motion>
        </div>

        <div class="md:col-span-8">
          <div class="border-t border-white/10">
            <div v-for="(item, idx) in content.items" :key="idx" class="border-b border-white/10 overflow-hidden group">
              <button @click="$emit('update:faqOpen', faqOpen === idx ? null : idx)"
                class="w-full py-6 md:py-8 flex items-start justify-between text-left focus:outline-none transition-colors relative active-press"
                :class="{ 'hover:bg-white/[0.02]': canHover }">
                <span :class="[ui.typography.title, 'text-white/80 transition-colors pr-8', { 'group-hover:text-white': canHover }]">
                  {{ item.question }}
                </span>
                <span class="relative flex-shrink-0 w-6 h-6 flex items-center justify-center mt-1">
                  <div class="absolute w-full h-[1px] bg-white transition-transform duration-300"
                    :class="faqOpen === idx ? 'rotate-180' : ''"></div>
                  <div class="absolute w-full h-[1px] bg-white transition-transform duration-300"
                    :class="faqOpen === idx ? 'rotate-180 opacity-0' : 'rotate-90'"></div>
                </span>
              </button>
              <AnimatePresence>
                <Motion v-if="faqOpen === idx" :initial="{ height: 0, opacity: 0, scaleY: 0.8 }"
                  :animate="{ height: 120, opacity: 1, scaleY: 1 }" :exit="{ height: 0, opacity: 0, scaleY: 0.8 }"
                  :transition="{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }" class="overflow-hidden origin-top">
                  <div class="pb-8 text-white/60 leading-relaxed text-sm max-w-md">
                    {{ item.answer }}
                  </div>
                </Motion>
              </AnimatePresence>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
