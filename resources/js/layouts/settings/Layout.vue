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
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4 md:p-8">
        <div class="w-full max-w-5xl bg-card border border-border rounded-xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
            <aside class="w-full md:w-64 bg-muted/10 border-b md:border-b-0 md:border-r border-border p-6 flex flex-col gap-6">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-foreground">Settings</h2>
                    <p class="text-sm text-muted-foreground">Manage your account</p>
                </div>

                <nav class="flex flex-col space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            urlIsActive(item.href, currentPath)
                                ? 'bg-muted hover:bg-muted'
                                : 'hover:bg-transparent hover:underline',
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <div class="flex-1 p-6 md:p-10 overflow-y-auto">
                <slot />
            </div>
        </div>
    </div>
</template>
