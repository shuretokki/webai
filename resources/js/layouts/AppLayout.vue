<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown, Menu, MessageSquare } from 'lucide-vue-next';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const user = page.props.auth.user;

</script>

<template>
    <div class="min-h-screen bg-background text-foreground font-space">
        <header
            class="sticky top-0 z-50 w-full border-b border-border bg-background/80 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="flex h-16 items-center px-4 sm:px-8 justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                    <AppLogo class="h-8 w-auto" />
                    </Link>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <button
                                class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-muted/50 transition-colors focus:outline-none">
                                <Avatar class="h-6 w-6 overflow-hidden rounded-full">
                                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                                    <AvatarFallback class="rounded-full bg-muted text-xs">{{ user.name.charAt(0) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span class="text-sm font-medium text-foreground">{{ user.name }}</span>
                                <ChevronsUpDown class="h-3 w-3 text-muted-foreground" />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="start" class="w-56 mt-2">
                            <UserMenuContent :user="user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-6">
                    <Link href="/chat"
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors mr-2">
                    <MessageSquare class="h-4 w-4" />
                    Return to Chat
                    </Link>
                    <Link href="/pricing"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                    Pricing</Link>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Enterprise</a>
                    <a href="https://laravel.com/docs/starter-kits#vue" target="_blank"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Docs</a>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Blog</a>
                </div>

                <!-- Mobile Nav -->
                <div class="md:hidden">
                    <Sheet>
                        <SheetTrigger as-child>
                            <Button variant="ghost" size="icon" class="text-muted-foreground">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent>
                            <div class="flex flex-col gap-6 mt-8">
                                <Link href="/chat" class="flex items-center gap-2 text-lg font-medium text-foreground">
                                <MessageSquare class="h-5 w-5" />
                                Return to Chat
                                </Link>
                                <Link href="/pricing"
                                    class="text-lg font-medium text-muted-foreground hover:text-foreground">Pricing
                                </Link>
                                <a href="#"
                                    class="text-lg font-medium text-muted-foreground hover:text-foreground">Enterprise</a>
                                <a href="https://laravel.com/docs/starter-kits#vue"
                                    class="text-lg font-medium text-muted-foreground hover:text-foreground">Docs</a>
                                <a href="#"
                                    class="text-lg font-medium text-muted-foreground hover:text-foreground">Blog</a>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
