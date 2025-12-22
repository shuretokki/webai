<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import {
  Activity,
  BarChart3,
  CheckCircle2,
  ChevronRight,
  Globe,
  User,
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

const item: any = {
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

    <div class="min-h-screen bg-black text-white font-space selection:bg-white/20 overflow-x-hidden">

      <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-black z-10 w-full h-full"></div>
        <img src="/images/heroSection.jpg" alt="Background"
          class="w-full h-full object-cover object-center opacity-30" />
      </div>

      <div class="relative z-10 min-h-screen max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-32 pb-32">


        <Motion initial="hidden" animate="show" :variants="container" class="text-center max-w-4xl mx-auto mb-48">
          <Motion :variants="item" class="flex justify-center mb-8">
            <div
              class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm text-sm font-medium text-white/70">
              <span class="flex h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
              New Enterprise Tier Available
            </div>
          </Motion>

          <Motion :variants="item">
            <h1 class="text-6xl md:text-8xl font-medium tracking-tighter mb-8 leading-[1] text-white">
              Scale your vision <br />
              <span class="text-white/50">Into Reality</span>
            </h1>
          </Motion>

          <Motion :variants="item">
            <p class="text-xl md:text-2xl text-white/80 mb-12 max-w-2xl mx-auto leading-relaxed font-light">
              The complete platform for building, deploying, and scaling AI agents.
              Secure, reliable, and faster than ever before.
            </p>
          </Motion>

          <Motion :variants="item" class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <Link href="/register">
            <Button size="lg"
              class="h-14 px-8 text-lg rounded-sm bg-white text-black hover:bg-white/90 transition-all font-medium border-b-4 border-gray-300 hover:border-gray-400 active:scale-95 duration-100">
              Start Building
            </Button>
            </Link>
            <Link href="/contact">
            <Button variant="outline" size="lg"
              class="cursor-pointer h-14 px-8 text-lg rounded-sm border-white/20 bg-black/50 hover:bg-white/10 text-white backdrop-blur-sm transition-all group">
              Contact Sales
              <ChevronRight class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" />
            </Button>
            </Link>
          </Motion>
        </Motion>


        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: true, margin: '-10% 0px -10% 0px' }"
          :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-48">

          <div
            class="md:col-span-2 relative group overflow-hidden rounded-sm border border-white/10 bg-[#111] p-8 shadow-sm hover:border-white/20 transition-colors duration-500">
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 rounded-none">
                    <BarChart3 class="w-6 h-6 text-white" />
                  </div>
                  <h3 class="text-lg font-medium text-white">Usage Analytics</h3>
                </div>
                <h4 class="text-4xl font-light text-white mb-2">2.4M+</h4>
                <p class="text-sm text-white/50">Tokens generated this week</p>
              </div>


              <div class="mt-8 flex items-end gap-2 h-32 w-full opacity-80 mix-blend-normal">
                <div
                  class="w-full bg-white/20 rounded-sm h-[40%] group-hover:h-[60%] transition-all duration-700 ease-out relative">
                </div>
                <div
                  class="w-full bg-white/20 rounded-sm h-[65%] group-hover:h-[45%] transition-all duration-700 ease-out delay-75 relative">
                </div>
                <div
                  class="w-full bg-white/20 rounded-sm h-[50%] group-hover:h-[80%] transition-all duration-700 ease-out delay-100 relative">
                </div>
                <div
                  class="w-full bg-white/20 rounded-sm h-[85%] group-hover:h-[70%] transition-all duration-700 ease-out delay-150 relative">
                </div>
                <div
                  class="w-full bg-white rounded-sm h-[75%] group-hover:h-[95%] transition-all duration-700 ease-out delay-200 relative">
                </div>
              </div>
            </div>
          </div>


          <div
            class="relative group overflow-hidden rounded-sm border border-white/10 bg-[#111] p-8 shadow-sm hover:border-white/20 transition-colors duration-500">
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2">
                  <Users class="w-6 h-6 text-white" />
                </div>
                <h3 class="text-lg font-medium text-white">Active Agents</h3>
              </div>
              <div class="flex -space-x-4 mb-6">
                <div v-for="i in 4" :key="i"
                  class="w-10 h-10 rounded-full border-2 border-black bg-zinc-900 flex items-center justify-center text-xs text-white relative z-10">
                  <User
                    class="size-6 rounded-full grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all" />
                </div>
                <div
                  class="w-10 h-10 rounded-full border-2 border-black bg-zinc-900 flex items-center justify-center text-xs text-white z-0">
                  +2k
                </div>
              </div>
              <p class="text-white/50 text-sm mb-4">
                Join thousands of developers building the future.
              </p>
              <Link href="/community">
              <Button variant="link"
                class="text-white p-0 h-auto hover:underline group-hover:translate-x-1 transition-transform decoration-white">
                Explore Community &rarr;
              </Button>
              </Link>
            </div>
          </div>


          <div
            class="relative group overflow-hidden rounded-sm border border-white/10 bg-[#111] p-8 shadow-sm hover:border-white/20 transition-colors duration-500">
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2">
                  <Globe class="w-6 h-6 text-white" />
                </div>
                <h3 class="text-lg font-medium text-white">Global Latency</h3>
              </div>
              <h4 class="text-4xl font-light text-white mb-2 font-mono">24ms</h4>
              <p class="text-sm text-white/50 mb-6">Average response time worldwide.</p>

              <div class="w-full h-2 bg-zinc-800 rounded-sm overflow-hidden">
                <div class="h-full bg-white w-[85%] rounded-sm relative overflow-hidden">
                  <div class="absolute inset-0 bg-black/20 animate-[shimmer_2s_infinite]"></div>
                </div>
              </div>
            </div>
          </div>


          <div
            class="md:col-span-2 relative group overflow-hidden rounded-sm border border-white/10 bg-[#111] p-8 shadow-sm flex flex-col md:flex-row items-center gap-8 hover:border-white/20 transition-colors duration-500">
            <div class="relative z-10 flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2">
                  <Shield class="w-6 h-6 text-white" />
                </div>
                <h3 class="text-lg font-medium text-white">Enterprise Security</h3>
              </div>
              <p class="text-white/60 leading-relaxed mb-6">
                We prioritize your data security with SOC2 Type II compliance, automated penetration testing, and
                private cloud deployment options.
              </p>
              <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-white/60">
                  <CheckCircle2 class="w-4 h-4 text-white" /> End-to-end Encryption
                </li>
                <li class="flex items-center gap-2 text-sm text-white/60">
                  <CheckCircle2 class="w-4 h-4 text-white" /> SSO & SAML Support
                </li>
              </ul>
            </div>
            <div class="relative z-10 w-full md:w-1/3 aspect-square flex items-center justify-center">
              <Shield class="w-24 h-24 text-zinc-700 transition-colors duration-500" />
              <div class="absolute inset-0 flex items-center justify-center">
                <div
                  class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center animate-pulse border border-white/10">
                  <span class="w-3 h-3 bg-white rounded-full"></span>
                </div>
              </div>
            </div>
          </div>
        </Motion>


        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: true, margin: '-20%' }" :transition="{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }"
          class="mb-48 relative">
          <div class="max-w-4xl mx-auto text-center px-4">


            <div class="relative inline-flex items-center justify-center mb-12 group">
              <div class="relative px-6 py-2 bg-[#111] rounded-full border border-white/10 flex items-center gap-2">

                <div
                  class="w-[1px] h-4 bg-white absolute left-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
                <span class="text-xs font-bold tracking-[0.2em] text-white/50 uppercase">Founders
                  Note</span>
              </div>

              <div
                class="absolute -top-[1px] left-1/2 -translate-x-1/2 w-1/2 h-[1px] bg-gradient-to-r from-transparent via-white to-transparent opacity-50">
              </div>
            </div>


            <div class="relative">
              <span class="absolute -top-10 -left-10 text-8xl text-zinc-800 font-serif leading-none opacity-50">"</span>
              <h3 class="text-3xl md:text-5xl font-medium leading-[1.3] text-white/90 mb-16 tracking-tight">
                " We gather your site data. <span class="bg-white/10 px-1 italic">We
                  know your target audience</span> & how your brand can standout from crowd. Best part is we also <span
                  class="bg-white/10 px-1 italic">help
                  you with Solutions</span> ,,
              </h3>
            </div>


            <div class="flex flex-col items-center justify-center gap-3">
            </div>
          </div>
        </Motion>

        <div class="mb-48">
          <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
            :viewport="{ once: false, margin: '-20%' }" :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }" class="
            text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4 text-white">Why Choose Ecnelis?</h2>
            <p class="text-white/50 max-w-2xl mx-auto">Built by developers, for developers.</p>
          </Motion>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Motion v-for="(feature, index) in features" :key="index"
              class="p-8 rounded-sm bg-[#111] border border-white/5 hover:bg-zinc-900 hover:border-white/10 transition-all duration-500 group">
              <div
                class="w-12 h-12 rounded-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <component :is="feature.icon" class="w-6 h-6 text-white" />
              </div>
              <h3 class="text-xl font-bold mb-3 text-white">{{ feature.title }}</h3>
              <p class="text-white/60 leading-relaxed text-sm">{{ feature.description }}</p>
            </Motion>
          </div>
        </div>


        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: false, margin: '-20%' }" :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }"
          class="mb-32 max-w-3xl mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Frequently Asked Questions</h2>
            <p class="text-white/50">Common questions about our Enterprise solutions.</p>
          </div>

          <div class="w-full space-y-4">
            <Collapsible v-for="(faq, index) in faqs" :key="index" v-model:open="faq.isOpen"
              class="border-b border-white/10 bg-transparent py-4 text-left group">
              <CollapsibleTrigger
                class="flex items-center justify-between w-full text-left font-medium text-lg text-white/90 hover:text-white transition-colors focus:outline-none">
                {{ faq.question }}
                <div class="relative w-6 h-6 flex items-center justify-center">
                  <ChevronDown :class="{ 'rotate-180': faq.isOpen }"
                    class="w-4 h-4 transition-transform duration-300 text-white/50 group-hover:text-white" />
                </div>
              </CollapsibleTrigger>
              <CollapsibleContent class="pt-4 text-white/60 leading-relaxed font-light">
                {{ faq.answer }}
              </CollapsibleContent>
            </Collapsible>
          </div>
        </Motion>


        <Motion :initial="{ opacity: 0, y: 40 }" :while-in-view="{ opacity: 1, y: 0 }"
          :viewport="{ once: false, margin: '-20%' }" :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }"
          class="mb-24 text-center">
          <div
            class="py-24 px-6 md:px-24 lg:px-64 rounded-sm border border-white/10 bg-gradient-to-br from-zinc-900 via-zinc-900 to-zinc-800 relative overflow-hidden group">

            <div
              class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 group-hover:opacity-20 transition-opacity duration-1000">
            </div>

            <div
              class="absolute -top-32 left-1/2 -translate-x-1/2 w-full h-full bg-indigo-400/10 blur-[150px] rounded-full group-hover:bg-indigo-400/20 transition-colors duration-700">
            </div>

            <h2 class="text-4xl md:text-6xl font-bold text-white mb-8 relative z-10 tracking-tight">
              Ready to transform
              your business?</h2>
            <p class="text-xl text-white/60 mb-12 max-w-2xl mx-auto relative z-10 font-light">
              Join industry leaders who trust Ecnelis for their mission-critical AI workloads.
            </p>
            <div class="relative z-10">
              <Link href="/contact">
              <Button size="lg"
                class="h-14 px-12 text-lg cursor-pointer rounded-sm bg-white text-black hover:bg-zinc-200 font-bold tracking-wide shadow-none">
                Talk to an Expert
              </Button>
              </Link>
            </div>
          </div>
        </Motion>

      </div>

      <div class="border-t border-white/10 bg-black py-12 text-center text-sm text-white/40">
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