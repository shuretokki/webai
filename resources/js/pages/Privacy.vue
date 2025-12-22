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

  <Head title="Privacy Policy" />

  <AppLayout>
    <div class="min-h-screen bg-black text-white/90 selection:bg-white/10 pt-32 pb-24">
      <div class="max-w-[1200px] mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-24">

          <div class="lg:col-span-1">
            <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-light tracking-tighter leading-[0.95] mb-8">
              Privacy <br />
              <span class="text-white/30 italic">Policy.</span>
            </h1>
            <p class="text-sm font-light text-white/40 tracking-tight">
              Last updated: December 23, 2025
            </p>
          </div>

          <div class="lg:col-span-2 space-y-24">
            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">01. Introduction</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  At Ecnelis, we believe privacy is a fundamental right. This policy outlines how we handle your data with the same precision and care we apply to our AI models. By using our platform, you trust us with your information, and we take that responsibility seriously.
                </p>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">02. Information We Collect</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  We collect information to provide a seamless AI experience. This includes:
                </p>
                <ul class="list-none space-y-4 pl-0">
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span><strong class="text-white/80 font-normal">Account Data:</strong> Email address, name, and profile preferences when you register.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span><strong class="text-white/80 font-normal">Content Data:</strong> The prompts, messages, and files (images, documents) you upload to interact with our AI.</span>
                  </li>
                  <li class="flex gap-4">
                    <span class="text-white/20 italic">/</span>
                    <span><strong class="text-white/80 font-normal">Technical Data:</strong> IP addresses, browser types, and usage patterns to optimize performance and security.</span>
                  </li>
                </ul>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">03. How We Use Your Data</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Your data is used exclusively to power your experience. We do not sell your personal information.
                </p>
                <p>
                  We use your content to generate responses via our integrated LLM providers (such as OpenAI and Anthropic). While these providers process your data to generate output, we ensure they adhere to strict privacy standards and do not use your data to train their foundation models unless explicitly stated.
                </p>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">04. Data Security & Retention</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  We employ industry-standard encryption for data at rest and in transit. Your chat history is stored securely so you can access it across devices. You may delete your chats or your entire account at any time, which will trigger the removal of your data from our active databases.
                </p>
              </div>
            </section>

            <section class="space-y-6">
              <h2 class="text-xl font-light tracking-tight text-white">05. Your Rights</h2>
              <div class="space-y-4 text-white/60 font-light leading-relaxed">
                <p>
                  Depending on your location, you may have rights under GDPR, CCPA, or similar regulations. This includes the right to access, correct, or delete your personal information. For any privacy-related inquiries, contact us at <a href="mailto:privacy@ecnelis.studio" class="text-white border-b border-white/20 hover:border-white transition-colors">privacy@ecnelis.studio</a>.
                </p>
              </div>
            </section>
          </div>
        </div>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
    </div>
  <RevealFooter :content="content" />
</AppLayout>
</template>
