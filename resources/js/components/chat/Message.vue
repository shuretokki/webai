<script setup lang="ts">
import { computed } from 'vue';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';
import { useMarkdown } from '@/lib/markdown';
import { useClipboard } from '@vueuse/core';
import { Sparkles, ChevronDown, Download, FileText, Check, Copy, RotateCw, Loader2 } from 'lucide-vue-next';
import { AnimatePresence } from 'motion-v';

interface Props {
    variant?: 'User/Text' | 'Responder/Text' | 'Responder/Image';
    content?: string;
    imageSrc?: string;
    language?: string;
    attachments?: Array<{ type: 'image' | 'file', url: string, name?: string }>;
    timestamp?: string;
    isStreaming?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'User/Text',
    content: '',
});

const isUser = computed(() => props.variant.startsWith('User'));
const isResponder = computed(() => props.variant.startsWith('Responder'));

const md = useMarkdown();
const { copy, copied } = useClipboard({ source: props.content });

const reasoning = computed(() => {
    if (!props.content) return null;

    const startTag = '<think>';
    const endTag = '</think>';

    if (!props.content.includes(startTag)) return null;

    const startIndex = props.content.indexOf(startTag) + startTag.length;
    const endIndex = props.content.indexOf(endTag);

    if (endIndex === -1) {
        return props.content.slice(startIndex).trim();
    }

    return props.content.slice(startIndex, endIndex).trim();
});

const isThinking = computed(() => {
    return props.content.includes('<think>') && !props.content.includes('</think>');
});

const cleanContent = computed(() => {
    if (!props.content) return '';

    const startTag = '<think>';
    const endTag = '</think>';

    if (props.content.includes(startTag)) {
        const startIndex = props.content.indexOf(startTag);
        const endIndex = props.content.indexOf(endTag);

        if (endIndex !== -1) {
            const pre = props.content.slice(0, startIndex);
            const post = props.content.slice(endIndex + endTag.length);
            const result = (pre + post).trim();
            console.log('[Message] cleanContent (complete):', { pre, post, result, contentLength: props.content.length });
            return result;
        } else {
            const result = props.content.slice(0, startIndex).trim();
            console.log('[Message] cleanContent (thinking):', { startIndex, result, contentLength: props.content.length });
            return result;
        }
    }

    return props.content.trim();
});

</script>

<template>
    <Motion :initial="{ opacity: 0, y: 10 }" :animate="{ opacity: 1, y: 0 }" :transition="{ duration: 0.3 }"
        class="w-full shrink-0 relative flex flex-col" :class="[
            isUser ? ui.chat.message.user : '',
            isResponder ? ui.chat.message.responder : ''
        ]">

        <div v-if="isResponder" class="relative shrink-0 flex flex-col items-start justify-center pb-2 pl-1">
            <div class="flex items-center gap-2">
                <Motion v-if="isStreaming || isThinking" v-bind="ui.animations.loadingLine"
                    class="h-[2px] bg-primary rounded-full" />
                <div v-else class="h-[2px] w-12 bg-primary rounded-full"></div>

                <p v-if="timestamp"
                    class="font-space font-bold text-sm text-foreground tracking-wide shadow-black drop-shadow-md">
                    {{ timestamp }}
                </p>
            </div>
        </div>

        <div class="relative shrink-0 max-w-full md:max-w-[85%] transition-all">
            <div v-if="isResponder && reasoning" class="mb-4">
                <details class="group/details" :open="isThinking">
                    <summary
                        class="cursor-pointer list-none flex items-center gap-2 py-1.5 text-xs text-muted-foreground/60 hover:text-foreground transition-colors w-fit font-space font-medium uppercase tracking-wider">
                        <span>{{ isThinking ? 'Thinking...' : 'Thought Process' }}</span>
                        <ChevronDown class="size-3 group-open/details:rotate-180 transition-transform" />
                    </summary>
                    <div
                        class="mt-2 ml-1 pl-4 border-l border-white/5 text-sm text-muted-foreground/50 font-space leading-relaxed italic whitespace-pre-wrap">
                        {{ reasoning }}
                        <span v-if="isThinking" class="inline-block w-1 h-3 bg-primary/40 animate-pulse ml-1"></span>
                    </div>
                </details>
            </div>

            <div v-if="variant === 'User/Text' || variant === 'Responder/Text'"
                :class="[ui.chat.message.content, isResponder ? 'text-foreground' : 'text-muted-foreground/80']"
                v-html="md.render(cleanContent || '')" />

            <div v-if="variant === 'Responder/Image'"
                class="relative rounded-lg overflow-hidden border border-border group cursor-pointer">
                <img alt="Generated Image"
                    class="w-full h-auto max-w-sm object-cover transition-transform duration-500 group-hover:scale-105"
                    :src="imageSrc" />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                    <button
                        class="p-2 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-colors">
                        <Download class="size-4" />
                    </button>
                </div>
            </div>
        </div>

        <div v-if="attachments?.length" class="flex gap-3 mt-3 overflow-x-auto pb-2 custom-scrollbar">
            <div v-for="(att, i) in attachments" :key="i" class="relative group shrink-0">
                <img v-if="att.type === 'image'" :src="att.url" :alt="att.name || 'Image'"
                    class="h-[100px] w-[100px] rounded-none border border-white/10 object-cover hover:border-primary/50 transition-colors" />
                <a v-else :href="att.url" target="_blank"
                    class="h-[100px] min-w-[200px] flex items-center gap-3 bg-white/5 px-4 py-2 rounded-none border border-white/10 hover:bg-white/10 hover:border-primary/50 transition-colors">
                    <div class="p-2 bg-background/50 border border-white/10 rounded-none shrink-0">
                        <FileText class="text-primary size-5" />
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-sm font-space truncate max-w-[120px]">{{ att.name || 'File' }}</span>
                        <span class="text-xs text-muted-foreground">Attachment</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="flex items-center gap-2 mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="copy()"
                class="text-muted-foreground hover:text-foreground transition-colors flex items-center gap-1">
                <Check v-if="copied" class="text-xs text-green-500 size-3" />
                <Copy v-else class="text-xs size-3" />
            </button>
            <button v-if="isResponder" class="text-muted-foreground hover:text-foreground transition-colors">
                <RotateCw class="text-xs size-3" />
            </button>
            <span v-if="isUser && timestamp" class="text-[10px] text-muted-foreground/50">{{ timestamp }}</span>
        </div>
    </Motion>
</template>

<style scoped>
:deep(.prose h1) {
    font-size: 1.5em;
    font-weight: 700;
    margin-top: 1em;
    margin-bottom: 0.5em;
    color: var(--color-primary);
}

:deep(.prose h2) {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1em;
    margin-bottom: 0.5em;
    color: var(--color-foreground);
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
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.02);
    border-radius: 4px;
    overflow-x: auto;
}
</style>