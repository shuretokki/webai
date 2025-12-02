<script setup lang="ts">
const props = defineProps<{
  messages: Array<{ role: string, content: string }>,
  chatId: number | null
}>();

const form = useForm({
  prompt: '',
  chat_id: props.chatId,
})

const messagesContainer = ref<HTMLElement | null>(null);

const scrollToBottom = () => {
  if (!messagesContainer.value) return;
    
  messagesContainer.value.scrollTop =
    messagesContainer.value.scrollHeight;
}

watch(() => props.messages, () => {
    nextTick(scrollToBottom);
}, { deep: true });

watch(() => props.chatId, (newId) => {
    form.chat_id = newId;
})

const submit = () => {
  if (!form.prompt.trim()) return;

  form.post('/chat/send', {
    onSuccess: () => {
        form.reset();
    },
  });
}

onMounted(() => {
  scrollToBottom();
})
</script>

<template>
    <div class="min-h-screen bg-neutral-900 flex items-center justify-center font-serif antialiased">
        <!-- Mobile Container -->
        <div class="w-full max-w-sm bg-black h-[100dvh] flex flex-col shadow-2xl border-x border-neutral-800 relative overflow-hidden">

            <!-- Messages Area -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-8 scroll-smooth scrollbar-hide">
                <div v-for="(message, index) in messages" :key="index" class="flex flex-col animate-in fade-in slide-in-from-bottom-2 duration-500">

                    <!-- User Message (Right) -->
                    <div v-if="message.role === 'user'" class="self-end max-w-[90%] text-right group">
                        <span class="text-[10px] text-neutral-600 mb-1 block uppercase tracking-[0.2em] opacity-0 group-hover:opacity-100 transition-opacity">You</span>
                        <p class="text-lg leading-relaxed text-white font-medium drop-shadow-sm">
                            {{ message.content }}
                        </p>
                    </div>

                    <!-- AI Message (Left) -->
                    <div v-else-if="message.role === 'assistant'" class="self-start max-w-[90%] text-left group">
                        <span class="text-[10px] text-red-900 mb-1 block uppercase tracking-[0.2em] font-bold">Doma</span>
                        <p class="text-lg leading-relaxed text-neutral-300 font-normal">
                            {{ message.content }}
                        </p>
                    </div>

                    <!-- System Message -->
                    <div v-else class="text-center text-xs text-neutral-800 italic my-4">
                        {{ message.content }}
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="messages.length === 0" class="h-full flex flex-col items-center justify-center text-neutral-800 space-y-2">
                    <!-- Solar Icon Example: 'solar:stars-minimalistic-linear' -->
                    <i-solar-stars-minimalistic-linear class="text-4xl opacity-20" />
                    <p class="text-xs uppercase tracking-widest">Prologue</p>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-5 bg-black border-t border-neutral-900 z-10">
                <form @submit.prevent="submit" class="relative flex items-end gap-3">
                    <input
                        type="text"
                        v-model="form.prompt"
                        placeholder="Speak..."
                        class="w-full bg-transparent text-neutral-200 placeholder-neutral-700 border-b border-neutral-800 focus:border-neutral-500 focus:ring-0 rounded-none py-2 px-0 text-base font-serif transition-colors"
                        :disabled="form.processing"
                    >
                    <button
                        type="submit"
                        class="text-neutral-500 hover:text-white transition-colors pb-2 uppercase text-[10px] tracking-widest font-bold flex items-center gap-1"
                        :disabled="form.processing"
                    >
                        <!-- Solar Icon Example: 'solar:plain-3-bold' -->
                        <i-solar-plain-3-bold class="text-lg" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>