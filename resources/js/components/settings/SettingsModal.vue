<script setup lang="ts">
import { ref, computed } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import { usePage, Link } from '@inertiajs/vue3';

defineProps<{
    show: boolean;
}>();

import { useWindowSize, useSwipe } from '@vueuse/core';
import SettingsContent from './SettingsContent.vue';

const emit = defineEmits(['close']);

const { width } = useWindowSize();
const isMobile = computed(() => width.value < 768);
const mobileView = ref<'list' | 'detail'>('list');

// Swipe to close logic
const modalHeader = ref<HTMLElement | null>(null);
const { isSwiping, direction } = useSwipe(modalHeader);

watch(isSwiping, (newValue) => {
    if (newValue && direction.value === 'down') {
        closeModal();
    }
});


const navigateToTab = (tabId: string) => {
    activeTab.value = tabId;
    mobileView.value = 'detail';
};

const handleBack = () => {
    if (mobileView.value === 'detail') {
        mobileView.value = 'list';
    } else {
        closeModal();
    }
};

const closeModal = () => {
    emit('close');
    setTimeout(() => {
        mobileView.value = 'list';
    }, 300);
};

const mobileTitle = computed(() => {
    if (mobileView.value === 'list') return 'Settings';
    return tabs.find(t => t.id === activeTab.value)?.label || 'Settings';
});

const tabs = [
    { id: 'account', label: 'Account', icon: 'i-solar-user-circle-linear' },
    { id: 'behavior', label: 'Behavior', icon: 'i-solar-settings-minimalistic-linear' },
    { id: 'customize', label: 'Customize', icon: 'i-solar-tuning-2-linear' },
    { id: 'data', label: 'Data Control', icon: 'i-solar-database-linear' },
];

const activeTab = ref('account');
const page = usePage();
const user = computed(() => page.props.auth.user);

const handleUpgrade = () => {
    window.location.href = '/settings/subscription';
};

</script>

<template>
    <Modal :show="show" :title="mobileTitle" @close="closeModal" max-width="3xl"
        :content-class="isMobile ? 'p-0 h-[80vh]' : 'p-0'" :hide-header="isMobile"
        :align="isMobile ? 'bottom' : 'center'">

        <div class="hidden md:flex h-[450px] overflow-hidden">
            <div class="w-1/3 border-r border-border bg-card/30 flex flex-col justify-between py-6">
                <div class="flex flex-col gap-1 px-3">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        class="px-3 py-2 text-sm font-space flex items-center gap-3 rounded-none transition-all duration-200 group relative"
                        :class="[
                            activeTab === tab.id
                                ? 'bg-white/10 text-foreground font-medium'
                                : 'text-muted-foreground hover:text-foreground hover:bg-white/5'
                        ]">
                        <div v-if="activeTab === tab.id" class="absolute left-0 top-0 bottom-0 w-0.5 bg-primary"></div>

                        <component :is="tab.icon" class="text-lg shrink-0"
                            :class="activeTab === tab.id ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground'" />
                        {{ tab.label }}
                    </button>
                </div>

                <div class="px-4 mt-auto pt-4">
                    <div
                        class="p-4 rounded-none border border-border bg-gradient-to-br from-card to-background relative overflow-hidden group hover:border-primary/50 transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="size-8 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10">
                                <div class="size-5 rounded-full border border-primary bg-primary/20"></div>
                            </div>
                            <span class="font-space font-medium text-sm text-foreground">Get ECNELIS+</span>
                        </div>

                        <button @click="handleUpgrade"
                            class="w-full text-xs py-1.5 px-3 rounded-full border border-border bg-white/5 hover:bg-white/10 hover:border-primary/50 transition-all text-muted-foreground hover:text-foreground">
                            Upgrade
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 bg-background p-6 overflow-y-auto custom-scrollbar">
                <SettingsContent :active-tab="activeTab" />
            </div>
        </div>

        <div class="md:hidden flex flex-col h-full bg-background overflow-hidden relative">
            <div ref="modalHeader"
                class="flex items-center justify-center pt-3 pb-4 relative shrink-0 cursor-grab active:cursor-grabbing touch-none">
                <div class="w-12 h-1.5 bg-border/50 rounded-full"></div>
            </div>

            <div class="px-4 pb-4 flex items-center gap-3 shrink-0" v-if="mobileView === 'detail'">
                <button @click="mobileView = 'list'"
                    class="text-muted-foreground hover:text-foreground transition-colors">
                    <i-solar-arrow-left-linear class="text-xl" />
                </button>
                <h3 class="text-lg font-space font-medium text-foreground">{{ mobileTitle }}</h3>
            </div>

            <div class="px-4 pb-4 shrink-0" v-else>
                <h3 class="text-lg font-space font-medium text-foreground">{{ mobileTitle }}</h3>
            </div>

            <div v-if="mobileView === 'list'" class="flex-1 overflow-y-auto p-0">
                <div class="flex items-center gap-4 p-4 border-b border-border active:bg-white/5 transition-colors"
                    @click="navigateToTab('account')">
                    <div
                        class="size-12 rounded-full bg-muted flex items-center justify-center text-xl font-bold text-muted-foreground overflow-hidden border border-border shrink-0">
                        <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                        <span v-else>{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-space font-medium text-foreground truncate">{{ user?.name }}</h4>
                        <p class="text-xs text-muted-foreground font-space truncate">{{ user?.email }}</p>
                    </div>
                    <button
                        class="px-5 py-2.5 text-sm border border-border rounded-full hover:bg-white/5 transition-colors text-muted-foreground font-medium">Manage</button>
                </div>

                <div class="flex items-center justify-between p-4 border-b border-border active:bg-white/5 transition-colors"
                    @click="handleUpgrade">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10">
                            <div class="size-6 rounded-full border border-primary bg-primary/20"></div>
                        </div>
                        <span class="font-space font-medium text-foreground">Get ECNELIS+</span>
                    </div>
                    <button
                        class="px-5 py-2.5 rounded-full border border-border text-sm text-muted-foreground hover:text-foreground hover:border-primary/50 transition-colors font-medium">Upgrade</button>
                </div>

                <div class="flex flex-col">
                    <button v-for="tab in tabs.filter(t => t.id !== 'account')" :key="tab.id"
                        @click="navigateToTab(tab.id)"
                        class="flex items-center justify-between p-4 border-b border-border text-left group active:bg-white/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <component :is="tab.icon"
                                class="text-2xl text-muted-foreground group-active:text-foreground transition-colors" />
                            <span class="font-space text-lg text-foreground">{{ tab.label }}</span>
                        </div>
                        <i-solar-alt-arrow-right-linear class="text-xl text-muted-foreground" />
                    </button>
                </div>
            </div>

            <div v-if="mobileView === 'detail'"
                class="flex-1 overflow-y-auto p-4 bg-background animate-in slide-in-from-right-10 duration-200">
                <SettingsContent :active-tab="activeTab" />
            </div>
        </div>
    </Modal>
</template>
