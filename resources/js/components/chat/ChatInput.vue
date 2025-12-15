<script setup lang="ts">
import { useTextareaAutosize, onClickOutside } from '@vueuse/core';
import { ref, computed } from 'vue';
import { Paperclip, FileText, X, Sparkles, ChevronDown, Check, ArrowRight } from 'lucide-vue-next';
import UpgradeModal from '@/components/chat/UpgradeModal.vue';
import UnderProgressModal from '@/components/chat/UnderProgressModal.vue';

const { textarea, input } = useTextareaAutosize();
const props = defineProps<{
    modelValue?: string,
    models?: Array<{ id: string, name: string, is_free: boolean, provider: string }>,
    userTier?: string,
    isStreaming?: boolean
}>();

const emit = defineEmits(['submit', 'update:modelValue']);

const isModelMenuOpen = ref(false);
const showUpgradeModal = ref(false);
const showUnderProgressModal = ref(false);
const modelMenuRef = ref(null);
onClickOutside(modelMenuRef, () => isModelMenuOpen.value = false);

const currentModelName = computed(() => {
    return props.models?.find(m => m.id === props.modelValue)?.name || 'Select Model';
});

const groupedModels = computed(() => {
    if (!props.models) return {};
    return props.models.reduce((acc, model) => {
        const provider = model.provider.charAt(0).toUpperCase() + model.provider.slice(1);
        if (!acc[provider]) acc[provider] = [];
        acc[provider].push(model);
        return acc;
    }, {} as Record<string, typeof props.models>);
});

const selectModel = (model: any) => {
    if (!['gemini-2.5-flash', 'gemini-2.5-flash-lite'].includes(model.id)) {
        showUnderProgressModal.value = true;
        isModelMenuOpen.value = false;
        return;
    }

    if (!model.is_free && !['plus', 'enterprise'].includes(props.userTier || 'free')) {
        showUpgradeModal.value = true
        return
    }
    emit('update:modelValue', model.id);
    isModelMenuOpen.value = false;
};

const fileInput = ref<HTMLInputElement | null>(null);
const attachments = ref<Array<{ file: File, preview: string, type: 'image' | 'file' }>>([]);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (!target.files?.length) return;

    Array.from(target.files).forEach(file => {
        const isImage = file.type.startsWith('image/');
        attachments.value.push({
            file,
            preview: isImage ? URL.createObjectURL(file) : '',
            type: isImage ? 'image' : 'file'
        });
    });

    if (fileInput.value) fileInput.value.value = '';
}

const removeAttachment = (index: number) => {
    const item = attachments.value[index];
    if (item.preview) URL.revokeObjectURL(item.preview);
    attachments.value.splice(index, 1);
}

const clearAttachments = () => {
    attachments.value.forEach(item => {
        if (item.preview) URL.revokeObjectURL(item.preview);
    });
    attachments.value = [];
}

const submit = () => {
    if (!input.value.trim() && attachments.value.length === 0) return;


    const files = attachments.value.length > 0
        ? attachments.value.map(a => a.file)
        : undefined;

    emit('submit', input.value, files);

    input.value = '';
    clearAttachments();
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submit();
    }
};

defineOptions({
    inheritAttrs: false
});
</script>

