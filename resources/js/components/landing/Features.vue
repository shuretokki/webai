<script setup lang="ts">
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

interface Props {
  content: {
    label: string;
    brands: Array<{ id: string; name: string; icon?: string }>;
  };
}

defineProps<Props>();
</script>

<template>
  <section id="features" :class="ui.features.section">
    <div :class="ui.layout.clampWidth" class="mb-16 px-6">
      <Motion :initial="{ opacity: 0, y: 20 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
        class="flex flex-col items-center text-center">
        <span class="text-xs font-mono uppercase tracking-[0.3em] text-white/30 mb-4">{{ content.label }}</span>
        <h2 :class="[ui.typography.display, 'text-3xl md:text-4xl capitalize']">Empowering the world's most innovative
          teams</h2>
      </Motion>
    </div>

    <div class="space-y-8 md:space-y-12">
      <!-- Row 1 -->
      <div class="relative flex overflow-hidden">
        <Motion :class="ui.features.track" :animate="{ x: ['0%', '-100%'] }"
          :transition="{ duration: 40, ease: 'linear', repeat: Infinity }">
          <div v-for="(brand, idx) in content.brands" :key="`r1-1-${idx}`" class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
          <div v-for="(brand, idx) in content.brands" :key="`r1-2-${idx}`" class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
        </Motion>
      </div>

      <!-- Row 2 -->
      <div class="relative flex overflow-hidden">
        <Motion :class="ui.features.track" :animate="{ x: ['-100%', '0%'] }"
          :transition="{ duration: 50, ease: 'linear', repeat: Infinity }">
          <div v-for="(brand, idx) in [...content.brands].reverse()" :key="`r2-1-${idx}`"
            class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
          <div v-for="(brand, idx) in [...content.brands].reverse()" :key="`r2-2-${idx}`"
            class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
        </Motion>
      </div>

      <!-- Row 3 -->
      <div class="relative flex overflow-hidden">
        <Motion :class="ui.features.track" :animate="{ x: ['0%', '-100%'] }"
          :transition="{ duration: 35, ease: 'linear', repeat: Infinity }">
          <div v-for="(brand, idx) in content.brands" :key="`r3-1-${idx}`" class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
          <div v-for="(brand, idx) in content.brands" :key="`r3-2-${idx}`" class="flex items-center gap-4 group">
            <div :class="ui.features.brandIcon">
              <img v-if="brand.icon" :src="brand.icon" :alt="brand.name" class="w-5 h-5 object-contain" />
              <span v-else
                class="text-white/20 group-hover:text-white/60 font-bold text-lg">{{ brand.name.charAt(0) }}</span>
            </div>
            <span :class="ui.features.brandName">{{ brand.name }}</span>
          </div>
        </Motion>
      </div>
    </div>
  </section>
</template>
