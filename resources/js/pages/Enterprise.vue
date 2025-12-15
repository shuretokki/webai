<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import {
  Activity,
  ArrowRight,
  BarChart3,
  Check,
  CheckCircle2,
  ChevronRight,
  Globe,
  Shield,
  Users,
  Zap
} from 'lucide-vue-next';

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

const plans = [
  {
    name: 'Starter',
    price: 'Free',
    period: '/ forever',
    description: 'Perfect for testing and personal projects.',
    features: ['1,000 requests/mo', 'Community support', 'Shared infrastructure', 'Basic analytics'],
    cta: 'Start for Free',
    popular: false
  },
  {
    name: 'Pro',
    price: '$49',
    period: '/ month',
    description: 'For growing teams and production apps.',
    features: ['100k requests/mo', 'Priority email support', 'Dedicated API keys', 'Advanced analytics', 'Custom rate limits'],
    cta: 'Get Started',
    popular: true
  },
  {
    name: 'Enterprise',
    price: 'Custom',
    period: '',
    description: 'For large organizations with specific needs.',
    features: ['Unlimited requests', '24/7 Phone support', 'Private VPC', 'SSO & Audit logs', 'Custom SLA', 'Dedicated Success Manager'],
    cta: 'Contact Sales',
    popular: false
  }
];

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
</script>

