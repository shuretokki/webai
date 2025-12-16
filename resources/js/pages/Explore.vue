<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import { Bot, Code, FileText, Image, Sparkles, Terminal, ArrowRight } from 'lucide-vue-next';

const agents = [];
</script>

<template>

    <Head title="Explore" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Explore
            </h2>
        </template>

        <div class="py-12 bg-black min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
                <!-- Banner -->
                <div
                    class="bg-gradient-to-r from-zinc-900 to-black border border-white/10 p-8 md:p-12 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20">
                    </div>
                    <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2864&auto=format&fit=crop"
                        class="absolute right-0 top-0 w-1/2 h-full object-cover opacity-20 mix-blend-overlay mask-image-gradient"
                        alt="Abstract" />
                    <div class="relative z-10 max-w-2xl">
                        <h1 class="text-3xl md:text-5xl font-bold text-white mb-6">Start your journey with AI.</h1>
                        <p class="text-zinc-400 text-lg mb-8 leading-relaxed">
                            Experience the power of our conversational agents. Ask questions, generate code, and explore
                            new
                            ideas.
                        </p>
                        <Link href="/chat"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white text-black font-medium hover:bg-zinc-200 transition-colors">
                        Start Chatting
                        <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <div class="p-4 md:p-0 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-white">Latest Updates</h2>
                        <Link href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">View all posts
                        </Link>
                    </div>

                    <div
                        class="w-full h-48 border border-white/10 border-dashed flex items-center justify-center bg-[#111]">
                        <p class="text-zinc-500 text-sm">No blog posts available yet.</p>
                    </div>
                </div>

                <div class="p-4 md:p-0 space-y-6">
                    <h2 class="text-2xl font-bold text-white">Explore Agents</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link href="/chat" class="block group">
                        <Card
                            class="bg-[#111] border-white/10 hover:border-white/20 transition-all p-4 rounded-none group-hover:bg-[#161616] flex flex-row items-center justify-between">
                            <div class="text-left">
                                <h3 class="font-bold text-white text-sm">Chat</h3>
                                <p class="text-zinc-500 text-xs">Your AI Assistant</p>
                            </div>
                            <ArrowRight class="w-4 h-4 text-zinc-600 group-hover:text-white transition-colors" />
                        </Card>
                        </Link>

                        <div class="block opacity-50 cursor-not-allowed">
                            <Card
                                class="bg-transparent border-white/5 p-4 rounded-none flex items-center justify-between border-dashed border">
                                <div>
                                    <h3 class="font-bold text-zinc-500 text-sm">More Coming Soon</h3>
                                    <p class="text-zinc-600 text-xs">Stay tuned</p>
                                </div>
                                <ArrowRight class="w-4 h-4 text-zinc-800" />
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
