<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, watchEffect, computed } from 'vue';
import { Motion, AnimatePresence } from 'motion-v';
import { useWindowScroll } from '@vueuse/core';
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
const { y } = useWindowScroll();

const parallaxY = ref(0);
const headerScale = ref(1);
const headerBlur = ref(0);

watchEffect(() => {
  parallaxY.value = y.value * 0.2;
  headerScale.value = Math.max(1, 1 - (y.value * 0.0008));
  headerBlur.value = Math.min(10, y.value * 0.02);
});

const navItems = [
  { label: 'About', href: '/about' },
  { label: 'Pricing', href: '#pricing' },
  { label: 'Blog', href: '/blog' },
  { label: 'Changelog', href: '/changelog' },
];

const footerLinks = {
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

/*
|--------------------------------------------------------------------------
| Animation Variants
|--------------------------------------------------------------------------
|
| Define all motion-v animation variants here for reusability and consistency
| across the component. These objects define the initial (offscreen) and
| target (onscreen/enter) states for animations.
|
*/

const fadeInUp = {
  initial: { opacity: 0, y: 40, filter: 'blur(10px)' },
  enter: {
    opacity: 1,
    y: 0,
    filter: 'blur(0px)',
    transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] as const }
  }
};

const staggerContainer = {
  enter: { transition: { staggerChildren: 0.15 } }
};

const slideUp = {
  offscreen: { opacity: 0, y: 50 },
  onscreen: { opacity: 1, y: 0, transition: { duration: 0.8, ease: "easeOut" as const } }
};

const zoomIn = {
  offscreen: { scale: 0.95, opacity: 0.5 },
  onscreen: { scale: 1, opacity: 1, transition: { duration: 0.6, ease: "easeOut" as const } }
};

const lineDraw = {
  offscreen: { pathLength: 0, opacity: 0 },
  onscreen: { pathLength: 1, opacity: 1, transition: { duration: 1.5, ease: "easeInOut" as const } }
};

const preFooterContainer = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.2,
      delayChildren: 0.1
    }
  }
};

const preFooterItem = {
  hidden: { opacity: 0, y: 20, filter: 'blur(10px)' },
  visible: {
    opacity: 1,
    y: 0,
    filter: 'blur(0px)',
    transition: { duration: 1, ease: [0.16, 1, 0.3, 1] as const }
  }
};

</script>

