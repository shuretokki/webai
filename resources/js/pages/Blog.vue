<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const posts = [
  {
    id: 1,
    title: 'Introducing the new Enterprise Tier',
    excerpt: 'Today we are announcing a major update to our platform with improved security and dedicated infrastructure.',
    date: 'Dec 15, 2025',
    author: 'Sarah Connor',
    category: 'Product'
  },
  {
    id: 2,
    title: 'Optimizing Token Usage',
    excerpt: 'Learn how to get the most out of your token budget with our new optimization strategies and caching techniques.',
    date: 'Dec 12, 2025',
    author: 'John Doe',
    category: 'Engineering'
  },
  {
    id: 3,
    title: 'The Future of AI Agents',
    excerpt: 'Our vision for autonomous agents and how they will transform the way we work and write code.',
    date: 'Dec 10, 2025',
    author: 'Jane Smith',
    category: 'Vision'
  }
];
</script>

<template>
  <AppLayout>

    <Head title="Blog" />

    <div class="min-h-screen bg-background py-24 sm:py-32">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
          <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl font-space">Blog</h1>
          <p class="mt-6 text-lg leading-8 text-muted-foreground">
            Latest updates, news, and insights from the team.
          </p>
        </div>

        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
          <article v-for="post in posts" :key="post.id"
            class="flex flex-col items-start justify-between border border-border bg-card/20 p-6 rounded-none hover:bg-card/40 transition-colors">
            <div class="flex items-center gap-x-4 text-xs">
              <time :datetime="post.date" class="text-muted-foreground">{{ post.date }}</time>
              <span
                class="relative z-10 rounded-full bg-primary/10 px-3 py-1.5 font-medium text-primary hover:bg-primary/20">{{ post.category }}</span>
            </div>
            <div class="group relative">
              <h3
                class="mt-3 text-lg font-semibold leading-6 text-foreground group-hover:text-primary transition-colors font-space">
                <a href="#">
                  <span class="absolute inset-0"></span>
                  {{ post.title }}
                </a>
              </h3>
              <p class="mt-5 line-clamp-3 text-sm leading-6 text-muted-foreground">{{ post.excerpt }}</p>
            </div>
          </article>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
