<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

interface Props {
  content: any;
  billingCycle: 'monthly' | 'yearly';
}

defineProps<Props>();
defineEmits(['update:billingCycle']);
</script>

<template>
  <section id="pricing" class="py-section border-b border-white/5 relative bg-black" :class="ui.layout.sectionPadding">
    <div :class="ui.layout.clampWidth" class="relative z-20">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-24 gap-12">
        <div class="max-w-2xl">
          <Motion :initial="{ opacity: 0, x: -20 }" :while-in-view="{ opacity: 1, x: 0 }" :viewport="{ once: true }"
            :transition="{ duration: 0.8 }">
            <span
              class="text-xs font-mono uppercase tracking-[0.3em] text-white/30 mb-6 block">{{ content.label }}</span>
            <h2 :class="[ui.typography.display, 'text-5xl md:text-6xl mb-8 leading-[0.9]']">{{ content.title }}</h2>
            <p :class="ui.typography.body" class="text-lg max-w-lg">{{ content.description }}</p>
          </Motion>
        </div>

        <div :class="ui.pricing.toggleWrapper">
          <Motion :class="ui.pricing.toggleActive"
            :animate="{ x: billingCycle === 'monthly' ? '4px' : 'calc(100% - 4px)' }"
            :transition="{ type: 'spring', stiffness: 300, damping: 30 }" class="left-0" />
          <button @click="$emit('update:billingCycle', 'monthly')"
            class="flex-1 relative z-10 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors"
            :class="billingCycle === 'monthly' ? 'text-white' : 'text-white/30'">Monthly</button>
          <button @click="$emit('update:billingCycle', 'yearly')"
            class="flex-1 relative z-10 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors"
            :class="billingCycle === 'yearly' ? 'text-white' : 'text-white/30'">Yearly</button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="(plan, idx) in content.plans" :key="plan.name" class="relative group">
          <Motion :initial="{ opacity: 0, y: 30 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
            :transition="{ delay: idx * 0.1, duration: 0.8 }"
            :class="idx === 1 ? ui.pricing.premiumCard : ui.pricing.card" class="h-full">
            <Motion v-if="idx === 1"
              class="absolute -top-40 -left-40 w-96 h-96 bg-white/[0.03] rounded-full blur-[100px] pointer-events-none"
              :animate="{
                scale: [1, 1.2, 1],
                opacity: [0.5, 0.8, 0.5]
              }" :transition="{ duration: 10, repeat: Infinity, ease: 'easeInOut' }" />

            <div v-if="idx === 1" :class="ui.pricing.popularBadge">
              Most Popular
            </div>

            <div class="relative z-10 flex flex-col h-full">
              <span :class="ui.typography.pricingPlan">{{ plan.name }}</span>

              <div class="flex items-baseline gap-2 mb-8">
                <span :class="ui.typography.pricingPrice">
                  {{ billingCycle === 'yearly' && plan.price !== 'Custom' ? '$' + (parseInt(plan.price.replace('$', '')) * 10) : plan.price }}
                </span>
                <span v-if="plan.period" class="text-white/20 text-sm font-light">
                  {{ billingCycle === 'yearly' ? '/yr' : '/mo' }}
                </span>
              </div>

              <p :class="ui.typography.cardBody" class="mb-12 text-[14px] leading-relaxed opacity-60">
                {{ plan.description }}
              </p>

              <ul class="space-y-5 mb-12 flex-1">
                <li v-for="feat in plan.features" :key="feat" :class="ui.pricing.featureItem">
                  <div class="w-1 h-1 rounded-full bg-white/40"></div>
                  {{ feat }}
                </li>
              </ul>

              <div class="mt-auto pt-8">
                <Link v-if="plan.href" :href="plan.href" :class="ui.layout.pricingButton">
                  {{ plan.cta }}
                </Link>
                <button v-else disabled :class="ui.layout.pricingButtonDisabled">
                  {{ plan.cta }}
                </button>
              </div>
            </div>
          </Motion>
        </div>
      </div>
    </div>

    <div class="absolute inset-0 z-0 pointer-events-none opacity-20" :style="ui.patterns.blueprint"></div>
  </section>
</template>
