<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Motion, AnimatePresence, useMotionValue, useTransform, useSpring } from 'motion-v';
import Lenis from 'lenis';
import { Menu, X } from 'lucide-vue-next';

import RevealFooter from '@/components/RevealFooter.vue';
import Hero from '@/components/landing/Hero.vue';
import Manifesto from '@/components/landing/Manifesto.vue';
import Features from '@/components/landing/Features.vue';
import Developer from '@/components/landing/Developer.vue';
import Pricing from '@/components/landing/Pricing.vue';
import Faq from '@/components/landing/Faq.vue';
import PreFooter from '@/components/landing/PreFooter.vue';

import { ui } from '@/config/ui';
import { useDevice }
  from '@/composables/useDevice';

defineProps<{
  canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);
const scrollY = useMotionValue(0);
const featuresSection = ref<HTMLElement | null>(null);

const { isMobile, canHover } = useDevice();
const vh = ref(typeof window !== "undefined" ? window.innerHeight : 0);
const pricingBillingCycle = ref<'monthly' | 'yearly'>('monthly');
const faqOpen = ref<number | null>(null);

const smoothScrollY = useSpring(scrollY, {
  damping: 30,
  stiffness: 200,
  mass: 0.5,
  restDelta: 0.001
});

let lenis: Lenis | null = null;

onMounted(() => {
  lenis = new Lenis({
    duration: 1.2,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    orientation: 'vertical',
    gestureOrientation: 'vertical',
    smoothWheel: true,
  });

  lenis.on('scroll', (e: any) => {
    scrollY.set(e.scroll);
  });

  vh.value = window.innerHeight;
  window.addEventListener('resize', () => {
    vh.value = window.innerHeight;
  });

  function raf(time: number) {
    lenis?.raf(time);
    requestAnimationFrame(raf);
  }

  requestAnimationFrame(raf);
});

onBeforeUnmount(() => {
  lenis?.destroy();
});

const heroImageScale = useTransform(
  scrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.imageScale);

const headerY = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.headerY);

const headerScale = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.headerScale,
  { clamp: true }
);

const headerLineHeight = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.lineSpacing,
  { clamp: true }
);

const headerBlur = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.opacityRange,
  ui.animations.scrollEffects.hero.blur,
  { clamp: true }
);

const headerOpacity = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.opacityRange,
  ui.animations.scrollEffects.hero.opacity,
  { clamp: true }
);

const preFooterImageY = useTransform(
  scrollY,
  ui.animations.scrollEffects.preFooter.y
);

const preFooterImageScale = useTransform(
  scrollY,
  ui.animations.scrollEffects.preFooter.scale
);

const navBackground = useTransform(
  scrollY,
  ui.navigation.scrollThreshold,
  [ui.navigation.background.initial,
  ui.navigation.background.scrolled]
);

const navBorder = useTransform(
  scrollY,
  ui.navigation.scrollThreshold,
  [ui.navigation.border.initial,
  ui.navigation.border.scrolled]
);

const navBackdrop = useTransform(
  scrollY,
  ui.navigation.scrollThreshold,
  [ui.navigation.backdrop.initial,
  ui.navigation.backdrop.scrolled]
);