<template>

  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div class="min-h-screen bg-black text-white font-sans selection:bg-white/20 overflow-x-hidden relative">

    <div class="fixed inset-0 pointer-events-none z-[60] opacity-[0.8] mix-blend-overlay"
      style="background-image: url('/images/noise.jpg');">
    </div>

    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 border-b border-transparent"
      :class="{ 'bg-black/80 backdrop-blur-md border-white/5': y > 50 }">
      <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between md:justify-center gap-8">
        <Link href="/" class="flex items-center gap-2 group">
          <AppLogoIcon class="w-5 h-5 text-white/90 group-hover:text-white transition-colors" />
          <span class="font-medium text-lg tracking-tight">Ecnelis</span>
        </Link>

        <div class="hidden md:flex items-center gap-8">
          <a v-for="item in navItems" :key="item.label" :href="item.href"
            class="text-sm font-medium text-white/70 hover:text-white transition-colors">
            {{ item.label }}
          </a>
        </div>

        <div class="hidden md:flex items-center gap-4">
          <template v-if="$page.props.auth.user">
            <Link href="/explore" class="text-sm font-medium text-white/70 hover:text-white transition-colors">
              Explore
            </Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-sm font-medium text-white hover:text-white/80 transition-colors">
              Log in
            </Link>
          </template>
        </div>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden z-50 text-white p-2">
          <Menu v-if="!mobileMenuOpen" />
          <X v-else />
        </button>
      </div>
    </nav>

    <AnimatePresence>
      <Motion v-if="mobileMenuOpen" :initial="{ opacity: 0, y: -20 }" :animate="{ opacity: 1, y: 0 }"
        :exit="{ opacity: 0, y: -20 }" class="fixed inset-0 z-40 bg-black pt-28 px-6 md:hidden">
        <div class="flex flex-col gap-8 text-2xl font-light">
          <a v-for="item in navItems" :key="item.label" :href="item.href" @click="mobileMenuOpen = false"
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
      <div class="absolute inset-0 z-0 overflow-hidden" :style="{ transform: `translateY(${y * 0.8}px)` }">
        <div class="absolute inset-0 bg-black/70 z-10 w-full h-full"></div>

        <img src="/images/heroSection.jpg" :style="{ transform: `scale(${1 + y * 0.001})` }"
          class="w-full h-[120%] object-cover object-center opacity-80 ease-out will-change-transform" />
        <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-black via-black/90 to-transparent z-20">
        </div>
      </div>

      <div class="relative z-20 text-center px-6 max-w-7xl mx-auto mt-20 w-full">
        <Motion initial="initial" animate="enter" :variants="staggerContainer">
          <Motion :variants="fadeInUp" class="mb-16 md:mb-24">
            <h1 :style="{
              transform: `translateY(${y * 1}px) scale(${headerScale})`,
              filter: `blur(${headerBlur}px)`
            }"
              class="text-5xl md:text-8xl lg:text-[9rem] font-medium tracking-tighter text-white leading-[0.9] origin-center will-change-transform">
              Where <span class="font-serif italic opacity-90 mx-2">thoughts</span> <br />
              become <span class="font-serif italic opacity-90 mx-2">actions</span>.
            </h1>
          </Motion>

          <Motion :variants="fadeInUp" class="md:max-w-2xl mx-auto text-center">
            <p :style="{
              transform: `translateY(${y * 1}px) scale(${headerScale})`,
              opacity: Math.max(0, 1 - (y * 0.002))
            }" class="text-xl md:text-2xl text-white/80 font-light leading-relaxed">
              An AI companion that whispers clarity, conjures ideas, and guides your every move.
            </p>
          </Motion>
        </Motion>
      </div>

      <div :style="{
        transform: `translateY(${y * 1}px) scale(${headerScale})`,
        opacity: Math.max(0, 1 - (y * 0.002))
      }"
        class="absolute bottom-12 left-1/2 -translate-x-1/2 text-white/40 text-xs uppercase tracking-[0.2em] flex flex-col items-center gap-4 z-20">
        Scroll to explore
        <Motion :animate="{ y: [0, 10, 0] }" :transition="{ duration: 2, repeat: Infinity, ease: 'easeInOut' }">
          <ChevronDown class="w-5 h-5 opacity-50" />
        </Motion>
      </div>
    </section>

    <div class="bg-black relative z-10 shadow-[0_-50px_100px_rgba(0,0,0,1)]">

      <section class="pt-32 px-6">
        <div class="max-w-[1400px] mx-auto">
          <Motion :initial="false" :while-in-view="{ opacity: 1, x: 0 }" viewport="{ once: true, margin: '-100px' }"
            class="flex items-center gap-4 mb-12">

            <div class="w-12 h-[1px] relative">
              <Motion is="svg" viewBox="0 0 48 1" class="absolute inset-0 w-full h-full text-white/40">
                <Motion is="line" x1="0" y1="0.5" x2="48" y2="0.5" stroke="currentColor" stroke-width="1"
                  :variants="lineDraw" initial="offscreen" while-in-view="onscreen" :viewport="{ once: true }" />
              </Motion>
            </div>
            <span class="text-sm text-white/40 font-medium">Introducing Message</span>
          </Motion>

          <Motion :initial="{ opacity: 0, y: 50, filter: 'blur(10px)' }"
            :while-in-view="{ opacity: 1, y: 0, filter: 'blur(0px)' }" :viewport="{ once: true }"
            :transition="{ duration: 1 }">
            <h2 class="text-4xl md:text-6xl font-light text-white/90 max-w-4xl leading-[1.1] tracking-tight mb-20">
              Harness invisible power to write faster, <br class="hidden md:block" />
              focus deeper, and save hours.
            </h2>
          </Motion>
        </div>
      </section>

      <section id="features" class="pb-32 px-6">
        <div class="max-w-[1400px] mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <Motion :variants="slideUp" initial="offscreen" while-in-view="onscreen" viewport="{ once: true }"
              class="group">
              <div
                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">

                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <img src="/images/featureTime.jpg"
                    class="w-full h-full object-cover opacity-50 grayscale mix-blend-screen transition-transform duration-1000 group-hover:scale-110"
                    alt="Time Unfolded" />
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
              </div>
              <h3 class="text-2xl font-light text-white mb-3">Time Unfolded</h3>
              <p class="text-white/50 leading-relaxed text-sm">
                Automate tasks and reclaim hours, your AI assistant turns routine into seconds so you can focus on
                growth.
              </p>
            </Motion>

            <Motion :variants="slideUp" initial="offscreen" while-in-view="onscreen"
              :viewport="{ once: true, margin: '-50px' }" :transition="{ delay: 0.2 }" class="group">
              <div
                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <img src="/images/featureWords.jpg"
                    class="w-full h-full object-cover opacity-30 grayscale mix-blend-screen transition-transform duration-1000 group-hover:scale-110"
                    alt="Words That Flow" />
                </div>
                <div class="absolute inset-0 flex items-center justify-center p-8">
                  <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-lg w-full max-w-xs shadow-2xl relative">
                    <div class="w-3 h-3 rounded-full bg-white/20 mb-3"></div>
                    <div class="h-2 bg-white/10 rounded w-3/4 mb-2"></div>
                    <div class="h-2 bg-white/10 rounded w-full mb-2"></div>
                    <div class="h-2 bg-white/10 rounded w-5/6"></div>
                  </div>
                </div>
              </div>
              <h3 class="text-2xl font-light text-white mb-3">Words That Flow</h3>
              <p class="text-white/50 leading-relaxed text-sm">
                Drafts, blogs, and emails written with clarity and speed — the elegance of language without the
                struggle.
              </p>
            </Motion>

            <Motion :variants="slideUp" initial="offscreen" while-in-view="onscreen"
              :viewport="{ once: true, margin: '-100px' }" :transition="{ delay: 0.4 }" class="group">
              <div
                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">
                <div class="absolute inset-0 w-full h-full overflow-hidden">
                  <img src="/images/featureGuide.jpg"
                    class="w-full h-full object-cover opacity-40 grayscale mix-blend-screen transition-transform duration-1000 group-hover:scale-110"
                    alt="Silent Guide" />
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
              </div>
              <h3 class="text-2xl font-light text-white mb-3">A Silent Guide</h3>
              <p class="text-white/50 leading-relaxed text-sm">
                Always present to keep you focused — suggestions, reminders, and insights right when you need them.
              </p>
            </Motion>

          </div>
        </div>
      </section>

      <section id="pricing" class="py-24 px-6 border-b border-white/5">
        <div class="max-w-[1400px] mx-auto">
          <Motion :initial="{ opacity: 0 }" :while-in-view="{ opacity: 1 }" :viewport="{ once: true }"
            class="flex items-center gap-4 mb-12">
            <div class="w-2 h-2 rounded-full bg-white/20"></div>
            <span class="text-sm text-white/40 font-medium">Introducing Benefit</span>
          </Motion>

          <div class=" mb-16">
            <h2 class="text-3xl md:text-5xl font-light text-white mb-6">Simple, Transparent Pricing</h2>
            <p class="text-white/60 text-lg">Choose the plan that's right for you. No hidden fees.</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <!-- Pricing cards slide from bottom -->
            <Motion :initial="{ y: 50, opacity: 0 }" :while-in-view="{ y: 0, opacity: 1 }"
              :transition="{ delay: 0.1, duration: 0.5 }" :viewport="{ once: true }"
              class="p-8 border border-white/10 bg-[#111] hover:border-white/20 transition-colors relative group h-full">
              <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Ecnelis</h3>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-4xl font-bold text-white">$0</span>
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

              <Link href="/explore"
                class="block w-full py-3 px-4 border border-white/20 text-center text-white text-sm font-medium hover:bg-white hover:text-black transition-colors rounded-sm">
                Start Building
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

              <h3
                class="text-sm font-bold uppercase tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 animate-gradient mb-4">
                Ecnelis+</h3>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-4xl font-bold text-white">$20</span>
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
              class="p-8 border border-white/10 bg-[#111] hover:border-white/20 transition-colors relative group h-full">

              <div
                class="absolute inset-0 bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
                <div class="bg-white text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">Coming Soon
                </div>
                <p class="text-white/80 font-medium">We're finalizing the details.</p>
              </div>

              <h3 class="text-sm font-bold uppercase tracking-widest text-white/50 mb-4">Enterprise</h3>
              <div class="flex items-baseline gap-1 mb-6">
                <span class="text-4xl font-bold text-white">Custom</span>
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

      <section id="faq" class="py-32 px-6 border-t border-white/5 bg-[#050505]">
        <div class="max-w-[1000px] mx-auto">
          <div class="text-center mb-20">
            <span class="text-sm font-mono text-white/40 mb-4 block uppercase tracking-widest">Support</span>
            <h2 class="text-3xl md:text-5xl font-light text-white/90 leading-tight">
              Frequently asked questions.
            </h2>
          </div>

          <div class="space-y-4">
            <div v-for="(faq, index) in faqs" :key="index"
              class="border-t border-white/10 bg-transparent transition-colors">
              <button @click="toggleFaq(index)" class="w-full flex items-center justify-between py-6 text-left group">
                <span
                  class="font-light text-xl text-white/90 group-hover:text-white transition-colors">{{ faq.question }}</span>
                <span class="text-white/30 group-hover:text-white transition-colors relative">
                  <Plus v-if="!faq.open" class="w-5 h-5" />
                  <X v-else class="w-5 h-5" />
                </span>
              </button>
              <div v-show="faq.open" class="pb-8">
                <div class="text-white/60 leading-relaxed max-w-2xl">
                  {{ faq.answer }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="h-screen relative flex items-center justify-center overflow-hidden mb-10 mx-6 rounded-sm group">
        <div class="absolute inset-0 bg-black z-10 w-full h-full"></div>

        <div
          class="absolute inset-0 w-full h-full overflow-hidden opacity-60 mix-blend-screen grayscale group-hover:grayscale-0 transition-all duration-[2s]">
          <img src="/images/preFooter.jpg" alt="Pre footer bg"
            class="absolute inset-0 w-full h-full object-cover object-center scale-110 group-hover:scale-100 transition-transform duration-[2s] ease-out"
            :style="{ transform: `translateY(${-50 + y * 0.05}px) scale(${1 + y * 0.0001})` }" />
        </div>

        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black via-transparent to-transparent z-20">
        </div>

        <div class="relative z-30 max-w-5xl mx-auto px-6 text-center">
          <Motion :variants="preFooterContainer" initial="hidden" while-in-view="visible"
            :viewport="{ once: true, margin: '-20%' }">

            <Motion :variants="preFooterItem">
              <span
                class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-white/5 border border-white/10 text-[10px] font-mono uppercase tracking-widest text-white/60 mb-10 backdrop-blur-md hover:bg-white/10 hover:border-white/20 transition-all duration-500 cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Start your journey
              </span>
            </Motion>

            <Motion :variants="preFooterItem">
              <h2 class="text-5xl md:text-8xl lg:text-9xl font-medium text-white mb-12 tracking-tighter leading-[0.9]">
                Shape the <br />
                <span
                  class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white/80 to-white/40 italic font-serif">
                  Future.
                </span>
              </h2>
            </Motion>

            <Motion :variants="preFooterItem" class="flex flex-col items-center gap-6">
              <Link href="/register"
                class="group relative inline-flex items-center justify-center gap-3 px-12 py-6 bg-white text-black text-lg font-medium rounded-full overflow-hidden transition-all duration-300 hover:shadow-[0_0_40px_rgba(255,255,255,0.4)] hover:scale-105 active:scale-95">
                <span class="relative z-10">Get Started Now</span>
                <ArrowRight class="w-5 h-5 relative z-10 transition-transform duration-300 group-hover:translate-x-1" />
                <div
                  class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                </div>
              </Link>
              <p class="text-white/40 text-sm">No credit card required for standard plans.</p>
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
                <li v-for="link in footerLinks.index" :key="link.label">
                  <a :href="link.href" class="hover:text-white transition-colors">{{ link.label }}</a>
                </li>
              </ul>
            </div>

            <div class="md:col-span-2">
              <h4 class="text-sm text-white/50 mb-8 font-normal tracking-wide">Social</h4>
              <ul class="space-y-3 text-sm text-white/80">
                <li v-for="link in footerLinks.social" :key="link.label">
                  <a :href="link.href" class="hover:text-white transition-colors flex items-center gap-2">
                    {{ link.label }}
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
                  <a href="#" class="hover:text-white transition-colors">Write Us</a>
                  <a href="#" class="hover:text-white transition-colors">Newsletter Signup</a>
                </div>
                <p class="pt-8 opacity-30">20:40:16 (GMT+2)</p>
              </div>
            </div>
          </div>

          <div class="border-t border-white/10 pt-4 md:pt-8 flex justify-center overflow-hidden w-full">
            <h1
              class="text-[19vw] md:text-[20vw] leading-[0.8] font-semibold tracking-tighter text-center uppercase whitespace-nowrap opacity-90 select-none pointer-events-none w-full block">
              Ecnelis
            </h1>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.animate-gradient {
  background-size: 200% auto;
  animation: gradient 4s linear infinite;
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}
</style>
