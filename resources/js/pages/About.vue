<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>

  <Head title="About" />

  <AppLayout>
    <div class="py-12 bg-black min-h-screen text-white">
      <div class="max-w-4xl mx-auto px-6">
        <div class="mb-16">
          <h1 class="text-4xl font-light mb-6">About Ecnelis</h1>
          <p class="text-xl text-white/50 leading-relaxed font-light">
            We are building the interface between human thought and artificial intelligence.
          </p>
        </div>

        <div class="space-y-12 text-white/80 leading-relaxed font-light">
          <section>
            <h2 class="text-2xl font-normal text-white mb-4">Our Mission</h2>
            <p class="mb-4">
              Ecnelis was founded on the belief that AI should be an extension of the mind, not a replacement. We strive
              to create tools that amplify human creativity, streamline workflows, and make advanced technology
              accessible to everyone.
            </p>
          </section>

          <section>
            <h2 class="text-2xl font-normal text-white mb-4">The Platform</h2>
            <p class="mb-4">
              Our platform integrates state-of-the-art AI models into a unified, intuitive workspace. Whether you are
              coding, writing, or designing, Ecnelis provides the intelligence you need, right when you need it.
            </p>
          </section>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
