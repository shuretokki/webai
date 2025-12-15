<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

interface UsageStats {
  messages: number;
  tokens: number;
  cost: string;
  bytes: string;
}

interface UsageData {
  stats: UsageStats;
  limits: {
    messages: number;
  };
  percentage: number;
  tier: string;
}

const loading = ref(true);
const error = ref('');
const usage = ref<UsageData | null>(null);

const showWarning = computed(() => {
  if (!usage.value)
    return false;

  return usage.value.percentage >= 80;
});

const warningMessage = computed(() => {
  if (!usage.value)
    return '';

  const remaining =
    usage.value.limits.messages -
    usage.value.stats.messages;

  if (usage.value.percentage >= 95) {
    return `${remaining} messages left this month!`;
  } else if (usage.value.percentage >= 80) {
    return `${remaining} messages remaining. Consider upgrading.`;
  }

  return ''

});

async function fetchUsage() {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('/api/usage/current');

    if (!response.ok) {
      throw new Error('Failed to load usage data');
    }

    usage.value = await response.json();
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Unknown error';
  } finally {
    loading.value = false;
  }
}

let refreshInterval: number | null = null;

onMounted(() => {
  fetchUsage();

  refreshInterval = setInterval(() => {
    fetchUsage();
  }, 30000);

});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});

</script>

<template>
  <AppLayout>
    <SettingsLayout>
      <div class="space-y-8">
        <div>
          <h3 class="text-2xl font-normal text-foreground mb-2">Usage & Billing</h3>
          <p class="text-muted-foreground mb-6">Track your AI usage and manage your subscription.</p>
        </div>

        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
          <p class="mt-4 text-muted-foreground font-space">Loading usage data...</p>
        </div>

        <div v-else-if="error" class="bg-destructive/10 border border-destructive/20 rounded-none p-4">
          <p class="text-destructive font-space">{{ error }}</p>
        </div>

        <div v-else-if="usage" class="space-y-8">
          <div class="flex items-center justify-between border-b border-white/10 pb-6">
            <div>
              <span class="text-sm text-muted-foreground uppercase tracking-wider">Current Plan</span>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-3xl font-normal text-foreground">{{ usage.tier.toUpperCase() }}</span>
                <span
                  class="px-2 py-0.5 text-xs bg-primary/20 text-primary border border-primary/30 rounded-full">Active</span>
              </div>
            </div>
            <div v-if="showWarning" class="bg-yellow-500/10 border border-yellow-500/50 p-3 rounded-none">
              <div class="flex items-center gap-2">
                <TriangleAlert class="text-yellow-500 text-lg" />
                <p class="text-sm text-yellow-500 font-space">{{ warningMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <div class="flex justify-between items-end mb-3">
              <label class="text-base font-medium text-foreground">Monthly Message Quota</label>
              <span class="text-sm text-foreground font-mono">{{ usage.stats.messages }} /
                {{ usage.limits.messages }}</span>
            </div>
            <div class="w-full bg-secondary/50 h-4 rounded-none overflow-hidden border border-white/5">
              <div class="h-full transition-all duration-500 ease-out"
                :class="usage.percentage >= 90 ? 'bg-destructive' : usage.percentage >= 70 ? 'bg-yellow-500' : 'bg-primary'"
                :style="{ width: usage.percentage + '%' }"></div>
            </div>
            <p class="mt-2 text-sm text-muted-foreground">{{ usage.percentage }}% used. Resets on the 1st of the month.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            <div class="group p-5 border border-white/10 bg-white/5 hover:bg-white/10 transition-colors rounded-none">
              <h3 class="text-xs font-space text-muted-foreground uppercase tracking-wider mb-2">AI Tokens</h3>
              <p class="text-2xl font-space text-foreground">{{ usage.stats.tokens.toLocaleString() }}</p>
            </div>

            <div class="group p-5 border border-white/10 bg-white/5 hover:bg-white/10 transition-colors rounded-none">
              <h3 class="text-xs font-space text-muted-foreground uppercase tracking-wider mb-2">Storage</h3>
              <p class="text-2xl font-space text-foreground">{{ usage.stats.bytes }}</p>
            </div>

            <div class="group p-5 border border-white/10 bg-white/5 hover:bg-white/10 transition-colors rounded-none">
              <h3 class="text-xs font-space text-muted-foreground uppercase tracking-wider mb-2">Est. Cost</h3>
              <p class="text-2xl font-space text-foreground">${{ usage.stats.cost }}</p>
            </div>
          </div>

          <div
            class="mt-8 flex items-center justify-between text-sm text-muted-foreground pt-6 border-t border-white/10">
            <span>Billing period: Monthly</span>
            <span>Next invoice: 1st of next month</span>
          </div>

        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>