<template>
  <AppLayout>

    <Head title="Enterprise" />

    <div class="min-h-screen bg-black text-white overflow-hidden relative font-space selection:bg-blue-500/30">
      <!-- Ambient Background Effects -->
      <div class="fixed inset-0 z-0 pointer-events-none">
        <div
          class="absolute top-[-10%] left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-blue-600/20 blur-[120px] rounded-full opacity-50">
        </div>
        <div
          class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[600px] bg-purple-600/10 blur-[100px] rounded-full opacity-30">
        </div>
        <div
          class="absolute top-[20%] left-[-10%] w-[600px] h-[600px] bg-indigo-600/10 blur-[100px] rounded-full opacity-30">
        </div>

        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
        <div
          class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]">
        </div>
      </div>

      <div class="relative z-10 max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 pt-24 pb-32">

        <!-- Hero Section -->
        <Motion initial="hidden" animate="show" :variants="container" class="text-center max-w-4xl mx-auto mb-32">
          <Motion :variants="item" class="flex justify-center mb-6">
            <div
              class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm text-xs font-medium text-blue-400">
              <span class="flex h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
              New Enterprise Tier Available
            </div>
          </Motion>

          <Motion :variants="item">
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 leading-tight">
              Scale your vision with <br />
              <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 animate-gradient-x">Intelligent
                Infrastructure</span>
            </h1>
          </Motion>

          <Motion :variants="item">
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
              The complete platform for building, deploying, and scaling AI agents.
              Secure, reliable, and faster than ever before.
            </p>
          </Motion>

          <Motion :variants="item" class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Button size="lg"
              class="h-12 px-8 text-base rounded-full bg-white text-black hover:bg-gray-200 transition-all font-semibold shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)]">
              Start Building
            </Button>
            <Button variant="outline" size="lg"
              class="h-12 px-8 text-base rounded-full border-white/10 bg-white/5 hover:bg-white/10 text-white backdrop-blur-sm transition-all group">
              Contact Sales
              <ChevronRight class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" />
            </Button>
          </Motion>
        </Motion>

        <!-- Bento Grid / Stats Section -->
        <Motion :initial="{ opacity: 0, y: 50 }" :while-in-view="{ opacity: 1, y: 0 }" :viewport="{ once: true }"
          :transition="{ duration: 0.8 }" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-32">
          <!-- Card 1: Revenue/Growth -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 rounded-lg bg-blue-500/20 border border-blue-500/30">
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
                  class="w-full bg-blue-500/20 rounded-t-sm h-[40%] group-hover:h-[60%] transition-all duration-700 ease-out relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-blue-400 shadow-[0_0_10px_rgba(96,165,250,0.8)]">
                  </div>
                </div>
                <div
                  class="w-full bg-blue-500/20 rounded-t-sm h-[65%] group-hover:h-[45%] transition-all duration-700 ease-out delay-75 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-blue-400 shadow-[0_0_10px_rgba(96,165,250,0.8)]">
                  </div>
                </div>
                <div
                  class="w-full bg-blue-500/20 rounded-t-sm h-[50%] group-hover:h-[80%] transition-all duration-700 ease-out delay-100 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-blue-400 shadow-[0_0_10px_rgba(96,165,250,0.8)]">
                  </div>
                </div>
                <div
                  class="w-full bg-blue-500/20 rounded-t-sm h-[85%] group-hover:h-[70%] transition-all duration-700 ease-out delay-150 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-blue-400 shadow-[0_0_10px_rgba(96,165,250,0.8)]">
                  </div>
                </div>
                <div
                  class="w-full bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-sm h-[75%] group-hover:h-[95%] transition-all duration-700 ease-out delay-200 relative shadow-lg shadow-blue-500/20">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-white shadow-[0_0_15px_rgba(255,255,255,0.8)]">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Active Users -->
          <div
            class="relative group overflow-hidden rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-purple-500/20 border border-purple-500/30">
                  <Users class="w-6 h-6 text-purple-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Active Agents</h3>
              </div>
              <div class="flex -space-x-4 mb-6">
                <div v-for="i in 4" :key="i"
                  class="w-10 h-10 rounded-full border-2 border-[#0a0a0a] bg-gray-800 flex items-center justify-center text-xs text-white relative z-10">
                  <img :src="`https://ui-avatars.com/api/?name=User+${i}&background=random&color=fff`"
                    class="w-full h-full rounded-full" />
                </div>
                <div
                  class="w-10 h-10 rounded-full border-2 border-[#0a0a0a] bg-gray-800 flex items-center justify-center text-xs text-white z-0">
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
            class="relative group overflow-hidden rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-lg bg-green-500/20 border border-green-500/30">
                  <Globe class="w-6 h-6 text-green-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Global Latency</h3>
              </div>
              <h4 class="text-4xl font-bold text-white mb-2 font-mono">24ms</h4>
              <p class="text-sm text-gray-400 mb-6">Average response time worldwide.</p>

              <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 w-[85%] rounded-full relative overflow-hidden">
                  <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Security -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-8 shadow-2xl flex flex-col md:flex-row items-center gap-8">
            <div
              class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 rounded-lg bg-orange-500/20 border border-orange-500/30">
                  <Shield class="w-6 h-6 text-orange-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-200">Enterprise Security</h3>
              </div>
              <p class="text-gray-400 leading-relaxed mb-6">
                We prioritize your data security with SOC2 Type II compliance, automated penetration testing, and
                private cloud deployment options.
              </p>
              <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-gray-300">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> End-to-end Encryption
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-300">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> SSO & SAML Support
                </li>
              </ul>
            </div>
            <div
              class="relative z-10 w-full md:w-1/3 aspect-square bg-black/40 rounded-xl border border-white/10 flex items-center justify-center">
              <Shield class="w-24 h-24 text-gray-700/50" />
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 bg-orange-500/20 rounded-full flex items-center justify-center animate-pulse">
                  <span class="w-3 h-3 bg-orange-500 rounded-full shadow-[0_0_10px_#f97316]"></span>
                </div>
              </div>
            </div>
          </div>
        </Motion>


        <!-- Features List -->
        <div class="mb-32">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4">Why Choose Ecnelis?</h2>
            <p class="text-gray-400 max-w-2xl mx-auto">Built by developers, for developers. We understand what it takes
              to scale.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="(feature, index) in features" :key="index"
              class="p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
              <component :is="feature.icon" class="w-10 h-10 text-white mb-4" />
              <h3 class="text-xl font-bold mb-2 text-white">{{ feature.title }}</h3>
              <p class="text-gray-400 leading-relaxed">{{ feature.description }}</p>
            </div>
          </div>
        </div>

        <!-- Pricing Section -->
        <div>
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4">Pricing Plans</h2>
            <p class="text-gray-400 max-w-2xl mx-auto">Transparent pricing that scales with you.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="plan in plans" :key="plan.name" :class="[
              'relative p-8 rounded-3xl border transition-all duration-300 hover:-translate-y-2',
              plan.popular
                ? 'bg-gradient-to-b from-blue-900/20 to-black border-blue-500/50 shadow-[0_0_30px_rgba(59,130,246,0.15)]'
                : 'bg-black/40 border-white/10 hover:border-white/20'
            ]">
              <div v-if="plan.popular"
                class="absolute -top-4 left-1/2 -translate-x-1/2 text-xs font-bold bg-blue-500 text-white px-3 py-1 rounded-full uppercase tracking-wider">
                Most Popular
              </div>

              <h3 class="text-xl font-bold text-white mb-2">{{ plan.name }}</h3>
              <p class="text-gray-400 text-sm mb-6 min-h-[40px]">{{ plan.description }}</p>

              <div class="mb-6">
                <span class="text-4xl font-bold text-white">{{ plan.price }}</span>
                <span class="text-gray-500">{{ plan.period }}</span>
              </div>

              <Button class="w-full mb-8 rounded-full" :variant="plan.popular ? 'default' : 'secondary'">
                {{ plan.cta }}
              </Button>

              <ul class="space-y-3">
                <li v-for="feature in plan.features" :key="feature"
                  class="flex items-center gap-2 text-sm text-gray-300">
                  <Check class="w-4 h-4 text-blue-400 flex-shrink-0" />
                  {{ feature }}
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Simple -->
      <div class="border-t border-white/10 bg-black py-12 text-center text-sm text-gray-500">
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

@keyframes gradient-x {

  0%,
  100% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }
}

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradient-x 15s ease infinite;
}
</style>
