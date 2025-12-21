<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Motion, AnimatePresence, useMotionValue, useTransform, useSpring } from 'motion-v';
import Lenis from 'lenis';
import {
  ChevronDown,
  ArrowRight,
  Menu,
  X,
  Plus,
  Sparkles,
  Apple,
  Monitor,
  Terminal
} from 'lucide-vue-next';
import RevealFooter from '@/components/RevealFooter.vue';
import MagneticButton from '@/components/MagneticButton.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { ui } from '@/config/ui';

defineProps<{
  canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);
const scrollY = useMotionValue(0);
const pricingSection = ref<HTMLElement | null>(null);
const featuresSection = ref<HTMLElement | null>(null);
const featuresScrollRange = ref([0, 0]);

const updateFeaturesRange = () => {
  if (featuresSection.value) {
    const rect = featuresSection.value.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const start = rect.top + scrollTop;
    featuresScrollRange.value = [start, start + featuresSection.value.offsetHeight];
  }
};
const pricingMouseX = useMotionValue(0);
const pricingMouseY = useMotionValue(0);
const pricingMouseXPx = useTransform(pricingMouseX, (v) => `${v}px`);
const pricingMouseYPx = useTransform(pricingMouseY, (v) => `${v}px`);
const vh = ref(typeof window !== "undefined" ? window.innerHeight : 0);
const pricingBillingCycle = ref<'monthly' | 'yearly'>('monthly');
const faqOpen = ref<number | null>(0);


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

  if (pricingSection.value) {
    pricingSection.value.addEventListener('mousemove', (e) => {
      const rect = pricingSection.value!.getBoundingClientRect();
      pricingMouseX.set(e.clientX - rect.left);
      pricingMouseY.set(e.clientY - rect.top);
    });
  }

  updateFeaturesRange();
  vh.value = window.innerHeight;
  window.addEventListener('resize', () => {
    updateFeaturesRange();
    vh.value = window.innerHeight;
  });
  window.addEventListener('load', updateFeaturesRange);

  function raf(time: number) {
    lenis?.raf(time);
    requestAnimationFrame(raf);
  }

  requestAnimationFrame(raf);

});

onBeforeUnmount(() => {
  lenis?.destroy();
});

const heroImageY = useTransform(
  scrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.imageY);

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
  ui.animations.scrollEffects.hero.headerScale
);

const headerLineHeight = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.range,
  ui.animations.scrollEffects.hero.lineSpacing
);

const headerBlur = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.opacityRange,
  ui.animations.scrollEffects.hero.blur
);

