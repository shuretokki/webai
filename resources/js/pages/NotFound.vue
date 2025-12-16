<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
</script>

<template>

  <Head title="Page Not Found" />

  <AppLayout>
    <div class="min-h-screen bg-black flex items-center justify-center px-4">
      <div class="text-center">
        <h1 class="text-9xl font-bold text-white/10 mb-8">404</h1>
        <h2 class="text-3xl font-light text-white mb-4">Page Not Found</h2>
        <p class="text-white/50 mb-12 max-w-md mx-auto">
          The page you are looking for doesn't exist or has been moved.
        </p>

        <Link href="/"
          class="inline-flex items-center gap-2 px-6 py-3 bg-white text-black font-medium hover:bg-white/90 transition-colors rounded-sm">
        <ArrowLeft class="w-4 h-4" />
        Back to Home
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
