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
  Sparkles
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

const loading = ref(true);
const loadingProgress = ref(0);

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

  const interval = setInterval(() => {
    loadingProgress.value += Math.floor(Math.random() * 10) + 1;
    if (loadingProgress.value >= 100) {
      loadingProgress.value = 100;
      clearInterval(interval);
      setTimeout(() => {
        loading.value = false;
      }, 500);
    }
  }, 100);
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
        label: 'Time Unfolded',
        description: 'Automate tasks and reclaim hours, your AI assistant turns routine into seconds so you can focus on growth.',
        image: '/images/featureTime.png'
      },
      {
        id: 'words',
        label: 'Words That Flow',
        description: 'Drafts, blogs, and emails written with clarity and speed — the elegance of language without the struggle.',
        image: '/images/featureWords.png'
      },
      {
        id: 'guide',
        label: 'A Silent Guide',
        description: 'Always present to keep you focused — suggestions, reminders, and insights right when you need them.',
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
    <AnimatePresence>
      <Motion v-if="loading" initial="initial" animate="animate" exit="exit"
        :variants="{ initial: { y: 0 }, exit: { y: '-100%', transition: { duration: 0.8, ease: [0.76, 0, 0.24, 1] } } }"
        class="fixed inset-0 z-[100] bg-black flex items-end justify-between p-8 md:p-12 cursor-wait">
        <Motion :initial="{ opacity: 0 }" :animate="{ opacity: 1 }"
          class="text-white text-5xl md:text-8xl font-medium tracking-tighter">
          {{ loadingProgress }}%
        </Motion>
        <div class="text-xs uppercase tracking-widest text-white/40 animate-pulse">
          Initializing System...
        </div>
      </Motion>
    </AnimatePresence>

    <div class="fixed inset-0 pointer-events-none z-40 opacity-[0.3] mix-blend-overlay"
      style="background-image: url('/images/noise.jpg');">
    </div>

    <nav class="fixed top-6 inset-x-0 z-50 flex justify-center pointer-events-none"
      :style="{ paddingTop: 'var(--sat, 0px)' }">
      <Motion
        class="pointer-events-auto px-6 py-3 rounded-full border border-white/10 flex items-center gap-8 backdrop-blur-md shadow-2xl"
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
            <Link href="/register"
              class="bg-white text-black text-xs font-bold px-3 py-1.5 rounded-full hover:bg-white/90 transition-colors">
              Get Started
            </Link>
          </template>
        </div>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white ml-2">
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
                class="block w-full text-center py-4 bg-white text-black font-medium hover:bg-white/90 transition-colors">
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

            <Motion class="mt-10 md:mt-14" :initial="ui.animations.pageTransition.initial"
              :animate="{ ...ui.animations.pageTransition.enter, transition: { ...ui.animations.pageTransition.enter.transition, delay: 0.45 } }">
              <Link href="/explore">
                <MagneticButton>
                  <span :class="ui.layout.button">
                    Get Started
                    <ArrowRight class="w-5 h-5" />
                  </span>
                </MagneticButton>
              </Link>
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

      <section id="features" ref="featuresSection" class="relative bg-black"
        :style="{ height: `${content.sections.features.length * 100}vh` }">
        <div v-for="(feature, idx) in content.sections.features" :key="feature.id"
          class="sticky top-0 h-screen w-full flex items-center overflow-hidden border-b border-white/5 bg-black overflow-x-hidden">

          <Motion class="relative z-30 w-full" :style="{
            scale: useTransform(scrollY, [featuresScrollRange[0] + idx * vh, featuresScrollRange[0] + (idx + 1) * vh], [1, 0.9]),
            opacity: useTransform(scrollY, [featuresScrollRange[0] + idx * vh, featuresScrollRange[0] + (idx + 1) * vh], [1, 0.5])
          }">
            <div :class="[ui.layout.clampWidth]">

              <template v-if="idx === 0">
                <div
                  class="absolute inset-0 z-0 pointer-events-none -mx-[5vw] md:-mx-[15vw] h-[140%] -top-[20%] opacity-40">
                  <Motion class="w-full h-full" :initial="{ scale: 1.2 }" :while-in-view="{ scale: 1 }"
                    :transition="{ duration: 2, ease: ui.animations.easing.smooth }">
                    <img :src="feature.image" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
                  </Motion>
                </div>

                <Motion initial="initial" while-in-view="enter" :viewport="{ once: true, margin: '-20%' }">
                  <div class="mb-12">
                    <Motion :variants="{ initial: { opacity: 0, x: -20 }, enter: { opacity: 1, x: 0 } }"
                      class="text-xs font-mono text-white/40 tracking-[0.3em] uppercase mb-8 flex items-center gap-4">
                      <span class="w-8 h-[1px] bg-white/20"></span> Feature 01
                    </Motion>
                    <h3 :class="[ui.typography.hero, 'text-4xl md:text-9xl mb-12 max-w-4xl flex flex-wrap gap-x-4']">
                      <div v-for="(word, i) in feature.label.split(' ')" :key="i" class="overflow-hidden">
                        <Motion
                          :variants="{ initial: { y: '100%', rotateZ: 5 }, enter: { y: 0, rotateZ: 0, transition: { duration: 0.8, ease: ui.animations.easing.smooth, delay: i * 0.05 } } }">
                          {{ word }}
                        </Motion>
                      </div>
                    </h3>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-12">
                    <div class="md:col-span-5 md:col-start-8">
                      <p
                        :class="[ui.typography.body, 'text-xl md:text-2xl text-white/70 border-l border-white/20 pl-8']">
                        {{ feature.description }}
                      </p>
                    </div>
                  </div>
                </Motion>
              </template>

              <template v-else-if="idx === 1">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                  <Motion
                    class="relative aspect-video lg:aspect-square bg-white/[0.03] border border-white/10 rounded-lg p-6 overflow-hidden group/terminal"
                    :initial="{ opacity: 0, x: -50 }" :while-in-view="{ opacity: 1, x: 0 }"
                    :transition="{ duration: 1, ease: ui.animations.easing.smooth }">
                    <div class="flex items-center gap-2 mb-8">
                      <div class="w-2 h-2 rounded-full bg-red-500/40"></div>
                      <div class="w-2 h-2 rounded-full bg-yellow-500/40"></div>
                      <div class="w-2 h-2 rounded-full bg-green-500/40"></div>
                    </div>
                    <div class="space-y-4 font-mono text-xs md:text-sm">
                      <Motion v-for="i in 5" :key="i" :initial="{ opacity: 0, x: -10 }"
                        :while-in-view="{ opacity: 1, x: 0 }" :transition="{ delay: 0.1 * i }" class="flex gap-4">
                        <span class="text-white/20">0{{ i }}</span>
                        <span class="text-white/40 italic" v-if="i === 1">Initializing stream...</span>
                        <span class="text-indigo-400" v-else-if="i === 2">const engine = new SemanticAIEngine();</span>
                        <span class="text-purple-400" v-else-if="i === 3">engine.process(inputData);</span>
                        <span class="text-white/80" v-else-if="i === 4">> Words are flowing smoothly.</span>
                        <span class="text-green-400 animate-pulse" v-else>_</span>
                      </Motion>
                    </div>
                    <img :src="feature.image"
                      class="absolute -bottom-20 -right-20 w-3/4 opacity-20 grayscale group-hover/terminal:opacity-40 transition-opacity duration-1000" />
                  </Motion>

                  <Motion initial="initial" while-in-view="enter" :viewport="{ once: true }">
                    <Motion :variants="{ initial: { opacity: 0, x: 20 }, enter: { opacity: 1, x: 0 } }"
                      class="text-xs font-mono text-white/40 tracking-[0.3em] uppercase mb-8">
                      Feature 02
                    </Motion>
                    <h3 :class="[ui.typography.hero, 'text-4xl md:text-7xl mb-12 uppercase italic tracking-tighter']">
                      {{ feature.label }}
                    </h3>
                    <p :class="[ui.typography.body, 'text-xl text-white/60 mb-12 max-w-lg']">
                      {{ feature.description }}
                    </p>
                    <div class="flex items-center gap-6">
                      <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center">
                        <Sparkles class="w-5 h-5 text-indigo-400" />
                      </div>
                      <span class="text-sm font-mono text-white/40">Real-time inference optimized</span>
                    </div>
                  </Motion>
                </div>
              </template>

              <template v-else>
                <div class="flex flex-col items-center text-center">
                  <Motion class="relative w-full max-w-2xl aspect-[16/10] mb-20 overflow-hidden rounded-sm"
                    :initial="{ opacity: 0, scale: 0.8, y: 100 }" :while-in-view="{ opacity: 1, scale: 1, y: 0 }"
                    :transition="{ duration: 1.2, ease: ui.animations.easing.default }">
                    <Motion class="w-full h-full" :while-hover="{ scale: 1.1 }" :transition="{ duration: 1.5 }">
                      <img :src="feature.image"
                        class="w-full h-full object-cover grayscale brightness-75 hover:grayscale-0 transition-all duration-1000" />
                    </Motion>
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                  </Motion>

                  <Motion initial="initial" while-in-view="enter" :viewport="{ once: true }">
                    <Motion :variants="{ initial: { opacity: 0, y: 20 }, enter: { opacity: 1, y: 0 } }"
                      class="text-xs font-mono text-white/40 tracking-[0.3em] uppercase mb-8">
                      Feature 03
                    </Motion>
                    <h3 :class="[ui.typography.hero, 'text-5xl md:text-9xl mb-12 leading-none uppercase']">
                      {{ feature.label.split(' ')[0] }}<br />
                      <span class="text-white/20 italic">{{ feature.label.split(' ').slice(1).join(' ') }}</span>
                    </h3>
                    <p :class="[ui.typography.body, 'text-xl text-white/60 max-w-xl mx-auto']">
                      {{ feature.description }}
                    </p>
                  </Motion>
                </div>
              </template>
            </div>
          </Motion>
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
            class="bg-white/5 border border-white/10 rounded-full p-1 flex items-center relative w-full max-w-[280px] sm:max-w-xs md:mx-0">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
          <Motion v-for="(plan, idx) in content.sections.pricing.plans" :key="plan.name"
            :initial="{ y: 50 + idx * 20, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
            :transition="{ delay: idx * 0.1, duration: 0.5 + idx * 0.1 }" :viewport="{ once: true }"
            class="p-6 md:p-10 border border-white/10 bg-[#080808] relative group/card h-full min-h-[500px] md:min-h-[600px] flex flex-col cursor-default overflow-hidden transition-colors hover:border-white/20"
            :class="{ 'border-white/20 bg-white/[0.02]': plan.name.includes('+') }">

            <div v-if="plan.name.includes('+')"
              class="absolute inset-0 bg-gradient-to-b from-indigo-500/5 via-purple-500/5 to-transparent pointer-events-none opacity-50">
            </div>

            <div class="mb-auto relative z-10">
              <Motion v-if="plan.name.includes('+')" is="h3"
                class="text-xs font-bold uppercase tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 mb-6"
                :animate="{ backgroundPosition: ['0% 50%', '100% 50%', '0% 50%'] }"
                :transition="{ duration: 5, repeat: Infinity, ease: 'linear' }"
                :style="{ backgroundSize: '200% auto' }">
                {{ plan.name }}
              </Motion>
              <h3 v-else class="text-xs font-bold uppercase tracking-[0.2em] text-white/40 mb-6">{{ plan.name }}</h3>

              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-4xl md:text-5xl font-light text-white tracking-tighter">
                  {{ pricingBillingCycle === 'yearly' && plan.price !== 'Custom' ? '$' + (parseInt(plan.price.replace('$', '')) * 10) : plan.price }}
                </span>
                <span v-if="plan.period" class="text-white/40 text-sm font-mono">/
                  {{ pricingBillingCycle === 'yearly' ? 'year' : 'mo' }}</span>
              </div>

              <p class="text-white/60 mb-8 leading-relaxed text-sm min-h-[3em]">{{ plan.description }}</p>
            </div>


            <ul class="space-y-4 mb-12 relative z-10">
              <li v-for="feat in plan.features" :key="feat"
                class="flex items-start gap-3 text-white/70 text-sm group-hover/card:text-white transition-colors">
                <span
                  class="mt-1.5 w-1 h-1 rounded-full bg-white/40 group-hover/card:bg-white transition-colors"></span>
                {{ feat }}
              </li>
            </ul>

            <div class="relative z-10 mt-6">
              <template v-if="plan.link">
                <Link :href="plan.link"
                  class="block w-full text-center py-4 border border-white/20 text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-black transition-all">
                  {{ plan.cta }}
                </Link>
              </template>
              <button v-else :disabled="plan.comingSoon"
                class="block w-full text-center py-4 border border-white/10 text-xs font-bold uppercase tracking-widest text-white/20 cursor-not-allowed">
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
                  class="w-full py-8 flex items-start justify-between text-left focus:outline-none group-hover:bg-white/[0.02] transition-colors relative">
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
              <Motion class="absolute inset-0 rounded-full"
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