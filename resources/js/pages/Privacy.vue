<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>

  <Head title="Privacy Policy" />

  <AppLayout>
    <div class="py-12 bg-black min-h-screen text-white">
      <div class="max-w-4xl mx-auto px-6">
        <div class="mb-16">
          <h1 class="text-4xl font-light mb-6">Privacy Policy</h1>
          <p class="text-white/50">Last updated: December 16, 2025</p>
        </div>

        <div class="space-y-8 text-white/70 leading-relaxed text-sm">
          <section>
            <h2 class="text-xl font-medium text-white mb-4">1. Information We Collect</h2>
            <p>We collect information you provide directly to us, such as when you create an account, subscribe to our
              newsletter, or contact us for support.</p>
          </section>

          <section>
            <h2 class="text-xl font-medium text-white mb-4">2. How We Use Your Information</h2>
            <p>We use the information we collect to provide, maintain, and improve our services, to develop new ones,
              and to protect Ecnelis and our users.</p>
          </section>

          <section>
            <h2 class="text-xl font-medium text-white mb-4">3. Data Security</h2>
            <p>We use appropriate technical and organizational measures to protect the personal information that we
              collect and process about you. The measures we use are designed to provide a level of security appropriate
              to the risk of processing your personal information.</p>
          </section>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