const content = {
  appName: 'Ecnelis',
  navigation: [
    { label: 'About', href: '/about' },
    { label: 'Pricing', href: '/pricing' },
    { label: 'Blog', href: '/blog' },
    { label: 'Changelog', href: '/changelog' },
  ],
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
  },
  hero: {
    image: '/images/heroSection.webp',
    title: {
      line1: 'Chat',
      line2: 'with our',
      line3: 'smarter',
      line4: 'AI'
    },
    description: "Talk to AI using text, images, voice, and video. Get instant answers and creative ideas in one place.",
    cta: 'Scroll to explore'
  },
  sections: {
    introducing: {
      label: 'About Ecnelis',
      title: 'Chat with AI using text, images, voice, and video all in one place.'
    },
    features: {
      label: 'Trusted Brands',
      brands: [
        { id: '1', name: 'Nvidia', icon: '/images/brands/nvidia.svg' },
        { id: '2', name: 'OpenAI', icon: '/images/brands/openai.svg' },
        { id: '3', name: 'Anthropic', icon: '/images/brands/anthropic.svg' },
        { id: '4', name: 'Meta', icon: '/images/brands/meta.svg' },
        { id: '5', name: 'Mistral', icon: '/images/brands/mistral.svg' },
        { id: '6', name: 'CoreWeave', icon: '/images/brands/coreweave.svg' },
        { id: '7', name: 'HuggingFace', icon: '/images/brands/huggingface.svg' },
        { id: '8', name: 'Groq', icon: '/images/brands/groq.svg' },
        { id: '9', name: 'Perplexity', icon: '/images/brands/perplexity.svg' },
        { id: '10', name: 'DeepMind', icon: '/images/brands/deepmind.svg' }
      ]
    },
    pricing: {
      label: 'Plans',
      title: 'Simple, Transparent Pricing',
      description: "Choose the plan that fits your needs. No surprises.",
      plans: [
        {
          name: 'Free',
          price: '$0',
          period: '/mo',
          description: 'Perfect for trying out AI',
          features: ['Basic AI models', '50 messages per month', 'Community help'],
          cta: 'Start Free',
          href: '/explore',
          comingSoon: false
        },
        {
          name: 'Premium',
          price: '$20',
          period: '/mo',
          description: 'For people who use AI regularly',
          features: ['Advanced AI models', 'Unlimited messages', 'Priority support'],
          cta: 'Join Waitlist',
          comingSoon: true
        },
        {
          name: 'Business',
          price: 'Custom',
          period: '',
          description: 'For teams and companies',
          features: ['Custom AI training', 'Dedicated support', 'Security guarantees'],
          cta: 'Contact Sales',
          href: '/contact',
          comingSoon: true
        }
      ]
    },
    developers: {
      title: 'Your AI Assistant',
      description: 'Write code, analyze data, or bring your creative ideas to life. All in one conversation.',
      cta: 'Start Chatting',
      cards: [
        {
          id: 'inference',
          title: 'Lightning Fast',
          description: 'Get AI responses in milliseconds thanks to our optimized servers located around the world.',
          image: '/images/landing/lightning.webp',
          span: 'col-span-1 md:col-span-7'
        },
        {
          id: 'tools',
          title: 'Powerful Tools',
          description: 'Give your AI the ability to run code, search the web, and connect with other services seamlessly.',
          image: '/images/landing/bot.webp',
          span: 'col-span-1 md:col-span-5'
        },
        {
          id: 'security',
          title: 'Your Data is Safe',
          description: 'Enterprise encryption and private servers mean your data stays completely secure and private.',
          image: '/images/landing/shield.webp',
          span: 'col-span-1 md:col-span-5'
        },
        {
          id: 'scaling',
          title: 'Grows With You',
          description: 'Whether you have 10 users or 10 million, our infrastructure automatically scales to match your needs.',
          image: '/images/landing/server.webp',
          span: 'col-span-1 md:col-span-7'
        }
      ]
    },
    faq: {
      label: 'Help',
      title: 'Frequently Asked Questions',
      items: [
        {
          question: "What can I do with Ecnelis?",
          answer: "Chat with AI using text, images, voice, or video. Ask questions, get creative ideas, write code, or just have a conversation.",
          open: false
        },
        {
          question: "Do I need to be tech-savvy to use this?",
          answer: "Not at all. If you can send a text message, you can use Ecnelis. It's designed to be simple for everyone.",
          open: false
        },
        {
          question: "Which AI models can I use?",
          answer: "We support the best AI models available, including GPT-4, Claude, and others. You can switch between them anytime.",
          open: false
        },
        {
          question: "Can I use this for my business?",
          answer: "Yes! Our Business plan includes features for teams, advanced security, and dedicated support.",
          open: false
        },
        {
          question: "What if I need help?",
          answer: "We're here 24/7 through chat and email. Business customers also get a dedicated support manager.",
          open: false
        }
      ]
    },
    preFooter: {
      title: 'Start chatting today',
      cta: 'Get Started Free',
      subtext: 'No credit card needed to start.',
      image: '/images/preFooter.jpg'
    }
  }
};

</script>

