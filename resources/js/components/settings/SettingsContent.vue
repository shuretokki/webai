<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { User, Palette, BarChart2, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
  activeTab: string;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const autoScroll = useStorage('settings_auto_scroll', true);

const handleUpgrade = () => {
  window.location.href = '/pricing';
};

const handleDeleteAllChats = () => {
  if (confirm('Are you sure you want to delete all chats? This action cannot be undone.')) {
    console.log('Delete all chats requested');
  }
};
</script>

<template>
  <div class="h-full">
    <div v-if="activeTab === 'account'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="flex items-center p-4 border border-border bg-card/20 rounded-none gap-4">
        <div
          class="size-12 rounded-full bg-muted flex items-center justify-center text-xl font-bold text-muted-foreground overflow-hidden border border-border">
          <User class="size-6 object-cover" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-space font-medium text-foreground truncate text-lg md:text-base">{{ user?.name }}</h4>
          <p class="text-sm md:text-xs text-muted-foreground font-space truncate">{{ user?.email }}</p>
        </div>
        <Link href="/settings/profile"
          class="px-3 py-1.5 text-base md:text-xs border border-border rounded-full hover:bg-white/5 hover:border-primary/50 transition-colors text-muted-foreground hover:text-foreground">
        Manage
        </Link>
      </div>

      <div
        class="bg-card/10 border border-border p-6 flex flex-col items-center justify-center gap-3 text-center rounded-none">
        <div class="size-12 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10 mb-2">
          <div class="size-8 rounded-full border border-primary bg-primary/20"></div>
        </div>
        <h3 class="font-space font-medium text-xl md:text-lg">Get ECNELIS+</h3>
        <p class="text-lg md:text-sm text-muted-foreground max-w-[200px]">Unlock more features and higher limits.
        </p>
        <button @click="handleUpgrade"
          class="mt-2 px-6 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary hover:bg-primary/20 transition-colors text-lg md:text-sm font-medium">
          Upgrade
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'behavior'" class="space-y-1 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none">
        <span class="text-lg md:text-sm font-space text-foreground">Enable Auto Scroll</span>
        <button @click="autoScroll = !autoScroll"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
          :class="autoScroll ? 'bg-primary' : 'bg-input'">
          <span class="sr-only">Enable Auto Scroll</span>
          <span
            class="inline-block size-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-sm"
            :class="autoScroll ? 'translate-x-6' : 'translate-x-1'" />
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'customize'"
      class="h-full flex flex-col items-center justify-center text-muted-foreground animate-in fade-in slide-in-from-right-4 duration-300">
      <Palette class="text-4xl mb-4 opacity-50 size-10" />
      <p class="font-space text-sm">Theme customization coming soon</p>
    </div>

    <div v-if="activeTab === 'data'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="space-y-1">
        <h4 class="font-space text-sm font-medium text-foreground">Usage</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3">
            <BarChart2 class="text-xl text-muted-foreground size-5" />
            <span class="text-lg md:text-sm font-space font-medium text-foreground">Data Usage</span>
          </div>
          <Link href="/settings/usage"
            class="px-4 py-1.5 rounded-full border border-border text-base md:text-xs hover:bg-white/5 transition-colors font-medium">
          View
          </Link>
        </div>

        <h4 class="font-space text-sm font-medium text-foreground mt-4">Sensitive</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3 text-destructive">
            <Trash2 class="text-xl size-5" />
            <span class="text-lg md:text-sm font-space font-medium">Delete all chats</span>
          </div>
          <button @click="handleDeleteAllChats"
            class="px-4 py-1.5 rounded-full border border-destructive/50 text-destructive text-base md:text-xs hover:bg-destructive/10 transition-colors font-medium">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