const headerOpacity = useTransform(
  smoothScrollY,
  ui.animations.scrollEffects.hero.opacityRange,
  ui.animations.scrollEffects.hero.opacity
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
    { label: 'Pricing', href: '#pricing' },
    { label: 'Blog', href: '/blog' },
    { label: 'Changelog', href: '/changelog' },
  ],
  hero: {
    image: '/images/heroSection.png',
    title: {
      line1: 'Where',
      line2: 'thoughts',
      line3: 'become',
      line4: 'actions'
    },
    description: "An AI companion that whispers clarity, conjures ideas, and guides your every move into the void.",
    cta: 'Scroll to explore'
  },
  sections: {
    introducing: {
      label: 'Introducing Message',
      title: 'Harness invisible power to write faster, focus deeper, and save hours.'
    },
    features: [
      {
        id: 'time',
        label: 'Smart Focus',
        description: 'Raycast is a blazingly fast, totally extendable launcher. It lets you complete tasks, calculate, share common links and much more.',
        image: '/images/featureTime.png'
      },
      {
        id: 'words',
        label: 'Write Better',
        description: 'Write smarter, not harder. Use AI to improve your writing, summarize text, or generate ideas in any app.',
        image: '/images/featureWords.png'
      },
      {
        id: 'guide',
        label: 'Your Guide',
        description: 'Stay on top of your schedule, manage windows, and control your system with simple commands.',
        image: '/images/featureGuide.png'
      }
    ],
    pricing: {
      label: 'Introducing Benefit',
      title: 'Simple, Transparent Pricing',
      description: "Choose the plan that's right for you. No hidden fees.",
      plans: [
        {
          name: 'Ecnelis',
          price: '$0',
          period: '/mo',
          description: 'Perfect for getting started with AI',
          features: ['Access to standard models', '50 messages / month', 'Community support'],
          cta: 'Start Building',
          link: '/explore',
          comingSoon: false
        },
        {
          name: 'Ecnelis+',
          price: '$20',
          period: '/mo',
          description: 'For power users who need more available resources.',
          features: ['Access to premium models', 'Unlimited messages', 'Priority support'],
          cta: 'Join Waitlist',
          comingSoon: true
        },
        {
          name: 'Enterprise',
          price: 'Custom',
          period: '',
          description: 'For organizations with custom needs to scale.',
          features: ['Custom model fine-tuning', 'Dedicated support manager', 'SLA guarantees'],
          cta: 'Contact Sales',
          comingSoon: true
        }
      ]
    },
    developers: {
      title: 'Build the perfect tools.',
      description: 'Our extension API is designed to allow anyone with web development skills to unleash the power of Raycast.',
      cta: 'Read the docs',
      cards: [
        {
          id: 'macos',
          title: 'React to macOS',
          description: 'Build rich, native extensions with the technologies you already know: React, TypeScript and Node.',
          image: '/images/dev_macintosh_1766309616558.png',
          span: 'col-span-1 md:col-span-7'
        },
        {
          id: 'ui',
          title: 'Built-in UI',
          description: 'Our UI component library allows you to create beautiful tools in minutes while we push the pixels.',
          image: '/images/dev_ui_components_blueprint_1766309631142.png',
          span: 'col-span-1 md:col-span-5'
        },
        {
          id: 'batteries',
          title: 'Batteries included',
          description: 'A strongly typed API, hot reloading and secure tooling that make it a blast to work with.',
          image: '/images/dev_battery_charger_1766309647277.png',
          span: 'col-span-1 md:col-span-5'
        },
        {
          id: 'store',
          title: 'Publish to the Store',
          description: 'Submit your extension to the Raycast Store and share it with thousands of users.',
          image: '/images/dev_floppy_disk_1766309662097.png',
          span: 'col-span-1 md:col-span-7'
        }
      ]
    },
    faq: {
      label: 'Support',
      title: 'Frequently asked questions.',
      items: [
        {
          question: "What is this AI platform designed for?",
          answer: "It helps you generate, test, and deploy ideas with advanced AI models — all in one simple workspace.",
          open: false
        },
        {
          question: "Do I need technical knowledge to use it?",
          answer: "Not at all. The platform is built for everyone — from beginners exploring AI to professionals building complex workflows.",
          open: false
        },
        {
          question: "Which AI models power the tool?",
          answer: "We integrate with top-tier models like GPT-4, Claude 3, and refined open-source models to ensure the best results.",
          open: false
        },
        {
          question: "Can I use this for business purposes?",
          answer: "Absolutely. Our Enterprise plan allows for commercial use, team collaboration, and advanced security features.",
          open: false
        },
        {
          question: "How can I get support if I have issues?",
          answer: "Our support team is available 24/7 via chat and email. Enterprise customers get a dedicated success manager.",
          open: false
        }
      ]
    },
    preFooter: {
      title: 'Shape the Future.',
      cta: 'Get Started Now',
      subtext: 'No credit card required for standard plans.',
      image: '/images/preFooter.jpg'
    }
  },
  footer: {
    links: {
      index: [
        { label: 'Home', href: '/' },
        { label: 'Work', href: '/work' },
        { label: 'About', href: '/about' },
        { label: 'Services', href: '/services' },
        { label: 'Privacy Policy', href: '/privacy' },
      ],
      social: [
        { label: 'Instagram', href: '#', icon: 'Instagram' },
        { label: 'Facebook', href: '#', icon: 'Facebook' },
        { label: 'LinkedIn', href: '#', icon: 'LinkedIn' },
        { label: 'Awwwards', href: '#', icon: 'Awwwards' },
        { label: 'Behance', href: '#', icon: 'Behance' },
      ]
    },
    contact: {
      title: "Tell us about your project.\nLet's collaborate.",
      phone: '+27 (0) 78 054 8476',
      actions: [
        { label: 'Write Us', href: '#' },
        { label: 'Newsletter Signup', href: '#' }
      ]
    }
  }
};

