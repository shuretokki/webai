<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
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

  <Head title="Terms of Service">
    <meta name="description"
      content="Read our Terms of Service to understand your rights and responsibilities when using Ecnelis AI platform." />
  </Head>

  <AppLayout>
    <div class="min-h-screen bg-black text-white/90 selection:bg-white/10 pt-32 pb-24">
      <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-24">
          
          <!-- Sidebar / Title -->
          <div class="lg:col-span-1">
            <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-light tracking-tighter leading-[0.95] mb-8">
              Terms of <br />
              <span class="text-white/30 italic">Service.</span>
            </h1>
            <p class="text-sm font-light text-white/40 tracking-tight">
              Last updated: December 23, 2025
            </p>
          </div>

          <!-- Content -->
          <div class="lg:col-span-2 space-y-24">
            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">01. Acceptance</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  By accessing or using Ecnelis, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our platform. We reserve the right to update these terms at any time, and your continued use of the service constitutes acceptance of those changes.
                </p>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">02. Use of Service</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Ecnelis provides AI-powered tools for creativity and productivity. You are responsible for:
                </p>
                <ul class="list-none space-y-4 pl-0">
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>Maintaining the confidentiality of your account credentials.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>All activities that occur under your account.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>Ensuring your use of the service complies with all applicable laws.</span>
                  </li>
                </ul>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">03. AI Content & Accuracy</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Our platform utilizes Large Language Models (LLMs). You acknowledge that:
                </p>
                <ul class="list-none space-y-4 pl-0">
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>AI-generated content may be inaccurate, incomplete, or biased.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>You are solely responsible for verifying the accuracy of any AI output before relying on it.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>You own the inputs you provide and the outputs generated, subject to these terms and our privacy policy.</span>
                  </li>
                </ul>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">04. Prohibited Conduct</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  You agree not to use Ecnelis to:
                </p>
                <ul class="list-none space-y-4 pl-0">
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>Generate illegal, harmful, or offensive content.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>Attempt to reverse engineer or disrupt the platform's infrastructure.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span>Bypass any safety filters or usage limits.</span>
                  </li>
                </ul>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">05. Subscriptions & Payments</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Paid subscriptions are billed in advance on a recurring basis. You may cancel your subscription at any time through your account settings. Refunds are handled on a case-by-case basis in accordance with our refund policy.
                </p>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">06. Limitation of Liability</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Ecnelis is provided "as is" without warranties of any kind. In no event shall Ecnelis be liable for any indirect, incidental, or consequential damages arising out of your use of the service.
                </p>
              </div>
            </section>
          </div>
        </div>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>
