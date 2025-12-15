<script setup lang="ts">
import { onKeyStroke, useDebounceFn } from '@vueuse/core';

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

// Search function with debouncing
const performSearch = useDebounceFn(async () => {
    if (!query.value.trim()) {
        results.value = [];
        return;
    }

    isLoading.value = true;
    try {
        const response = await fetch(`/chat/search?q=${encodeURIComponent(query.value)}`, {
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

// Keyboard navigation
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
    window.location.href = result.url;
};

// Reset state when modal closes
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

// Global keyboard shortcut (Cmd/Ctrl + K)
onKeyStroke(['k', 'K'], (e) => {
    if ((e.metaKey || e.ctrlKey) && !isOpen.value) {
        e.preventDefault();
        isOpen.value = true;
    }
});

// Close on Escape
onKeyStroke('Escape', () => {
    if (isOpen.value) {
        isOpen.value = false;
    }
});

// Arrow key navigation
onKeyStroke('ArrowDown', (e) => {
    if (isOpen.value) {
        e.preventDefault();
        handleArrowDown();
    }
});

onKeyStroke('ArrowUp', (e) => {
    if (isOpen.value) {
        e.preventDefault();
        handleArrowUp();
    }
});

onKeyStroke('Enter', (e) => {
    if (isOpen.value && results.value.length > 0) {
        e.preventDefault();
        handleEnter();
    }
});

const highlightMatch = (text: string, query: string): string => {
    if (!query.trim()) return text;

    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-[#dbf156]/30 text-[#dbf156]">$1</mark>');
};
</script><template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-start justify-center pt-[15vh] px-4"
                @click.self="isOpen = false"
            >
                <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-4 scale-95"
                >
                    <div
                        v-if="isOpen"
                        class="w-full max-w-2xl bg-[#2a2a2a] border border-white/10 rounded-lg shadow-2xl overflow-hidden"
                    >
                        <!-- Search Input -->
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-white/10">
                            <i-solar-magnifer-linear class="text-xl text-white/40" />
                            <input
                                ref="searchInput"
                                v-model="query"
                                type="text"
                                placeholder="Search chats..."
                                class="flex-1 bg-transparent text-white placeholder:text-white/40 focus:outline-none"
                            />
                            <div class="flex items-center gap-1 text-xs text-white/40">
                                <kbd class="px-2 py-0.5 bg-white/10 rounded border border-white/20">Esc</kbd>
                                <span>to close</span>
                            </div>
                        </div>

                        <!-- Results -->
                        <div class="max-h-[60vh] overflow-y-auto">
                            <!-- Loading State -->
                            <div v-if="isLoading" class="p-8 text-center text-white/40">
                                <i-solar-restart-circle-linear class="text-3xl animate-spin mx-auto mb-2" />
                                <p>Searching...</p>
                            </div>

                            <!-- Empty State (No Query) -->
                            <div v-else-if="!query.trim()" class="p-8 text-center text-white/40">
                                <i-solar-magnifer-linear class="text-4xl mx-auto mb-2" />
                                <p class="text-sm">Type to search your chats</p>
                                <p class="text-xs mt-1 text-white/20">Search by title or message content</p>
                            </div>

                            <!-- No Results -->
                            <div v-else-if="results.length === 0 && !isLoading" class="p-8 text-center text-white/40">
                                <i-solar-magnifer-linear class="text-4xl mx-auto mb-2 opacity-50" />
                                <p class="text-sm">No chats found</p>
                                <p class="text-xs mt-1 text-white/20">Try a different search term</p>
                            </div>

                            <!-- Results List -->
                            <div v-else class="py-2">
                                <button
                                    v-for="(result, index) in results"
                                    :key="result.id"
                                    type="button"
                                    @click="selectChat(result)"
                                    class="w-full px-4 py-3 text-left hover:bg-white/5 transition-colors flex items-start gap-3 group"
                                    :class="{ 'bg-white/10': index === selectedIndex }"
                                >
                                    <i-solar-chat-round-dots-linear class="text-xl text-white/40 group-hover:text-[#dbf156] transition-colors mt-0.5 shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-baseline justify-between gap-2 mb-1">
                                            <h3
                                                class="font-medium text-white truncate"
                                                v-html="highlightMatch(result.title, query)"
                                            ></h3>
                                            <span class="text-xs text-white/40 shrink-0 capitalize">{{ result.type }}</span>
                                        </div>
                                        <p
                                            class="text-sm text-white/60 line-clamp-2"
                                            v-html="highlightMatch(result.subtitle, query)"
                                        ></p>
                                    </div>
                                    <i-solar-alt-arrow-right-linear
                                        class="text-lg text-white/20 group-hover:text-white/40 transition-colors mt-1 shrink-0 opacity-0 group-hover:opacity-100"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-4 py-2 border-t border-white/10 bg-white/5 flex items-center justify-between text-xs text-white/40">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <kbd class="px-1.5 py-0.5 bg-white/10 rounded border border-white/20">↑</kbd>
                                    <kbd class="px-1.5 py-0.5 bg-white/10 rounded border border-white/20">↓</kbd>
                                    <span>to navigate</span>
                                </span>
                                <span class="flex items-center gap-1">
                                    <kbd class="px-1.5 py-0.5 bg-white/10 rounded border border-white/20">Enter</kbd>
                                    <span>to select</span>
                                </span>
                            </div>
                            <div v-if="results.length > 0" class="text-white/60">
                                {{ results.length }} {{ results.length === 1 ? 'result' : 'results' }}
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
