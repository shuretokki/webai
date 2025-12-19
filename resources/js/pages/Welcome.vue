<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Motion, AnimatePresence, useMotionValue, useTransform } from 'motion-v';
import { useEventListener } from '@vueuse/core';
import {
  ChevronDown,
  ArrowRight,
  Menu,
  X,
  Plus,
  Instagram,
  Facebook,
  Linkedin,
  Dribbble,
  Sparkles
} from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
  canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);

const scrollY = useMotionValue(0);

if (typeof window !== 'undefined') {
  useEventListener(window, 'scroll', () => {
    scrollY.set(window.scrollY);
  });
}

const heroImageY = useTransform(scrollY, [0, 1000], [0, 800 * 0.8]);
const heroImageScale = useTransform(scrollY, [0, 1000], [1, 1 + 1000 * 0.001]);
const headerY = useTransform(scrollY, [0, 1000], [0, 1000 * 1]);
const headerScale = useTransform(scrollY, [0, 1000], [1, Math.max(0, 1 - 1000 * 0.0008)]);
const headerBlur = useTransform(scrollY, [0, 500], ['blur(0px)', 'blur(10px)']);
const headerOpacity = useTransform(scrollY, [0, 500], [1, 0]);

const preFooterImageY = useTransform(scrollY, value => -50 + value * 0.05);
const preFooterImageScale = useTransform(scrollY, value => 1 + value * 0.0001);

const navBackground = useTransform(
  scrollY,
  [0, 50],
  ['rgba(0, 0, 0, 0)', 'rgba(0, 0, 0, 0.8)']
);
const navBorder = useTransform(
  scrollY,
  [0, 50],
  ['rgba(255, 255, 255, 0)', 'rgba(255, 255, 255, 0.05)']
);
const navBackdrop = useTransform(
  scrollY,
  [0, 50],
  ['blur(0px)', 'blur(12px)']
);


const nav = [
  { label: 'About', href: '/about' },
  { label: 'Pricing', href: '#pricing' },
  { label: 'Blog', href: '/blog' },
  { label: 'Changelog', href: '/changelog' },
];

const footer = {
  index: [
    { label: 'Home', href: '/' },
    { label: 'Work', href: '#' },
    { label: 'About', href: '/about' },
    { label: 'Services', href: '#' },
    { label: 'Privacy Policy', href: '/privacy' },
  ],
  social: [
    { label: 'Instagram', href: '#', icon: Instagram },
    { label: 'Facebook', href: '#', icon: Facebook },
    { label: 'LinkedIn', href: '#', icon: Linkedin },
    { label: 'Awwwards', href: '#' },
    { label: 'Behance', href: '#', icon: Dribbble },
  ]
};

