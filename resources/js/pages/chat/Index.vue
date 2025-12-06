<script setup lang="ts">
import Sidebar from '@/components/chat/Sidebar.vue';
import Message from '@/components/chat/Message.vue';
import ChatInput from '@/components/chat/ChatInput.vue';

const props = defineProps<{
    chats: Array<{ id: number, title:string, created_at: string}>,
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

const handleSendMessage = (text: string) => {
    form.prompt = text;
    form.post('/chat/send', {
        onSuccess: () => {
            form.reset('prompt');
        },
    });
};

const bgStyle = {
    backgroundImage: "url('data:image/svg+xml;utf8,<svg viewBox=\\'0 0 880 835\\' xmlns=\\'http://www.w3.org/2000/svg\\' preserveAspectRatio=\\'none\\'><rect x=\\'0\\' y=\\'0\\' height=\\'100%\\' width=\\'100%\\' fill=\\'url(%23grad)\\' opacity=\\'1\\'/><defs><radialGradient id=\\'grad\\' gradientUnits=\\'userSpaceOnUse\\' cx=\\'0\\' cy=\\'0\\' r=\\'10\\' gradientTransform=\\'matrix(-58.667 -57.85 60.968 -43.485 590.24 586.5)\\'><stop stop-color=\\'rgba(0,0,0,0)\\' offset=\\'0\\'/><stop stop-color=\\'rgba(0,0,0,1)\\' offset=\\'1\\'/></radialGradient></defs></svg>'), linear-gradient(180deg, rgba(0, 0, 0, 1) 21.677%, rgba(0, 0, 0, 0) 100%), linear-gradient(90deg, rgba(30, 30, 30, 1) 0%, rgba(30, 30, 30, 1) 100%)"
};
</script>

<template>
    <div class="w-full h-screen relative flex flex-col items-center content-stretch overflow-hidden bg-[#1e1e1e]">
        <div class="absolute inset-0 pointer-events-none" :style="bgStyle"></div>

        <div class="w-full h-full shrink-0 relative flex items-start justify-center content-stretch overflow-hidden">
            <!-- Sidebar (Desktop) -->
            <Sidebar :chats="chats" class="hidden md:flex h-full border-r border-white/10" />

            <!-- Sidebar (Mobile Drawer) -->
            <AnimatePresence>
                <div v-if="isSidebarOpen" class="fixed inset-0 z-50 md:hidden flex">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="toggleSidebar"></div>
                    <Sidebar class="h-full shadow-2xl" @close="toggleSidebar" />
                </div>
            </AnimatePresence>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 h-full items-center content-stretch min-w-0 relative shrink-0 backdrop-blur-[6px]">

                <!-- Navbar (Content) -->
                <div class="w-full shrink-0 relative h-[60px] flex items-center justify-between px-4 md:px-6 py-0 z-10">
                    <!-- Mobile Menu Button -->
                    <button @click="toggleSidebar" class="md:hidden text-white p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i-solar-hamburger-menu-linear class="text-2xl" />
                    </button>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-3 ml-auto">
                        <button class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <i-solar-menu-dots-linear class="text-xl" />
                        </button>
                        <button class="text-white/60 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <i-solar-pen-new-square-linear class="text-xl" />
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="w-full flex-1 relative flex flex-col items-center overflow-y-auto overflow-x-hidden px-4 pb-32 scroll-smooth">
                    <div class="w-full max-w-3xl flex flex-col gap-4 py-4">
                        <Message
                            v-for="(msg, index) in uiMessages"
                            :key="index"
                            :variant="msg.variant as any"
                            :content="msg.content"
                        />
                    </div>
                </div>

                <!-- Input Area -->
                <div class="w-full absolute bottom-0 left-0 right-0 p-4 flex justify-center bg-gradient-to-t from-[#1e1e1e] via-[#1e1e1e]/90 to-transparent pt-12 z-20">
                    <ChatInput @submit="handleSendMessage" class="w-full max-w-3xl shadow-2xl" />
                </div>
            </div>
        </div>
    </div>
</template>