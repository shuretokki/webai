<script setup lang="ts">
import Modal from '@/components/ui/Modal.vue';

defineProps<{
    open: boolean
}>();

const emit = defineEmits(['update:open']);
</script>

<template>
    <Modal :show="open" title="Upgrade Plan" @close="emit('update:open', false)">
        <div class="flex flex-col gap-4">
            <p class="text-white/60 text-sm leading-relaxed">
                Access to premium models like GPT-4o and Claude 3.5 Sonnet requires a Pro subscription.
                Upgrade now to unlock advanced reasoning and faster speeds.
            </p>
            
            <div class="flex justify-end gap-3 mt-2">
                <button @click="emit('update:open', false)" 
                    class="px-4 py-2 text-sm text-white/60 hover:text-white transition-colors">
                    Cancel
                </button>
                <button @click="emit('update:open', false)" 
                    class="px-4 py-2 bg-[#dbf156] text-black rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Upgrade to Pro
                </button>
            </div>
        </div>
    </Modal>
</template>
