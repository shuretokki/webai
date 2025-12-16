<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import {
  Activity,
  BarChart3,
  CheckCircle2,
  ChevronRight,
  Globe,
  Quote,
  Shield,
  Users,
  Zap,
  ChevronDown
} from 'lucide-vue-next';
import { ref } from 'vue';

const features = [
  {
    icon: Shield,
    title: 'Bank-grade Security',
    description: 'SOC2 compliant infrastructure with end-to-end encryption and private VPC peering options.'
  },
  {
    icon: Zap,
    title: 'Global Edge Network',
    description: 'Sub-millisecond latency worldwide through our distributed edge inference nodes.'
  },
  {
    icon: Activity,
    title: '99.99% Uptime SLA',
    description: 'Guaranteed availability with redundant failover systems and 24/7 dedicated support.'
  }
];

const faqs = ref([
  {
    question: "Can I deploy on-premise?",
    answer: "Yes, our Enterprise plan supports full on-premise deployment with air-gapped environments for maximum control and security.",
    isOpen: false
  },
  {
    question: "What kind of support is included?",
    answer: "Enterprise customers receive 24/7 dedicated support via Slack and phone, with a guaranteed 1-hour response time for critical issues.",
    isOpen: false
  },
  {
    question: "How do you handle data privacy?",
    answer: "We adhere to strict data privacy standards. Your data is never used to train our public models, and we offer optional zero-retention policies.",
    isOpen: false
  },
  {
    question: "Is there a volume discount?",
    answer: "Absolutely. We offer tailored volume licensing agreements designed to scale efficiently with your organization's growth.",
    isOpen: false
  }
]);

const container = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1
    }
  }
};

const item = {
  hidden: { opacity: 0, y: 30 },
  show: {
    opacity: 1,
    y: 0,
    transition: {
      duration: 0.8,
      ease: [0.16, 1, 0.3, 1]
    }
  }
};
</script>

