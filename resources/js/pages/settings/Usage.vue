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
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          Usage & Billing
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Track your AI usage and manage your subscription
        </p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading usage data...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-red-800">{{ error }}</p>
      </div>

      <div v-else-if="usage" class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
              {{ usage.tier.toUpperCase() }} Plan
            </span>
          </div>
          <div v-if="showWarning"
            class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                  {{ warningMessage }}
                </p>
              </div>
            </div>
          </div>
          <!-- TODO: Re-enable when Xendit payment integration is implemented -->
          <!-- <Link
            href="/subscription"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-block"
          >
            Upgrade Plan
          </Link> -->
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              Messages
            </h3>
            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
              {{ usage.stats.messages }} / {{ usage.limits.messages }}
            </span>
          </div>

          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-300"
              :class="usage.percentage >= 90 ? 'bg-red-600' : usage.percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500'"
              :style="{ width: usage.percentage + '%' }"></div>
          </div>

          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ usage.percentage }}% of monthly quota used
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6"> <!-- Tokens -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
              AI Tokens Used
            </h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
              {{ usage.stats.tokens.toLocaleString() }}
            </p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
              Storage Used
            </h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
              {{ usage.stats.bytes }}
            </p>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
              Estimated Cost
            </h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
              ${{ usage.stats.cost }}
            </p>
          </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
          <p class="text-sm text-blue-800 dark:text-blue-200">
            <strong>Billing Cycle:</strong> Resets on the 1st of each month
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>