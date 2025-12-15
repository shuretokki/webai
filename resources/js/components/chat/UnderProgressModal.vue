<script setup lang="ts">
import Modal from '@/components/ui/Modal.vue';

defineProps<{
    open: boolean
}>();

const emit = defineEmits(['update:open']);
</script>

<template>
    <Modal :show="open" title="Under Progress" @close="emit('update:open', false)">
        <div class="flex flex-col gap-4">
            <p class="text-white/60 text-sm leading-relaxed">
                This model is currently under development or integration is in progress.
                Please check back later or use one of the available models.
            </p>

            <div class="flex justify-end gap-3 mt-2">
                <button @click="emit('update:open', false)"
                    class="px-4 py-2 bg-[#dbf156] text-black rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Got it
                </button>
            </div>
        </div>
    </Modal>
</template>
