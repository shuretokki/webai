<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Sidebar from '@/components/chat/Sidebar.vue';
import Message from '@/components/chat/Message.vue';
import ChatInput from '@/components/chat/ChatInput.vue';
import { chat as Chat } from '@/routes/index'

const props = defineProps<{
    chats: Array<{ id: number, title: string, created_at: string }>,
    messages: Array<{ role: string, content: string }>,
    chatId: number | null
}>();

const form = useForm({
    prompt: '',
    chat_id: props.chatId
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
        content: msg.content
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

const handleSendMessage = async (text: string) => {
    props.messages.push({ role: 'user', content: text });

    isStreaming.value = true;
    streaming.value = '';

    try {
        const response = await fetch('/chat/stream', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
            },
            body: JSON.stringify({
                prompt: text,
                chat_id: props.chatId
            })
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

                const data = line.slice(6);
                if (data === '[Done]')
                    continue;

                try {
                    const json = JSON.parse(data);
                    if (!json.chat_id) {
                        streaming.value += json.text;
                    } else {
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('chat_id', json.chat_id);
                        window.history.replaceState({}, '', newUrl);

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

onMounted(() => {
    scrollToBottom();
});

</script>
<template>
    <div class="w-full h-screen relative flex flex-col items-center content-stretch overflow-hidden bg-[#1e1e1e]">
        <div class="absolute inset-0 pointer-events-none"></div>

        <div class=" w-full h-full shrink-0 relative flex items-start justify-center content-stretch overflow-hidden">
            <Sidebar :chats="chats" class="hidden md:flex h-full border-r border-white/10" />
            <AnimatePresence>
                <div v-if="isSidebarOpen" class="fixed inset-0 z-50 md:hidden flex">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="toggleSidebar"></div>
                    <Sidebar class="h-full shadow-2xl" @close="toggleSidebar" />
                </div>
            </AnimatePresence>

            <div
                class="flex flex-col flex-1 h-full items-center content-stretch min-w-0 relative shrink-0 backdrop-blur-[6px]">

                <div class="w-full shrink-0 relative h-[60px] flex items-center justify-between px-4 md:px-6 py-0 z-10">
                    <button @click="toggleSidebar"
                        class="md:hidden text-white p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i-solar-hamburger-menu-linear class="text-2xl" />
                    </button>

                    <div class="flex items-center gap-3 ml-auto">
                        <button
                            class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
                            <i-solar-menu-dots-linear class="text-xl" />
                        </button>
                        <Link :href="Chat().url"
                            class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/10 cursor-pointer transition-colors">
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

                    <ChatInput @submit="handleSendMessage" class="w-full max-w-3xl shadow-2xl" />
                </div>
            </div>
        </div>
    </div>
</template>