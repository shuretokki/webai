<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { User, Settings, Sliders, Database, ArrowLeft, ChevronRight } from 'lucide-vue-next';
import { useWindowSize } from '@vueuse/core';
import SettingsContent from './SettingsContent.vue';
import Modal from '@/components/ui/Modal.vue';
import { ui } from '@/config/ui';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits(['close']);

const { width } = useWindowSize();
const isMobile = computed(() => width.value < 768);
const mobileView = ref<'list' | 'detail'>('list');

const navigateToTab = (tabId: string) => {
    activeTab.value = tabId;
    mobileView.value = 'detail';
};

const closeModal = () => {
    emit('close');
    setTimeout(() => {
        mobileView.value = 'list';
    }, 300);
};

const activeTab = ref('account');
const page = usePage();
const user = computed(() => page.props.auth.user);

const handleUpgrade = () => {
    window.location.href = '/pricing';
};

const tabs = [
    { id: 'account', label: 'Account', icon: User },
    { id: 'behavior', label: 'Behavior', icon: Settings },
    { id: 'customize', label: 'Customize', icon: Sliders },
    { id: 'data', label: 'Data Control', icon: Database },
];

const mobileTitle = computed(() => {
    if (mobileView.value === 'list') return 'Settings';
    return tabs.find(t => t.id === activeTab.value)?.label || 'Settings';
});

</script>

<template>
    <Modal :show="show" :type="isMobile ? 'drawer' : 'modal'" :title="isMobile ? mobileTitle : 'Settings'"
        :maxWidth="ui.modal.settings.maxWidth" :rounded="ui.modal.settings.rounded" :bg="ui.modal.settings.bg"
        :height="isMobile ? 'h-[90vh]' : ''"
        contentClass="p-0 font-inter" @close="closeModal">
        <div v-if="!isMobile" class="flex" :class="[ui.modal.settings.height]">
            <div class="w-1/3 flex flex-col justify-between py-6" :class="[ui.modal.settings.sidebarBg]">
                <div class="flex flex-col gap-1 px-3">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        class="px-3 py-2.5 text-[16px] font-inter flex items-center gap-3 rounded-none transition-all duration-200 group relative"
                        :class="[
                            activeTab === tab.id
                                ? ui.modal.settings.tabActive
                                : ui.modal.settings.tabInactive
                        ]">
                        <div v-if="activeTab === tab.id" class="absolute left-0 top-0 bottom-0 w-0.5 bg-primary"></div>

                        <component :is="tab.icon" class="text-lg shrink-0"
                            :class="activeTab === tab.id ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground'" />

                        {{ tab.label }}
                    </button>
                </div>

                <div class="px-4 mt-auto pt-4"
                    v-if="!['plus', 'enterprise'].includes((user as any)?.subscription_tier)">
                    <div
                        class="p-4 rounded-none border border-border bg-gradient-to-br from-card to-background relative overflow-hidden group transition-colors">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="size-8 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10">
                                <div class="size-5 rounded-full border border-primary bg-primary/20"></div>
                            </div>
                            <span class="font-inter font-medium text-sm text-foreground">Get ECNELIS+</span>
                        </div>

                        <button @click="handleUpgrade"
                            class="w-full text-xs py-1.5 px-3 rounded-full border border-border bg-white/5 hover:bg-white/10 cursor-pointer transition-all text-muted-foreground hover:text-foreground">
                            Upgrade
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 p-6 overflow-y-auto custom-scrollbar" :class="[ui.modal.settings.contentBg]">
                <SettingsContent :active-tab="activeTab" />
            </div>
        </div>

        <div v-else class="flex flex-col h-full overflow-hidden">
            <div class="px-4 pb-4 flex items-center gap-3 shrink-0" v-if="mobileView === 'detail'">
                <button @click="mobileView = 'list'"
                    class="text-muted-foreground hover:text-foreground transition-colors p-2 -ml-2">
                    <ArrowLeft class="size-5" />
                </button>
            </div>

            <div v-if="mobileView === 'list'" class="flex-1 overflow-y-auto">
                <div class="flex items-center gap-4 p-4 border-b border-border active:bg-white/5 transition-colors"
                    @click="navigateToTab('account')">
                    <div
                        class="size-12 rounded-full bg-muted flex items-center justify-center text-xl font-bold text-muted-foreground overflow-hidden border border-border shrink-0">
                        <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                        <span v-else>{{ user?.name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-inter font-medium text-foreground truncate text-xl flex items-center gap-2">
                            {{ user?.name }}
                            <span v-if="['plus', 'enterprise'].includes((user as any)?.subscription_tier)"
                                class="px-1.5 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase border border-primary/20 tracking-wider">
                                {{ (user as any)?.subscription_tier === 'enterprise' ? 'PRO' : 'PLUS' }}
                            </span>
                        </h4>
                        <p class="text-lg text-muted-foreground font-inter truncate">{{ user?.email }}</p>
                    </div>
                    <button
                        class="px-5 py-2.5 text-base border border-border rounded-full hover:bg-white/5 transition-colors text-muted-foreground font-medium">Manage</button>
                </div>

                <div v-if="!['plus', 'enterprise'].includes((user as any)?.subscription_tier)"
                    class="flex items-center justify-between p-4 border-b border-border active:bg-white/5 transition-colors"
                    @click="handleUpgrade">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full border border-primary/30 flex items-center justify-center bg-primary/10">
                            <div class="size-6 rounded-full border border-primary bg-primary/20"></div>
                        </div>
                        <span class="font-inter font-medium text-foreground! text-lg">Get ECNELIS+</span>
                    </div>
                    <button
                        class="px-5 py-2.5 rounded-full border border-border text-base text-muted-foreground hover:text-foreground hover:border-primary/50 transition-colors font-medium">Upgrade</button>
                </div>

                <div class="flex flex-col">
                    <button v-for="tab in tabs.filter(t => t.id !== 'account')" :key="tab.id"
                        @click="navigateToTab(tab.id)"
                        class="flex items-center justify-between p-4 border-b border-border text-left group active:bg-white/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <component :is="tab.icon"
                                class="text-2xl text-muted-foreground group-active:text-foreground transition-colors" />
                            <span class="font-inter text-lg text-foreground">{{ tab.label }}</span>
                        </div>
                        <ChevronRight class="size-5 text-muted-foreground" />
                    </button>
                </div>
            </div>

            <div v-if="mobileView === 'detail'" class="flex-1 overflow-y-auto p-4 text-lg">
                <SettingsContent :active-tab="activeTab" />
            </div>
        </div>
    </Modal>
</template>
