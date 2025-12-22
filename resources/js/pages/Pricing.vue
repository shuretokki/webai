<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Check } from 'lucide-vue-next';
import { Motion } from 'motion-v';

const plans = [
  {
    name: 'ECNELIS',
    price: '$0',
    period: '/mo',
    description: 'Perfect for getting started with AI',
    features: [
      'Access to standard models',
      '50 messages / month',
      'Community support',
      'Standard processing speed'
    ],
    cta: 'Start Building',
    href: '/explore',
    comingSoon: false,
    highlight: false
  },
  {
    name: 'ECNELIS+',
    price: '$20',
    period: '/mo',
    description: 'For power users who need more available resources.',
    features: [
      'Access to premium models',
      'Unlimited messages',
      'Priority support',
      'Fast processing speed',
      'Early access to new features'
    ],
    cta: 'Join Waitlist',
    href: '#',
    comingSoon: true,
    highlight: true
  },
  {
    name: 'ENTERPRISE',
    price: 'Custom',
    period: '',
    description: 'For organizations with custom needs to scale.',
    features: [
      'Custom model fine-tuning',
      'Dedicated support manager',
      'SLA guarantees',
      'SSO & Advanced security',
      'Usage analytics dashboard'
    ],
    cta: 'Contact Sales',
    href: '/contact',
    comingSoon: true,
    highlight: false
  }
];

const container = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1
    }
  }
};

const item: any = {
  hidden: { opacity: 0, y: 30 },
  show: {
    opacity: 1,
    y: 0,
    transition: {
      duration: 0.8,
      ease: [0.16, 1, 0.3, 1]
    }
  }
};
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-white dark:bg-black font-space py-24 px-4 sm:px-6 lg:px-8 relative overflow-hidden">

      <!-- Background Noise -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div
          class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] dark:opacity-20">
        </div>
        <!-- Animated Tech Lines -->
        <div
          class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-black/10 dark:via-white/10 to-transparent animate-scan-y">
        </div>
        <div
          class="absolute top-0 right-0 w-[1px] h-full bg-gradient-to-b from-transparent via-black/10 dark:via-white/10 to-transparent animate-scan-x delay-1000">
        </div>
        <div
          class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-black/10 dark:via-white/10 to-transparent animate-scan-y delay-500">
        </div>
      </div>

      <Motion initial="hidden" animate="show" :variants="container" class="max-w-7xl mx-auto relative z-10">
        <div class="text-left mb-24">
          <Motion :variants="item">
            <h1 class="text-5xl md:text-6xl font-bold text-black dark:text-white mb-6 tracking-tight">Simple,
              Transparent Pricing</h1>
          </Motion>
          <Motion :variants="item">
            <p class="text-xl text-zinc-600 dark:text-zinc-400 max-w-2xl">
              Choose the plan that's right for you. No hidden fees.
            </p>
          </Motion>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
          <Motion v-for="(plan, index) in plans" :key="plan.name" :variants="item"
            class="relative flex flex-col p-8 lg:p-10 rounded-none border transition-all duration-300 group h-full border-black/10 dark:border-white/10 bg-white dark:bg-black"
            :class="[
              plan.highlight ? 'z-10 bg-zinc-50 dark:bg-white/5' : ''
            ]">

            <!-- Coming Soon Overlay (Permanent) -->
            <div v-if="plan.comingSoon"
              class="absolute inset-0 bg-white/10 dark:bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
              <div
                class="bg-black dark:bg-white text-white dark:text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">
                Coming Soon</div>
              <p class="text-zinc-900 dark:text-zinc-100 font-medium">We're finalizing the details.</p>
            </div>

            <div class="mb-8 relative z-10">
              <h3 class="text-lg font-bold uppercase tracking-widest text-zinc-500 mb-4">{{ plan.name }}</h3>

              <div class="flex items-baseline gap-1">
                <span class="text-5xl font-bold text-black dark:text-white">{{ plan.price }}</span>
                <span v-if="plan.period" class="text-zinc-500">{{ plan.period }}</span>
              </div>
              <p class="mt-6 text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ plan.description }}</p>
            </div>

            <ul class="flex-1 space-y-4 mb-10 relative z-10">
              <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-3">
                <div
                  class="mt-1 w-4 h-4 rounded-full bg-black/5 dark:bg-white/10 flex items-center justify-center shrink-0">
                  <Check class="h-2.5 w-2.5 text-black dark:text-white" />
                </div>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ feature }}</span>
              </li>
            </ul>

            <div class="relative z-10 mt-auto">
              <Link :href="plan.href || '#'" :class="{ 'pointer-events-none': plan.comingSoon }">
                <Button class="w-full h-12 rounded-none font-bold text-sm tracking-wide transition-all"
                  :variant="plan.highlight ? 'default' : 'outline'" :class="[
                    plan.highlight ? 'bg-black text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-200' : 'border-black/20 dark:border-white/20 hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black'
                  ]" :disabled="plan.comingSoon">
                  {{ plan.cta }}
                </Button>
              </Link>
            </div>
          </Motion>
        </div>
      </Motion>
    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes scan-y {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }

  50% {
    opacity: 1;
  }

  100% {
    transform: translateY(100vh);
    opacity: 0;
  }
}

@keyframes scan-x {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }

  50% {
    opacity: 1;
  }

  100% {
    transform: translateX(100vw);
    opacity: 0;
  }
}

.animate-scan-y {
  animation: scan-y 8s linear infinite;
}

.animate-scan-x {
  animation: scan-x 12s linear infinite;
}
</style>
