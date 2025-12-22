<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import RevealFooter from '@/components/RevealFooter.vue';

const changes = [
  {
    version: 'v1.1.0',
    date: 'December 23, 2025',
    title: 'UI Refinement & UX Polish',
    items: [
      'Redesigned Settings modal with a minimalist dark aesthetic and Inter typography.',
      'Fixed mobile horizontal scrolling issues on the Explore page hero section.',
      'Implemented scrollbar-gutter to prevent layout shifting during modal transitions.',
      'Synchronized standalone Pricing page with the landing page design system.',
      'Integrated the RevealFooter across all marketing, legal, and documentation pages.',
      'Optimized mobile navigation and touch targets for better accessibility.',
    ]
  },
  {
    version: 'v1.0.0',
    date: 'December 16, 2025',
    title: 'Initial Release',
    items: [
      'Launched Ecnelis AI platform.',
      'Added Chat, Explore, and Settings pages.',
      'Integrated multiple AI models.',
    ]
  }
];

const content = {
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
  }
};
</script>

<template>

  <Head title="Changelog" />

  <AppLayout>
    <div class="py-12 bg-black min-h-screen text-white">
      <div class="max-w-4xl mx-auto px-6">
        <div class="mb-16">
          <h1 class="text-4xl font-light mb-4">Changelog</h1>
          <p class="text-white/50 text-lg">Stay updated with the latest improvements and features.</p>
        </div>

        <div class="space-y-12">
          <div v-for="release in changes" :key="release.version"
            class="relative pl-8 border-l border-white/10 pb-12 last:pb-0">
            <div class="absolute -left-1.5 top-2 w-3 h-3 bg-white rounded-full border-4 border-black"></div>

            <div class="mb-6">
              <div class="flex items-center gap-3 mb-2">
                <span class="text-xl font-medium">{{ release.version }}</span>
                <span class="text-xs px-2 py-0.5 bg-white/10 rounded text-white/60">{{ release.date }}</span>
              </div>
              <h3 class="text-lg text-white/80">{{ release.title }}</h3>
            </div>

            <ul class="space-y-3">
              <li v-for="item in release.items" :key="item" class="flex items-start gap-3 text-white/60 text-sm">
                <span class="block w-1.5 h-1.5 mt-1.5 bg-white/20 rounded-full"></span>
                {{ item }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>
