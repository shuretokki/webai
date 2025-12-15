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
  hidden: { opacity: 0, y: 20 },
  show: { opacity: 1, y: 0 }
};

const textReveal = {
  hidden: { backgroundSize: "0% 100%" },
  show: {
    backgroundSize: "100% 100%",
    transition: { duration: 1, ease: "easeInOut" }
  }
};
</script>

<template>
  <AppLayout>

    <Head title="Enterprise" />

    <div class="min-h-screen bg-black text-white overflow-hidden relative font-space selection:bg-blue-500/30">

      <!-- Ambient Background Effects (Restricted to Hero) -->
      <div class="absolute top-0 left-0 right-0 h-[800px] z-0 overflow-hidden pointer-events-none">
        <div
          class="absolute top-[-20%] left-1/2 -translate-x-1/2 w-[1200px] h-[600px] bg-blue-900/20 blur-[150px] rounded-full opacity-60 mix-blend-screen animate-pulse-slow">
        </div>
        <div
          class="absolute top-[10%] right-[-10%] w-[600px] h-[600px] bg-indigo-900/10 blur-[120px] rounded-full opacity-40 mix-blend-screen">
        </div>

        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
        <div
          class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]">
        </div>

        <!-- Spotlight Light Effect -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1px] h-[400px] bg-gradient-to-b from-blue-500/0 via-blue-500/50 to-blue-500/0 blur-sm"></div>

        <!-- Mouse Light Follower (Simulated with simple position for now, could be JS driven) -->
        <div class="absolute top-[200px] left-[60%] w-[300px] h-[300px] bg-blue-400/5 blur-[80px] rounded-full pointer-events-none"></div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-32 pb-32">

        <!-- Hero Section -->
        <Motion initial="hidden" animate="show" :variants="container" class="text-center max-w-4xl mx-auto mb-48">
          <Motion :variants="item" class="flex justify-center mb-8">
            <div
              class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-blue-500/20 bg-blue-500/5 backdrop-blur-sm text-sm font-medium text-blue-300 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
              <span class="flex h-2 w-2 rounded-full bg-blue-400 animate-pulse shadow-[0_0_10px_#60a5fa]"></span>
              New Enterprise Tier Available
            </div>
          </Motion>

          <Motion :variants="item">
            <h1 class="text-6xl md:text-8xl font-bold tracking-tight mb-8 leading-tight text-white/90">
              Scale your vision <br />
              <span
                class="text-transparent bg-clip-text bg-gradient-to-b from-white via-white/80 to-white/40">Into Reality</span>
            </h1>
          </Motion>

          <Motion :variants="item">
            <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto leading-relaxed">
              The complete platform for building, deploying, and scaling AI agents.
              Secure, reliable, and faster than ever before.
            </p>
          </Motion>

          <Motion :variants="item" class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <Button size="lg"
              class="h-14 px-8 text-lg rounded-none bg-blue-600/90 text-white hover:bg-blue-600 transition-all font-semibold shadow-[0_0_40px_rgba(37,99,235,0.3)] hover:shadow-[0_0_60px_rgba(37,99,235,0.5)] border border-blue-400/20">
              Start Building
            </Button>
            <Button variant="outline" size="lg"
              class="h-14 px-8 text-lg rounded-none border-white/10 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm transition-all group">
              Contact Sales
              <ChevronRight class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" />
            </Button>
          </Motion>
        </Motion>

        <!-- Bento Grid / Stats Section - Scroll Triggered -->
        <Motion :initial="{ opacity: 0, scale: 0.95 }" :while-in-view="{ opacity: 1, scale: 1 }" :viewport="{ once: true, margin: '-100px' }"
          :transition="{ duration: 0.8, ease: 'easeOut' }" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-48">
          <!-- Card 1: Revenue/Growth -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-white/10 bg-black/40 backdrop-blur-md p-8 shadow-2xl hover:border-blue-500/30 transition-colors duration-500">
            <div
              class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 rounded-none bg-blue-500/10 border border-blue-500/20">
                    <BarChart3 class="w-6 h-6 text-blue-400" />
                  </div>
                  <h3 class="text-lg font-medium text-gray-200">Usage Analytics</h3>
                </div>
                <h4 class="text-4xl font-bold text-white mb-2">2.4M+</h4>
                <p class="text-sm text-gray-400">Tokens generated this week</p>
              </div>

              <!-- Mock Graph -->
              <div class="mt-8 flex items-end gap-2 h-32 w-full opacity-80">
                <div
                  class="w-full bg-blue-500/10 rounded-none h-[40%] group-hover:h-[60%] transition-all duration-700 ease-out relative border-t border-blue-500/20">
                </div>
                <div
                  class="w-full bg-blue-500/10 rounded-none h-[65%] group-hover:h-[45%] transition-all duration-700 ease-out delay-75 relative border-t border-blue-500/20">
                </div>
                <div
                  class="w-full bg-blue-500/10 rounded-none h-[50%] group-hover:h-[80%] transition-all duration-700 ease-out delay-100 relative border-t border-blue-500/20">
                </div>
                <div
                  class="w-full bg-blue-500/10 rounded-none h-[85%] group-hover:h-[70%] transition-all duration-700 ease-out delay-150 relative border-t border-blue-500/20">
                </div>
                <div
                  class="w-full bg-gradient-to-t from-blue-600/40 to-blue-400/40 rounded-none h-[75%] group-hover:h-[95%] transition-all duration-700 ease-out delay-200 relative shadow-[0_0_20px_rgba(59,130,246,0.2)] border-t border-blue-400">
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Active Users -->
          <div
            class="relative group overflow-hidden rounded-none border border-white/10 bg-black/40 backdrop-blur-md p-8 shadow-2xl hover:border-purple-500/30 transition-colors duration-500">
            <div
              class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-none bg-purple-500/10 border border-purple-500/20">
                  <Users class="w-6 h-6 text-purple-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Active Agents</h3>
              </div>
              <div class="flex -space-x-4 mb-6">
                <div v-for="i in 4" :key="i"
                  class="w-10 h-10 rounded-full border-2 border-black bg-gray-900 flex items-center justify-center text-xs text-white relative z-10">
                  <img :src="`https://ui-avatars.com/api/?name=User+${i}&background=random&color=fff`"
                    class="w-full h-full rounded-full grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all" />
                </div>
                <div
                  class="w-10 h-10 rounded-full border-2 border-black bg-gray-900 flex items-center justify-center text-xs text-white z-0">
                  +2k
                </div>
              </div>
              <p class="text-gray-400 text-sm mb-4">
                Join thousands of developers building the future.
              </p>
              <Button variant="link"
                class="text-purple-400 p-0 h-auto hover:text-purple-300 group-hover:translate-x-1 transition-transform">
                Explore Community &rarr;
              </Button>
            </div>
          </div>

          <!-- Card 3: Global Latency -->
          <div
            class="relative group overflow-hidden rounded-none border border-white/10 bg-black/40 backdrop-blur-md p-8 shadow-2xl hover:border-green-500/30 transition-colors duration-500">
            <div
              class="absolute inset-0 bg-gradient-to-br from-green-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-none bg-green-500/10 border border-green-500/20">
                  <Globe class="w-6 h-6 text-green-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Global Latency</h3>
              </div>
              <h4 class="text-4xl font-bold text-white mb-2 font-mono">24ms</h4>
              <p class="text-sm text-gray-400 mb-6">Average response time worldwide.</p>

              <div class="w-full h-2 bg-gray-800 rounded-none overflow-hidden">
                <div class="h-full bg-green-500 w-[85%] rounded-none relative overflow-hidden shadow-[0_0_10px_#22c55e]">
                  <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Security -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-white/10 bg-black/40 backdrop-blur-md p-8 shadow-2xl flex flex-col md:flex-row items-center gap-8 hover:border-orange-500/30 transition-colors duration-500">
            <div
              class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 rounded-none bg-orange-500/10 border border-orange-500/20">
                  <Shield class="w-6 h-6 text-orange-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Enterprise Security</h3>
              </div>
              <p class="text-gray-400 leading-relaxed mb-6">
                We prioritize your data security with SOC2 Type II compliance, automated penetration testing, and
                private cloud deployment options.
              </p>
              <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-gray-400">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> End-to-end Encryption
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-400">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> SSO & SAML Support
                </li>
              </ul>
            </div>
            <div
              class="relative z-10 w-full md:w-1/3 aspect-square bg-black/60 rounded-none border border-white/5 flex items-center justify-center group-hover:bg-orange-500/5 transition-colors duration-500">
              <Shield class="w-24 h-24 text-gray-800 group-hover:text-orange-900/50 transition-colors duration-500" />
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 bg-orange-500/10 rounded-full flex items-center justify-center animate-pulse border border-orange-500/30">
                  <span class="w-3 h-3 bg-orange-500 rounded-full shadow-[0_0_15px_#f97316]"></span>
                </div>
              </div>
            </div>
          </div>
        </Motion>

        <!-- Founder Notes (Redesigned) -->
        <Motion
            :initial="{ opacity: 0, y: 30 }"
            :while-in-view="{ opacity: 1, y: 0 }"
            :viewport="{ once: true }"
            class="mb-48 relative"
        >
           <div class="max-w-4xl mx-auto text-center px-4">
               <!-- Badge -->
               <div class="relative inline-flex items-center justify-center mb-12 group">
                   <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-600 to-violet-600 blur opacity-40 group-hover:opacity-60 transition-opacity duration-300"></div>
                   <div class="relative px-6 py-2 bg-black rounded-full border border-white/10 flex items-center gap-2">
                        <!-- Left Spotline -->
                        <div class="w-[1px] h-4 bg-gradient-to-b from-blue-500 to-transparent absolute left-0 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="text-xs font-bold tracking-[0.2em] text-blue-300 uppercase">Founders Note</span>
                   </div>
                   <!-- Top curved Light arc -->
                   <div class="absolute -top-[1px] left-1/2 -translate-x-1/2 w-1/2 h-[1px] bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
               </div>

               <!-- Quote Text -->
               <div class="relative">
                   <span class="absolute -top-10 -left-10 text-8xl text-blue-900/20 font-serif leading-none opacity-50">"</span>
                   <h3 class="text-3xl md:text-5xl font-medium leading-[1.3] text-white/90 mb-16 tracking-tight">
                       " We gather your site data. <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-violet-400 font-semibold">We know your target audience</span> & how your brand can standout from crowd. Best part is we also <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-violet-400 font-semibold">help you with Solutions</span> ,,
                   </h3>
               </div>

               <!-- Author -->
               <div class="flex flex-col items-center justify-center gap-3">
                   <div class="w-16 h-16 rounded-full p-[2px] bg-gradient-to-b from-blue-500 to-transparent">
                        <img src="https://ui-avatars.com/api/?name=Shure+Tokki&background=000&color=fff" class="w-full h-full rounded-full border-2 border-black" />
                   </div>
                   <div class="text-center">
                       <div class="text-sm font-medium text-white tracking-wide uppercase opacity-80">Co-founder & ex google designer</div>
                   </div>
               </div>
           </div>
        </Motion>

        <!-- Features List -->
        <div class="mb-48">
          <Motion
            :initial="{ opacity: 0 }"
            :while-in-view="{ opacity: 1 }"
            :viewport="{ once: true }"
            class="text-center mb-16"
          >
            <h2 class="text-3xl md:text-5xl font-bold mb-4 text-white">Why Choose Ecnelis?</h2>
            <p class="text-gray-400 max-w-2xl mx-auto">Built by developers, for developers.</p>
          </Motion>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Motion
                v-for="(feature, index) in features"
                :key="index"
                :initial="{ opacity: 0, y: 20 }"
                :while-in-view="{ opacity: 1, y: 0 }"
                :viewport="{ once: true }"
                :transition="{ delay: index * 0.1 }"
                class="p-8 rounded-none bg-white/[0.02] border border-white/5 hover:bg-white/[0.05] hover:border-white/10 transition-all duration-300 group"
            >
              <div class="w-12 h-12 bg-white/5 rounded-none flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                  <component :is="feature.icon" class="w-6 h-6 text-blue-400" />
              </div>
              <h3 class="text-xl font-bold mb-3 text-white">{{ feature.title }}</h3>
              <p class="text-gray-400 leading-relaxed text-sm">{{ feature.description }}</p>
            </Motion>
          </div>
        </div>

        <!-- FAQs -->
        <Motion
            :initial="{ opacity: 0 }"
            :while-in-view="{ opacity: 1 }"
            :viewport="{ once: true }"
            class="mb-32 max-w-3xl mx-auto"
        >
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-white">Frequently Asked Questions</h2>
            <p class="text-gray-500">Common questions about our Enterprise solutions.</p>
          </div>

          <div class="w-full space-y-4">
            <Collapsible v-for="(faq, index) in faqs" :key="index" v-model:open="faq.isOpen"
              class="border-b border-white/10 bg-transparent py-4 text-left group">
              <CollapsibleTrigger
                class="flex items-center justify-between w-full text-left font-medium text-lg text-white/90 hover:text-white transition-colors focus:outline-none">
                {{ faq.question }}
                <div class="relative w-6 h-6 flex items-center justify-center">
                    <!-- Custom Plus/Minus for cleaner aesthetic -->
                    <div class="absolute w-full h-[1px] bg-white/50 group-hover:bg-white transition-colors"></div>
                    <div :class="{'rotate-90': !faq.isOpen, 'rotate-180': faq.isOpen}" class="absolute w-full h-[1px] bg-white/50 group-hover:bg-white transition-transform duration-300 transform"></div>
                </div>
              </CollapsibleTrigger>
              <CollapsibleContent class="pt-4 text-gray-400 leading-relaxed font-light">
                {{ faq.answer }}
              </CollapsibleContent>
            </Collapsible>
          </div>
        </Motion>

        <!-- Bottom Hero / CTA -->
        <Motion
            :initial="{ opacity: 0, scale: 0.9 }"
            :while-in-view="{ opacity: 1, scale: 1 }"
            :viewport="{ once: true }"
            class="mb-24 text-center"
        >
          <div
            class="py-24 px-6 rounded-none border border-white/10 bg-gradient-to-b from-white/5 to-black relative overflow-hidden group">

            <!-- Hover Parallax Effect -->
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 group-hover:opacity-20 transition-opacity duration-1000"></div>

            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-blue-600/20 blur-[150px] rounded-full group-hover:bg-blue-600/30 transition-colors duration-700"></div>

            <h2 class="text-4xl md:text-6xl font-bold text-white mb-8 relative z-10 tracking-tight">Ready to transform your business?</h2>
            <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto relative z-10 font-light">
              Join industry leaders who trust Ecnelis for their mission-critical AI workloads.
            </p>
            <div class="relative z-10">
              <Button size="lg"
                class="h-14 px-12 text-lg rounded-none bg-white text-black hover:bg-gray-200 font-bold tracking-wide shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                Talk to an Expert
              </Button>
            </div>
          </div>
        </Motion>

      </div>

      <!-- Footer Simple -->
      <div class="border-t border-white/10 bg-black py-12 text-center text-sm text-gray-600">
        <p>&copy; 2025 Ecnelis Inc. All rights reserved.</p>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.4; transform: translateX(-50%) scale(1); }
    50% { opacity: 0.6; transform: translateX(-50%) scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 8s ease-in-out infinite;
}
</style>
