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

const toggleFaq = (index: number) => {
  faqs.value[index].isOpen = !faqs.value[index].isOpen;
};
</script>

<template>
  <AppLayout>

    <Head title="Enterprise" />

    <div class="min-h-screen bg-background text-foreground overflow-hidden relative font-space selection:bg-primary/30">
      <!-- Ambient Background Effects -->
      <div class="fixed inset-0 z-0 pointer-events-none">
        <div
          class="absolute top-[-10%] left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-primary/20 blur-[120px] rounded-full opacity-50">
        </div>
        <div
          class="absolute bottom-[-10%] right-[-10%] w-[800px] h-[600px] bg-blue-600/10 blur-[100px] rounded-full opacity-30">
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
              class="inline-flex items-center gap-2 px-3 py-1 rounded-none border border-border bg-card/50 backdrop-blur-sm text-xs font-medium text-primary">
              <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
              New Enterprise Tier Available
            </div>
          </Motion>

          <Motion :variants="item">
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 leading-tight text-foreground">
              Scale your vision with <br />
              <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-blue-400 to-indigo-400 animate-gradient-x">Intelligent
                Infrastructure</span>
            </h1>
          </Motion>

          <Motion :variants="item">
            <p class="text-xl text-muted-foreground mb-10 max-w-2xl mx-auto leading-relaxed">
              The complete platform for building, deploying, and scaling AI agents.
              Secure, reliable, and faster than ever before.
            </p>
          </Motion>

          <Motion :variants="item" class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Button size="lg"
              class="h-12 px-8 text-base rounded-none bg-primary text-primary-foreground hover:bg-primary/90 transition-all font-semibold shadow-[0_0_20px_rgba(var(--primary),0.3)]">
              Start Building
            </Button>
            <Button variant="outline" size="lg"
              class="h-12 px-8 text-base rounded-none border-border bg-card/50 hover:bg-card/80 text-foreground backdrop-blur-sm transition-all group">
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
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-border bg-card/30 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 rounded-none bg-primary/20 border border-primary/30">
                    <BarChart3 class="w-6 h-6 text-primary" />
                  </div>
                  <h3 class="text-lg font-medium text-foreground">Usage Analytics</h3>
                </div>
                <h4 class="text-4xl font-bold text-foreground mb-2">2.4M+</h4>
                <p class="text-sm text-muted-foreground">Tokens generated this week</p>
              </div>

              <!-- Mock Graph -->
              <div class="mt-8 flex items-end gap-2 h-32 w-full opacity-80">
                <div
                  class="w-full bg-primary/20 rounded-none h-[40%] group-hover:h-[60%] transition-all duration-700 ease-out relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-primary shadow-[0_0_10px_currentColor]">
                  </div>
                </div>
                <div
                  class="w-full bg-primary/20 rounded-none h-[65%] group-hover:h-[45%] transition-all duration-700 ease-out delay-75 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-primary shadow-[0_0_10px_currentColor]">
                  </div>
                </div>
                <div
                  class="w-full bg-primary/20 rounded-none h-[50%] group-hover:h-[80%] transition-all duration-700 ease-out delay-100 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-primary shadow-[0_0_10px_currentColor]">
                  </div>
                </div>
                <div
                  class="w-full bg-primary/20 rounded-none h-[85%] group-hover:h-[70%] transition-all duration-700 ease-out delay-150 relative">
                  <div class="absolute top-0 left-0 right-0 h-[2px] bg-primary shadow-[0_0_10px_currentColor]">
                  </div>
                </div>
                <div
                  class="w-full bg-gradient-to-t from-primary to-blue-400 rounded-none h-[75%] group-hover:h-[95%] transition-all duration-700 ease-out delay-200 relative shadow-lg shadow-primary/20">
                  <div
                    class="absolute top-0 left-0 right-0 h-[2px] bg-foreground shadow-[0_0_15px_rgba(255,255,255,0.8)]">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Active Users -->
          <div
            class="relative group overflow-hidden rounded-none border border-border bg-card/30 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-none bg-indigo-500/20 border border-indigo-500/30">
                  <Users class="w-6 h-6 text-indigo-400" />
                </div>
                <h3 class="text-lg font-medium text-foreground">Active Agents</h3>
              </div>
              <div class="flex -space-x-4 mb-6">
                <div v-for="i in 4" :key="i"
                  class="w-10 h-10 rounded-full border-2 border-background bg-card flex items-center justify-center text-xs text-foreground relative z-10">
                  <img :src="`https://ui-avatars.com/api/?name=User+${i}&background=random&color=fff`"
                    class="w-full h-full rounded-full" />
                </div>
                <div
                  class="w-10 h-10 rounded-full border-2 border-background bg-card flex items-center justify-center text-xs text-foreground z-0">
                  +2k
                </div>
              </div>
              <p class="text-muted-foreground text-sm mb-4">
                Join thousands of developers building the future.
              </p>
              <Button variant="link"
                class="text-indigo-400 p-0 h-auto hover:text-indigo-300 group-hover:translate-x-1 transition-transform">
                Explore Community &rarr;
              </Button>
            </div>
          </div>

          <!-- Card 3: Global Latency -->
          <div
            class="relative group overflow-hidden rounded-none border border-border bg-card/30 backdrop-blur-md p-8 shadow-2xl">
            <div
              class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-none bg-emerald-500/20 border border-emerald-500/30">
                  <Globe class="w-6 h-6 text-emerald-400" />
                </div>
                <h3 class="text-lg font-medium text-foreground">Global Latency</h3>
              </div>
              <h4 class="text-4xl font-bold text-foreground mb-2 font-mono">24ms</h4>
              <p class="text-sm text-muted-foreground mb-6">Average response time worldwide.</p>

              <div class="w-full h-2 bg-muted rounded-none overflow-hidden">
                <div class="h-full bg-emerald-500 w-[85%] rounded-none relative overflow-hidden">
                  <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 4: Security -->
          <div
            class="md:col-span-2 relative group overflow-hidden rounded-none border border-border bg-card/30 backdrop-blur-md p-8 shadow-2xl flex flex-col md:flex-row items-center gap-8">
            <div
              class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10 flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="p-2 rounded-none bg-orange-500/20 border border-orange-500/30">
                  <Shield class="w-6 h-6 text-orange-400" />
                </div>
                <h3 class="text-lg font-medium text-foreground">Enterprise Security</h3>
              </div>
              <p class="text-muted-foreground leading-relaxed mb-6">
                We prioritize your data security with SOC2 Type II compliance, automated penetration testing, and
                private cloud deployment options.
              </p>
              <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-muted-foreground">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> End-to-end Encryption
                </li>
                <li class="flex items-center gap-2 text-sm text-muted-foreground">
                  <CheckCircle2 class="w-4 h-4 text-orange-400" /> SSO & SAML Support
                </li>
              </ul>
            </div>
            <div
              class="relative z-10 w-full md:w-1/3 aspect-square bg-black/40 rounded-none border border-border/50 flex items-center justify-center">
              <Shield class="w-24 h-24 text-muted" />
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
            <h2 class="text-3xl md:text-5xl font-bold mb-4 text-foreground">Why Choose Ecnelis?</h2>
            <p class="text-muted-foreground max-w-2xl mx-auto">Built by developers, for developers. We understand what
              it takes
              to scale.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="(feature, index) in features" :key="index"
              class="p-6 rounded-none bg-card/20 border border-border hover:bg-card/40 transition-colors">
              <component :is="feature.icon" class="w-10 h-10 text-primary mb-4" />
              <h3 class="text-xl font-bold mb-2 text-foreground">{{ feature.title }}</h3>
              <p class="text-muted-foreground leading-relaxed">{{ feature.description }}</p>
            </div>
          </div>
        </div>

        <!-- Founder Notes -->
        <div class="mb-32 max-w-4xl mx-auto">
          <div class="relative p-10 md:p-14 bg-card/20 border border-border rounded-none overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
            <Quote class="absolute top-8 right-8 w-24 h-24 text-primary/5 rotate-12" />

            <h3 class="text-2xl font-bold text-foreground mb-6 font-space">A Note from the Founder</h3>

            <div class="space-y-6 text-lg text-muted-foreground leading-relaxed font-light">
              <p>
                "When we started Ecnelis, our goal wasn't just to build another AI platform. We wanted to create the
                infrastructure that we wished existed when we were hacking together our first agents."
              </p>
              <p>
                "We believe that the future belongs to those who build with intelligence. That's why we've obsessed over
                every millisecond of latency, every packet of data, and every security protocol. We're not just selling
                a service; we're partnering with you to build the next generation of software."
              </p>
            </div>

            <div class="mt-10 flex items-center gap-4">
              <div
                class="w-12 h-12 bg-primary/20 rounded-none flex items-center justify-center border border-primary/30">
                <span class="font-bold text-primary text-xl">S</span>
              </div>
              <div>
                <div class="text-foreground font-bold font-space">Shure Tokki</div>
                <div class="text-sm text-muted-foreground">Founder & CEO, Ecnelis</div>
              </div>
            </div>
          </div>
        </div>

        <!-- FAQs -->
        <div class="mb-32 max-w-3xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-foreground">Frequently Asked Questions</h2>
            <p class="text-muted-foreground">Common questions about our Enterprise solutions.</p>
          </div>

          <div class="w-full space-y-4">
            <Collapsible v-for="(faq, index) in faqs" :key="index" v-model:open="faq.isOpen"
              class="border border-border bg-card/20 p-4 rounded-none">
              <CollapsibleTrigger
                class="flex items-center justify-between w-full text-left font-medium text-foreground hover:text-primary transition-colors focus:outline-none">
                {{ faq.question }}
                <ChevronDown :class="{ 'rotate-180': faq.isOpen }" class="w-5 h-5 transition-transform duration-200" />
              </CollapsibleTrigger>
              <CollapsibleContent class="pt-4 text-muted-foreground leading-relaxed">
                {{ faq.answer }}
              </CollapsibleContent>
            </Collapsible>
          </div>
        </div>

        <!-- Bottom Hero / CTA -->
        <div class="mb-24 text-center">
          <div
            class="py-20 px-6 rounded-none border border-border bg-gradient-to-b from-card/50 to-background relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/20 blur-[100px] rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-600/20 blur-[100px] rounded-full"></div>

            <h2 class="text-4xl md:text-5xl font-bold text-foreground mb-6 relative z-10">Ready to transform your
              business?</h2>
            <p class="text-xl text-muted-foreground mb-8 max-w-2xl mx-auto relative z-10">
              Join industry leaders who trust Ecnelis for their mission-critical AI workloads.
            </p>
            <div class="relative z-10">
              <Button size="lg"
                class="h-14 px-10 text-lg rounded-none bg-foreground text-background hover:bg-foreground/90 font-bold">
                Talk to an Expert
              </Button>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Simple -->
      <div class="border-t border-border bg-background py-12 text-center text-sm text-muted-foreground">
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
