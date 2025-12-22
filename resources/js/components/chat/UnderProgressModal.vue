<script setup lang="ts">
import { Sparkles } from 'lucide-vue-next';
import { Motion, AnimatePresence } from 'motion-v';
import { ui } from '@/config/ui';

const isOpen = defineModel<boolean>('open', { default: false });

const close = () => {
    isOpen.value = false;
};
</script>

<template>
    <Teleport to="body">
        <AnimatePresence>
            <div v-if="isOpen"
                class="fixed inset-0 z-[100] flex items-center justify-center px-4 pointer-events-none">
                <Motion initial="initial" animate="animate" exit="exit" :variants="ui.modal.overlay"
                    class="absolute inset-0 bg-background/60 backdrop-blur-md pointer-events-auto" @click="close" />

                <Motion initial="{ opacity: 0, scale: 0.95, y: 20 }" animate="{ opacity: 1, scale: 1, y: 0 }"
                    exit="{ opacity: 0, scale: 0.95, y: 20 }"
                    :transition="{ type: 'spring', damping: 25, stiffness: 300 }"
                    class="relative w-full max-w-md pointer-events-auto font-sans"
                    :class="[ui.chat.search.classes.container]">

                    <div class="text-start p-8">
                        <h2 class="text-2xl font-semibold text-white mb-3">
                            Model Under Development
                        </h2>
                        <p class="text-white/60 text-sm leading-relaxed">
                            This model is currently being integrated into our platform.
                            Please use one of our available models in the meantime.
                        </p>
                    </div>

                    <div class="bg-white/[0.02] border border-white/5 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <Sparkles class="size-5 text-primary shrink-0 mt-0.5" />
                            <div>
                                <h3 class="text-sm font-medium text-white mb-1">Available Now</h3>
                                <p class="text-xs text-white/50 leading-relaxed">
                                    Gemini 2.5 Flash, Gemini 2.5 Flash Lite, and Llama 3.3 70B are ready to use with full features.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button @click="close"
                        class="w-full px-4 py-3 bg-primary text-black rounded-lg text-sm font-medium hover:opacity-90 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Got it
                    </button>
                </Motion>
            </div>
        </AnimatePresence>
    </Teleport>
</template>
