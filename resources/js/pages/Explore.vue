<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import {
    ArrowRight,
    MessageSquare,
    Zap
} from 'lucide-vue-next';
import { ui } from '@/config/ui';

// Original content preserved: No agents or news hallucinated
</script>

<template>

    <Head title="Explore" />

    <AppLayout>
        <div class="min-h-screen bg-black text-white/90 selection:bg-white/10">
            <div class="max-w-[1200px] mx-auto px-6 py-32">

                <!-- Minimal Hero -->
                <header class="mb-32 relative group">
                    <div
                        class="absolute -inset-20 bg-gradient-to-r from-blue-500/5 via-transparent to-purple-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000">
                    </div>
                    <Motion initial="{ opacity: 0, y: 20 }" animate="{ opacity: 1, y: 0 }"
                        :transition="{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }" class="relative z-10">
                        <div class="flex items-start justify-between gap-12">
                            <div class="flex-1">
                                <h1 class="text-[clamp(3rem,8vw,6rem)] font-light tracking-tighter leading-[0.95] mb-8">
                                    Start your <br />
                                    <span class="text-white/30 italic">journey with AI.</span>
                                </h1>
                                <p class="max-w-md text-base text-white/40 leading-relaxed font-light mb-12">
                                    Experience the power of our conversational agents. Ask questions, generate code, and
                                    explore new ideas.
                                </p>

                                <Link href="/chat" class="inline-flex items-center gap-4 group/btn">
                                    <div
                                        class="px-6 py-3 bg-white text-black text-sm font-medium hover:bg-white transition-all group-hover/btn:px-8">
                                        Start Chatting
                                    </div>
                                    <div
                                        class="size-11 border border-white/10 flex items-center justify-center group-hover/btn:border-white transition-colors">
                                        <ArrowRight class="size-4" />
                                    </div>
                                </Link>
                            </div>

                            <div
                                class="hidden lg:block w-96 aspect-[3/4] overflow-hidden bg-white/5 border border-white/10 relative">
                                <div class="absolute inset-0 bg-noise opacity-20 z-10"></div>
                                <img src="https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?q=80&w=2874&auto=format&fit=crop"
                                    class="w-full h-full object-cover grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-60 transition-all duration-1000 scale-110 group-hover:scale-100"
                                    alt="Abstract Intelligence" />
                                <div
                                    class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black to-transparent z-20">
                                </div>
                            </div>
                        </div>
                    </Motion>
                </header>

                <!-- Explore Agents -->
                <section class="mb-32">
                    <div class="flex items-center justify-between mb-12 border-b border-white/5 pb-4">
                        <span class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20">Explore
                            Agents</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-white/5 border border-white/5">
                        <Link href="/c" class="group relative bg-black p-10 transition-colors hover:bg-white/[0.02]">
                            <div class="size-10 mb-12 text-white/20 group-hover:text-white transition-colors">
                                <MessageSquare class="size-full" stroke-width="1" />
                            </div>

                            <h3 class="text-xl font-light mb-3 tracking-tight">Chat</h3>
                            <p class="text-sm text-white/30 leading-relaxed mb-10 max-w-[200px]">
                                Your AI Assistant
                            </p>

                            <div
                                class="flex items-center gap-2 text-[10px] uppercase tracking-widest text-white/20 opacity-0 group-hover:opacity-100 transition-all translate-x-[-4px] group-hover:translate-x-0">
                                Open Assistant
                                <ArrowRight class="size-3" />
                            </div>
                        </Link>

                        <!-- Coming Soon placeholder from original -->
                        <div
                            class="bg-black p-10 border-l border-white/5 flex flex-col justify-center items-center text-center opacity-30 cursor-default">
                            <Zap class="size-5 mb-4 text-white/20" />
                            <h3 class="text-sm font-light mb-1 tracking-tight">More Coming Soon</h3>
                            <p class="text-[10px] text-white/20 uppercase tracking-widest">Stay tuned</p>
                        </div>
                    </div>
                </section>

                <!-- Latest Updates -->
                <section>
                    <div class="flex items-center justify-between mb-12 border-b border-white/5 pb-4">
                        <span class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20">Latest
                            Updates</span>
                    </div>

                    <div class="py-20 flex items-center justify-center border border-white/5 border-dashed">
                        <p class="text-sm text-white/20 italic font-light tracking-wide">No blog posts available yet.
                        </p>
                    </div>
                </section>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
section {
    animation: reveal 1.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes reveal {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
