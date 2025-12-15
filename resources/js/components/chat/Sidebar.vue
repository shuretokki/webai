```vue
<script setup lang="ts">
import { ref, computed } from 'vue';
import { Motion } from 'motion-v';
import { Link, usePage, router, useForm } from '@inertiajs/vue3';
import { chat as Chat } from '@/routes/index'
import Modal from '@/components/ui/Modal.vue';
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import SettingsModal from '@/components/settings/SettingsModal.vue';
import { edit as profileEdit } from '@/routes/profile';
import { useWindowSize } from '@vueuse/core';

const props = defineProps<{
    isOpen?: boolean;
    chats: Array<{ id: number, title: string, created_at: string }>;
}>();

const { width } = useWindowSize(); // Reactive window size
const page = usePage();
const user = computed(() => page.props.auth.user);

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

const showDeleteModal = ref(false);
const deletingChatId = ref<number | null>(null);
const isDeleting = ref(false);

const openDeleteModal = (id: number) => {
    deletingChatId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!deletingChatId.value) return;

    isDeleting.value = true;
    router.delete(`/chat/${deletingChatId.value}`, {
        preserveState: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
            deletingChatId.value = null;
        }
    });
};

const deleteChat = (id: number) => {
    openDeleteModal(id);
}

const emit = defineEmits(['close']);
const isCollapsed = ref(false);
const showSettingsModal = ref(false);

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
};

const handleSidebarToggle = () => {
    if (width.value < 768) {
        emit('close');
    } else {
        toggleCollapse();
    }
}

defineOptions({
    inheritAttrs: false
});
</script>

<template>
    <SettingsModal :show="showSettingsModal" @close="showSettingsModal = false" />

    <Motion v-bind="$attrs"
        class="shrink-0 relative h-full flex flex-col items-start content-stretch bg-sidebar border-r border-sidebar-border overflow-hidden z-20"
        :class="[isCollapsed ? 'items-center w-[80px]' : 'items-start w-[300px]']">
        <div class="w-full shrink-0 relative h-[60px] flex items-center"
            :class="[isCollapsed ? 'justify-center' : 'pl-4 pr-4 justify-between']">
            <div class="h-8 flex items-center justify-center text-sidebar-foreground">
                <AppLogoIcon class="size-8 text-sidebar-primary" />
            </div>
        </div>

        <div class="w-full shrink-0 flex flex-col gap-2 px-3 mt-2">
            <Link :href="Chat().url"
                class="w-full relative flex items-center gap-3 p-2 rounded-none cursor-pointer transition-colors group"
                :class="[isCollapsed ? 'justify-center' : '']">
            <i-solar-pen-new-square-linear
                class="text-xl text-sidebar-foreground/60 group-hover:text-sidebar-foreground transition-colors" />
            <p v-if="!isCollapsed"
                class="font-space font-normal text-sm text-sidebar-foreground/80 group-hover:text-sidebar-foreground transition-colors whitespace-nowrap">
                New Chat</p>
            </Link>
        </div>

        <div v-if="!isCollapsed" class="w-full shrink-0 relative flex items-center px-6 py-2 mt-4">
            <p class="font-space font-normal text-sm text-sidebar-foreground/40 uppercase tracking-wider">Recent Chats
            </p>
        </div>
        <div v-else class="w-full h-px bg-sidebar-border my-4 mx-0"></div>

        <div class="w-full shrink-0 flex flex-col flex-1 gap-1 overflow-y-auto overflow-x-hidden px-4 custom-scrollbar">

            <Modal :show="showDeleteModal" title="Delete Chat" @close="showDeleteModal = false" max-width="sm"
                :align="width < 768 ? 'bottom' : 'center'" :content-class="width < 768 ? 'mb-0 rounded-b-none' : ''">
                <div class="flex flex-col gap-4">
                    <p class="text-sm font-space text-muted-foreground">
                        Are you sure you want to delete this chat? This action cannot be undone.
                    </p>
                    <div class="flex justify-end gap-2">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 rounded-none text-muted-foreground hover:text-foreground hover:bg-muted transition-colors font-space text-sm">
                            Cancel
                        </button>
                        <button @click="confirmDelete" :disabled="isDeleting"
                            class="px-4 py-2 rounded-none bg-destructive text-destructive-foreground font-space text-sm font-medium hover:opacity-90 transition-colors disabled:opacity-50">
                            Delete
                        </button>
                    </div>
                </div>
            </Modal>

            <Link v-for="chat in filteredChats" :key="chat.id" :href="Chat(chat.id).url" :preserve-state="false"
                class="w-full flex items-center p-2 gap-3 cursor-pointer transition-colors group relative"
                :class="[isCollapsed ? 'justify-left' : '']">

            <i-solar-chat-round-line-linear v-if="isCollapsed"
                class="text-sidebar-foreground/40 group-hover:text-sidebar-primary transition-colors shrink-0" />

            <div v-if="!isCollapsed" class="flex-1 min-w-0 flex items-center justify-between">
                <p
                    class="font-space text-sm text-sidebar-foreground/70 truncate group-hover:text-sidebar-foreground transition-colors flex-1 overflow-hidden whitespace-nowrap block w-full">
                    {{ chat.title || 'Untitled' }}
                </p>
            </div>
            </Link>

            <Modal :show="showEditModal" title="Edit Chat Title" @close="showEditModal = false">
                <div class="flex flex-col gap-4">
                    <div>
                        <label
                            class="block text-xs font-space text-muted-foreground uppercase tracking-wider mb-2">Title</label>
                        <input v-model="editForm.title" type="text"
                            class="w-full bg-muted border border-border rounded-none px-4 py-2 text-foreground focus:border-primary focus:outline-none transition-colors"
                            placeholder="Enter chat title..." @keydown.enter="saveTitle" autoFocus />
                    </div>

                    <div class="flex justify-end gap-2 mt-2">
                        <button @click="showEditModal = false"
                            class="px-4 py-2 rounded-none text-muted-foreground hover:text-foreground hover:bg-muted transition-colors font-space text-sm">
                            Cancel
                        </button>
                        <button @click="saveTitle" :disabled="editForm.processing"
                            class="px-4 py-2 rounded-none bg-primary text-primary-foreground font-space text-sm font-medium hover:opacity-90 transition-colors disabled:opacity-50">
                            Save Changes
                        </button>
                    </div>
                </div>
            </Modal>

            <div v-if="chats.length === 0 && !isCollapsed"
                class="px-4 py-8 text-center text-sidebar-foreground/20 text-xs">
                No history yet.
            </div>


        </div>
        <div class="w-full shrink-0 mt-auto pt-2 pb-4 px-2 border-t border-sidebar-border flex flex-col gap-2">
            <div class="w-full flex items-center gap-2"
                :class="[isCollapsed ? 'flex-col justify-center' : 'justify-between']">

                <button @click="showSettingsModal = true"
                    class="flex items-center gap-3 p-2 rounded-none cursor-pointer transition-colors flex-1 min-w-0 text-left group"
                    :class="[isCollapsed ? 'justify-center w-full' : '']">
                    <div
                        class="size-8 rounded-none bg-sidebar-primary flex items-center justify-center text-sidebar-primary-foreground font-bold shrink-0 overflow-hidden">
                        <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                        <span v-else>{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div v-if="!isCollapsed" class="flex flex-col min-w-0">
                        <span
                            class="text-sm font-medium truncate text-sidebar-foreground group-hover:text-white transition-colors">{{
                                user?.name }}</span>
                        <span
                            class="text-xs text-muted-foreground truncate group-hover:text-sidebar-foreground transition-colors">{{
                                user?.email }}</span>
                    </div>
                </button>

                <!-- Modified logic: On mobile, arrow key ALWAYS closes the sidebar (emits close) -->
                <button @click.stop="handleSidebarToggle"
                    class="p-2 rounded-none text-sidebar-foreground/40 hover:text-sidebar-foreground transition-colors shrink-0"
                    :class="[isCollapsed ? 'w-full flex justify-center' : '']">
                    <i-solar-alt-arrow-right-linear v-if="isCollapsed" class="text-xl" />
                    <i-solar-alt-arrow-left-linear v-else class="text-xl" />
                </button>
            </div>
        </div>
    </Motion>
</template>