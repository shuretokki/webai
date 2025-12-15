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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Quick Start -->
          <div
            class="p-8 border border-border bg-card/30 hover:bg-card/50 transition-colors rounded-none group cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
              <div class="p-3 bg-primary/10 rounded-sm">
                <Zap class="w-6 h-6 text-primary" />
              </div>
              <h3 class="text-xl font-semibold font-space text-foreground">Quick Start</h3>
            </div>
            <p class="text-muted-foreground mb-4">Get up and running with the API in less than 5 minutes.</p>
            <span class="text-primary font-medium group-hover:underline">Read Guide &rarr;</span>
          </div>

          <!-- API Reference -->
          <div
            class="p-8 border border-border bg-card/30 hover:bg-card/50 transition-colors rounded-none group cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
              <div class="p-3 bg-blue-500/10 rounded-sm">
                <Terminal class="w-6 h-6 text-blue-500" />
              </div>
              <h3 class="text-xl font-semibold font-space text-foreground">API Reference</h3>
            </div>
            <p class="text-muted-foreground mb-4">Detailed endpoints, parameters, and response formats.</p>
            <span class="text-blue-500 font-medium group-hover:underline">View Reference &rarr;</span>
          </div>

          <!-- SDKs -->
          <div
            class="p-8 border border-border bg-card/30 hover:bg-card/50 transition-colors rounded-none group cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
              <div class="p-3 bg-green-500/10 rounded-sm">
                <Code class="w-6 h-6 text-green-500" />
              </div>
              <h3 class="text-xl font-semibold font-space text-foreground">SDKs & Libraries</h3>
            </div>
            <p class="text-muted-foreground mb-4">Official client libraries for Python, Node.js, and Go.</p>
            <span class="text-green-500 font-medium group-hover:underline">Download SDKs &rarr;</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
