<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
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

        <div class="text-center py-12">
          <p class="text-muted-foreground">There's no content here yet.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
