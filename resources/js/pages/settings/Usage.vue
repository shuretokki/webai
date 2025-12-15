<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

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
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-space font-bold text-foreground">
          Usage & Billing
        </h1>
        <p class="mt-2 text-muted-foreground font-space">
          Track your AI usage and manage your subscription
        </p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
        <p class="mt-4 text-muted-foreground font-space">Loading usage data...</p>
      </div>

      <div v-else-if="error" class="bg-destructive/10 border border-destructive/20 rounded-none p-4">
        <p class="text-destructive font-space">{{ error }}</p>
      </div>

      <div v-else-if="usage" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <span
              class="inline-flex items-center px-3 py-1 rounded-none text-sm font-space font-medium bg-primary/10 text-primary border border-primary/20">
              {{ usage.tier.toUpperCase() }} Plan
            </span>
          </div>
          <div v-if="showWarning" class="bg-yellow-500/10 border-l-4 border-yellow-500 p-4 rounded-none">
            <div class="flex">
              <div class="flex-shrink-0">
                <i-solar-danger-triangle-linear class="text-yellow-500 text-xl" />
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-500 font-space">
                  {{ warningMessage }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-card/30 border border-border rounded-none p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-space font-semibold text-foreground">
              Messages
            </h3>
            <span class="text-2xl font-space font-bold text-foreground">
              {{ usage.stats.messages }} / {{ usage.limits.messages }}
            </span>
          </div>

          <div class="w-full bg-secondary rounded-none h-3 overflow-hidden">
            <div class="h-3 rounded-none transition-all duration-300"
              :class="usage.percentage >= 90 ? 'bg-destructive' : usage.percentage >= 70 ? 'bg-yellow-500' : 'bg-primary'"
              :style="{ width: usage.percentage + '%' }"></div>
          </div>

          <p class="mt-2 text-sm text-muted-foreground font-space">
            {{ usage.percentage }}% of monthly quota used
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-card/30 border border-border rounded-none p-6">
            <h3 class="text-sm font-space font-medium text-muted-foreground mb-2">
              AI Tokens Used
            </h3>
            <p class="text-3xl font-space font-bold text-foreground">
              {{ usage.stats.tokens.toLocaleString() }}
            </p>
          </div>

          <div class="bg-card/30 border border-border rounded-none p-6">
            <h3 class="text-sm font-space font-medium text-muted-foreground mb-2">
              Storage Used
            </h3>
            <p class="text-3xl font-space font-bold text-foreground">
              {{ usage.stats.bytes }}
            </p>
          </div>

          <div class="bg-card/30 border border-border rounded-none p-6">
            <h3 class="text-sm font-space font-medium text-muted-foreground mb-2">
              Estimated Cost
            </h3>
            <p class="text-3xl font-space font-bold text-foreground">
              ${{ usage.stats.cost }}
            </p>
          </div>
        </div>

        <div class="bg-primary/5 border border-primary/20 rounded-none p-4">
          <p class="text-sm text-foreground font-space">
            <strong>Billing Cycle:</strong> Resets on the 1st of each month
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>