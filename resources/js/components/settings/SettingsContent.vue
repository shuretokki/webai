<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

const props = defineProps<{
  activeTab: string;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const settings = ref({
  autoScroll: true,
  enableLog: true,
  errorReporting: true,
  experimental1: false,
  experimental2: false,
  experimental3: false,
});

const handleUpgrade = () => {
  window.location.href = '/settings/subscription';
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
          <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
          <span v-else>{{ user?.name?.charAt(0) || 'U' }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-space font-medium text-foreground truncate">{{ user?.name }}</h4>
          <p class="text-xs text-muted-foreground font-space truncate">{{ user?.email }}</p>
        </div>
        <Link href="/settings/profile"
          class="px-3 py-1.5 text-xs border border-border rounded-full hover:bg-white/5 hover:border-primary/50 transition-colors text-muted-foreground hover:text-foreground">
        Manage
        </Link>
      </div>

      <div
        class="bg-card/10 border border-border p-6 flex flex-col items-center justify-center gap-3 text-center rounded-none">
        <div class="size-12 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10 mb-2">
          <div class="size-8 rounded-full border border-primary bg-primary/20"></div>
        </div>
        <h3 class="font-space text-lg font-medium">Get ECNELIS+</h3>
        <p class="text-sm text-muted-foreground max-w-[200px]">Unlock more features and higher limits.
        </p>
        <button @click="handleUpgrade"
          class="mt-2 px-6 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary hover:bg-primary/20 transition-colors text-sm font-medium">
          Upgrade
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'behavior'" class="space-y-1 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none">
        <span class="text-sm font-space text-foreground">Enable Auto Scroll</span>
        <button @click="settings.autoScroll = !settings.autoScroll"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
          :class="settings.autoScroll ? 'bg-primary' : 'bg-input'">
          <span class="sr-only">Enable Auto Scroll</span>
          <span
            class="inline-block size-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-sm"
            :class="settings.autoScroll ? 'translate-x-6' : 'translate-x-1'" />
        </button>
      </div>

      <div class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none">
        <span class="text-sm font-space text-foreground">Enable Log</span>
        <button @click="settings.enableLog = !settings.enableLog"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
          :class="settings.enableLog ? 'bg-primary' : 'bg-input'">
          <span class="sr-only">Enable Log</span>
          <span
            class="inline-block size-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-sm"
            :class="settings.enableLog ? 'translate-x-6' : 'translate-x-1'" />
        </button>
      </div>

      <div class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none">
        <span class="text-sm font-space text-foreground">Enable Error Reporting</span>
        <button @click="settings.errorReporting = !settings.errorReporting"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
          :class="settings.errorReporting ? 'bg-primary' : 'bg-input'">
          <span class="sr-only">Enable Error Reporting</span>
          <span
            class="inline-block size-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-sm"
            :class="settings.errorReporting ? 'translate-x-6' : 'translate-x-1'" />
        </button>
      </div>

      <div class="h-px bg-border my-2"></div>

      <div
        class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none opacity-60">
        <span class="text-sm font-space text-foreground">Enable #1</span>
        <button disabled
          class="relative inline-flex h-6 w-11 items-center rounded-full bg-input transition-colors cursor-not-allowed">
          <span class="translate-x-1 inline-block size-4 transform rounded-full bg-white/50 shadow-sm"></span>
        </button>
      </div>
      <div
        class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none opacity-60">
        <span class="text-sm font-space text-foreground">Enable #2</span>
        <button disabled
          class="relative inline-flex h-6 w-11 items-center rounded-full bg-input transition-colors cursor-not-allowed">
          <span class="translate-x-1 inline-block size-4 transform rounded-full bg-white/50 shadow-sm"></span>
        </button>
      </div>
      <div
        class="flex items-center justify-between py-3 px-2 hover:bg-white/5 transition-colors rounded-none opacity-60">
        <span class="text-sm font-space text-foreground">Enable #3</span>
        <button disabled
          class="relative inline-flex h-6 w-11 items-center rounded-full bg-input transition-colors cursor-not-allowed">
          <span class="translate-x-1 inline-block size-4 transform rounded-full bg-white/50 shadow-sm"></span>
        </button>
      </div>
    </div>

    <div v-if="activeTab === 'customize'"
      class="h-full flex flex-col items-center justify-center text-muted-foreground animate-in fade-in slide-in-from-right-4 duration-300">
      <i-solar-pallete-2-linear class="text-4xl mb-4 opacity-50" />
      <p class="font-space text-sm">Theme customization coming soon</p>
    </div>

    <div v-if="activeTab === 'data'" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
      <div class="space-y-1">
        <h4 class="font-space text-sm font-medium text-foreground">Usage</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3">
            <i-solar-chart-square-linear class="text-xl text-muted-foreground" />
            <span class="text-sm font-space font-medium text-foreground">Data Usage</span>
          </div>
          <Link href="/settings/usage"
            class="px-4 py-1.5 rounded-full border border-border text-xs hover:bg-white/5 transition-colors font-medium">
          View
          </Link>
        </div>

        <h4 class="font-space text-sm font-medium text-foreground mt-4">Sensitive</h4>
        <div class="flex items-center justify-between py-4 border-b border-border">
          <div class="flex items-center gap-3 text-destructive">
            <i-solar-trash-bin-trash-linear class="text-xl" />
            <span class="text-sm font-space font-medium">Delete all chats</span>
          </div>
          <button @click="handleDeleteAllChats"
            class="px-4 py-1.5 rounded-full border border-destructive/50 text-destructive text-xs hover:bg-destructive/10 transition-colors font-medium">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
