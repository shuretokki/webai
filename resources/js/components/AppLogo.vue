<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex items-center gap-2">
        <div
            class="flex aspect-square size-8 items-center justify-center rounded-none text-sidebar-primary-foreground"
        >
            <AppLogoIcon class="size-6 fill-current text-primary" />
        </div>
        <div class="grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate leading-tight font-bold font-philosopher text-xl tracking-tight bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent"
                >Ecnelis</span
            >
        </div>
    </div>
</template>
