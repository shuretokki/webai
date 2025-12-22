<script setup lang="ts">
import { usePage, Link, router } from '@inertiajs/vue3';
import { useStorage } from '@vueuse/core';
import { User, Palette, BarChart2, Trash2, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
  activeTab: string;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);
const mustVerifyEmail = computed(() => page.props.mustVerifyEmail || false);

const autoScroll = useStorage('settings_auto_scroll', true);

const handleUpgrade = () => {
  window.location.href = '/pricing';
};

const handleDeleteAllChats = () => {
  if (confirm('Are you sure you want to delete all chats? This action cannot be undone.')) {
    router.delete('/c', {
      preserveState: false,
      onSuccess: () => {
        router.visit('/c');
      },
    });
  }
};
</script>

<template>
  <div class="h-full">
    <!-- Unverified Email Banner (shown globally in settings) -->
    <div v-if="mustVerifyEmail && user && !user.email_verified_at"
      class="mb-4 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-none flex items-start gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
      <AlertCircle class="size-5 text-yellow-600 dark:text-yellow-500 shrink-0 mt-0.5" />
      <div class="flex-1">
        <p class="text-sm text-yellow-600 dark:text-yellow-500">
          <span class="font-medium">Email verification required.</span>
          Your email <span class="font-medium">{{ user.email }}</span> is not verified.
          <Link href="/settings/profile" class="underline hover:no-underline ml-1">
            Update email settings
          </Link>
          or
          <Link href="/email/verification-notification" method="post" as="button"
            class="underline hover:no-underline ml-1">
            resend verification email
          </Link>.
        </p>
      </div>
    </div>

    <div v-if="activeTab === 'account'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="flex items-center p-4 border border-border bg-muted/20 rounded-none gap-4">
        <div
          class="size-12 rounded-full bg-muted flex items-center justify-center text-xl font-bold text-muted-foreground overflow-hidden border border-border">
          <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
          <User v-else class="size-6" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-inter font-medium text-foreground truncate text-xl md:text-lg">{{ user?.name }}</h4>
          <p class="text-base md:text-sm text-muted-foreground font-inter truncate">{{ user?.email }}</p>
        </div>
        <Link href="/settings/profile"
          class="px-3 py-1.5 text-base md:text-xs border border-border rounded-full hover:bg-white/5 hover:border-primary/50 transition-colors text-muted-foreground hover:text-foreground">
          Manage
        </Link>
      </div>

      <div
        class="bg-muted/10 border border-border p-6 flex flex-col items-center justify-center gap-3 text-center rounded-none">
        <div class="size-12 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10 mb-2">
          <div class="size-8 rounded-full border border-primary bg-primary/20"></div>
        </div>
        <h3 class="font-inter font-medium text-2xl md:text-xl">Get ECNELIS+</h3>
        <p class="text-xl md:text-base text-muted-foreground max-w-[200px]">Unlock more features and higher limits.
        </p>
        <button @click="handleUpgrade"
          class="mt-2 px-6 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary hover:bg-primary/20 transition-colors text-lg md:text-sm font-medium">
          Upgrade
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'behavior'" class="space-y-1 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none">
        <span class="text-xl md:text-base font-inter text-foreground">Enable Auto Scroll</span>
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
      <p class="font-inter text-sm">Theme customization coming soon</p>
    </div>

    <div v-if="activeTab === 'data'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="space-y-1">
        <h4 class="font-inter text-base font-medium text-foreground">Usage</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3">
            <BarChart2 class="text-xl text-muted-foreground size-5" />
            <span class="text-xl md:text-base font-inter font-medium text-foreground">Data Usage</span>
          </div>
          <Link href="/settings/usage"
            class="px-4 py-1.5 rounded-full border border-border text-base md:text-xs hover:bg-white/5 transition-colors font-medium">
            View
          </Link>
        </div>

        <h4 class="font-inter text-base font-medium text-foreground mt-4">Sensitive</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3 text-destructive">
            <Trash2 class="text-xl size-5" />
            <span class="text-xl md:text-base font-inter font-medium">Delete all chats</span>
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
