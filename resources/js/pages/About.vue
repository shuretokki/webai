<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import RevealFooter from '@/components/RevealFooter.vue';
import PreFooter from '@/components/PreFooter.vue';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

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

const container = {
  initial: { opacity: 0 },
  enter: {
    opacity: 1,
    transition: {
      staggerChildren: 0.15,
      delayChildren: 0.3
    }
  }
};

const reveal = {
  initial: { opacity: 0, y: 40, filter: 'blur(10px)', scale: 0.95 },
  enter: {
    opacity: 1,
    y: 0,
    filter: 'blur(0px)',
    scale: 1,
    transition: {
      duration: 1.2,
      ease: [0.16, 1, 0.3, 1] as const
    }
  }
};
</script>

<template>

  <Head title="About" />

  <AppLayout>
    <div class="bg-black min-h-screen text-white selection:bg-white/20">

      <section class="min-h-[90vh] flex items-center justify-center px-6">
        <Motion :initial="'initial'" :animate="'enter'" :variants="container" class="text-center">
          <Motion :variants="reveal" class="mb-6">
            <span :class="ui.typography.label" class="justify-center">
              Ecnelis
            </span>
          </Motion>

          <Motion :variants="reveal">
            <h1 :class="ui.typography.manifesto" class="max-w-5xl mx-auto">
              The interface <br />
              <span class="text-white/20">of thought.</span>
            </h1>
          </Motion>
        </Motion>
      </section>

      <section class="py-64 px-6">
        <div class="max-w-4xl mx-auto text-center">
          <Motion :initial="'initial'" :while-in-view="'enter'" :viewport="{ once: true, margin: '-20%' }"
            :variants="reveal">
            <h2 class="text-white/30 uppercase tracking-[0.3em] text-[10px] font-mono mb-12">Mission</h2>
            <p :class="ui.typography.display" class="text-white/90">
              We believe AI should be an extension of the mind, not a replacement.
            </p>
          </Motion>
        </div>
      </section>


      <section class="py-64 px-6 bg-white/[0.02]">
        <div class="max-w-4xl mx-auto text-center">
          <Motion :initial="'initial'" :while-in-view="'enter'" :viewport="{ once: true, margin: '-20%' }"
            :variants="reveal">
            <h2 class="text-white/30 uppercase tracking-[0.3em] text-[10px] font-mono mb-12">Vision</h2>
            <p :class="ui.typography.display" class="text-white/90">
              Zero friction between <br />
              <span class="text-white/40 italic font-serif">idea and execution.</span>
            </p>
          </Motion>
        </div>
      </section>


      <section class="py-64 px-6">
        <div class="max-w-2xl mx-auto">
          <Motion :initial="'initial'" :while-in-view="'enter'" :viewport="{ once: true, margin: '-20%' }"
            :variants="container" class="space-y-32">
            <Motion :variants="reveal" class="group">
              <h3 class="text-white/40 text-xs font-mono mb-4">03.1</h3>
              <p class="text-2xl font-light tracking-tight group-hover:text-white transition-colors duration-500">
                Simplicity is the ultimate sophistication. We strip away the noise so you can focus on the signal.
              </p>
            </Motion>

            <Motion :variants="reveal" class="group">
              <h3 class="text-white/40 text-xs font-mono mb-4">03.2</h3>
              <p class="text-2xl font-light tracking-tight group-hover:text-white transition-colors duration-500">
                Human-centric by design. Every feature we build starts with the question: "How does this empower the
                user?"
              </p>
            </Motion>

            <Motion :variants="reveal" class="group">
              <h3 class="text-white/40 text-xs font-mono mb-4">03.3</h3>
              <p class="text-2xl font-light tracking-tight group-hover:text-white transition-colors duration-500">
                Radical innovation. We don't follow trends; we build the tools that define the future of work.
              </p>
            </Motion>
          </Motion>
        </div>
      </section>

      <PreFooter />

      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>