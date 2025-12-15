<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import type { BreadcrumbItemType } from '@/types';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const user = page.props.auth.user;

const navItems = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit', // Keeping original link or updating if needed
        icon: Folder,
        external: true,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue', // Keeping original link
        icon: BookOpen,
        external: true,
    },
];
</script>

<template>
    <div class="min-h-screen bg-background text-foreground font-space">
        <header
            class="sticky top-0 z-50 w-full border-b border-white/10 bg-background/80 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="flex h-16 items-center px-4 sm:px-8 justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                    <AppLogo class="h-8 w-auto" />
                    </Link>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <button
                                class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-md hover:bg-white/5 transition-colors focus:outline-none">
                                <Avatar class="h-6 w-6 overflow-hidden rounded-full">
                                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                                    <AvatarFallback class="rounded-full bg-muted text-xs">{{ user.name.charAt(0) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-medium text-foreground">{{ user.name }}</span>
                                <ChevronsUpDown class="h-3 w-3 text-muted-foreground" />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="start" class="w-56 mt-2 bg-[#1a1a1a] border-white/10 text-gray-300">
                            <UserMenuContent :user="user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                <div class="flex items-center gap-6">
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Pricing</a>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Enterprise</a>
                    <a href="https://laravel.com/docs/starter-kits#vue" target="_blank"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Docs</a>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Blog</a>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