<template>

  <Head title="ECNELIS">
    <meta name="description"
      content="Ecnelis is a sentient canvas for your digital neural network. Explore foundation models, build autonomous agents, and expand your thoughts into actions." />
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <link rel="preload" href="/images/heroSection.jpg" as="image" />
  </Head>

  <div class="min-h-dvh bg-black text-white font-space selection:bg-white/20 overflow-x-hidden relative z-10">

    <div class="fixed inset-0 pointer-events-none z-40 opacity-[0.2] mix-blend-overlay"
      style="background-image: url('/images/noise.webp');">
    </div>

    <header class="fixed top-6 inset-x-0 z-50 flex justify-center pointer-events-none"
      :style="{ paddingTop: 'var(--sat, 0px)' }">
      <Motion
        :class="['pointer-events-auto px-6 rounded-full border border-white/10 flex items-center gap-8 shadow-2xl', ui.navigation.height]"
        :style="{
          backgroundColor: navBackground,
          borderColor: navBorder,
          backdropFilter: isMobile ? 'none' : navBackdrop
        }">
        <Link id="nav-logo" href="/" class="flex items-center group">
          <img src="/assets/LOGO.svg" alt="Ecnelis"
            class="h-6 w-auto md:hidden group-hover:opacity-80 transition-opacity" />
          <img src="/assets/LOGOTYPE.svg" alt="Ecnelis"
            class="hidden md:block h-6 w-auto group-hover:opacity-80 transition-opacity" />
        </Link>
        <nav class="hidden md:flex items-center gap-2">
          <Link v-for="item in content.navigation" :key="item.label"
            :id="`nav-link-${item.label.toLowerCase().replace(/\s+/g, '-')}`" :href="item.href"
            class="relative px-4 py-2 group">
            <span class="relative z-10 text-white/60 transition-colors group-hover:text-white"
              :class="{ 'text-white': $page.url === item.href }">
              {{ item.label }}
            </span>
            <Motion v-if="$page.url === item.href" layout-id="nav-active"
              class="absolute inset-0 bg-white/10 rounded-full z-0" />
            <Motion class="absolute inset-0 bg-white/5 rounded-full z-0 opacity-0 group-hover:opacity-100"
              initial={false} :transition="{ duration: 0.2 }" />
          </Link>
        </nav>

        <div class="hidden md:flex items-center gap-4 pl-4 border-l border-white/10">
          <Link v-if="!$page.props.auth.user" href="/login" class="px-4 py-2 group relative">
            <span class="relative z-10 text-white/60 transition-colors group-hover:text-white">Log in</span>
            <Motion class="absolute inset-0 bg-white/5 rounded-full z-0 opacity-0 group-hover:opacity-100"
              initial={false} :transition="{ duration: 0.2 }" />
          </Link>
          <Link v-else href="/explore" class="px-4 py-2 group relative">
            <span class="relative z-10 text-white/60 transition-colors group-hover:text-white">Explore</span>
            <Motion class="absolute inset-0 bg-white/5 rounded-full z-0 opacity-0 group-hover:opacity-100"
              initial={false} :transition="{ duration: 0.2 }" />
          </Link>
        </div>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white ml-2 pointer-events-auto"
          id="mobile-menu-toggle">
          <Menu v-if="!mobileMenuOpen" class="w-5 h-5" />
          <X v-else class="w-5 h-5" />
        </button>
      </Motion>
    </header>

    <AnimatePresence>
      <Motion v-if="mobileMenuOpen" :initial="{ opacity: 0, y: -20 }" :animate="{ opacity: 1, y: 0 }"
        :exit="{ opacity: 0, y: -20 }" class="fixed inset-0 z-40 bg-black pt-28 px-6 md:hidden flex flex-col"
        :style="{ paddingBottom: 'var(--sab, 0px)', paddingTop: 'calc(var(--sat, 0px) + 7rem)' }">
        <div class="flex flex-col gap-8 text-2xl font-light">
          <Link v-for="item in content.navigation" :key="item.label" :href="item.href" @click="mobileMenuOpen = false"
            class="block py-2 border-b border-white/10 text-white/90">
            {{ item.label }}
          </Link>
          <div class="flex flex-col gap-4 mt-0">
            <template v-if="$page.props.auth.user">
              <Link href="/explore" class="text-2xl font-light block py-2 border-b border-white/10 text-white/90">
                Explore
              </Link>
            </template>
            <template v-else>
              <Link href="/login"
                class="block w-full text-center font-medium py-4 border border-white/20 text-white/90 transition-colors active-press"
                :class="{ 'hover:bg-white/5': canHover }">
                Log in
              </Link>
              <Link href="/register"
                class="block w-full text-center font-medium py-4 border bg-white border-white/20 text-black/90 transition-colors active-press"
                :style="{ paddingTop: '0.75rem', paddingBottom: '0.75rem' }">
                Get Started
              </Link>
            </template>
          </div>
        </div>
      </Motion>
    </AnimatePresence>

    <main id="main-content">
      <div class="relative">
        <Hero id="hero-section" :content="content.hero" :hero-image-scale="heroImageScale" :header-y="headerY"
          :header-scale="headerScale" :header-blur="headerBlur" :header-opacity="headerOpacity"
          :header-line-height="headerLineHeight" />

        <div class="relative z-10 bg-black">
          <Manifesto id="manifesto-section" :content="content.sections.introducing" :is-mobile="isMobile" />
          <Developer id="developer-section" :content="content.sections.developers" :can-hover="canHover" />
          <!-- <Features id="features-section" :content="content.sections.features" :can-hover="canHover" /> -->
        </div>
      </div>

      <Pricing id="pricing-section" v-model:billing-cycle="pricingBillingCycle" :content="content.sections.pricing" />

      <Faq id="faq-section" v-model:faq-open="faqOpen" :content="content.sections.faq" :can-hover="canHover" />

      <PreFooter id="pre-footer-section" :content="content.sections.preFooter" :can-hover="canHover"
        :pre-footer-image-y="preFooterImageY" :pre-footer-image-scale="preFooterImageScale" />
    </main>

    <div class="h-[10dvh] bg-black relative z-20"></div>
    <RevealFooter :content="content" />
  </div>
</template>