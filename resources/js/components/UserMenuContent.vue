<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Laptop, Sun, Moon } from 'lucide-vue-next';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>

    <DropdownMenuGroup>
        <DropdownMenuItem as-child>
            <Link :href="edit()" class="w-full cursor-pointer">
            Account settings
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem>
            API tokens
        </DropdownMenuItem>
        <DropdownMenuItem>
            View profile
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator class="bg-white/10 my-1" />

    <DropdownMenuGroup>
        <DropdownMenuItem>
            <div class="flex items-center gap-2">
                <div class="border border-white/20 rounded-full p-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                </div>
                Create organization
            </div>
        </DropdownMenuItem>
        <DropdownMenuItem as-child>
            <Link :href="logout()" as="button" @click="handleLogout"
                class="w-full text-[#ff6b6b] hover:text-[#ff6b6b] focus:text-[#ff6b6b] cursor-pointer flex items-center gap-2">
            <LogOut class="h-4 w-4 rotate-180" />
            Sign out
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator class="bg-white/10 my-1" />

    <div class="px-2 py-1.5 flex items-center justify-between text-sm">
        <span class="text-muted-foreground">Theme</span>
        <div class="flex items-center gap-1 border border-white/10 rounded-md p-1">
            <button class="p-1 rounded hover:bg-white/10 text-foreground">
                <Laptop class="h-3 w-3" />
            </button>
            <button class="p-1 rounded hover:bg-white/10 text-muted-foreground">
                <Sun class="h-3 w-3" />
            </button>
            <button class="p-1 rounded hover:bg-white/10 text-muted-foreground">
                <Moon class="h-3 w-3" />
            </button>
        </div>
    </div>
</template>
