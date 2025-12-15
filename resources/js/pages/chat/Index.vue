<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AnimatePresence } from 'motion-v';
import Sidebar from '@/components/chat/Sidebar.vue';
import Message from '@/components/chat/Message.vue';
import ChatInput from '@/components/chat/ChatInput.vue';
import SearchModal from '@/components/chat/SearchModal.vue';
import Modal from '@/components/ui/Modal.vue';
import { chat as Chat } from '@/routes/index'
import { useEcho } from '@laravel/echo-vue'
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    chats: Array<{ id: number, title: string, created_at: string }>,
    messages: Array<{
        role: string, content: string, attachments?: Array<{
            type: string,
            url: string,
            name: string,
            mime_type?: string,
            path?: string
        }>
    }>,
    chatId: number | null
}>();

const page = usePage<any>();
const models = computed(() => page.props.ai?.models || []);
const userTier = computed(() => page.props.auth?.user?.subscription_tier || 'free');

const isSearchOpen = ref(false);
const isMenuOpen = ref(false);
const menuRef = ref<HTMLElement | null>(null);

import { onClickOutside, useWindowSize } from '@vueuse/core';
const { width } = useWindowSize();
onClickOutside(menuRef, () => isMenuOpen.value = false);

const exportChat = (format: 'pdf' | 'md') => {
    if (!props.chatId) return;
    window.location.href = `/chat/${props.chatId}/export/${format}`;
    isMenuOpen.value = false;
};

const showEditModal = ref(false);
const editForm = useForm({
    title: ''
});

const openEditModal = () => {
    if (!props.chatId) return;
    const currentChat = props.chats.find(c => c.id === props.chatId);
    if (currentChat) {
        editForm.title = currentChat.title;
        showEditModal.value = true;
        isMenuOpen.value = false;
    }
};

