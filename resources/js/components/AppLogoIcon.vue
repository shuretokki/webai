<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        :class="className"
        v-bind="$attrs"
    >
        <circle cx="20" cy="20" r="18" fill="none" stroke="currentColor" stroke-width="2" />
        <circle cx="20" cy="20" r="12" fill="none" stroke="currentColor" stroke-width="2" />
        <circle cx="20" cy="20" r="6" fill="none" stroke="currentColor" stroke-width="2" />
    </svg>
</template>