const faqs = ref(content.sections.faq.items);

const toggleFaq = (index: number) => {
  faqs.value[index].open = !faqs.value[index].open;
};

</script>

<template>

  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <link rel="preload" href="/images/heroSection.jpg" as="image" />
  </Head>

  <div class="min-h-dvh bg-black text-white font-sans selection:bg-white/20 overflow-x-hidden relative z-10">

    <div class="fixed inset-0 pointer-events-none z-40 opacity-[0.35] mix-blend-overlay"
      style="background-image: url('/images/noise.jpg');">
    </div>

    <nav class="fixed top-6 inset-x-0 z-50 flex justify-center pointer-events-none"
      :style="{ paddingTop: 'var(--sat, 0px)' }">
      <Motion
        class="pointer-events-auto px-6 py-3 rounded-2xl border border-white/10 flex items-center gap-8 backdrop-blur-md shadow-2xl"
        :style="{ backgroundColor: navBackground, borderColor: navBorder }">
        <Link href="/" class="flex items-center gap-2 group">
          <AppLogoIcon class="w-5 h-5 text-white/90 group-hover:text-white transition-colors" />
          <span class="font-medium text-lg tracking-tight sr-only md:not-sr-only">{{ content.appName }}</span>
        </Link>

        <div class="hidden md:flex items-center gap-6">
          <template v-for="item in content.navigation" :key="item.label">
            <Link v-if="item.href.startsWith('/')" :href="item.href"
              class="text-sm font-medium cursor-pointer relative px-2 py-1">
              <span class="relative z-10 text-white/60 hover:text-white transition-colors">{{ item.label }}</span>
            </Link>
            <a v-else :href="item.href" class="text-sm font-medium cursor-pointer relative px-2 py-1">
              <span class="relative z-10 text-white/60 hover:text-white transition-colors">{{ item.label }}</span>
            </a>
          </template>
        </div>

        <div class="flex items-center gap-4 pl-4 border-l border-white/10">
          <template v-if="$page.props.auth.user">
            <Link href="/explore">
              <Motion is="span" class="text-sm font-medium block text-white/60 hover:text-white transition-colors">
                Explore
              </Motion>
            </Link>
          </template>
          <template v-else>
            <Link href="/login" class="hidden md:block">
              <span class="text-sm font-medium text-white/60 hover:text-white transition-colors cursor-pointer">
                Log in
              </span>
            </Link>
            <Link href="/register" :class="ui.layout.button">
              Get Started
            </Link>
          </template>
        </div>

        <button @click=" mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white ml-2">
          <Menu v-if="!mobileMenuOpen" class="w-5 h-5" />
          <X v-else class="w-5 h-5" />
        </button>
      </Motion>
    </nav>

    <AnimatePresence>
      <Motion v-if="mobileMenuOpen" :initial="{ opacity: 0, y: -20 }" :animate="{ opacity: 1, y: 0 }"
        :exit="{ opacity: 0, y: -20 }" class="fixed inset-0 z-40 bg-black pt-28 px-6 md:hidden flex flex-col"
        :style="{ paddingBottom: 'var(--sab, 0px)', paddingTop: 'calc(var(--sat, 0px) + 7rem)' }">
        <div class="flex flex-col gap-8 text-2xl font-light">
          <a v-for="item in content.navigation" :key="item.label" :href="item.href" @click="mobileMenuOpen = false"
            class="block py-2 border-b border-white/10 text-white/90">
            {{ item.label }}
          </a>
          <div class="flex flex-col gap-4 mt-0">
            <template v-if="$page.props.auth.user">
              <Link href="/explore" class="text-2xl font-light block py-2 border-b border-white/10 text-white/90">
                Explore
              </Link>
            </template>
            <template v-else>
              <Link href="/login"
                class="block w-full text-center py-4 border border-white/20 text-white/90 hover:bg-white/5 transition-colors">
                Log in
              </Link>
              <Link href="/register"
                class="block w-full text-center py-3 bg-white text-black font-medium rounded-lg hover:bg-neutral-200 transition-colors">
                Get Started
              </Link>
            </template>
          </div>
        </div>
      </Motion>
    </AnimatePresence>

    <div class="bg-black relative">
      <section class="min-h-dvh h-hero-reserved flex items-center justify-center relative overflow-hidden group">
        <Motion class="absolute inset-0 z-0 pointer-events-none will-change-transform"
          :style="{ y: heroImageY, scale: heroImageScale, transform: 'translateZ(0)' }">
          <div class="absolute inset-0 bg-black/40 z-10 bg-noise mix-blend-overlay"></div>
          <img :src="content.hero.image" alt="Hero background" class="w-full h-full object-cover" />
          <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-black via-black/90 to-transparent z-20">
          </div>
        </Motion>

        <div :class="ui.layout.hero">
          <Motion initial="initial" animate="enter" :variants="{ enter: { transition: { staggerChildren: 0.2 } } }">
            <Motion class="mb-10 md:mb-16" :initial="ui.animations.pageTransition.initial"
              :animate="ui.animations.pageTransition.enter">
              <Motion is="h1" :class="[ui.typography.hero, 'origin-center will-change-transform']"
                :style="{ y: headerY, scale: headerScale, filter: headerBlur, opacity: headerOpacity, lineHeight: headerLineHeight }">
                {{ content.hero.title.line1 }} <span :class="[ui.typography.accentHero, 'mx-2']">{{
                  content.hero.title.line2 }}</span> <br class="md:hidden" />
                {{ content.hero.title.line3 }} <span :class="[ui.typography.accentHero, 'mx-2']">{{
                  content.hero.title.line4 }}</span>.
              </Motion>
            </Motion>

            <Motion class="max-w-xl md:max-w-3xl mx-auto text-center" :initial="ui.animations.pageTransition.initial"
              :animate="{ ...ui.animations.pageTransition.enter, transition: { ...ui.animations.pageTransition.enter.transition, delay: 0.2 } }">
              <Motion is="p" :class="[ui.typography.body, 'text-lg md:leading-normal']"
                :style="{ y: headerY, scale: headerScale, opacity: headerOpacity }">
                {{ content.hero.description }}
              </Motion>
            </Motion>

          </Motion>
        </div>

        <Motion
          class="absolute bottom-12 left-1/2 -translate-x-1/2 text-white/40 text-xs uppercase tracking-[0.2em] flex flex-col items-center gap-4 z-20"
          :style="{ y: headerY, scale: headerScale, opacity: headerOpacity }">
          {{ content.hero.cta }}
          <Motion :animate="{ y: [0, 10, 0] }" :transition="{ duration: 2, repeat: Infinity, ease: 'easeInOut' }">
            <ChevronDown class="w-5 h-5 opacity-50" />
          </Motion>
        </Motion>
      </section>
      <section class="pt-section" :class="ui.layout.sectionPadding">
        <div :class="ui.layout.sectionContainer">
          <Motion :initial="false" :while-in-view="{ opacity: 1, x: 0 }" :viewport="{ once: true, margin: '-100px' }"
            class="flex items-center gap-4 mb-12">
            <div class="w-12 h-[1px] relative">
              <Motion is="svg" viewBox="0 0 48 1" class="absolute inset-0 w-full h-full text-white/40">
                <Motion is="line" x1="0" y1="0.5" x2="48" y2="0.5" stroke="currentColor" stroke-width="1"
                  :initial="{ pathLength: 0, opacity: 0 }"
                  :while-in-view="{ pathLength: 1, opacity: 1, transition: { duration: 1.5, ease: 'easeInOut' } }"
                  :viewport="{ once: true }" />
              </Motion>
            </div>
            <span class="text-sm text-white/40 font-medium">{{ content.sections.introducing.label }}</span>
          </Motion>

          <Motion initial="initial" :while-in-view="'enter'" :viewport="{ once: true, margin: '-10%' }"
            class="max-w-4xl mb-12 md:mb-24">
            <h2 :class="[ui.typography.display, 'overflow-hidden flex flex-wrap gap-x-[0.3em] gap-y-[0.1em]']">
              <div v-for="(word, i) in content.sections.introducing.title.split(' ')" :key="i" class="overflow-hidden">
                <Motion :variants="{
                  initial: { y: '100%', opacity: 0, rotateZ: 5 },
                  enter: {
                    y: 0,
                    opacity: 1,
                    rotateZ: 0,
                    transition: {
                      duration: 0.8,
                      ease: ui.animations.easing.smooth,
                      delay: i * 0.03
                    }
                  }
                }" class="inline-block origin-top-left">
                  {{ word }}
                </Motion>
              </div>
            </h2>
          </Motion>
        </div>
      </section>

      <section id="features" class="py-32 md:py-48" :class="ui.layout.sectionPadding">
        <div :class="ui.layout.clampWidth">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Motion v-for="(feature, idx) in content.sections.features" :key="feature.id"
              :initial="{ opacity: 0, y: 20 }" :while-in-view="{ opacity: 1, y: 0 }"
              :transition="{ delay: idx * 0.1, duration: 0.8 }" :viewport="{ once: true }"
              class="group relative bg-[#080808] border border-white/5 rounded-2xl p-8 overflow-hidden hover:border-white/10 transition-colors">
              <div
                class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
              </div>
              <div class="relative z-10">
                <div
                  class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center mb-10 group-hover:scale-110 transition-transform duration-500">
                  <component :is="idx === 0 ? Sparkles : (idx === 1 ? Apple : Monitor)"
                    class="w-6 h-6 text-white/40 group-hover:text-white transition-colors" />
                </div>
                <h3 class="text-xl font-medium text-white mb-4 tracking-tight">{{ feature.label }}</h3>
                <p class="text-sm text-white/40 leading-relaxed mb-10 min-h-[4em]">
                  {{ feature.description }}
                </p>
                <div
                  class="aspect-video bg-black/40 rounded-lg border border-white/5 overflow-hidden relative group-hover:border-white/10 transition-colors">
                  <div
                    class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-transparent to-transparent opacity-50">
                  </div>
                  <img :src="feature.image"
                    class="w-full h-full object-cover grayscale opacity-30 group-hover:grayscale-0 group-hover:opacity-80 transition-all duration-1000" />
                </div>
              </div>
            </Motion>
          </div>
        </div>
      </section>

      <section id="developer" class="py-32 md:py-48 relative overflow-hidden" :class="ui.layout.sectionPadding">
        <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none"
          style="background-image: linear-gradient(to right, #fff 1px, transparent 1px), linear-gradient(to bottom, #fff 1px, transparent 1px); background-size: 4rem 4rem;">
        </div>

        <div :class="ui.layout.clampWidth" class="relative z-10">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center mb-32">
            <div class="md:col-span-5">
              <Motion :initial="{ opacity: 0, x: -20 }" :while-in-view="{ opacity: 1, x: 0 }" :viewport="{ once: true }"
                :transition="{ duration: 0.8 }">
                <h2 class="text-5xl md:text-6xl font-medium text-white mb-8 tracking-tighter">
                  {{ content.sections.developers.title }}</h2>
                <p class="text-white/40 mb-10 text-lg leading-relaxed max-w-sm">
                  {{ content.sections.developers.description }}</p>
                <Link href="/docs"
                  class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-widest text-white/60 hover:text-white transition-colors group">
                  {{ content.sections.developers.cta }}
                  <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </Link>
              </Motion>
            </div>
            <div class="md:col-span-7">
              <Motion :initial="{ opacity: 0, scale: 0.95 }" :while-in-view="{ opacity: 1, scale: 1 }"
                :viewport="{ once: true }" :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }">
                <div
                  class="aspect-[4/3] relative rounded-2xl overflow-hidden border border-white/10 bg-black shadow-2xl shimmer-raycast">
                  <img src="/images/dev_ui_stack_1766309601070.png" class="w-full h-full object-cover opacity-80" />
                </div>
              </Motion>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <Motion v-for="(card, i) in content.sections.developers.cards" :key="card.id"
              :initial="{ opacity: 0, y: 20 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
              :transition="{ delay: i * 0.1, duration: 0.8 }"
              :class="[card.span, 'relative bg-[#080808]/50 border border-white/5 rounded-2xl overflow-hidden group hover:border-white/10 transition-colors h-[480px] md:h-[540px] flex flex-col']">

              <div :class="[i % 2 === 0 ? 'flex-col' : 'flex-col-reverse', 'h-full flex']">
                <div class="flex-1 relative overflow-hidden flex items-center justify-center p-12">
                  <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.05)_0%,transparent_70%)]">
                  </div>
                  <img :src="card.image"
                    class="w-full h-full object-contain filter brightness-75 group-hover:brightness-100 transition-all duration-1000 group-hover:scale-105" />
                </div>
                <div class="p-10 border-t border-white/5 bg-black/40 backdrop-blur-sm relative z-10">
                  <h3 class="text-xl font-medium text-white mb-3 tracking-tight">{{ card.title }}</h3>
                  <p class="text-[13px] text-white/30 leading-relaxed max-w-xs">{{ card.description }}</p>
                </div>
              </div>

              <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                <ArrowRight class="w-4 h-4 text-white/40" />
              </div>
            </Motion>
          </div>
        </div>
      </section>
    </div>
    <section id="pricing" ref="pricingSection" class="py-section border-b border-white/5 relative group/spotlight"
      :class="ui.layout.sectionPadding">

      <Motion
        class="pointer-events-none absolute -inset-px opacity-0 group-hover/spotlight:opacity-100 transition-opacity duration-500 z-10"
        :style="{
          '--x': pricingMouseXPx,
          '--y': pricingMouseYPx,
          background: `radial-gradient(600px circle at var(--x) var(--y), rgba(255,255,255,0.06), transparent 40%)`
        }" />

      <div :class="ui.layout.clampWidth" class="relative z-20">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 md:mb-24 gap-8">
          <div class="max-w-xl">
            <Motion :initial="{ opacity: 0 }" :while-in-view="{ opacity: 1 }" :viewport="{ once: true }"
              class="flex items-center gap-4 mb-8">
              <div class="w-1 h-4 bg-white/20"></div>
              <span
                class="text-sm text-white/40 font-mono uppercase tracking-widest">{{ content.sections.pricing.label }}</span>
            </Motion>
            <h2 :class="[ui.typography.display, 'text-white mb-6 leading-none']">{{ content.sections.pricing.title }}
            </h2>
            <p :class="ui.typography.body">{{ content.sections.pricing.description }}</p>
          </div>

          <div
            class="bg-white/5 border border-white/10 rounded-full p-1 flex items-center relative w-full max-w-[280px] sm:max-w-xs mx-auto md:mx-0">
            <div
              class="absolute inset-y-1 w-[calc(50%-4px)] bg-white/10 rounded-full transition-all duration-300 ease-out"
              :class="pricingBillingCycle === 'monthly' ? 'left-1' : 'left-[50%] ml-px'"></div>
            <button @click="pricingBillingCycle = 'monthly'"
              class="flex-1 relative z-10 px-4 md:px-6 py-2.5 text-[10px] md:text-xs font-bold uppercase tracking-wider transition-colors"
              :class="pricingBillingCycle === 'monthly' ? 'text-white' : 'text-white/40'">Monthly</button>
            <button @click="pricingBillingCycle = 'yearly'"
              class="flex-1 relative z-10 px-4 md:px-6 py-2.5 text-[10px] md:text-xs font-bold uppercase tracking-wider transition-colors"
              :class="pricingBillingCycle === 'yearly' ? 'text-white' : 'text-white/40'">Yearly</button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto">
          <Motion v-for="(plan, idx) in content.sections.pricing.plans" :key="plan.name"
            :initial="{ y: 50 + idx * 20, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
            :transition="{ delay: idx * 0.1, duration: 0.5 + idx * 0.1 }" :viewport="{ once: true }"
            class="p-8 md:p-10 rounded-2xl border border-white/5 bg-[#050505] flex flex-col relative overflow-hidden group/card hover:border-white/10 transition-colors">

            <div v-if="plan.name.includes('+')"
              class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none opacity-40">
            </div>

            <div class="relative z-10 mb-auto">
              <span
                class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/30 block mb-6">{{ plan.name }}</span>
              <div class="flex items-baseline gap-1 mb-8">
                <span
                  class="text-5xl md:text-6xl font-light tracking-tighter">{{ pricingBillingCycle === 'yearly' && plan.price !== 'Custom' ? '$' + (parseInt(plan.price.replace('$', '')) * 10) : plan.price }}</span>
                <span v-if="plan.period" class="text-white/20 text-sm">/mo</span>
              </div>
              <p class="text-sm text-white/40 leading-relaxed mb-10 min-h-[3em]">{{ plan.description }}</p>

              <ul class="space-y-4 mb-12">
                <li v-for="feat in plan.features" :key="feat" class="flex items-start gap-3 text-[13px] text-white/60">
                  <div class="mt-1.5 w-1 h-1 rounded-full bg-white/20"></div>
                  {{ feat }}
                </li>
              </ul>
            </div>

            <div class="relative z-10">
              <Link v-if="plan.link" :href="plan.link" :class="ui.layout.pricingButton">
                {{ plan.cta }}
              </Link>
              <button v-else disabled :class="ui.layout.pricingButtonDisabled">
                {{ plan.cta }}
              </button>
            </div>
          </Motion>
        </div>
      </div>
    </section>

    <section id="faq" class="py-section border-t border-white/5 bg-[#050505]" :class="ui.layout.sectionPadding">
      <div :class="ui.layout.clampWidth">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-24">
          <div class="md:col-span-4">
            <Motion :initial="{ opacity: 0, y: 20 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
              class="sticky top-32">
              <span class="text-xs font-mono text-white/40 mb-6 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                {{ content.sections.faq.label }}
              </span>
              <h2 :class="[ui.typography.display, 'text-white mb-6 leading-[0.9]']">
                {{ content.sections.faq.title.split(' ')[0] }}<br />
                <span class="text-white/40">{{ content.sections.faq.title.split(' ').slice(1).join(' ') }}</span>
              </h2>
              <Link href="/contact"
                class="inline-flex items-center gap-2 text-sm text-white border-b border-white/20 pb-0.5 hover:border-white transition-colors mt-4">
                Still have questions?
                <ArrowRight class="w-4 h-4 ml-1" />
              </Link>
            </Motion>
          </div>

          <div class="md:col-span-8">
            <div class="border-t border-white/10">
              <div v-for="(item, idx) in content.sections.faq.items" :key="idx"
                class="border-b border-white/10 overflow-hidden group">
                <button @click="faqOpen = faqOpen === idx ? null : idx"
                  class="w-full py-6 md:py-8 flex items-start justify-between text-left focus:outline-none group-hover:bg-white/[0.02] transition-colors relative">
                  <span
                    class="text-xl md:text-2xl font-light tracking-tight text-white/80 group-hover:text-white transition-colors pr-8">
                    {{ item.question }}
                  </span>
                  <span class="relative flex-shrink-0 w-6 h-6 flex items-center justify-center mt-1">
                    <div class="absolute w-full h-[1px] bg-white transition-transform duration-300"
                      :class="faqOpen === idx ? 'rotate-180' : ''"></div>
                    <div class="absolute w-full h-[1px] bg-white transition-transform duration-300"
                      :class="faqOpen === idx ? 'rotate-180 opacity-0' : 'rotate-90'"></div>
                  </span>
                </button>
                <AnimatePresence>
                  <Motion v-if="faqOpen === idx" :initial="{ height: 0, opacity: 0 }"
                    :animate="{ height: 'auto', opacity: 1 }" :exit="{ height: 0, opacity: 0 }"
                    :transition="{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }" class="overflow-hidden">
                    <div class="pb-8 text-white/60 leading-relaxed text-sm max-w-md">
                      {{ item.answer }}
                    </div>
                  </Motion>
                </AnimatePresence>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section
      class="min-h-dvh h-[80dvh] relative flex items-center justify-center overflow-hidden mb-10 mx-6 rounded-sm group">
      <div class="absolute inset-0 bg-black z-10 w-full h-full"></div>
      <Motion class="absolute inset-0 w-full h-full overflow-hidden opacity-60 mix-blend-screen grayscale"
        :while-hover="{ filter: 'grayscale(0%)' }" :transition="{ duration: 2 }">
        <Motion class="w-full h-full" :style="{ y: preFooterImageY, scale: preFooterImageScale }">
          <img :src="content.sections.preFooter.image" alt="Pre footer bg"
            class="w-full h-full object-cover object-center" />
        </Motion>
      </Motion>

      <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-transparent to-transparent z-20">
      </div>

      <div class="relative z-30 w-[clamp(320px,70%,1400px)] mx-auto px-6 text-center">
        <Motion :initial="{ opacity: 0 }"
          :while-in-view="{ opacity: 1, transition: { staggerChildren: 0.2, delayChildren: 0.1 } }"
          :viewport="{ once: true, margin: '-20%' }">
          <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
            :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }">
            <h1 :class="[ui.typography.hero, 'mb-8 md:mb-12']">{{ content.sections.preFooter.title }}</h1>
          </Motion>
          <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
            :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }"
            class="flex flex-col items-center gap-6">
            <Link href="/register" :class="ui.layout.button">
              <Motion class="absolute inset-0 bg-white" :while-hover="{ scale: 1.05 }" />
              <Motion class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent"
                :initial="{ x: '-100%' }" :while-hover="{ x: '100%', transition: { duration: 0.7 } }" />
              <Motion class="absolute inset-0 rounded-lg"
                :while-hover="{ boxShadow: '0 0 40px rgba(255,255,255,0.4)' }" />
              <span class="relative z-10">{{ content.sections.preFooter.cta }}</span>
              <ArrowRight class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1" />
            </Link>
            <p class="text-white/40 text-xs md:text-sm">{{ content.sections.preFooter.subtext }}</p>
          </Motion>
        </Motion>
      </div>
    </section>

    <div class="h-[10dvh] bg-black relative z-20"></div>
    <RevealFooter :content="content" />
  </div>
</template>

<style scoped></style>