<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted } from 'vue';
import { useDebounceFn, useWindowSize, useEventListener } from '@vueuse/core';
import { Search, Loader2, MessageSquare, ChevronRight } from 'lucide-vue-next';
import { Motion, AnimatePresence } from 'motion-v';
import { ui } from '@/config/ui';

interface SearchResult {
    type: 'chat' | 'message';
    id: number;
    title: string;
    subtitle: string;
    url: string;
}

const isOpen = defineModel<boolean>('open', { default: false });
const query = ref('');
const results = ref<SearchResult[]>([]);
const selectedIndex = ref(0);
const isLoading = ref(false);
const searchInput = ref<HTMLInputElement | null>(null);

const { width } = useWindowSize();
const isMobile = computed(() => width.value < 768);

const performSearch = useDebounceFn(async () => {
    if (!query.value.trim()) {
        results.value = [];
        return;
    }

    isLoading.value = true;
    try {
        const response = await fetch(`/s?q=${encodeURIComponent(query.value)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.ok) {
            const data = await response.json();
            results.value = data.results || [];
            selectedIndex.value = 0;
        }
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isLoading.value = false;
    }
}, 300);

watch(query, () => {
    performSearch();
});

const handleArrowDown = () => {
    if (selectedIndex.value < results.value.length - 1) {
        selectedIndex.value++;
    }
};

const handleArrowUp = () => {
    if (selectedIndex.value > 0) {
        selectedIndex.value--;
    }
};

const handleEnter = () => {
    if (results.value.length > 0 && results.value[selectedIndex.value]) {
        selectChat(results.value[selectedIndex.value]);
    }
};

const selectChat = (result: SearchResult) => {
    isOpen.value = false;
    window.location.href = result.url;
};

const close = () => {
    isOpen.value = false;
};

watch(isOpen, async (open) => {
    if (open) {
        await nextTick();
        searchInput.value?.focus();
    } else {
        query.value = '';
        results.value = [];
        selectedIndex.value = 0;
    }
});

// Use robust native listeners for global shortcuts
onMounted(() => {
    useEventListener('keydown', (e: KeyboardEvent) => {
        if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            isOpen.value = !isOpen.value;
        }

        if (e.key === 'Escape' && isOpen.value) {
            e.preventDefault();
            close();
        }

        if (isOpen.value) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                handleArrowDown();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                handleArrowUp();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                handleEnter();
            }
        }
    });
});

const highlightMatch = (text: string, query: string): string => {
    if (!query.trim()) return text;

    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-primary/20 text-primary">$1</mark>');
};
</script>

<template>
    <Teleport to="body">
        <AnimatePresence>
            <div v-if="isOpen"
                class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh] px-4 pointer-events-none">
                <!-- Overlay -->
                <Motion initial="initial" animate="animate" exit="exit" :variants="ui.modal.overlay"
                    class="absolute inset-0 bg-background/60 backdrop-blur-md pointer-events-auto" @click="close" />

                <!-- Search Panel -->
                <Motion initial="{ opacity: 0, scale: 0.95, y: -20 }" animate="{ opacity: 1, scale: 1, y: 0 }"
                    exit="{ opacity: 0, scale: 0.95, y: -20 }"
                    :transition="{ type: 'spring', damping: 25, stiffness: 300 }"
                    class="relative w-full pointer-events-auto"
                    :class="[ui.chat.search.maxWidth, ui.chat.search.classes.container]">
                    <!-- Header -->
                    <div :class="ui.chat.search.classes.inputWrapper">
                        <Search class="text-white/40 size-5" />
                        <input ref="searchInput" v-model="query" type="text" placeholder="Search chats..."
                            :class="ui.chat.search.classes.input" />
                        <div
                            class="hidden md:flex items-center gap-1 text-[10px] text-muted-foreground uppercase tracking-widest">
                            <kbd class="px-2 py-0.5 bg-white/5 rounded border border-white/10">Esc</kbd>
                        </div>
                    </div>

                    <!-- Results Area -->
                    <div class="max-h-[50vh] overflow-y-auto custom-scrollbar">
                        <!-- Loading State -->
                        <div v-if="isLoading" class="p-12 text-center text-muted-foreground">
                            <Loader2 class="size-8 animate-spin mx-auto mb-4 text-primary" />
                            <p class="animate-pulse">Searching knowledge...</p>
                        </div>

                        <!-- Initial State -->
                        <div v-else-if="!query.trim()" class="p-12 text-center text-muted-foreground">
                            <Search class="size-10 mx-auto mb-4 opacity-10" />
                            <p class="text-base text-white/50">Start typing to search chats</p>
                        </div>

                        <!-- No Results -->
                        <div v-else-if="results.length === 0" class="p-12 text-center text-muted-foreground">
                            <p>No matches found for <span class="text-white">"{{ query }}"</span></p>
                        </div>

                        <!-- Results List -->
                        <div v-else class="py-2">
                            <button v-for="(result, index) in results" :key="result.id" @click="selectChat(result)"
                                :class="[
                                    ui.chat.search.classes.resultItem,
                                    index === selectedIndex ? ui.chat.search.classes.resultActive : ''
                                ]">
                                <div
                                    class="size-10 rounded-none bg-white/5 border border-white/5 flex items-center justify-center shrink-0 group-hover:border-white/20 transition-colors">
                                    <MessageSquare
                                        class="size-5 text-white/40 group-hover:text-white transition-colors" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline justify-between gap-2 mb-1">
                                        <h3 class="font-medium text-white truncate text-base"
                                            v-html="highlightMatch(result.title, query)"></h3>
                                        <span
                                            class="text-[9px] text-white/30 uppercase tracking-[0.2em] bg-white/[0.03] px-1.5 py-0.5 border border-white/5">{{ result.type }}</span>
                                    </div>
                                    <p class="text-xs text-white/40 line-clamp-2 leading-relaxed"
                                        v-html="highlightMatch(result.subtitle, query)"></p>
                                </div>
                                <ChevronRight
                                    class="size-4 text-white/20 group-hover:text-white transition-all translate-x-1 opacity-0 group-hover:opacity-100 mt-1" />
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div :class="ui.chat.search.classes.footer">
                        <div class="flex items-center gap-6">
                            <span class="flex items-center gap-2">
                                <kbd class="px-1 py-0.5 bg-white/5 rounded border border-white/10 opacity-50">↑↓</kbd>
                                <span>Navigate</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <kbd
                                    class="px-1 py-0.5 bg-white/5 rounded border border-white/10 opacity-50">Enter</kbd>
                                <span>Open</span>
                            </span>
                        </div>
                        <div v-if="results.length > 0" class="text-white/20 lowercase italic font-normal">
                            {{ results.length }} matches
                        </div>
                    </div>
                </Motion>
            </div>
        </AnimatePresence>
    </Teleport>
</template>