<template>
    <div v-bind="$attrs" class="w-full max-w-[600px] flex flex-col gap-2">
        <form @submit.prevent="submit"
            class="w-full shrink-0 relative flex items-end gap-2 bg-card border-l-4 border-primary rounded-none shadow-sm transition-all focus-within:ring-1 focus-within:ring-primary/50">
            <button type="button" @click="triggerFileInput"
                class="shrink-0 p-3 text-muted-foreground hover:text-primary cursor-pointer transition-colors rounded-none h-[48px] w-[48px] flex items-center justify-center">
                <Paperclip class="size-5" />
            </button>

            <div class="flex-1 min-w-0 py-3 flex flex-col gap-3">
                <div v-if="attachments.length > 0" class="flex flex-wrap gap-2">
                    <div v-for="(item, index) in attachments" :key="index" class="relative group">
                        <img v-if="item.type === 'image'" :src="item.preview"
                            class="h-16 w-16 rounded-none object-cover border border-border" />

                        <div v-else
                            class="h-16 w-16 rounded-none border border-border bg-muted flex items-center justify-center">
                            <FileText class="size-6 text-muted-foreground" />
                        </div>

                        <button type="button" @click="removeAttachment(index)"
                            class="absolute -top-2 -right-2 bg-destructive text-destructive-foreground rounded-full p-0.5 border border-border opacity-0 group-hover:opacity-100 transition-opacity">
                            <X class="size-3" />
                        </button>
                    </div>
                </div>

                <textarea ref="textarea" v-model="input" rows="1" placeholder="Type a message..."
                    @keydown="handleKeydown"
                    class="w-full bg-transparent border-none outline-none font-space font-normal text-[16px] text-foreground placeholder-muted-foreground focus:ring-0 p-0 resize-none max-h-[200px] overflow-y-auto custom-scrollbar"></textarea>
            </div>

            <div class="relative shrink-0 mb-2.5" ref="modelMenuRef" v-if="models && models.length > 0">
                <button type="button" @click="isModelMenuOpen = !isModelMenuOpen"
                    class="flex items-center gap-2 px-2 py-1.5 bg-transparent rounded-none text-xs text-muted-foreground hover:text-foreground transition-all">
                    <Sparkles class="size-3.5" />
                    <span class="hidden sm:inline">{{ currentModelName }}</span>
                    <ChevronDown class="size-3" />
                </button>

                <div v-if="isModelMenuOpen"
                    class="absolute bottom-full right-0 mb-2 w-[80vw] sm:w-96 max-h-[400px] overflow-y-auto bg-popover border border-border rounded-none shadow-xl z-50 flex flex-col custom-scrollbar">
                    <div class="p-4 grid grid-cols-1 gap-4">
                        <div v-for="(models, provider) in groupedModels" :key="provider" class="flex flex-col gap-2">
                            <div class="text-xs font-medium text-muted-foreground uppercase tracking-wider px-2">
                                {{ provider }}
                            </div>
                            <div class="flex flex-col gap-1">
                                <button v-for="model in models" :key="model.id" @click="selectModel(model)"
                                    class="w-full px-3 py-2 text-left text-sm text-foreground hover:bg-accent/50 rounded-none flex items-center justify-between gap-2 transition-colors group"
                                    :class="{ 'opacity-50': !model.is_free && !['plus', 'enterprise'].includes(userTier || 'free') }">
                                    <div class="flex flex-col min-w-0">
                                        <span class="truncate"
                                            :class="{ 'text-primary': modelValue === model.id }">{{ model.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <div v-if="!model.is_free && !['plus', 'enterprise'].includes(userTier || 'free')"
                                            class="px-1.5 py-0.5 text-sm text-muted-foreground group-hover:text-primary transition-colors">
                                            PLUS
                                        </div>
                                        <Check v-if="modelValue === model.id" class="size-4 text-primary" />
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" :disabled="(!input.trim() && attachments.length === 0) || isStreaming"
                class="shrink-0 size-[48px] relative flex items-center justify-center rounded-none text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-all overflow-hidden group">
                <div class="absolute inset-0 opacity-30">
                    <div class="absolute inset-0 scale-0 group-hover:scale-150 transition-transform duration-500">
                    </div>
                </div>

                <ArrowRight class="size-6 relative z-10" />
            </button>
        </form>
    </div>

    <input type="file" ref="fileInput" class="hidden" multiple accept="image/*,application/pdf"
        @change="handleFileChange" />

    <UpgradeModal v-model:open="showUpgradeModal" />
    <UnderProgressModal v-model:open="showUnderProgressModal" />
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