const saveTitle = () => {
    if (!props.chatId) return;
    editForm.patch(`/chat/${props.chatId}`, {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
};

const showDeleteModal = ref(false);
const isDeleting = ref(false);

const confirmDelete = () => {
    if (!props.chatId) return;

    isDeleting.value = true;
    router.delete(`/chat/${props.chatId}`, {
        preserveState: true,
        onSuccess: () => {
            isMenuOpen.value = false;
            showDeleteModal.value = false;
            isDeleting.value = false;
        },
        onError: () => {
            isDeleting.value = false;
        }
    });
};

const deleteChat = () => {
    if (!props.chatId) return;
    showDeleteModal.value = true;
};

const model = ref('gemini-2.5-flash');
const form = useForm({
    prompt: '',
    chat_id: props.chatId,
    model: model.value,
});

watch(model, (newModel) => {
    form.model = newModel;
});

watch(() => props.chatId, (newId) => {
    form.chat_id = newId;
});

const isSidebarOpen = ref(false);
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const uiMessages = computed(() => {
    return props.messages.map(msg => ({
        variant: msg.role === 'user' ? 'User/Text' : 'Responder/Text',
        content: msg.content,
        attachments: msg.attachments
    }));
})

const container = ref<HTMLElement | null>(null);
const scrollToBottom = async () => {
    await nextTick();
    if (!container.value)
        return;

    container.value.scrollTop =
        container.value.scrollHeight;
}
watch(() => props.messages, scrollToBottom, { deep: true });

const streaming = ref('');
const isStreaming = ref(false);
watch(streaming, () => {
    scrollToBottom();
});

const handleSendMessage = async (text: string, files?: File[]) => {
    const attachment = files?.map(file => ({
        type: file.type.startsWith('image/') ? 'image' : 'file',
        url: URL.createObjectURL(file),
        name: file.name,
        _optimistic: true
    })) || [];

    props.messages.push({
        role: 'user',
        content: text,
        attachments: attachment
    });

    isStreaming.value = true;
    streaming.value = '';

    try {
        const formData = new FormData();
        formData.append('prompt', text);
        formData.append('chat_id', props.chatId?.toString() || '');
        formData.append('model', model.value);

        if (files) {
            files.forEach(file => {
                formData.append('files[]', file);
            });
        }

        const response = await fetch('/chat/stream', {
            method: 'POST',
            headers: {
                'X-XSRF-TOKEN': decodeURIComponent(
                    document.
                        cookie.
                        split('; ').
                        find(row => row.
                            startsWith('XSRF-TOKEN='))?.
                        split('=')[1] || '')
            },
            body: formData as any
        });

        if (!response.body)
            throw new Error('No response body');

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { done, value } = await reader.read();

            if (done)
                break;

            const chunk = decoder.decode(value, { stream: true });
            const lines = chunk.split('\n');

            for (const line of lines) {
                const trimmed = line.trim()
                if (!trimmed || !trimmed.startsWith('data: '))
                    continue

                const data = line.slice(6).trim();
                if (data === '[Done]' || data === '[DONE]')
                    continue;

                try {
                    const json = JSON.parse(data);

                    if (json.error) {
                        console.error('Stream error:', json.error);
                        streaming.value += '\n\n⚠️ Error: ' + json.error;
                        continue;
                    }

                    if (!json.chat_id) {
                        streaming.value += json.text;
                    } else {
                        window.history.replaceState(
                            {}, '', `/chat/${json.chat_id}`);
                        form.chat_id = json.chat_id;
                    }
                } catch (e) {
                    console.error('Error parsing JSON', e);
                }
            }
        }
    } catch (error) {
        console.error('Stream failed', error);
    } finally {
        isStreaming.value = false;

        if (streaming.value) {
            props.messages.push({
                role: 'assistant',
                content: streaming.value
            });
        }

        streaming.value = '';
        router.reload({ only: ['chats'] });
    }

    scrollToBottom();
};

let echoControl: any = null;

onMounted(() => {
    scrollToBottom();

    if (import.meta.env.VITE_REVERB_APP_KEY && props.chatId) {
        try {
            echoControl = useEcho(
                `chats.${props.chatId}`,
                '.message.sent',
                (event: any) => {
                    if (event.message && event.message.role === 'assistant') {
                        const isDuplicate = props.messages.some(
                            m => m.role === 'assistant' && m.content === event.message.content
                        );

                        if (!isDuplicate) {
                            props.messages.push({
                                role: event.message.role,
                                content: event.message.content,
                                attachments: []
                            });
                            scrollToBottom();
                        }
                    }
                },
                [],
                'private'
            );
        } catch (error) {
            console.warn('WebSocket connection not available:', error);
        }
    }
});

onUnmounted(() => {
    if (echoControl) {
        echoControl.leaveChannel();
    }
});

</script>
<template>
    <div class="w-full h-screen relative flex flex-col items-center content-stretch overflow-hidden bg-background">
        <div class="absolute inset-0 pointer-events-none"></div>

        <div class=" w-full h-full shrink-0 relative flex items-start justify-center content-stretch overflow-hidden">
            <Sidebar :chats="chats" class="hidden md:flex h-full border-r border-border" />
            <AnimatePresence>
                <div v-if="isSidebarOpen" class="fixed inset-0 z-50 md:hidden flex">
                    <div class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="toggleSidebar"></div>
                    <Sidebar :chats="chats" class="h-full shadow-2xl" @close="toggleSidebar" />
                </div>
            </AnimatePresence>

            <div
                class="flex flex-col flex-1 h-full items-center content-stretch min-w-0 relative shrink-0 backdrop-blur-[6px]">

                <div class="w-full shrink-0 relative h-[60px] flex items-center justify-between px-4 md:px-6 py-0 z-10">
                    <button @click="toggleSidebar" class="md:hidden text-foreground p-2 rounded-none transition-colors">
                        <i-solar-hamburger-menu-linear class="text-2xl" />
                    </button>

                    <div class="flex items-center gap-3 ml-auto">
                        <div class="relative" ref="menuRef">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="text-white/60 hover:text-white p-2 rounded-none cursor-pointer transition-colors"
                                :class="{ 'text-white': isMenuOpen }">
                                <i-solar-menu-dots-linear class="text-xl" />
                            </button>

                            <div v-if="isMenuOpen"
                                class="absolute right-0 top-full mt-2 w-48 bg-[#2a2a2a] border border-white/10 rounded-none shadow-xl overflow-hidden z-50 py-1">
                                <div class="px-3 py-2 text-xs font-medium text-white/40 uppercase tracking-wider">
                                    Chat Options
                                </div>
                                <button @click="openEditModal"
                                    class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/5 flex items-center gap-2 transition-colors"
                                    :disabled="!chatId" :class="{ 'opacity-50 cursor-not-allowed': !chatId }">
                                    <i-solar-pen-linear class="text-lg" />
                                    <span>Rename Chat</span>
                                </button>
                                <button @click="deleteChat"
                                    class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-white/5 flex items-center gap-2 transition-colors"
                                    :disabled="!chatId" :class="{ 'opacity-50 cursor-not-allowed': !chatId }">
                                    <i-solar-trash-bin-trash-linear class="text-lg" />
                                    <span>Delete Chat</span>
                                </button>
                                <div class="h-px bg-white/10 my-1"></div>
                                <div class="px-3 py-2 text-xs font-medium text-white/40 uppercase tracking-wider">
                                    Export Chat
                                </div>
                                <button @click="exportChat('pdf')"
                                    class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/5 flex items-center gap-2 transition-colors"
                                    :disabled="!chatId" :class="{ 'opacity-50 cursor-not-allowed': !chatId }">
                                    <i-solar-file-text-linear class="text-lg" />
                                    <span>Export as PDF</span>
                                </button>
                                <button @click="exportChat('md')"
                                    class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/5 flex items-center gap-2 transition-colors"
                                    :disabled="!chatId" :class="{ 'opacity-50 cursor-not-allowed': !chatId }">
                                    <i-solar-code-square-linear class="text-lg" />
                                    <span>Export as Markdown</span>
                                </button>
                            </div>
                        </div>
                        <button @click="isSearchOpen = true"
                            class="text-white/60 hover:text-white p-2 rounded-none cursor-pointer transition-colors"
                            title="Search (Cmd/Ctrl+K)">
                            <i-solar-magnifer-linear class="text-xl" />
                        </button>
                        <Link :href="Chat().url"
                            class="text-white/60 hover:text-white p-2 rounded-none cursor-pointer transition-colors">
                        <i-solar-pen-new-square-linear class="text-xl" />
                        </Link>
                    </div>
                </div>

                <div class="w-full flex-1 relative flex flex-col items-center overflow-y-auto overflow-x-hidden px-4 pb-32 scroll-smooth"
                    ref="container">
                    <div class="w-full max-w-3xl flex flex-col gap-4 py-4">
                        <Message v-for="(msg, index) in uiMessages" :key="index" :variant="msg.variant as any"
                            :content="msg.content" />

                        <Message v-if="isStreaming || streaming" variant="Responder/Text" :content="streaming" />
                    </div>
                </div>

                <div
                    class="w-full absolute bottom-0 left-0 right-0 p-4 flex justify-center bg-gradient-to-t from-[#1e1e1e] via-[#1e1e1e]/90 to-transparent pt-12 z-20">

                    <ChatInput @submit="handleSendMessage" class="w-full max-w-3xl shadow-2xl"
                        v-model:modelValue="model" :models="models" :userTier="userTier" :is-streaming="isStreaming" />
                </div>
            </div>
        </div>

        <!-- Search Modal -->
        <SearchModal v-model:open="isSearchOpen" />

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
    </div>
</template>