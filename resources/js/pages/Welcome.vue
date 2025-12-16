<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { Motion, AnimatePresence } from 'motion-v';
import { useWindowScroll } from '@vueuse/core';
import {
    ChevronDown,
    ArrowRight,
    Menu,
    X,
    Plus
} from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);
const { y } = useWindowScroll();
const parallaxY = ref(0);

watchEffect(() => {
    parallaxY.value = y.value * 0.2;
});

const navItems = [
    { label: 'About', href: '/about' },
    { label: 'Pricing', href: '#pricing' },
    { label: 'Blog', href: '/blog' },
    { label: 'Changelog', href: '/changelog' },
];

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

const fadeInUp: any = {
    initial: { opacity: 0, y: 30 },
    enter: { opacity: 1, y: 0, transition: { duration: 0.8, ease: [0.22, 1, 0.36, 1] } }
};

const staggerContainer = {
    enter: { transition: { staggerChildren: 1 } }
};

</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-black text-white font-sans selection:bg-white/20 overflow-x-hidden">

        <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 border-b border-transparent"
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
                        <Link href="/explore"
                            class="text-sm font-medium text-white/70 hover:text-white transition-colors">
                        Explore
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login"
                            class="text-sm font-medium text-white hover:text-white/80 transition-colors">
                        Log in
                        </Link>
                        <Link href="/register" v-if="canRegister"
                            class="px-5 py-2.5 bg-white text-black text-sm font-medium hover:bg-white/90 transition-colors rounded-sm">
                        Get Started
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
                            <Link href="/explore"
                                class="text-2xl font-light block py-2 border-b border-white/10 text-white/90">
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

        <section class="min-h-screen flex items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 z-0" :style="{ transform: `translateY(${y * 0.5}px)` }">
                <div class="absolute inset-0 bg-black/60 z-10 w-full h-[120%]"></div>
                <img src="/images/heroSection.jpg" alt="Foggy Mountain"
                    class="w-full h-[120%] object-cover object-center opacity-80" />
                <div
                    class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-black via-black/80 to-transparent z-20">
                </div>
            </div>

            <div class="relative z-10 text-center px-6 max-w-5xl mx-auto mt-20"
                :style="{ transform: `translateY(${y * -0.2}px)` }">
                <Motion initial="initial" animate="enter" :variants="staggerContainer">
                    <Motion :variants="fadeInUp" class="mb-8">
                        <h1
                            class="text-5xl md:text-7xl lg:text-8xl font-medium tracking-tighter text-white mb-8 leading-[1]">
                            Where thoughts <br /> become actions.
                        </h1>
                    </Motion>

                    <Motion :variants="fadeInUp" class="mb-12">
                        <p class="text-xl md:text-2xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed">
                            An AI companion that whispers clarity, <br class="hidden md:block" />
                            conjures ideas, and guides your every move.
                        </p>
                    </Motion>

                    <Motion :variants="fadeInUp">
                        <Link href="/register"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-white text-black font-medium border-b-4 border-gray-300 hover:border-gray-400 hover:bg-gray-50 transition-all active:scale-95 duration-100 rounded-sm">
                        Begin Journey
                        <ArrowRight class="w-4 h-4 ml-1" />
                        </Link>
                    </Motion>
                </Motion>
            </div>

            <div
                class="absolute bottom-12 left-1/2 -translate-x-1/2 text-white/40 text-xs uppercase tracking-[0.2em] flex flex-col items-center gap-4 z-20">
                Scroll to explore
                <Motion :animate="{ y: [0, 10, 0] }" :transition="{ duration: 2, repeat: Infinity, ease: 'easeInOut' }">
                    <ChevronDown class="w-5 h-5 opacity-50" />
                </Motion>
            </div>
        </section>

        <div class="bg-black relative z-10">

            <section class="pt-32 px-6">
                <div class="max-w-[1400px] mx-auto">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="w-2 h-2 rounded-full bg-white/20"></div>
                        <span class="text-sm text-white/40 font-medium">Introducing Message</span>
                    </div>

                    <h2
                        class="text-4xl md:text-6xl font-light text-white/90 max-w-4xl leading-[1.1] tracking-tight mb-20">
                        Harness invisible power to write faster, <br class="hidden md:block" />
                        focus deeper, and save hours.
                    </h2>
                </div>
            </section>

            <section id="features" class="pb-32 px-6">
                <div class="max-w-[1400px] mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        <div class="group">
                            <div
                                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">
                                <img src="/images/featureTime.jpg"
                                    class="absolute inset-0 w-full h-full object-cover opacity-50 grayscale mix-blend-screen"
                                    alt="Time Unfolded" />
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
                                Automate tasks and reclaim hours, your AI assistant turns routine into seconds so you
                                can focus on growth.
                            </p>
                        </div>

                        <div class="group">
                            <div
                                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">
                                <img src="/images/featureWords.jpg"
                                    class="absolute inset-0 w-full h-full object-cover opacity-30 grayscale mix-blend-screen"
                                    alt="Words That Flow" />
                                <div class="absolute inset-0 flex items-center justify-center p-8">
                                    <div
                                        class="bg-[#1a1a1a] border border-white/10 p-6 rounded-lg w-full max-w-xs shadow-2xl relative">
                                        <div class="w-3 h-3 rounded-full bg-white/20 mb-3"></div>
                                        <div class="h-2 bg-white/10 rounded w-3/4 mb-2"></div>
                                        <div class="h-2 bg-white/10 rounded w-full mb-2"></div>
                                        <div class="h-2 bg-white/10 rounded w-5/6"></div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-2xl font-light text-white mb-3">Words That Flow</h3>
                            <p class="text-white/50 leading-relaxed text-sm">
                                Drafts, blogs, and emails written with clarity and speed — the elegance of language
                                without the struggle.
                            </p>
                        </div>

                        <div class="group">
                            <div
                                class="aspect-square bg-[#111] overflow-hidden rounded-sm relative mb-8 border border-white/5 hover:border-white/10 transition-colors">
                                <img src="/images/featureGuide.jpg"
                                    class="absolute inset-0 w-full h-full object-cover opacity-40 grayscale mix-blend-screen"
                                    alt="Silent Guide" />
                                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 p-8">
                                    <div class="flex gap-2 justify-end w-full max-w-[200px]">
                                        <div
                                            class="bg-white text-black text-[10px] uppercase font-bold px-2 py-0.5 rounded-sm">
                                            Unfold</div>
                                    </div>
                                    <div
                                        class="bg-zinc-900/90 border border-white/10 p-4 rounded-sm w-full max-w-[200px] backdrop-blur">
                                        <p class="text-xs text-white/80">Ready to turn thoughts into actions?</p>
                                    </div>
                                    <div class="flex gap-2 justify-end w-full max-w-[200px]">
                                        <div
                                            class="bg-white text-black text-[10px] uppercase font-bold px-2 py-0.5 rounded-sm">
                                            Capture</div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-2xl font-light text-white mb-3">A Silent Guide</h3>
                            <p class="text-white/50 leading-relaxed text-sm">
                                Always present to keep you focused — suggestions, reminders, and insights right when you
                                need them.
                            </p>
                        </div>

                    </div>
                </div>
            </section>

            <section id="pricing" class="py-24 px-6 border-b border-white/5">
                <div class="max-w-[1400px] mx-auto">
                    <div class="flex items-center gap-4 mb-12">
                        <div class="w-2 h-2 rounded-full bg-white/20"></div>
                        <span class="text-sm text-white/40 font-medium">Introducing Benefit</span>
                    </div>

                    <div class=" mb-16">
                        <h2 class="text-3xl md:text-5xl font-light text-white mb-6">Simple, Transparent Pricing</h2>
                        <p class="text-white/60 text-lg">Choose the plan that's right for you. No hidden fees.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                        <div
                            class="p-8 border border-white/10 bg-[#111] hover:border-white/20 transition-colors relative group">
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
                        </div>

                        <div class="p-8 border border-white/20 bg-white/5 relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none">
                            </div>

                            <div
                                class="absolute inset-0 bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
                                <div
                                    class="bg-white text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">
                                    Coming Soon</div>
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
                        </div>

                        <div
                            class="p-8 border border-white/10 bg-[#111] hover:border-white/20 transition-colors relative group">

                            <div
                                class="absolute inset-0 bg-black/80 backdrop-blur-[2px] z-20 flex flex-col items-center justify-center text-center p-6">
                                <div
                                    class="bg-white text-black px-4 py-2 text-sm font-bold uppercase tracking-widest mb-4">
                                    Coming Soon</div>
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
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative h-[600px] flex items-center mb-10 mx-6 rounded-sm overflow-hidden">
                <div class="absolute inset-0 bg-black/40 z-10 w-full h-full"></div>
                <img src="/images/preFooter.jpg" alt="Pre footer bg"
                    class="absolute inset-0 w-full h-full object-cover object-center" />

                <div class="relative z-20 max-w-[1400px] mx-auto px-12 md:px-20 w-full">
                    <h2 class="text-4xl md:text-6xl font-light text-white mb-6 max-w-2xl leading-tight">
                        Step into the future, <br /> guided by AI clarity
                    </h2>
                    <p class="text-white/80 text-lg mb-10 max-w-md font-light">
                        Experience the tool right now. Just dive in and see what AI can do for you.
                    </p>
                    <Link href="/register"
                        class="inline-block px-8 py-3 bg-white text-black font-medium hover:scale-105 transition-transform duration-300">
                    Try It Now
                    </Link>
                </div>
            </section>

            <section id="faq" class="py-32 px-6 border-t border-white/5 bg-[#050505]">
                <div class="max-w-[1000px] mx-auto">
                    <div class="text-center mb-20">
                        <span
                            class="text-sm font-mono text-white/40 mb-4 block uppercase tracking-widest">Support</span>
                        <h2 class="text-3xl md:text-5xl font-light text-white/90 leading-tight">
                            Frequently asked questions.
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <div v-for="(faq, index) in faqs" :key="index"
                            class="border-t border-white/10 bg-transparent transition-colors">
                            <button @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between py-6 text-left group">
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

            <footer class="py-24 bg-black px-6 border-t border-white/5">
                <div class="max-w-[1400px] mx-auto">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-12 mb-20">
                        <div class="col-span-2 md:col-span-1">
                            <Link href="/" class="flex items-center gap-2 group mb-4">
                            <AppLogoIcon class="w-5 h-5 text-white/90" />
                            <span class="font-medium text-lg tracking-tight">Ecnelis</span>
                            </Link>
                            <p class="text-sm text-white/40 leading-relaxed max-w-xs">
                                Designed to amplify your thoughts and streamline your workflow.
                            </p>
                        </div>

                        <div>
                            <h4 class="font-medium mb-6 text-white text-sm">Product</h4>
                            <ul class="space-y-4 text-sm text-white/50">
                                <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                                <li><a href="/changelog" class="hover:text-white transition-colors">Changelog</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Docs</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-medium mb-6 text-white text-sm">Company</h4>
                            <ul class="space-y-4 text-sm text-white/50">
                                <li><a href="/about" class="hover:text-white transition-colors">About</a></li>
                                <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                                <li><a href="/contact" class="hover:text-white transition-colors">Contact</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-medium mb-6 text-white text-sm">Legal</h4>
                            <ul class="space-y-4 text-sm text-white/50">
                                <li><a href="/privacy" class="hover:text-white transition-colors">Privacy</a></li>
                                <li><a href="/terms" class="hover:text-white transition-colors">Terms</a></li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-white/5 gap-6">
                        <p class="text-xs text-white/30">
                            © 2025 Ecnelis Studio. All rights reserved.
                        </p>
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
