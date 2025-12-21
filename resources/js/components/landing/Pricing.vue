<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

interface Props {
  content: any;
  canHover: boolean;
  billingCycle: 'monthly' | 'yearly';
}

defineProps<Props>();
defineEmits(['update:billingCycle']);
</script>

<template>
  <section id="pricing" class="py-section border-b border-white/5 relative group/spotlight"
    :class="ui.layout.sectionPadding">

    <div :class="ui.layout.clampWidth" class="relative z-20">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 md:mb-24 gap-8">
        <div class="max-w-xl">
          <h2 :class="[ui.typography.display, 'text-white mb-6 leading-none']">{{ content.title }}
          </h2>
          <p :class="ui.typography.body">{{ content.description }}</p>
        </div>

        <div
          class="bg-white/5 border border-white/10 rounded-full p-1 flex items-center relative w-full max-w-[280px] sm:max-w-xs mx-auto md:mx-0">
          <div
            class="absolute inset-y-1 w-[calc(50%-4px)] bg-white/10 rounded-full transition-all duration-300 ease-out"
            :class="billingCycle === 'monthly' ? 'left-1' : 'left-[50%] ml-px'"></div>
          <button @click="$emit('update:billingCycle', 'monthly')"
            class="flex-1 relative z-10 px-4 md:px-6 py-2.5 text-[10px] md:text-xs font-bold uppercase tracking-wider transition-colors"
            :class="billingCycle === 'monthly' ? 'text-white' : 'text-white/40'">Monthly</button>
          <button @click="$emit('update:billingCycle', 'yearly')"
            class="flex-1 relative z-10 px-4 md:px-6 py-2.5 text-[10px] md:text-xs font-bold uppercase tracking-wider transition-colors"
            :class="billingCycle === 'yearly' ? 'text-white' : 'text-white/40'">Yearly</button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <Motion v-for="(plan, idx) in content.plans" :key="plan.name" :initial="{ y: 50 + idx * 20, opacity: 0 }"
          :while-in-view="{ y: 0, opacity: 1 }" :transition="ui.animations.stagger(idx)" :viewport="{ once: true }"
          :class="[ui.layout.card.pricing, 'hover-border']" :while-hover="canHover ? ui.animations.hover.card : {}"
          :while-tap="ui.animations.hover.active">

          <div v-if="plan.name.includes('+')"
            class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none opacity-40">
          </div>

          <div class="relative z-10 mb-auto">
            <span :class="ui.typography.pricingPlan">{{ plan.name }}</span>
            <div class="flex items-baseline gap-1 mb-8">
              <span
                :class="ui.typography.pricingPrice">{{ billingCycle === 'yearly' && plan.price !== 'Custom' ? '$' + (parseInt(plan.price.replace('$', '')) * 10) : plan.price }}</span>
              <span v-if="plan.period"
                class="text-white/20 text-sm">{{ billingCycle === 'yearly' ? '/yr' : '/mo' }}</span>
            </div>
            <p :class="ui.typography.cardBody" class="mb-10 min-h-[3em]">{{ plan.description }}</p>

            <ul class="space-y-4 mb-12">
              <li v-for="feat in plan.features" :key="feat" class="flex items-start gap-3 text-[13px] text-white/60">
                <div class="mt-1.5 w-1 h-1 rounded-full bg-white/20"></div>
                {{ feat }}
              </li>
            </ul>
          </div>

          <div class="relative z-10">
            <Link v-if="plan.href" :href="plan.href" :class="ui.layout.pricingButton">
              {{ plan.cta }}
            </Link>
            <button v-else disabled :class="ui.layout.pricingButtonDisabled">
              {{ plan.cta }}
            </button>
          </div>
        </Motion>
      </div>
    </div>
  </section>
</template>
