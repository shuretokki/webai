<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>

  <Head title="Terms of Service">
    <meta name="description"
      content="Read our Terms of Service to understand your rights and responsibilities when using Ecnelis AI platform." />
  </Head>

  <AppLayout>
    <div id="terms-container" class="py-12 bg-black min-h-screen text-white">
      <div class="max-w-4xl mx-auto px-6">
        <div class="mb-16">
          <h1 id="terms-title" class="text-4xl font-light mb-6">Terms of Service</h1>
          <p id="terms-last-updated" class="text-white/50">Last updated: December 16, 2025</p>
        </div>

        <div class="space-y-8 text-white/70 leading-relaxed text-sm">
          <section id="acceptance-of-terms">
            <h2 class="text-xl font-medium text-white mb-4">1. Acceptance of Terms</h2>
            <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this
              agreement.</p>
          </section>

          <section id="use-license">
            <h2 class="text-xl font-medium text-white mb-4">2. Use License</h2>
            <p>Permission is granted to temporarily download one copy of the materials (information or software) on
              Ecnelis's website for personal, non-commercial transitory viewing only.</p>
          </section>

          <section id="disclaimer">
            <h2 class="text-xl font-medium text-white mb-4">3. Disclaimer</h2>
            <p>The materials on Ecnelis's website are provided "as is". Ecnelis makes no warranties, expressed or
              implied, and hereby disclaims and negates all other warranties, including without limitation, implied
              warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of
              intellectual property or other violation of rights.</p>
          </section>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
