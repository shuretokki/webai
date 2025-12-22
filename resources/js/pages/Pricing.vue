<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Pricing from '@/components/landing/Pricing.vue';
import RevealFooter from '@/components/RevealFooter.vue';
import { ref } from 'vue';

const billingCycle = ref<'monthly' | 'yearly'>('monthly');

const content = {
  appName: 'Ecnelis',
  footer: {
    links: {
      index: [
        { label: 'Works', href: '/works' },
        { label: 'Enterprise', href: '/enterprise' },
        { label: 'Docs', href: '/docs' },
      ],
      social: [
        { label: 'Twitter', href: '#' },
        { label: 'GitHub', href: '#' },
        { label: 'Discord', href: '#' },
      ]
    }
  },
  label: 'Plans',
  title: 'Simple, Transparent Pricing',
  description: "Choose the plan that fits your needs. No surprises.",
  plans: [
    {
      name: 'Free',
      price: '$0',
      period: '/mo',
      description: 'Perfect for trying out AI',
      features: ['Basic AI models', '50 messages per month', 'Community help'],
      cta: 'Start Free',
      href: '/explore',
      comingSoon: false
    },
    {
      name: 'Premium',
      price: '$20',
      period: '/mo',
      description: 'For people who use AI regularly',
      features: ['Advanced AI models', 'Unlimited messages', 'Priority support'],
      cta: 'Join Waitlist',
      comingSoon: true
    },
    {
      name: 'Business',
      price: 'Custom',
      period: '',
      description: 'For teams and companies',
      features: ['Custom AI training', 'Dedicated support', 'Security guarantees'],
      cta: 'Contact Sales',
      href: '/contact',
      comingSoon: true
    }
  ]
};
</script>

<template>
  <AppLayout>
    <div class="bg-black min-h-screen pt-20">
      <Pricing
        v-model:billing-cycle="billingCycle"
        :content="content"
      />

      <div class="h-[10dvh] bg-black relative z-20"></div>
      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>

<style scoped>
</style>
