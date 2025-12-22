<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <Link href="/explore" class="flex items-center group">
    <img src="/assets/LOGO.svg" alt="Ecnelis" class="h-6 w-auto md:hidden group-hover:opacity-80 transition-opacity" />
    <img src="/assets/LOGOTYPE.svg" alt="Ecnelis"
        class="hidden md:block h-6 w-auto group-hover:opacity-80 transition-opacity" />
    </Link>
</template>
