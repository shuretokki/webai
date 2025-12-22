<script setup lang="ts">
import { onMounted, onUnmounted, computed, ref } from 'vue';
import { Motion, AnimatePresence } from 'motion-v';
import { X } from 'lucide-vue-next';
import { ui } from '@/config/ui';

const props = defineProps<{
    show: boolean;
    title?: string;
    maxWidth?: string;
    contentClass?: string;
    hideHeader?: boolean;
    align?: 'center' | 'bottom';
    type?: 'modal' | 'drawer';
    rounded?: string;
    bg?: string;
}>();

const emit = defineEmits(['close']);
const close = () => emit('close');

// Reactive drag progress for overlay opacity
const dragY = ref(0);
const overlayOpacity = computed(() => {
    if (!isDrawer.value || dragY.value <= 0) return 1;
    // Fade out as it drags down (max threshold 400px)
    return Math.max(0, 1 - (dragY.value / 400));
});

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') close();
};

onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));

const isDrawer = computed(() => props.type === 'drawer' || props.align === 'bottom');

const maxWidthClass = computed(() => {
    if (isDrawer.value) return 'max-w-3xl';
    switch (props.maxWidth) {
        case 'sm': return 'max-w-sm';
        case 'md': return 'max-w-md';
        case 'lg': return 'max-w-lg';
        case 'xl': return 'max-w-xl';
        case '2xl': return 'max-w-2xl';
        case '3xl': return 'max-w-3xl';
        case '4xl': return 'max-w-4xl';
        case '5xl': return 'max-w-5xl';
        case '6xl': return 'max-w-6xl';
        default: return 'max-w-md';
    }
});

const containerClasses = computed(() => {
    if (isDrawer.value) {
        return 'items-end justify-center px-0';
    }
    return 'items-center justify-center px-4';
});

const handleDrag = (_: any, info: any) => {
    dragY.value = info.offset.y;
};

const handleDragEnd = (_: any, info: any) => {
    const threshold = 120;
    const velocityThreshold = 400;

    if (info.offset.y > threshold || info.velocity.y > velocityThreshold) {
        close();
    } else {
        dragY.value = 0;
    }
};

</script>

<template>
    <Teleport to="body">
        <AnimatePresence>
            <div v-if="show" class="fixed inset-0 z-50 flex overflow-hidden" :class="containerClasses">
                <Motion :initial="ui.modal.overlay.initial" :animate="{ opacity: overlayOpacity }"
                    :exit="ui.modal.overlay.exit" class="absolute inset-0 bg-background/80 backdrop-blur-sm"
                    @click="close" />

                <Motion v-if="isDrawer" :initial="ui.modal.drawer.initial" :animate="ui.modal.drawer.animate"
                    :exit="ui.modal.drawer.exit" :transition="ui.modal.drawer.transition"
                    :drag="ui.modal.drawer.drag.y ? 'y' : false" :drag-constraints="{ top: 0, bottom: 500 }"
                    :drag-elastic="{ top: 0.05, bottom: 0.5 }" @drag="handleDrag" @drag-end="handleDragEnd"
                    class="relative w-full bg-popover border-t border-x border-border rounded-t-[28px] shadow-[0_-12px_40px_rgba(0,0,0,0.3)] overflow-hidden flex flex-col max-h-[94vh]"
                    :class="[maxWidthClass]">
                    <!-- Grab Handle -->
                    <div class="shrink-0 pt-3 pb-1 touch-none group">
                        <div
                            class="w-12 h-1.5 bg-border/40 group-active:bg-primary/40 rounded-full mx-auto my-3 cursor-grab active:cursor-grabbing transition-colors" />
                    </div>

                    <div v-if="!hideHeader"
                        class="flex items-center justify-between px-6 py-2 border-b border-border/10">
                        <h3 class="text-xl font-space font-medium text-foreground tracking-tight">{{ title }}</h3>
                        <button @click="close"
                            class="p-2 -mr-2 text-muted-foreground hover:text-foreground transition-colors">
                            <X class="size-6" />
                        </button>
                    </div>

                    <div :class="[contentClass || 'p-6', 'overflow-y-auto custom-scrollbar flex-1 pb-safe']">
                        <slot />
                    </div>
                </Motion>

                <Motion v-else :initial="{ opacity: 0, scale: 0.96, y: 12 }" :animate="{ opacity: 1, scale: 1, y: 0 }"
                    :exit="{ opacity: 0, scale: 0.96, y: 12 }"
                    :transition="{ type: 'spring', damping: 28, stiffness: 350 }" :class="[
                        'relative w-full border border-border overflow-hidden',
                        rounded || 'rounded-2xl',
                        bg || 'bg-popover',
                        !rounded && 'shadow-2xl',
                        maxWidthClass
                    ]">
                    <div v-if="!hideHeader"
                        class="flex items-center justify-between px-6 py-4 border-b border-border/10">
                        <h3 class="text-xl font-space font-medium text-foreground tracking-tight">{{ title }}</h3>
                        <button @click="close"
                            class="p-2 -mr-2 text-muted-foreground hover:text-foreground transition-colors">
                            <X class="size-6" />
                        </button>
                    </div>

                    <div :class="contentClass || 'p-6'">
                        <slot />
                    </div>
                </Motion>
            </div>
        </AnimatePresence>
    </Teleport>
</template>