<script setup lang="ts">
import { computed } from 'vue';
import { Motion } from 'motion-v';
import { useMarkdown } from '@/lib/markdown';

interface Props {
    variant?: 'User/Text' | 'Responder/Text' | 'Responder/Image';
    content?: string;
    imageSrc?: string;
    language?: string;
    attachments?: Array<{ type: 'image' | 'file', url: string, name?: string }>;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'User/Text',
    content: '',
});

const isUser = computed(() => props.variant.startsWith('User'));
const isResponder = computed(() => props.variant.startsWith('Responder'));

const md = useMarkdown();

</script>

<template>
    <Motion :initial="{ opacity: 0, y: 20 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.4 }"
        class="w-full shrink-0 relative flex flex-col" :class="[
            isUser ? 'items-end py-2 pl-16 pr-4' : '',
            isResponder ? 'items-start py-2 pl-4 pr-16' : ''
        ]">

        <div v-if="isResponder" class="relative shrink-0 flex flex-col items-start justify-center pb-2 pl-1">
            <div class="flex items-center gap-2">
                <div class="h-[2px] w-12 bg-gradient-to-r from-[#acb564] to-transparent"></div>
                <p class="font-philosopher font-bold text-sm text-[#f3f3f3] tracking-wide shadow-black drop-shadow-md">
                </p>
            </div>
        </div>

        <div class="relative shrink-0 max-w-full md:max-w-[80%] overflow-hidden transition-all">
            <div v-if="variant === 'User/Text' || variant === 'Responder/Text'"
                class="prose prose-invert font-space font-normal text-[16px] leading-relaxed break-words"
                :class="isResponder ? 'text-[#f8ffd7]' : 'text-[#f3f3f3]'" v-html="md.render(content)" />

            <div v-if="variant === 'Responder/Image'"
                class="relative rounded-lg overflow-hidden border border-white/10 group cursor-pointer">
                <img alt="Generated Image"
                    class="w-full h-auto max-w-sm object-cover transition-transform duration-500 group-hover:scale-105"
                    :src="imageSrc" />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                    <button
                        class="p-2 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-colors">
                        <i-solar-download-linear />
                    </button>
                </div>
            </div>
        </div>

        <div v-if="attachments?.length" class="flex flex-wrap gap-2 mt-2">
            <div v-for="(att, i) in attachments" :key="i" class="relative group">
                <img v-if="att.type === 'image'" :src="att.url" class="h-32 w-auto rounded-lg border border-white/10" />
                <a v-else :href="att.url" target="_blank"
                    class="flex items-center gap-2 bg-white/5 p-2 rounded-lg border border-white/10 hover:bg-white/10">
                    <i-solar-file-text-linear class="text-xl" />
                    <span class="text-xs">{{ att.name || 'File' }}</span>
                </a>
            </div>
        </div>

        <div class="flex items-center gap-2 mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button class="text-white/20 hover:text-white transition-colors">
                <i-solar-copy-linear class="text-xs" />
            </button>
            <button v-if="isResponder" class="text-white/20 hover:text-white transition-colors">
                <i-solar-restart-linear class="text-xs" />
            </button>
        </div>
    </Motion>
</template>

<style scoped>
:deep(.prose h1) {
    font-size: 1.5em;
    font-weight: 700;
    margin-top: 1em;
    margin-bottom: 0.5em;
    color: #dbf156;
}

:deep(.prose h2) {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1em;
    margin-bottom: 0.5em;
    color: #f3f3f3;
}

:deep(.prose ul) {
    list-style-type: disc;
    padding-left: 1.5em;
}

:deep(.prose ol) {
    list-style-type: decimal;
    padding-left: 1.5em;
}

:deep(.prose code) {
    padding: 0.2em 0.4em;
    font-family: monospace;
}

:deep(.prose pre) {
    padding: 1em;
    border: 4px solid #1e1e1e;
    border-radius: 0;
    overflow-x: auto;
}
</style>