<template>
  <AppLayout>

    <Head title="Enterprise" />

    <div
      class="min-h-screen bg-white dark:bg-black text-black dark:text-white overflow-hidden relative font-space selection:bg-zinc-500/30 selection:text-white">

      <!-- Ambient Background Effects (Colorless/Monochrome) -->
      <div class="absolute top-0 left-0 right-0 h-[800px] z-0 overflow-hidden pointer-events-none">

        <!-- Grid Pattern -->
        <div
          class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] dark:opacity-20">
        </div>
        <div
          class="absolute inset-0 bg-[linear-gradient(to_right,#00000008_1px,transparent_1px),linear-gradient(to_bottom,#00000008_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]">
        </div>

        <!-- Spotlight Light Effect -->
        <div
          class="absolute top-0 left-1/2 -translate-x-1/2 w-[1px] h-[400px] bg-gradient-to-b from-black/0 via-black/20 to-black/0 dark:from-white/0 dark:via-white/20 dark:to-white/0 blur-sm">
        </div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-32 pb-32">

        <!-- Hero Section -->
        <Motion initial="hidden" animate="show" :variants="container" class="text-center max-w-4xl mx-auto mb-48">
          <Motion :variants="item" class="flex justify-center mb-8">
            <div
              class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none border border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5 backdrop-blur-sm text-sm font-medium text-black/70 dark:text-white/70">
              <span class="flex h-1.5 w-1.5 rounded-full bg-black dark:bg-white animate-pulse"></span>
              New Enterprise Tier Available
            </div>
          </Motion>

          <Motion :variants="item">
            <h1 class="text-6xl md:text-8xl font-bold tracking-tight mb-8 leading-tight text-black dark:text-white">
              Scale your vision <br />
              <span class="text-zinc-500 dark:text-zinc-500">Into Reality</span>
            </h1>
          </Motion>

          <Motion :variants="item">
            <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-12 max-w-2xl mx-auto leading-relaxed">
              The complete platform for building, deploying, and scaling AI agents.
              Secure, reliable, and faster than ever before.
            </p>
          </Motion>

          <Motion :variants="item" class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <Button size="lg"
              class="h-14 px-8 text-lg rounded-none bg-black text-white hover:bg-zinc-800 dark:bg-white dark:text-black dark:hover:bg-zinc-200 transition-all font-semibold border border-transparent cursor-pointer">
              Start Building
            </Button>
            <Button variant="outline" size="lg"
              class="cursor-pointer h-14 px-8 text-lg rounded-none border-black/10 dark:border-white/10 bg-white/50 dark:bg-white/5 hover:bg-black/5 dark:hover:bg-white/10 text-black dark:text-white backdrop-blur-sm transition-all group">
              Contact Sales
              <ChevronRight class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" />
            </Button>
          </Motion>
        </Motion>

        <!-- Bento Grid / Stats Section - Scroll Triggered -->
        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: true, margin: '-10% 0px -10% 0px' }"
          :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-48">
          <!-- Card 1: Revenue/Growth -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-black/10 dark:border-white/10 bg-white dark:bg-black p-8 shadow-sm hover:border-black/30 dark:hover:border-white/30 transition-colors duration-500">
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 rounded-none bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10">
                    <BarChart3 class="w-6 h-6 text-black dark:text-white" />
                  </div>
                  <h3 class="text-lg font-medium text-black dark:text-white">Usage Analytics</h3>
                </div>
                <h4 class="text-4xl font-bold text-black dark:text-white mb-2">2.4M+</h4>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Tokens generated this week</p>
              </div>

              <!-- Mock Graph -->
              <div class="mt-8 flex items-end gap-2 h-32 w-full opacity-80 mix-blend-multiply dark:mix-blend-normal">
                <div
                  class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-none h-[40%] group-hover:h-[60%] transition-all duration-700 ease-out relative">
                </div>
                <div
                  class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-none h-[65%] group-hover:h-[45%] transition-all duration-700 ease-out delay-75 relative">
                </div>
                <div
                  class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-none h-[50%] group-hover:h-[80%] transition-all duration-700 ease-out delay-100 relative">
                </div>
                <div
                  class="w-full bg-zinc-200 dark:bg-zinc-800 rounded-none h-[85%] group-hover:h-[70%] transition-all duration-700 ease-out delay-150 relative">
                </div>
                <div
                  class="w-full bg-zinc-800 dark:bg-zinc-200 rounded-none h-[75%] group-hover:h-[95%] transition-all duration-700 ease-out delay-200 relative">
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Active Users -->
          <div
            class="relative group overflow-hidden rounded-none border border-black/10 dark:border-white/10 bg-white dark:bg-black p-8 shadow-sm hover:border-black/30 dark:hover:border-white/30 transition-colors duration-500">
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-none bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10">
                  <Users class="w-6 h-6 text-black dark:text-white" />
                </div>
                <h3 class="text-lg font-medium text-black dark:text-white">Active Agents</h3>
              </div>
              <div class="flex -space-x-4 mb-6">
                <div v-for="i in 4" :key="i"
                  class="w-10 h-10 rounded-full border-2 border-white dark:border-black bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center text-xs text-black dark:text-white relative z-10">
                  <img :src="`https://ui-avatars.com/api/?name=User+${i}&background=random&color=fff`"
                    class="w-full h-full rounded-full grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all" />
                </div>
                <div
                  class="w-10 h-10 rounded-full border-2 border-white dark:border-black bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center text-xs text-black dark:text-white z-0">
                  +2k
                </div>
              </div>
              <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-4">
                Join thousands of developers building the future.
              </p>
              <Button variant="link"
                class="text-black dark:text-white p-0 h-auto hover:underline group-hover:translate-x-1 transition-transform decoration-black dark:decoration-white">
                Explore Community &rarr;
              </Button>
            </div>
          </div>

          <!-- Card 3: Global Latency -->
          <div
            class="relative group overflow-hidden rounded-none border border-black/10 dark:border-white/10 bg-white dark:bg-black p-8 shadow-sm hover:border-black/30 dark:hover:border-white/30 transition-colors duration-500">
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-none bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10">
                  <Globe class="w-6 h-6 text-black dark:text-white" />
                </div>
                <h3 class="text-lg font-medium text-black dark:text-white">Global Latency</h3>
              </div>
              <h4 class="text-4xl font-bold text-black dark:text-white mb-2 font-mono">24ms</h4>
              <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">Average response time worldwide.</p>

              <div class="w-full h-2 bg-zinc-100 dark:bg-zinc-800 rounded-none overflow-hidden">
                <div class="h-full bg-black dark:bg-white w-[85%] rounded-none relative overflow-hidden">
                  <div class="absolute inset-0 bg-white/20 dark:bg-black/20 animate-[shimmer_2s_infinite]"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Security -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-black/10 dark:border-white/10 bg-white dark:bg-black p-8 shadow-sm flex flex-col md:flex-row items-center gap-8 hover:border-black/30 dark:hover:border-white/30 transition-colors duration-500">
            <div class="relative z-10 flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 rounded-none bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10">
                  <Shield class="w-6 h-6 text-black dark:text-white" />
                </div>
                <h3 class="text-lg font-medium text-black dark:text-white">Enterprise Security</h3>
              </div>
              <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mb-6">
                We prioritize your data security with SOC2 Type II compliance, automated penetration testing, and
                private cloud deployment options.
              </p>
              <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                  <CheckCircle2 class="w-4 h-4 text-black dark:text-white" /> End-to-end Encryption
                </li>
                <li class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                  <CheckCircle2 class="w-4 h-4 text-black dark:text-white" /> SSO & SAML Support
                </li>
              </ul>
            </div>
            <div
              class="relative z-10 w-full md:w-1/3 aspect-square bg-zinc-100 dark:bg-zinc-900 rounded-none border border-black/5 dark:border-white/5 flex items-center justify-center group-hover:bg-zinc-200 dark:group-hover:bg-zinc-800 transition-colors duration-500">
              <Shield class="w-24 h-24 text-zinc-300 dark:text-zinc-700 transition-colors duration-500" />
              <div class="absolute inset-0 flex items-center justify-center">
                <div
                  class="w-16 h-16 bg-black/5 dark:bg-white/5 rounded-full flex items-center justify-center animate-pulse border border-black/10 dark:border-white/10">
                  <span class="w-3 h-3 bg-black dark:bg-white rounded-full"></span>
                </div>
              </div>
            </div>
          </div>
        </Motion>

        <!-- Founder Notes (Redesigned) -->
        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: true, margin: '-20%' }" :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }"
          class="mb-48 relative">
          <div class="max-w-4xl mx-auto text-center px-4">

            <!-- Badge -->
            <div class="relative inline-flex items-center justify-center mb-12 group">
              <div
                class="relative px-6 py-2 bg-white dark:bg-black rounded-full border border-black/10 dark:border-white/10 flex items-center gap-2">
                <!-- Left Spotline -->
                <div
                  class="w-[1px] h-4 bg-black dark:bg-white absolute left-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <span class="text-xs font-bold tracking-[0.2em] text-zinc-500 dark:text-zinc-400 uppercase">Founders
                  Note</span>
              </div>
              <!-- Top curved Light arc -->
              <div
                class="absolute -top-[1px] left-1/2 -translate-x-1/2 w-1/2 h-[1px] bg-gradient-to-r from-transparent via-black dark:via-white to-transparent opacity-50">
              </div>
            </div>

            <!-- Quote Text -->
            <div class="relative">
              <span
                class="absolute -top-10 -left-10 text-8xl text-zinc-200 dark:text-zinc-800 font-serif leading-none opacity-50">"</span>
              <h3
                class="text-3xl md:text-5xl font-medium leading-[1.3] text-black/90 dark:text-white/90 mb-16 tracking-tight">
                " We gather your site data. <span class="bg-black/5 dark:bg-white/10 px-1 italic">We
                  know your target audience</span> & how your brand can standout from crowd. Best part is we also <span
                  class="bg-black/5 dark:bg-white/10 px-1 italic">help
                  you with Solutions</span> ,,
              </h3>
            </div>

            <!-- Author -->
            <div class="flex flex-col items-center justify-center gap-3">
              <div class="w-16 h-16 rounded-full p-[2px] border border-black/10 dark:border-white/10">
                <img src="https://ui-avatars.com/api/?name=Shure+Tokki&background=000&color=fff"
                  class="w-full h-full rounded-full grayscale" />
              </div>
              <div class="text-center">
                <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200 tracking-wide uppercase opacity-80">
                  Co-founder & ex google designer</div>
              </div>
            </div>
          </div>
        </Motion>

        <!-- Features List -->
        <div class="mb-48">
          <Motion :initial="{ opacity: 0, y: 30 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
            :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }" class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4 text-black dark:text-white">Why Choose Ecnelis?</h2>
            <p class="text-zinc-500 max-w-2xl mx-auto">Built by developers, for developers.</p>
          </Motion>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Motion v-for="(feature, index) in features" :key="index" :initial="{ opacity: 0, y: 40 }"
              :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
              :transition="{ delay: index * 0.1, duration: 0.8, ease: [0.16, 1, 0.3, 1] }"
              class="p-8 rounded-none bg-zinc-50 dark:bg-zinc-900/50 border border-black/5 dark:border-white/5 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:border-black/10 dark:hover:border-white/10 transition-all duration-500 group">
              <div
                class="w-12 h-12 bg-black/5 dark:bg-white/5 rounded-none flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <component :is="feature.icon" class="w-6 h-6 text-black dark:text-white" />
              </div>
              <h3 class="text-xl font-bold mb-3 text-black dark:text-white">{{ feature.title }}</h3>
              <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-sm">{{ feature.description }}</p>
            </Motion>
          </div>
        </div>

        <!-- FAQs -->
        <Motion :initial="{ opacity: 0, y: 30 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
          :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }" class="mb-32 max-w-3xl mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-black dark:text-white">Frequently Asked Questions</h2>
            <p class="text-zinc-500">Common questions about our Enterprise solutions.</p>
          </div>

          <div class="w-full space-y-4">
            <Collapsible v-for="(faq, index) in faqs" :key="index" v-model:open="faq.isOpen"
              class="border-b border-black/10 dark:border-white/10 bg-transparent py-4 text-left group">
              <CollapsibleTrigger
                class="flex items-center justify-between w-full text-left font-medium text-lg text-black/90 dark:text-white/90 hover:text-black dark:hover:text-white transition-colors focus:outline-none">
                {{ faq.question }}
                <div class="relative w-6 h-6 flex items-center justify-center">
                  <div
                    class="absolute w-full h-[1px] bg-black/50 dark:bg-white/50 group-hover:bg-black dark:group-hover:bg-white transition-colors">
                  </div>
                  <div :class="{ 'rotate-90': !faq.isOpen, 'rotate-180': faq.isOpen }"
                    class="absolute w-full h-[1px] bg-black/50 dark:bg-white/50 group-hover:bg-black dark:group-hover:bg-white transition-transform duration-300 transform">
                  </div>
                </div>
              </CollapsibleTrigger>
              <CollapsibleContent class="pt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed font-light">
                {{ faq.answer }}
              </CollapsibleContent>
            </Collapsible>
          </div>
        </Motion>

        <!-- Bottom Hero / CTA -->
        <Motion :initial="{ opacity: 0, scale: 0.95, y: 20 }" :while-in-view="{ opacity: 1, scale: 1, y: 0 }"
          :viewport="{ once: true }" :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }" class="mb-24 text-center">
          <div
            class="py-24 px-64 rounded-none border border-black/10 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900 relative overflow-hidden group">

            <div
              class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-5 dark:opacity-10 group-hover:opacity-10 dark:group-hover:opacity-20 transition-opacity duration-1000">
            </div>

            <div
              class="absolute -top-32 left-1/2 -translate-x-1/2 w-full h-full bg-black/5 dark:bg-white/5 blur-[150px] rounded-full group-hover:bg-black/10 dark:group-hover:bg-white/10 transition-colors duration-700">
            </div>

            <h2 class="text-4xl md:text-6xl font-bold text-black dark:text-white mb-8 relative z-10 tracking-tight">
              Ready to transform
              your business?</h2>
            <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-12 max-w-2xl mx-auto relative z-10 font-light">
              Join industry leaders who trust Ecnelis for their mission-critical AI workloads.
            </p>
            <div class="relative z-10">
              <Button size="lg"
                class="h-14 px-12 text-lg cursor-pointer rounded-none bg-black dark:bg-white text-white dark:text-black hover:bg-zinc-800 dark:hover:bg-zinc-200 font-bold tracking-wide shadow-none">
                Talk to an Expert
              </Button>
            </div>
          </div>
        </Motion>

      </div>

      <div
        class="border-t border-black/10 dark:border-white/10 bg-white dark:bg-black py-12 text-center text-sm text-zinc-500">
        <p>&copy; 2025 Ecnelis Inc. All rights reserved.</p>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }

  100% {
    transform: translateX(100%);
  }
}
</style>
