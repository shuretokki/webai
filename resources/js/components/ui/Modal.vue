<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { Motion, AnimatePresence } from 'motion-v';
defineProps<{
    show: boolean;
    title?: string;
}>();
const emit = defineEmits(['close']);
const close = () => emit('close');

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') close();
};
onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));
</script>
<template>
    <AnimatePresence>
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <Motion
                :initial="{ opacity: 0 }"
                :animate="{ opacity: 1 }"
                :exit="{ opacity: 0 }"
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                @click="close"
            />
            <Motion
                :initial="{ opacity: 0, scale: 0.95, y: 10 }"
                :animate="{ opacity: 1, scale: 1, y: 0 }"
                :exit="{ opacity: 0, scale: 0.95, y: 10 }"
                class="relative w-full max-w-md bg-[#1e1e1e] border border-white/10 rounded-2xl shadow-2xl overflow-hidden"
            >
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h3 class="text-lg font-space text-white">{{ title }}</h3>
                    <button @click="close" class="text-white/40 hover:text-white transition-colors">
                        <i-solar-close-circle-linear class="text-xl" />
                    </button>
                </div>

                <div class="p-6">
                    <slot />
                </div>
            </Motion>
        </div>
    </AnimatePresence>
</template>