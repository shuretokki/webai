<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import UserInfo from '@/components/UserInfo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, MessageSquare } from 'lucide-vue-next';
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
            <div class="flex h-16 items-center px-4 sm:px-8">
                <div class="flex items-center gap-8">
                    <Link href="/dashboard" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                    <AppLogo class="h-6 w-auto" />
                    </Link>

                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                        <template v-for="item in navItems" :key="item.href">
                            <a v-if="item.external" :href="item.href" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2 text-muted-foreground transition-colors hover:text-foreground">
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
                            </a>
                            <Link v-else :href="item.href"
                                class="flex items-center gap-2 text-muted-foreground transition-colors hover:text-foreground"
                                :class="{ 'text-foreground': $page.url === item.href }">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                            </Link>
                        </template>
                    </nav>
                </div>

                <div class="ml-auto flex items-center gap-4">
                    <Button variant="ghost" as-child class="text-muted-foreground hover:text-foreground">
                        <Link href="/chat">
                        <MessageSquare class="mr-2 h-4 w-4" />
                        Return to Chat
                        </Link>
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" class="relative gap-2 hover:bg-white/5">
                                <UserInfo :user="user" class="text-sm" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
