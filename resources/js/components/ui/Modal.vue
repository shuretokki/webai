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
    <Teleport to="body">
        <AnimatePresence>
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                <Motion
                    :initial="{ opacity: 0 }"
                    :animate="{ opacity: 1 }"
                    :exit="{ opacity: 0 }"
                    class="absolute inset-0 bg-background/80 backdrop-blur-sm"
                    @click="close"
                />
                <Motion
                    :initial="{ opacity: 0, scale: 0.95, y: 10 }"
                    :animate="{ opacity: 1, scale: 1, y: 0 }"
                    :exit="{ opacity: 0, scale: 0.95, y: 10 }"
                    class="relative w-full max-w-md bg-popover border border-border rounded-none shadow-2xl overflow-hidden"
                >
                    <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                        <h3 class="text-lg font-space text-popover-foreground">{{ title }}</h3>
                        <button @click="close" class="text-muted-foreground hover:text-foreground transition-colors">
                            <i-solar-close-circle-linear class="text-xl" />
                        </button>
                    </div>

                    <div class="p-6">
                        <slot />
                    </div>
                </Motion>
            </div>
        </AnimatePresence>
    </Teleport>
</template>