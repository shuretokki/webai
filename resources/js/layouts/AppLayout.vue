<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Sparkles,
    MessageSquare,
    Menu,
    X,
    User,
    LogOut,
    Settings
} from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { ref, computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { router } from '@inertiajs/vue3';

defineProps<{
    breadcrumbs?: any[];
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);
const mobileMenuOpen = ref(false);

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-black text-white font-space selection:bg-white/20">
        <header class="sticky top-0 z-50 w-full border-b border-white/5 bg-black/80 backdrop-blur">
            <div class="flex h-20 items-center px-6 md:px-8 justify-between max-w-[1400px] mx-auto">

                <div class="flex items-center gap-12">
                    <Link href="/explore" class="flex items-center group">
                        <img src="/assets/LOGO.svg" alt="Ecnelis"
                            class="h-6 w-auto md:hidden group-hover:opacity-80 transition-opacity" />
                        <img src="/assets/LOGOTYPE.svg" alt="Ecnelis"
                            class="hidden md:block h-6 w-auto group-hover:opacity-80 transition-opacity" />
                    </Link>

                    <nav v-if="user" class="hidden md:flex items-center gap-8">
                        <Link href="/explore" class="text-[11px] uppercase tracking-[0.2em] font-medium transition-all"
                            :class="$page.url === '/explore' ? 'text-white' : 'text-white/30 hover:text-white/60'">
                            Explore
                        </Link>
                        <Link href="/c" class="text-[11px] uppercase tracking-[0.2em] font-medium transition-all"
                            :class="$page.url.startsWith('/c') ? 'text-white' : 'text-white/30 hover:text-white/60'">
                            Chat
                        </Link>
                        <Link href="/enterprise"
                            class="text-[11px] uppercase tracking-[0.2em] font-medium transition-all"
                            :class="$page.url === '/enterprise' ? 'text-white' : 'text-white/30 hover:text-white/60'">
                            Enterprise
                        </Link>
                        <Link href="/docs" target="_blank"
                            class="text-[11px] uppercase tracking-[0.2em] font-medium text-white/30 hover:text-white/60 transition-all">
                            Docs
                        </Link>
                    </nav>

                    <nav v-else class="hidden md:flex items-center gap-6">
                        <Link href="/about"
                            class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                            About
                        </Link>
                        <Link href="/?section=pricing"
                            class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                            Pricing
                        </Link>
                        <Link href="/enterprise"
                            class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                            Enterprise
                        </Link>
                        <Link href="/blog" class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                            Blog
                        </Link>
                        <Link href="/changelog"
                            class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                            Changelog
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-4">

                    <DropdownMenu v-if="user">
                        <DropdownMenuTrigger class="focus:outline-none">
                            <div
                                class="flex items-center gap-3 pl-3 pr-1 py-1 rounded-sm hover:bg-white/5 transition-colors cursor-pointer border border-transparent hover:border-white/5">
                                <div class="hidden md:block text-right">
                                    <p class="text-sm font-medium text-white leading-none">{{ user.name }}</p>
                                </div>
                                <Avatar class="h-8 w-8 rounded-full border border-white/10">
                                    <AvatarImage :src="user.avatar || ''" />
                                    <AvatarFallback class="bg-white/10 text-white rounded-full">
                                        {{ user.name.charAt(0) }}
                                    </AvatarFallback>
                                </Avatar>
                            </div>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end"
                            class="w-56 bg-[#111] border-white/10 text-white rounded-sm p-1">
                            <DropdownMenuItem class="focus:bg-white/10 focus:text-white rounded-sm cursor-pointer">
                                <Link href="/settings" class="flex items-center w-full">
                                    <User class="w-4 h-4 mr-2 opacity-50" /> Profile
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem class="focus:bg-white/10 focus:text-white rounded-sm cursor-pointer">
                                <Link href="/settings" class="flex items-center w-full">
                                    <Settings class="w-4 h-4 mr-2 opacity-50" /> Settings
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator class="bg-white/10" />
                            <DropdownMenuItem @click="logout"
                                class="focus:bg-destructive/20 focus:text-destructive text-red-400 rounded-sm cursor-pointer">
                                <div class="flex items-center w-full">
                                    <LogOut class="w-4 h-4 mr-2 opacity-50" /> Log out
                                </div>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <div v-else class="hidden md:flex items-center gap-4">
                        <Link href="/login"
                            class="text-sm font-medium text-white hover:text-white/80 transition-colors">
                            Log in
                        </Link>
                        <Link href="/register"
                            class="px-5 py-2.5 bg-white text-black text-sm font-medium hover:bg-white/90 transition-colors rounded-sm">
                            Get Started
                        </Link>
                    </div>

                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 text-white/80 hover:text-white">
                        <Menu v-if="!mobileMenuOpen" />
                        <X v-else />
                    </button>
                </div>
            </div>
        </header>

        <div v-if="mobileMenuOpen" class="fixed inset-0 top-20 z-40 bg-black p-6 md:hidden border-t border-white/5">
            <nav v-if="user" class="flex flex-col gap-4">
                <Link href="/explore" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Explore
                </Link>
                <Link href="/c" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Chat</Link>
                <Link href="/enterprise" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">
                    Enterprise</Link>
                <Link href="/docs" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Docs</Link>
                <button @click="logout" class="text-lg font-medium text-red-400 py-2 text-left">Log out</button>
            </nav>
            <nav v-else class="flex flex-col gap-4">
                <Link href="/about" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">About</Link>
                <Link href="/pricing" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Pricing
                </Link>
                <Link href="/blog" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Blog</Link>
                <Link href="/changelog" class="text-lg font-medium text-white/80 py-2 border-b border-white/5">Changelog
                </Link>
                <div class="flex flex-col gap-4 mt-4">
                    <Link href="/login"
                        class="block w-full text-center py-4 border border-white/20 text-white/90 hover:bg-white/5 transition-colors">
                        Log in
                    </Link>
                    <Link href="/register"
                        class="block w-full text-center py-4 bg-white text-black font-medium hover:bg-white/90 transition-colors">
                        Get Started
                    </Link>
                </div>
            </nav>
        </div>

        <main>
            <slot />
        </main>
    </div>
</template>
