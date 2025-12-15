<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Book, FileText, Code, Terminal } from 'lucide-vue-next';

</script>

<template>
  <AppLayout>

    <Head title="Documentation" />

    <div class="min-h-screen bg-background">
      <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-4xl text-center mb-16">
          <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl font-space">Documentation</h1>
          <p class="mt-6 text-lg leading-8 text-muted-foreground">
            Everything you need to integrate and build with our platform.
          </p>
        </div>

        <div class="text-center py-12">
          <p class="text-muted-foreground">There's no content here yet.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
