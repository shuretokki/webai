<script setup lang="ts">
import { computed } from 'vue';
import { Motion } from 'motion-v';
import MarkdownIt from 'markdown-it';
import { createHighlighter } from 'shiki';

interface Props {
    variant?: 'User/Text' | 'Responder/Text' | 'Responder/Code' | 'Responder/Image';
    content?: string;
    imageSrc?: string;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'User/Text',
    content: '',
});

const isUser = computed(() => props.variant.startsWith('User'));
const isResponder = computed(() => props.variant.startsWith('Responder'));

const imgImage = "https://www.figma.com/api/mcp/asset/f79a321c-5666-4648-ab56-cb9a2ac2b1b5";

const md = ref(new MarkdownIt({
    html: false,
    linkify: true,
    typographer: true,
}));

onMounted(async() => {
    const highlighter = await createHighlighter({
        themes: ['vitesse-dark'],
        langs: ['javascript', 'typescipt', 'php', 'python', 'html', 'css', 'json', 'bash', 'sql'],
    });

    md.value = new MarkdownIt({
        html: false,
        linkify: true,
        typographer: true,
        highlight: (code, lang) => {
            if (!lang || !highlighter.getLoadedLanguages().includes(lang)) {
                return highlighter.codeToHtml(code, { lang: 'text', theme: 'vitesse-dark'});
            }

            return highlighter.codeToHtml(code, { lang, theme: 'vitesse-dark'});
        }
    });
});

</script>

<template>
    <Motion
        :initial="{ opacity: 0, y: 20 }"
        :animate="{ opacity: 1, y: 0 }"
        :transition="{ duration: 0.4 }"
        class="w-full shrink-0 relative flex flex-col"
        :class="[
            isUser ? 'items-end py-2 pl-16 pr-4' : '',
            isResponder ? 'items-start py-2 pl-4 pr-16' : ''
        ]"
    >
        <!-- Responder Header -->
        <div v-if="isResponder" class="relative shrink-0 flex flex-col items-start justify-center pb-2 pl-1">
            <div class="flex items-center gap-2">
                <div class="h-[2px] w-12 bg-gradient-to-r from-[#acb564] to-transparent"></div>
                <p class="font-philosopher font-bold text-sm text-[#f3f3f3] tracking-wide shadow-black drop-shadow-md">
                    Silence
                </p>
            </div>
        </div>

        <!-- Content Container -->
        <div
            class="relative shrink-0 max-w-full md:max-w-[80%] rounded-2xl overflow-hidden transition-all"
            :class="[
                variant === 'Responder/Code'
                    ? 'bg-[#1e1e1e] border-l-4 border-[#acb564] p-4 rounded-none'
                    : isResponder
                        ? 'bg-white/5 p-4 backdrop-blur-sm border border-white/10 rounded-tl-none'
                        : 'bg-[#dbf156]/10 p-4 border border-[#dbf156]/20 rounded-tr-none'
            ]"
        >
            <!-- Text Content -->
            <div v-if="variant === 'User/Text' || variant === 'Responder/Text'"
                class="prose prose-invert font-space font-normal text-[16px] leading-relaxed break-words"
                :class="isResponder ? 'text-[#f8ffd7]' : 'text-[#f3f3f3]'"
                v-html="md.render(content)"
            />

            <!-- Code Content -->
            <div v-if="variant === 'Responder/Code'" class="w-full overflow-x-auto">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-white/10">
                    <span class="text-xs text-white/40 font-mono">C++</span>
                    <button class="text-white/40 hover:text-white transition-colors">
                        <i-solar-copy-linear class="text-sm" />
                    </button>
                </div>
                <pre class="font-mono text-sm text-[#d4d4d4] leading-relaxed"><code><span class="text-[#569cd6]">#include</span> <span class="text-[#ce9178]">&lt;iostream&gt;</span>

<span class="text-[#569cd6]">int</span> main() {
    std::cout &lt;&lt; <span class="text-[#ce9178]">"Hello World!"</span> &lt;&lt; std::endl;
    <span class="text-[#569cd6]">return</span> <span class="text-[#b5cea8]">0</span>;
}</code></pre>
            </div>

            <!-- Image Content -->
            <div v-if="variant === 'Responder/Image'" class="relative rounded-lg overflow-hidden border border-white/10 group cursor-pointer">
                <img
                    alt="Generated Image"
                    class="w-full h-auto max-w-sm object-cover transition-transform duration-500 group-hover:scale-105"
                    :src="imageSrc || imgImage"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                    <button class="p-2 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-colors">
                        <i-solar-download-linear />
                    </button>
                </div>
            </div>
        </div>

        <!-- Message Actions (Optional) -->
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
/* :deep(.prose h1) {
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
    background-color: rgba(255,255,255,0.1);
    padding: 0.2em 0.4em;
    border-radius: 0.25em;
    font-family: monospace;
}
:deep(.prose pre) {
    background-color: #1e1e1e;
    padding: 1em;
    border-radius: 0.5em;
    overflow-x: auto;
} */
</style>