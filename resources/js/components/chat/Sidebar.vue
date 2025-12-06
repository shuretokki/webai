<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ref } from 'vue';
import { chat as Chat } from '@/routes/index'

defineProps<{
    isOpen?: boolean;
    chats: Array<{id:number,title:string,created_at:string}>;
}>();

const emit = defineEmits(['close']);
const isCollapsed = ref(false);

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <Motion
        :initial="{ opacity: 0, x: -20 }"
        :animate="{
            opacity: 1,
            x: 0,
            width: isCollapsed ? '80px' : '268px'
        }"
        :transition="{
            type: 'spring',
            duration: 0.5,
            bounce: 0
        }"
        class="shrink-0 relative h-full flex flex-col items-start content-stretch bg-[#1e1e1e] border-r border-white/10 overflow-hidden z-20"
        :class="[isCollapsed ? 'items-center' : 'items-start']"
    >
        <div class="w-full shrink-0 relative h-[60px] flex items-center" :class="[isCollapsed ? 'justify-center' : 'justify-between px-4']">
            <div class="size-8 flex items-center justify-center text-white">
                <i-solar-atom-bold-duotone class="text-2xl text-[#dbf156]" />
            </div>
            <button @click="$emit('close')" class="md:hidden text-white" v-if="!isCollapsed">
                <i-solar-close-circle-linear class="text-2xl" />
            </button>
        </div>

        <div class="w-full shrink-0 flex flex-col gap-2 px-2 mt-2">
            <div
                class="w-full relative flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors group"
                :class="[isCollapsed ? 'justify-center' : '']"
            >
                <i-solar-magnifer-linear class="text-xl text-white/60 group-hover:text-white transition-colors" />
                <p v-if="!isCollapsed" class="font-space font-normal text-[16px] text-white/80 group-hover:text-white transition-colors whitespace-nowrap">Search</p>
            </div>

            <Link
                :href="Chat().url"
                class="w-full relative flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors group"
                :class="[isCollapsed ? 'justify-center' : '']"
            >
                <i-solar-pen-new-square-linear class="text-xl text-white/60 group-hover:text-white transition-colors" />
                <p v-if="!isCollapsed" class="font-space font-normal text-[16px] text-white/80 group-hover:text-white transition-colors whitespace-nowrap">New Chat</p>
        </Link>
        </div>

        <div v-if="!isCollapsed" class="w-full shrink-0 relative flex items-center px-4 py-2 mt-4">
            <p class="font-space font-normal text-sm text-white/40 uppercase tracking-wider">History</p>
        </div>
        <div v-else class="w-full h-px bg-white/10 my-4 mx-2"></div>

        <div class="w-full shrink-0 flex flex-col flex-1 gap-1 overflow-y-auto overflow-x-hidden px-2 custom-scrollbar">
            <p v-if="!isCollapsed" class="px-2 py-1 font-space text-xs text-white/30">
                Recent Chats
            </p>

            <Link
                v-for="chat in chats"
                :key="chat.id"
                :href="Chat({query: {chat_id: chat.id}}).url"
                class="w-full flex items-center p-2 gap-3 hover:bg-white/5 cursor-pointer transition-colors"
                :class="[isCollapsed ? 'justify-center' : '']"
            >
            <i-solar-chat-round-line-linear class="text-white/40 group-hover:text-[#dbf156] transition-colors shrink-0" />
            <p v-if ="!isCollapsed" class="flex-1 font-space text-sm text-white/70 trunctae group-hover:text-white transition-colors">
                {{  chat.title || 'Untitled' }}
            </p>
        </Link>

        <div v-if="chats.length === 0 && !isCollapsed" class="px-4 py-8 text-center text-white/20 text-xs">
            No history yet.
        </div>


        </div>
        <div class="w-full shrink-0 mt-auto pt-2 pb-4 px-2 border-t border-white/10 flex flex-col gap-2">
            <div
                class="w-full flex items-center p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-colors group relative"
                :class="[isCollapsed ? 'flex-col justify-center gap-4' : 'justify-between']"
            >
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-full bg-gradient-to-br from-[#dbf156] to-[#acb564] flex items-center justify-center text-black font-bold shrink-0">
                        U
                    </div>
                </div>

                <button
                    @click.stop="toggleCollapse"
                    class="text-white/40 hover:text-white transition-colors"
                    :class="[isCollapsed ? '' : '']"
                >
                    <i-solar-alt-arrow-right-linear v-if="isCollapsed" class="text-xl" />
                    <i-solar-alt-arrow-left-linear v-else class="text-xl" />
                </button>
            </div>
        </div>
    </Motion>
</template>