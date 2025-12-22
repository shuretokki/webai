<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import RevealFooter from '@/components/RevealFooter.vue';

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
  <AppLayout>

    <Head title="Blog" />

    <div class="min-h-screen bg-black py-24 sm:py-32">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
          <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl font-space">Blog</h1>
          <p class="mt-6 text-lg leading-8 text-white/50">
            Latest updates, news, and insights from the team.
          </p>
        </div>

        <div class="text-center py-12">
          <p class="text-white/50">There's no content here yet.</p>
        </div>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>
