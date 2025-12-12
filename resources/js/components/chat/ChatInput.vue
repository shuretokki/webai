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
    <form @submit.prevent="submit"
        class="w-full max-w-[600px] shrink-0 relative flex items-end gap-2 p-2 bg-[#1e1e1e] border-l-4 border-[#abb463] rounded-none shadow-lg transition-all focus-within:ring-1 focus-within:ring-[#abb463]/50">
        <button type="button"
            class="shrink-0 p-3 text-white/60 hover:text-[#dbf156] cursor-pointer transition-colors rounded-none hover:bg-white/5 h-[48px] w-[48px] flex items-center justify-center">
            <i-solar-paperclip-linear class="text-xl" />
        </button>

        <div class="flex-1 min-w-0 py-3">
            <textarea ref="textarea" v-model="input" rows="1" placeholder="Type a message..." @keydown="handleKeydown"
                class="w-full bg-transparent border-none outline-none font-space font-normal text-[16px] text-[#f8ffd7] placeholder-white/30 focus:ring-0 p-0 resize-none max-h-[200px] overflow-y-auto custom-scrollbar"></textarea>
        </div>

        <button type="submit" :disabled="!input.trim()"
            class="shrink-0 size-[48px] relative flex items-center justify-center rounded-none bg-gradient-to-br from-[#dbf156] to-[#acb564] text-black hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-all overflow-hidden group">
            <div class="absolute inset-0 opacity-30">
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/50 via-transparent to-transparent scale-0 group-hover:scale-150 transition-transform duration-500">
                </div>
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 Q 50 50 100 100" stroke="white" stroke-width="2" fill="none" opacity="0.2" />
                    <path d="M-20 100 Q 50 30 120 100" stroke="white" stroke-width="2" fill="none" opacity="0.2" />
                    <path d="M-40 100 Q 50 10 140 100" stroke="white" stroke-width="2" fill="none" opacity="0.2" />
                </svg>
            </div>

            <i-solar-arrow-right-linear class="text-2xl relative z-10" />
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