const faqs = ref([
  {
    question: "What is this AI platform designed for?",
    answer: "It helps you generate, test, and deploy ideas with advanced AI models — all in one simple workspace.",
    open: true
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
]);

const toggleFaq = (index: number) => {
  faqs.value[index].open = !faqs.value[index].open;
};

const navItemHover = { scale: 1.05, color: '#ffffff' };
const buttonHover = { scale: 1.05, transition: { duration: 0.2 } };

</script>

<template>

  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div class="min-h-screen bg-black text-white font-sans selection:bg-white/20 overflow-x-hidden relative">

    <div class="fixed inset-0 pointer-events-none z-40 opacity-[0.8] mix-blend-overlay"
      style="background-image: url('/images/noise.jpg');">
    </div>

    <!-- Motion Navbar -->
    <Motion is="nav" class="fixed top-0 left-0 right-0 z-50 border-b"
      :style="{ backgroundColor: navBackground, borderColor: navBorder, backdropFilter: navBackdrop }">
      <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between md:justify-center gap-8">
        <Link href="/" class="flex items-center gap-2 group">
          <AppLogoIcon class="w-5 h-5 text-white/90 group-hover:text-white transition-colors" />
          <span class="font-medium text-lg tracking-tight">Ecnelis</span>
        </Link>

        <div class="hidden md:flex items-center gap-8">
          <template v-for="item in nav" :key="item.label">
            <Link v-if="item.href.startsWith('/')" :href="item.href"
              class="text-sm font-medium cursor-pointer relative">
              <Motion is="span" class="block" :style="{ color: 'rgba(255, 255, 255, 0.7)' }"
                :while-hover="{ color: '#ffffff', scale: 1.05 }" :transition="{ duration: 0.2 }">
                {{ item.label }}
              </Motion>
            </Link>
            <a v-else :href="item.href" class="text-sm font-medium cursor-pointer relative">
              <Motion is="span" class="block" :style="{ color: 'rgba(255, 255, 255, 0.7)' }"
                :while-hover="{ color: '#ffffff', scale: 1.05 }" :transition="{ duration: 0.2 }">
                {{ item.label }}
              </Motion>
            </a>
          </template>
        </div>

        <div class="hidden md:flex items-center gap-4">
          <template v-if="$page.props.auth.user">
            <Link href="/explore">
              <Motion is="span" class="text-sm font-medium block" :style="{ color: 'rgba(255, 255, 255, 0.7)' }"
                :while-hover="{ color: '#ffffff' }">
                Explore
              </Motion>
            </Link>
          </template>
          <template v-else>
            <Link href="/login">
              <Motion is="span" class="text-sm font-medium text-white block cursor-pointer"
                :while-hover="{ opacity: 0.8 }">
                Log in
              </Motion>
            </Link>
          </template>
        </div>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden z-50 text-white p-2">
          <Menu v-if="!mobileMenuOpen" />
          <X v-else />
        </button>
      </div>
    </Motion>

    <AnimatePresence>
      <Motion v-if="mobileMenuOpen" :initial="{ opacity: 0, y: -20 }" :animate="{ opacity: 1, y: 0 }"
        :exit="{ opacity: 0, y: -20 }" class="fixed inset-0 z-40 bg-black pt-28 px-6 md:hidden">
        <div class="flex flex-col gap-8 text-2xl font-light">
          <a v-for="item in nav" :key="item.label" :href="item.href" @click="mobileMenuOpen = false"
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

    <section class="min-h-screen flex items-center justify-center relative overflow-hidden group">
      <Motion class="absolute inset-0 z-0 overflow-hidden" :style="{ y: heroImageY }">
        <div class="absolute inset-0 bg-black/70 z-10 w-full h-full"></div>

        <Motion class="w-full h-[120%] will-change-transform" :style="{ scale: heroImageScale }">
          <img src="/images/heroSection.jpg" class="w-full h-full object-cover object-center opacity-80 ease-out" />
        </Motion>
        <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-black via-black/90 to-transparent z-20">
        </div>
      </Motion>

      <div class="relative z-20 text-center px-6 max-w-[90rem] mx-auto mt-20 w-full">
        <Motion initial="initial" animate="enter" :variants="{ enter: { transition: { staggerChildren: 0.2 } } }">
          <Motion class="mb-10 md:mb-16" :initial="{ opacity: 0, y: 40, filter: 'blur(10px)' }"
            :animate="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] as const } }">
            <Motion is="h1"
              class="text-hero font-medium tracking-tighter leading-[0.85] origin-center will-change-transform text-white"
              :style="{ y: headerY, scale: headerScale, filter: headerBlur, opacity: headerOpacity }">
              Where <span
                class="font-serif italic text-transparent bg-clip-text bg-gradient-to-b from-white to-white/40 mx-2">thoughts</span>
              <br class="md:hidden" />
              become <span
                class="font-serif italic text-transparent bg-clip-text bg-gradient-to-b from-white to-white/40 mx-2">actions</span>.
            </Motion>
          </Motion>

          <Motion class="max-w-xl md:max-w-3xl mx-auto text-center"
            :initial="{ opacity: 0, y: 40, filter: 'blur(10px)' }"
            :animate="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] as const, delay: 0.2 } }">
            <Motion is="p" class="text-lg md:text-xl font-light text-white/60 leading-relaxed md:leading-normal"
              :style="{ y: headerY, scale: headerScale, opacity: headerOpacity }">
              An AI companion that <span class="text-white/90">whispers clarity</span>, <br class="hidden md:block" />
              conjures ideas, and guides your every move into the void.
            </Motion>
          </Motion>
        </Motion>
      </div>

      <Motion
        class="absolute bottom-12 left-1/2 -translate-x-1/2 text-white/40 text-xs uppercase tracking-[0.2em] flex flex-col items-center gap-4 z-20"
        :style="{ y: headerY, scale: headerScale, opacity: headerOpacity }">
        Scroll to explore
        <Motion :animate="{ y: [0, 10, 0] }" :transition="{ duration: 2, repeat: Infinity, ease: 'easeInOut' }">
          <ChevronDown class="w-5 h-5 opacity-50" />
        </Motion>
      </Motion>
    </section>

    <div class="bg-black relative z-10 shadow-[0_-50px_100px_rgba(0,0,0,1)]">

      <section class="pt-section px-6">
        <div class="max-w-[1400px] mx-auto">
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
            <span class="text-sm text-white/40 font-medium">Introducing Message</span>
          </Motion>

          <Motion :initial="{ opacity: 0, y: 50, filter: 'blur(10px)' }"
            :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)' }" :viewport="{ once: true }"
            :transition="{ duration: 1 }">
            <h2 class="text-display font-light tracking-tight leading-[1.1] text-white/90 max-w-4xl mb-12 md:mb-24">
              Harness invisible power to write faster, <br class="hidden md:block" />
              focus deeper, and save hours.
            </h2>
          </Motion>
        </div>
      </section>

      <section id="features" class="pb-section px-6">
        <div class="max-w-[1400px] mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <Motion :initial="{ opacity: 0, y: 50 }"
              :while-in-view="{ opacity: 1, y: 0, transition: { duration: 0.8, ease: 'easeOut' as const } }"
              :viewport="{ once: true }" class="group cursor-default">
              <Motion class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5"
                :while-hover="{ borderColor: 'rgba(255,255,255,0.1)' }">

                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <Motion class="w-full h-full" :while-hover="{ scale: 1.1 }" :transition="{ duration: 1 }">
                    <img src="/images/featureTime.jpg"
                      class="w-full h-full object-cover opacity-50 grayscale mix-blend-screen" alt="Time Unfolded" />
                  </Motion>
                </div>
                <div class="absolute inset-0 flex items-center justify-center p-8">
                  <div class="w-full space-y-4 opacity-90">
                    <div
                      class="bg-zinc-800/80 backdrop-blur px-4 py-2 rounded text-xs text-center border border-white/10 w-max mx-auto">
                      8:00 AM Trigger</div>
                    <div class="w-0.5 h-4 bg-white/20 mx-auto"></div>
                    <div
                      class="bg-black/80 backdrop-blur px-4 py-3 rounded text-sm border border-white/10 flex items-center gap-2">
                      <div class="w-3 h-3 border border-white/50"></div> Fetch stats
                    </div>
                    <div
                      class="bg-black/80 backdrop-blur px-4 py-3 rounded text-sm border border-white/10 flex items-center gap-2">
                      <Sparkles class="w-3 h-3" /> AI Summary
                    </div>
                  </div>
                </div>
              </Motion>
              <h3 class="text-title font-light tracking-tight text-white mb-3">Time Unfolded</h3>
              <p class="text-white/50">
                Automate tasks and reclaim hours, your AI assistant turns routine into seconds so you can focus on
                growth.
              </p>
            </Motion>

            <Motion :initial="{ opacity: 0, y: 50 }"
              :while-in-view="{ opacity: 1, y: 0, transition: { duration: 0.8, ease: 'easeOut' as const } }"
              :viewport="{ once: true, margin: '-50px' }" :transition="{ delay: 0.2 }" class="group cursor-default">
              <Motion class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5"
                :while-hover="{ borderColor: 'rgba(255,255,255,0.1)' }">
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <Motion class="w-full h-full" :while-hover="{ scale: 1.1 }" :transition="{ duration: 1 }">
                    <img src="/images/featureWords.jpg"
                      class="w-full h-full object-cover opacity-30 grayscale mix-blend-screen" alt="Words That Flow" />
                  </Motion>
                </div>
                <div class="absolute inset-0 flex items-center justify-center p-8">
                  <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-lg w-full max-w-xs shadow-2xl relative">
                    <div class="w-3 h-3 rounded-full bg-white/20 mb-3"></div>
                    <div class="h-2 bg-white/10 rounded w-3/4 mb-2"></div>
                    <div class="h-2 bg-white/10 rounded w-full mb-2"></div>
                    <div class="h-2 bg-white/10 rounded w-5/6"></div>
                  </div>
                </div>
              </Motion>
              <h3 class="text-2xl font-light text-white mb-3">Words That Flow</h3>
              <p class="text-white/50 leading-relaxed text-sm">
                Drafts, blogs, and emails written with clarity and speed — the elegance of language without the
                struggle.
              </p>
            </Motion>

            <Motion :initial="{ opacity: 0, y: 50 }"
              :while-in-view="{ opacity: 1, y: 0, transition: { duration: 0.8, ease: 'easeOut' as const } }"
              :viewport="{ once: true, margin: '-100px' }" :transition="{ delay: 0.4 }" class="group cursor-default">
              <Motion class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5"
                :while-hover="{ borderColor: 'rgba(255,255,255,0.1)' }">
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <Motion class="w-full h-full" :while-hover="{ scale: 1.1 }" :transition="{ duration: 1 }">
                    <img src="/images/featureGuide.jpg"
                      class="w-full h-full object-cover opacity-40 grayscale mix-blend-screen" alt="Silent Guide" />
                  </Motion>
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 p-8">
                  <div class="flex gap-2 justify-end w-full max-w-[200px]">
                    <div class="bg-white text-black text-[10px] uppercase font-bold px-2 py-0.5 rounded-sm">
                      Unfold</div>
                  </div>
                  <div class="bg-zinc-900/90 border border-white/10 p-4 rounded-sm w-full max-w-[200px] backdrop-blur">
                    <p class="text-xs text-white/80">Ready to turn thoughts into actions?</p>
                  </div>
                  <div class="flex gap-2 justify-end w-full max-w-[200px]">
                    <div class="bg-white text-black text-[10px] uppercase font-bold px-2 py-0.5 rounded-sm">
                      Capture</div>
                  </div>
                </div>
              </Motion>
              <h3 class="text-2xl font-light text-white mb-3">A Silent Guide</h3>
              <p class="text-white/50 leading-relaxed text-sm">
                Always present to keep you focused — suggestions, reminders, and insights right when you need them.
              </p>
            </Motion>

          </div>
        </div>
      </section>

      <section id="pricing" class="py-section px-6 border-b border-white/5">
        <div class="max-w-[1400px] mx-auto">
          <Motion :initial="{ opacity: 0 }" :while-in-view="{ opacity: 1 }" :viewport="{ once: true }"
            class="flex items-center gap-4 mb-12">
            <div class="w-2 h-2 rounded-full bg-white/20"></div>
            <span class="text-sm text-white/40 font-medium">Introducing Benefit</span>
          </Motion>

          <div class="mb-12 md:mb-24">
            <h2 class="text-display font-light tracking-tight leading-[1.1] text-white mb-4 md:mb-6">Simple, Transparent
              Pricing</h2>
            <p class="text-base text-white/60">Choose the plan that's right for you. No hidden fees.</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <Motion :initial="{ y: 50, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
              :transition="{ delay: 0.1, duration: 0.5 }" :viewport="{ once: true }"
              class="p-8 border border-white/10 bg-[#111] relative group h-full cursor-default"
              :while-hover="{ borderColor: 'rgba(255,255,255,0.2)' }">
              <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Ecnelis</h3>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-white" style="font-size: var(--text-title);">$0</span>
                <span class="text-white/40">/mo</span>
              </div>
              <p class="text-white/60 mb-8 h-12">Perfect for getting started with AI</p>

              <ul class="space-y-4 mb-8">
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Access to standard models
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> 50 messages / month
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Community support
                </li>
              </ul>

              <Link href="/explore">
                <Motion
                  class="block w-full py-3 px-4 border border-white/20 text-center text-white text-sm font-medium rounded-sm"
                  :while-hover="{ backgroundColor: '#ffffff', color: '#000000', borderColor: '#ffffff' }">
                  Start Building
                </Motion>
              </Link>
            </Motion>

            <Motion :initial="{ y: 70, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
              :transition="{ delay: 0.2, duration: 0.6 }" :viewport="{ once: true }"
              class="p-8 border border-white/20 bg-white/5 relative group h-full scale-[1.02]">
              <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>

              <div
                class="absolute inset-0 bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
                <div class="bg-white text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">Coming Soon
                </div>
                <p class="text-white/80 font-medium">We're finalizing the details.</p>
              </div>

              <Motion is="h3"
                class="text-sm font-bold uppercase tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 mb-4"
                :animate="{ backgroundPosition: ['0% 50%', '100% 50%', '0% 50%'] }"
                :transition="{ duration: 4, repeat: Infinity, ease: 'linear' }"
                :style="{ backgroundSize: '200% auto' }">
                Ecnelis+
              </Motion>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-white" style="font-size: var(--text-title);">$20</span>
                <span class="text-white/40">/mo</span>
              </div>
              <p class="text-white/60 mb-8 h-12">For power users who need more available resources.</p>

              <ul class="space-y-4 mb-8">
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Access to premium models
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Unlimited messages
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Priority support
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Fast processing speed
                </li>
              </ul>

              <button disabled
                class="block w-full py-3 px-4 bg-white text-black text-center text-sm font-bold hover:bg-white/90 transition-colors rounded-sm cursor-not-allowed opacity-50">
                Join Waitlist
              </button>
            </Motion>

            <Motion :initial="{ y: 90, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
              :transition="{ delay: 0.3, duration: 0.6 }" :viewport="{ once: true }"
              class="p-8 border border-white/10 bg-[#111] relative group h-full"
              :while-hover="{ borderColor: 'rgba(255,255,255,0.2)' }">

              <div
                class="absolute inset-0 bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
                <div class="bg-white text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">Coming Soon
                </div>
                <p class="text-white/80 font-medium">We're finalizing the details.</p>
              </div>

              <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Enterprise</h3>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-white" style="font-size: var(--text-title);">Custom</span>
              </div>
              <p class="text-white/60 mb-8 h-12">For organizations with custom needs to scale.</p>

              <ul class="space-y-4 mb-8">
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Custom model fine-tuning
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> Dedicated support manager
                </li>
                <li class="flex items-center gap-3 text-white/80 text-sm">
                  <div class="w-1.5 h-1.5 rounded-full bg-white"></div> SLA guarantees
                </li>
              </ul>

              <button disabled
                class="block w-full py-3 px-4 border border-white/20 text-center text-white text-sm font-medium hover:bg-white hover:text-black transition-colors rounded-sm cursor-not-allowed opacity-50">
                Contact Sales
              </button>
            </Motion>
          </div>
        </div>
      </section>

      <section id="faq" class="py-section px-6 border-t border-white/5 bg-[#050505]">
        <div class="max-w-[1000px] mx-auto">
          <div class="text-center mb-12 md:mb-24">
            <span class="text-xs font-mono text-white/40 mb-4 block uppercase tracking-widest">Support</span>
            <h2 class="text-display font-light tracking-tight leading-[1.1] text-white/90">
              Frequently asked questions.
            </h2>
          </div>

          <div class="space-y-4">
            <div v-for="(faq, index) in faqs" :key="index"
              class="border-t border-white/10 bg-transparent transition-colors">
              <button @click="toggleFaq(index)" class="w-full flex items-center justify-between py-6 text-left group">
                <Motion is="span" class="font-light text-white/90 transition-colors"
                  :style="{ fontSize: 'var(--text-lg)' }" :while-hover="{ color: '#fff' }">{{ faq.question }}</Motion>
                <span class="text-white/30 group-hover:text-white transition-colors relative">
                  <Plus v-if="!faq.open" class="w-5 h-5" />
                  <X v-else class="w-5 h-5" />
                </span>
              </button>
              <AnimatePresence>
                <Motion v-if="faq.open" :initial="{ height: 0, opacity: 0 }" :animate="{ height: 'auto', opacity: 1 }"
                  :exit="{ height: 0, opacity: 0 }" class="overflow-hidden">
                  <div class="pb-8">
                    <div class="text-white/60 leading-relaxed max-w-2xl">
                      {{ faq.answer }}
                    </div>
                  </div>
                </Motion>
              </AnimatePresence>
            </div>
          </div>
        </div>
      </section>

      <section
        class="min-h-screen relative flex items-center justify-center overflow-hidden mb-10 mx-6 rounded-sm group">
        <div class="absolute inset-0 bg-black z-10 w-full h-full"></div>

        <Motion class="absolute inset-0 w-full h-full overflow-hidden opacity-60 mix-blend-screen grayscale"
          :while-hover="{ filter: 'grayscale(0%)' }" :transition="{ duration: 2 }">
          <Motion class="w-full h-full" :style="{ y: preFooterImageY, scale: preFooterImageScale }">
            <img src="/images/preFooter.jpg" alt="Pre footer bg" class="w-full h-full object-cover object-center" />
          </Motion>
        </Motion>

        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-transparent to-transparent z-20">
        </div>

        <div class="relative z-30 max-w-5xl mx-auto px-6 text-center">
          <Motion :initial="{ opacity: 0 }"
            :while-in-view="{ opacity: 1, transition: { staggerChildren: 0.2, delayChildren: 0.1 } }"
            :viewport="{ once: true, margin: '-20%' }">

            <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
              :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }">
              <h1 class="text-hero font-medium tracking-tighter leading-[0.9] text-white mb-8 md:mb-12">
                Shape the <br />
                <span
                  class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white/80 to-white/40 italic font-serif">
                  Future.
                </span>
              </h1>
            </Motion>

            <Motion :initial="{ opacity: 0, y: 20, filter: 'blur(10px)' }"
              :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)', transition: { duration: 1, ease: [0.16, 1, 0.3, 1] } }"
              class="flex flex-col items-center gap-6">
              <Link href="/register"
                class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 md:px-12 md:py-6 bg-white text-black text-base md:text-lg font-medium rounded-full overflow-hidden">

                <Motion class="absolute inset-0 bg-white" :while-hover="{ scale: 1.05 }" />
                <Motion class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent"
                  :initial="{ x: '-100%' }" :while-hover="{ x: '100%', transition: { duration: 0.7 } }" />
                <Motion class="absolute inset-0 rounded-full"
                  :while-hover="{ boxShadow: '0 0 40px rgba(255,255,255,0.4)' }" />

                <span class="relative z-10">Get Started Now</span>
                <ArrowRight class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1" />
              </Link>
              <p class="text-white/40 text-xs md:text-sm">No credit card required for standard plans.</p>
            </Motion>

          </Motion>
        </div>
      </section>

      <footer class="bg-black text-white px-6 pt-32 pb-4 relative overflow-hidden">
        <div class="max-w-[1600px] mx-auto relative z-10 font-sans">

          <div class="grid grid-cols-1 md:grid-cols-8 gap-12 mb-32">
            <div class="md:col-span-2">
              <h4 class="text-sm text-white/50 mb-8 font-normal tracking-wide">Site index</h4>
              <ul class="space-y-3 text-sm text-white/80">
                <li v-for="link in footer.index" :key="link.label">
                  <Link :href="link.href">
                    <Motion is="span" class="inline-block" :while-hover="{ color: '#fff', x: 2 }">{{ link.label }}
                    </Motion>
                  </Link>
                </li>
              </ul>
            </div>

            <div class="md:col-span-2">
              <h4 class="text-sm text-white/50 mb-8 font-normal tracking-wide">Social</h4>
              <ul class="space-y-3 text-sm text-white/80">
                <li v-for="link in footer.social" :key="link.label">
                  <a :href="link.href" class="flex items-center gap-2 group">
                    <Motion is="span" class="flex items-center gap-2" :while-hover="{ color: '#fff', x: 2 }">
                      {{ link.label }}
                    </Motion>
                  </a>
                </li>
              </ul>
            </div>

            <div class="md:col-span-4 flex flex-col items-start md:items-end md:text-right">

              <div class="w-full text-left md:text-right space-y-6 text-sm text-white/50 font-light">
                <p class="leading-relaxed">
                  Tell us about your project.<br>
                  Let's collaborate.
                </p>
                <p class="text-white text-base tracking-wide">+27 (0) 78 054 8476</p>
                <div class="flex flex-col md:items-end gap-2 text-white/70">
                  <Motion is="a" href="#" :while-hover="{ color: '#fff' }">Write Us</Motion>
                  <Motion is="a" href="#" :while-hover="{ color: '#fff' }">Newsletter Signup</Motion>
                </div>
                <p class="pt-8 opacity-30">20:40:16 (GMT+2)</p>
              </div>
            </div>
          </div>

          <div class="border-t border-white/10 pt-4 md:pt-8 flex justify-center overflow-hidden w-full">
            <h1
              class="text-[19vw] md:text-[20vw] leading-[0.8] font-semibold tracking-tighter text-center uppercase whitespace-nowrap select-none pointer-events-none w-full block text-transparent"
              style="-webkit-text-stroke: 1px rgba(255, 255, 255, 0.2);">
              Ecnelis
            </h1>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped></style>
