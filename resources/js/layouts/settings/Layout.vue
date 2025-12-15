<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: editProfile(),
    },
    {
        title: 'Password',
        href: editPassword(),
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
    },
    {
        title: 'Appearance',
        href: editAppearance(),
    },
    {
        title: 'Billing & Usage',
        href: '/settings/usage',
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="min-h-screen bg-background text-foreground font-space">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-10">
                <h1 class="text-3xl font-normal text-foreground">Account settings</h1>
            </div>

            <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
                <aside class="w-full md:w-64 shrink-0 border-b border-white/10 md:border-b-0 pb-6 md:pb-0 mb-6 md:mb-0">
                    <nav
                        class="flex flex-row md:flex-col overflow-x-auto md:overflow-visible gap-2 md:gap-1 pb-2 md:pb-0 scrollbar-hide">
                        <Link v-for="item in sidebarNavItems" :key="toUrl(item.href)" :href="item.href" :class="[
                            'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap',
                            urlIsActive(item.href, currentPath)
                                ? 'text-foreground font-bold bg-white/5 md:bg-transparent'
                                : 'text-muted-foreground hover:text-foreground'
                        ]">
                        <span
                            :class="{ 'opacity-100': urlIsActive(item.href, currentPath), 'opacity-0 group-hover:opacity-50 transition-opacity': !urlIsActive(item.href, currentPath) }"
                            class="mr-2 text-primary hidden md:inline-block">
                            <!-- Indicator dot -->
                            ●
                        </span>
                        {{ item.title }}
                        </Link>
                    </nav>
                </aside>

                <main class="flex-1 min-w-0">
                    <div class="max-w-3xl">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
