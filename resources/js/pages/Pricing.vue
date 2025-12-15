<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Check, X } from 'lucide-vue-next';
import { ref } from 'vue';

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
    cta: 'Active Plan',
    current: true,
    disabled: true,
    highlight: false
  },
  {
    name: 'ECNELIS+',
    price: '$20',
    period: '/mo',
    description: 'For power users who need more',
    features: [
      'Access to premium models',
      'Unlimited messages',
      'Priority support',
      'Fast processing speed',
      'Early access to new features'
    ],
    cta: 'Join Waitlist',
    current: false,
    disabled: false,
    highlight: true
  },
  {
    name: 'ENTERPRISE',
    price: 'Custom',
    period: '',
    description: 'For organizations with custom needs',
    features: [
      'Custom model fine-tuning',
      'Dedicated support manager',
      'SLA guarantees',
      'SSO & Advanced security',
      'Usage analytics dashboard'
    ],
    cta: 'Contact Sales',
    current: false,
    disabled: false,
    highlight: false
  }
];

const showWaitlistToast = ref(false);
const toastMessage = ref('');

/**
 * TODO: Change into email verified subscribe.
 * @param plan
 */
const handlePlanClick = (plan: any) => {
  if (plan.current) return;

  toastMessage.value = `You've been added to the waitlist for ${plan.name}!`;
  showWaitlistToast.value = true;
  setTimeout(() => {
    showWaitlistToast.value = false;
  }, 3000);
};

</script>

<template>
  <AppLayout>
    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="text-center mb-16">
        <h1 class="text-4xl font-space font-bold text-foreground mb-4">Simple, Transparent Pricing</h1>
        <p class="text-xl text-muted-foreground font-space max-w-2xl mx-auto">
          Choose the plan that's right for you. No hidden fees.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="plan in plans" :key="plan.name"
          class="relative flex flex-col p-8 rounded-none border transition-all duration-300 bg-background/50 backdrop-blur-sm"
          :class="[
            plan.highlight ? 'border-primary ring-1 ring-primary shadow-lg shadow-primary/10' : 'border-white/10 hover:border-white/20'
          ]">

          <div v-if="plan.highlight"
            class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-primary-foreground text-xs font-bold uppercase tracking-wider rounded-full">
            Popular
          </div>

          <div class="mb-8">
            <h3 class="text-lg font-space font-medium text-foreground mb-2">{{ plan.name }}</h3>
            <div class="flex items-baseline gap-1">
              <span class="text-4xl font-space font-bold text-foreground">{{ plan.price }}</span>
              <span class="text-muted-foreground font-space">{{ plan.period }}</span>
            </div>
            <p class="mt-4 text-sm text-muted-foreground font-space">{{ plan.description }}</p>
          </div>

          <ul class="flex-1 space-y-4 mb-8">
            <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-3">
              <Check class="h-5 w-5 text-primary shrink-0" />
              <span class="text-sm text-foreground/80 font-space">{{ feature }}</span>
            </li>
          </ul>

          <Button class="w-full rounded-none font-space font-medium transition-all"
            :variant="plan.highlight ? 'default' : 'outline'" :disabled="plan.current" @click="handlePlanClick(plan)">
            {{ plan.cta }}
          </Button>
        </div>
      </div>

      <Transition enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 translate-y-2" enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200" leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 translate-y-2">
        <div v-if="showWaitlistToast"
          class="fixed bottom-8 right-8 z-50 bg-primary text-primary-foreground px-6 py-4 rounded shadow-xl flex items-center gap-3">
          <Check class="h-5 w-5" />
          <span class="font-space font-medium">{{ toastMessage }}</span>
        </div>
      </Transition>
    </div>
  </AppLayout>
</template>
