<script setup lang="ts">
import { ref } from 'vue';
import { Motion } from 'motion-v';
import { Link } from '@inertiajs/vue3';
import { chat as Chat } from '@/routes/index'
import Modal from '@/components/ui/Modal.vue';

const props = defineProps<{
    isOpen?: boolean;
    chats: Array<{ id: number, title: string, created_at: string }>;
}>();

const searchQuery = ref('');
const isSearching = ref(false);

const filteredChats = computed(() => {
    if (!searchQuery.value)
        return props.chats;

    const query = searchQuery.value.toLowerCase();
    return props.chats.filter(chat =>
        (chat.title || 'Untitled').toLowerCase().includes(query)
    );
});

const startSearch = () => {
    isSearching.value = true;
}

const showEditModal = ref(false);
const editingChat = ref<{ id: number, title: string } | null>(null);

const openEditModal = (chat: { id: number, title: string }) => {
    editingChat.value = chat;
    editForm.title = chat.title;
    showEditModal.value = true;
};


const editingChatId = ref<number | null>(null);
const editForm = useForm({
    title: ''
});

const startEditing = (chat: { id: number, title: string }) => {
    editingChatId.value = chat.id;
    editForm.title = chat.title;
};

const cancelEditing = () => {
    editingChatId.value = null;
    editForm.reset();
};

const saveTitle = () => {
    if (!editingChat.value) return;

    editForm.patch(`/chat/${editingChat.value.id}`, {
        onSuccess: () => {
            showEditModal.value = false;
            editingChat.value = null;
        }
    });
};

const emit = defineEmits(['close']);
const isCollapsed = ref(false);

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
};
</script>

<template>
    <Motion
        class="shrink-0 relative h-full flex flex-col items-start content-stretch bg-[#1e1e1e] border-r border-white/10 overflow-hidden z-20"
        :class="[isCollapsed ? 'items-center' : 'items-start']">
        <div class="w-full shrink-0 relative h-[60px] flex items-center"
            :class="[isCollapsed ? 'justify-center' : 'justify-between px-4']">
            <div class="size-8 flex items-center justify-center text-white">
                <div class="text-2xl text-[#dbf156]" />
            </div>
            <button @click="$emit('close')" class="md:hidden text-white" v-if="!isCollapsed">
                <i-solar-close-circle-linear class="text-2xl" />
            </button>
        </div>

        <div class="w-full shrink-0 flex flex-col gap-2 px-2 mt-2">
            <div v-if="isSearching && !isCollapsed" class="w-full px-2">
                <div
                    class="relative flex items-center bg-white/5 rounded-lg border border-white/10 focus-within:border-[#dbf156]/50 transition-colors">
                    <i-solar-magnifer-linear class="absolute left-2 text-white/40 text-lg" />
                    <input v-model="searchQuery" ref="searchInput" type="text" placeholder="Search chats..."
                        class="w-full bg-transparent border-none text-sm text-white placeholder-white/30 pl-8 pr-8 py-2 focus:ring-0"
                        @blur="!searchQuery ? isSearching = false : null" />
                    <button v-if="searchQuery" @click="searchQuery = ''; isSearching = false"
                        class="absolute right-2 text-white/40 hover:text-white">
                        <i-solar-close-circle-linear />
                    </button>
                </div>
            </div>
            <div v-else @click="startSearch"
                class="w-full relative flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors group"
                :class="[isCollapsed ? 'justify-center' : '']">
                <i-solar-magnifer-linear class="text-xl text-white/60 group-hover:text-white transition-colors" />
                <p v-if="!isCollapsed"
                    class="font-space font-normal text-[16px] text-white/80 group-hover:text-white transition-colors whitespace-nowrap">
                    Search</p>
            </div>

            <Link :href="Chat().url"
                class="w-full relative flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors group"
                :class="[isCollapsed ? 'justify-center' : '']">
            <i-solar-pen-new-square-linear class="text-xl text-white/60 group-hover:text-white transition-colors" />
            <p v-if="!isCollapsed"
                class="font-space font-normal text-[16px] text-white/80 group-hover:text-white transition-colors whitespace-nowrap">
                New Chat</p>
            </Link>
        </div>

        <div v-if="!isCollapsed" class="w-full shrink-0 relative flex items-center px-4 py-2 mt-4">
            <p class="font-space font-normal text-sm text-white/40 uppercase tracking-wider">Recent Chats</p>
        </div>
        <div v-else class="w-full h-px bg-white/10 my-4 mx-2"></div>

        <div class="w-full shrink-0 flex flex-col flex-1 gap-1 overflow-y-auto overflow-x-hidden px-2 custom-scrollbar">

            <Link v-for="chat in filteredChats" :key="chat.id" :href="Chat({ query: { chat_id: chat.id } }).url"
                class="w-full flex items-center p-2 gap-3 hover:bg-white/5 cursor-pointer transition-colors group relative"
                :class="[isCollapsed ? 'justify-center' : '']">

            <i-solar-chat-round-line-linear
                class="text-white/40 group-hover:text-[#dbf156] transition-colors shrink-0" />

            <div v-if="!isCollapsed" class="flex-1 min-w-0 flex items-center justify-between">
                <p class="font-space text-sm text-white/70 truncate group-hover:text-white transition-colors flex-1">
                    {{ chat.title || 'Untitled' }}
                </p>
                <button @click.prevent.stop="openEditModal(chat)" class="...">
                    <i-solar-pen-linear />
                </button>
            </div>
            </Link>

            <Modal :show="showEditModal" title="Edit Chat Title" @close="showEditModal = false">
                <div class="flex flex-col gap-4">
                    <div>
                        <label
                            class="block text-xs font-space text-white/40 uppercase tracking-wider mb-2">Title</label>
                        <input v-model="editForm.title" type="text"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#dbf156] focus:outline-none transition-colors"
                            placeholder="Enter chat title..." @keydown.enter="saveTitle" autoFocus />
                    </div>

                    <div class="flex justify-end gap-2 mt-2">
                        <button @click="showEditModal = false"
                            class="px-4 py-2 rounded-lg text-white/60 hover:text-white hover:bg-white/5 transition-colors font-space text-sm">
                            Cancel
                        </button>
                        <button @click="saveTitle" :disabled="editForm.processing"
                            class="px-4 py-2 rounded-lg bg-[#dbf156] text-black font-space text-sm font-medium hover:bg-[#c5d94e] transition-colors disabled:opacity-50">
                            Save Changes
                        </button>
                    </div>
                </div>
            </Modal>

            <div v-if="chats.length === 0 && !isCollapsed" class="px-4 py-8 text-center text-white/20 text-xs">
                No history yet.
            </div>


        </div>
        <div class="w-full shrink-0 mt-auto pt-2 pb-4 px-2 border-t border-white/10 flex flex-col gap-2">
            <div class="w-full flex items-center p-2 rounded-xl hover:bg-white/5 cursor-pointer transition-colors group relative"
                :class="[isCollapsed ? 'flex-col justify-center gap-4' : 'justify-between']">
                <div class="flex items-center gap-3">
                    <div
                        class="size-8 rounded-full bg-[#dbf156] flex items-center justify-center text-black font-bold shrink-0">

                    </div>
                </div>

                <button @click.stop="toggleCollapse" class="text-white/40 hover:text-white transition-colors"
                    :class="[isCollapsed ? '' : '']">
                    <i-solar-alt-arrow-right-linear v-if="isCollapsed" class="text-xl" />
                    <i-solar-alt-arrow-left-linear v-else class="text-xl" />
                </button>
            </div>
        </div>
    </Motion>
</template>