<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { Github, Twitter, Youtube, MessageCircle, ArrowUpRight } from 'lucide-vue-next';

const container: any = {
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

const socials = [
  {
    name: 'Discord',
    description: 'Join our active community of 50k+ developers. Get help, share projects, and connect.',
    icon: MessageCircle,
    color: 'hover:border-[#5865F2] hover:text-[#5865F2]',
    link: '#',
    stat: '50k+ Members'
  },
  {
    name: 'Twitter / X',
    description: 'Follow for the latest updates, sneak peeks, and daily AI tips and tricks.',
    icon: Twitter,
    color: 'hover:border-black dark:hover:border-white hover:text-black dark:hover:text-white',
    link: '#',
    stat: '120k Followers'
  },
  {
    name: 'GitHub',
    description: 'Star our repo, contribute to the codebase, map out the future of open source AI.',
    icon: Github,
    color: 'hover:border-zinc-500 hover:text-zinc-500',
    link: '#',
    stat: '15k Stars'
  },
  {
    name: 'YouTube',
    description: 'Watch tutorials, deep dives, and developer interviews on our official channel.',
    icon: Youtube,
    color: 'hover:border-[#FF0000] hover:text-[#FF0000]',
    link: '#',
    stat: '25k Subs'
  }
];
</script>

<template>
  <AppLayout>

    <Head title="Community" />

    <div
      class="min-h-screen bg-white dark:bg-black text-black dark:text-white font-space relative overflow-hidden pt-32 pb-24">
      <!-- Background Noise -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div
          class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] dark:opacity-20">
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <Motion initial="hidden" animate="show" :variants="container" class="text-center mb-24">
          <Motion :variants="item">
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8">
              Join the <br />
              <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-400 to-zinc-900 dark:from-zinc-600 dark:to-white">Movement</span>
            </h1>
          </Motion>
          <Motion :variants="item">
            <p class="text-xl text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
              You're not just using a tool; you're joining a global community of innovators building the future of AI.
            </p>
          </Motion>
        </Motion>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
          <Motion v-for="(social, index) in socials" :key="index" :initial="{ opacity: 0, y: 20 }"
            :animate="{ opacity: 1, y: 0 }" :transition="{ delay: index * 0.1, duration: 0.6 }"
            class="group p-8 border border-black/10 dark:border-white/10 bg-zinc-50 dark:bg-white/5 transition-all duration-300 relative overflow-hidden"
            :class="social.color">
            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
              <ArrowUpRight class="w-6 h-6" />
            </div>

            <div class="mb-6 p-3 w-fit bg-white dark:bg-black border border-black/5 dark:border-white/5">
              <component :is="social.icon" class="w-8 h-8" />
            </div>

            <h3 class="text-2xl font-bold mb-2">{{ social.name }}</h3>
            <p class="text-zinc-500 mb-6 min-h-[48px]">{{ social.description }}</p>

            <div class="flex items-center gap-2 font-mono text-xs uppercase tracking-widest opacity-60">
              <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
              {{ social.stat }}
            </div>
          </Motion>
        </div>

        <!-- Event Section -->
        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
          class="mt-32 border-t border-black/10 dark:border-white/10 pt-24 text-center">
          <h2 class="text-3xl font-bold mb-8">Upcoming Events</h2>
          <div
            class="inline-flex flex-col items-center justify-center p-12 border border-dashed border-black/20 dark:border-white/20 bg-zinc-50/50 dark:bg-zinc-900/50">
            <p class="text-lg font-medium mb-2">Ecnelis CONF 2025</p>
            <p class="text-zinc-500 mb-6">San Francisco • June 15-17</p>
            <Button variant="outline" class="rounded-none border-black/20 dark:border-white/20">Notify Me</Button>
          </div>
        </Motion>

      </div>
    </div>
  </AppLayout>
</template>
