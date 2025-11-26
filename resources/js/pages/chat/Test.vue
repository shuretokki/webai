<script setup lang="ts">

defineProps<{
  response: string
}>();

</script>
<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold">{{ response }}</h1>
    </div>
</template>