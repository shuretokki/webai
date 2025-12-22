<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import RevealFooter from '@/components/RevealFooter.vue';

const changes = [
  {
    version: 'v1.1.2',
    date: 'December 23, 2025',
    title: 'New Free Tier Models',
    categories: [
      {
        name: 'Intelligence',
        items: [
          'Introduced new high-speed Groq models to the free tier for near-instant responses.',
          'Expanded model selection to provide more versatile AI options for all users.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Significantly reduced latency for basic chat interactions using optimized inference.',
        ]
      }
    ]
  },
  {
    version: 'v1.1.1',
    date: 'December 23, 2025',
    title: 'Legal & Layout Refinement',
    categories: [
      {
        name: 'Interface',
        items: [
          'Redesigned Privacy Policy and Terms of Service with a minimalist, high-readability layout.',
          'Refined footer alignment and spacing for better visual balance on mobile devices.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Updated legal documentation to provide clearer insights into how we handle AI data and privacy.',
          'Improved text selection and readability across all documentation pages.',
        ]
      }
    ]
  },
  {
    version: 'v1.1.0',
    date: 'December 23, 2025',
    title: 'The Polish Update',
    categories: [
      {
        name: 'Interface',
        items: [
          'Complete redesign of the Settings dashboard with a focused, minimalist aesthetic.',
          'New typography-driven Contact page for a more intuitive inquiry experience.',
          'Unified design system across Pricing, About, and Legal pages for a seamless brand journey.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Eliminated layout shifts and "jumps" when interacting with modals and menus.',
          'Optimized mobile navigation with improved touch targets and responsive layouts.',
          'Enhanced page transitions for a more fluid browsing experience.',
        ]
      },
      {
        name: 'Stability',
        items: [
          'Improved reliability and speed of profile avatar uploads.',
          'Fixed horizontal scrolling issues on mobile devices for a more stable viewport.',
        ]
      }
    ]
  },
  {
    version: 'v1.0.4',
    date: 'December 22, 2025',
    title: 'Search & Organization',
    categories: [
      {
        name: 'Interface',
        items: [
          'Introduced a new global search bar to quickly find past conversations.',
          'Added the ability to archive chats to keep your sidebar clean.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Improved chat categorization and tagging for better organization.',
          'Enhanced keyboard shortcuts for faster navigation between chats.',
        ]
      }
    ]
  },
  {
    version: 'v1.0.3',
    date: 'December 21, 2025',
    title: 'File Support & Media',
    categories: [
      {
        name: 'Interface',
        items: [
          'Added support for file attachments including PDFs, documents, and images.',
          'New drag-and-drop interface for seamless file uploads into chats.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Integrated image analysis capabilities for visual-based AI reasoning.',
          'Improved file previewing within the chat window for a smoother workflow.',
        ]
      }
    ]
  },
  {
    version: 'v1.0.2',
    date: 'December 20, 2025',
    title: 'Mobile & Performance',
    categories: [
      {
        name: 'Interface',
        items: [
          'Optimized the mobile sidebar for smoother transitions on iOS and Android.',
          'Refined the mobile chat input for better ergonomics.',
        ]
      },
      {
        name: 'Experience',
        items: [
          'Reduced initial load times by 30% through aggressive caching.',
          'Improved battery efficiency when using the platform on mobile browsers.',
        ]
      }
    ]
  },
  {
    version: 'v1.0.1',
    date: 'December 19, 2025',
    title: 'Stability & Performance',
    categories: [
      {
        name: 'Experience',
        items: [
          'Improved AI response streaming stability for longer conversations.',
          'Enhanced image preview quality in the chat interface.',
          'Faster loading times for the Explore page and model selection.',
        ]
      },
      {
        name: 'Stability',
        items: [
          'Resolved intermittent connection issues during peak usage.',
          'Fixed minor UI inconsistencies in the mobile chat view.',
        ]
      }
    ]
  },
  {
    version: 'v1.0.0',
    date: 'December 16, 2025',
    title: 'Initial Release',
    categories: [
      {
        name: 'Core Features',
        items: [
          'Launched Ecnelis AI platform with multi-model support.',
          'Integrated Chat, Explore, and Settings interfaces.',
          'Real-time streaming responses for AI interactions.',
        ]
      }
    ]
  }
];

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

  <Head title="Changelog" />

  <AppLayout>
    <div class="min-h-screen bg-black text-white/90 selection:bg-white/10 pt-32 pb-24">
      <div class="max-w-[1200px] mx-auto px-6">
        <div class="mb-24">
          <h1 class="text-[clamp(2.5rem,8vw,5rem)] font-light tracking-tighter leading-[0.95] mb-8">
            Change <br />
            <span class="text-white/30 italic">log.</span>
          </h1>
          <p class="text-xl font-light text-white/40 tracking-tight max-w-xl">
            A detailed record of our evolution, from architectural shifts to the smallest UI refinements.
          </p>
        </div>

        <div class="max-w-4xl">
          <div class="space-y-24">
            <div v-for="release in changes" :key="release.version"
              class="relative pl-12 border-l border-white/5 pb-12 last:pb-0">
              <div class="absolute -left-1.5 top-2 w-3 h-3 bg-white rounded-full border-4 border-black"></div>

              <div class="mb-12">
                <div class="flex items-center gap-4 mb-4">
                  <span class="text-2xl font-light tracking-tighter">{{ release.version }}</span>
                  <span class="text-[10px] px-2 py-0.5 border border-white/10 rounded uppercase tracking-widest text-white/40">{{ release.date }}</span>
                </div>
                <h3 class="text-3xl font-light tracking-tight text-white/90 italic">{{ release.title }}</h3>
              </div>

              <div class="space-y-12">
                <div v-for="category in release.categories" :key="category.name" class="space-y-4">
                  <h4 class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20">{{ category.name }}</h4>
                  <ul class="space-y-4">
                    <li v-for="item in category.items" :key="item" class="flex items-start gap-4 text-white/60 text-sm font-light leading-relaxed group">
                      <span class="block w-1 h-1 mt-2 bg-white/20 rounded-full group-hover:bg-white/40 transition-colors"></span>
                      <span class="flex-1">{{ item }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
    </div>
    <RevealFooter :content="content" />
  </AppLayout>
</template>
