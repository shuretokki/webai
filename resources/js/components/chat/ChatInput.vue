<script setup lang="ts">
import { useTextareaAutosize } from '@vueuse/core';
import { ref } from 'vue';

const { textarea, input } = useTextareaAutosize();
const emit = defineEmits(['submit']);

const submit = () => {
    if (!input.value.trim()) return;
    emit('submit', input.value);
    input.value = '';
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submit();
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="w-full max-w-[600px] shrink-0 relative flex items-end gap-2 p-2 bg-[#1e1e1e] border-l-4 border-[#abb463] rounded-none shadow-lg transition-all focus-within:ring-1 focus-within:ring-[#abb463]/50">
        <!-- Attachment Button -->
        <button type="button" class="shrink-0 p-3 text-white/60 hover:text-[#dbf156] transition-colors rounded-none hover:bg-white/5 h-[48px] w-[48px] flex items-center justify-center">
            <i-solar-paperclip-linear class="text-xl" />
        </button>

        <!-- Input -->
        <div class="flex-1 min-w-0 py-3">
            <textarea
                ref="textarea"
                v-model="input"
                rows="1"
                placeholder="Type a message..."
                @keydown="handleKeydown"
                class="w-full bg-transparent border-none outline-none font-space font-normal text-[16px] text-[#f8ffd7] placeholder-white/30 focus:ring-0 p-0 resize-none max-h-[200px] overflow-y-auto custom-scrollbar"
            ></textarea>
        </div>

        <!-- Send Button -->
        <button
            type="submit"
            :disabled="!input.trim()"
            class="shrink-0 size-[48px] flex items-center justify-center rounded-none bg-gradient-to-br from-[#dbf156] to-[#acb564] text-black hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
        >
            <i-solar-plain-2-bold-duotone class="text-2xl" />
        </button>
    </form>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>