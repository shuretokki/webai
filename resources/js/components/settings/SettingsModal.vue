<script setup lang="ts">
import { ref } from 'vue';
import Modal from '@/components/ui/Modal.vue';
import { usePage, Link } from '@inertiajs/vue3';

defineProps<{
    show: boolean;
}>();

const emit = defineEmits(['close']);

const tabs = [
    { id: 'profile', label: 'Profile', icon: 'i-solar-user-circle-linear' },
    { id: 'appearance', label: 'Appearance', icon: 'i-solar-palette-linear' },
    { id: 'usage', label: 'Usage', icon: 'i-solar-chart-square-linear' },
];

const activeTab = ref('profile');
const page = usePage();
const user = page.props.auth.user;

</script>

<template>
    <Modal :show="show" title="Settings" @close="$emit('close')">
        <div class="flex flex-col h-[400px]">
            <div class="flex border-b border-border mb-4">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="px-4 py-2 text-sm font-space flex items-center gap-2 border-b-2 transition-colors"
                    :class="[
                        activeTab === tab.id
                            ? 'border-primary text-primary'
                            : 'border-transparent text-muted-foreground hover:text-foreground'
                    ]"
                >
                    <component :is="tab.icon" class="text-lg" />
                    {{ tab.label }}
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div v-if="activeTab === 'profile'" class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="size-16 rounded-none bg-muted flex items-center justify-center text-2xl font-bold text-muted-foreground overflow-hidden">
                            <img v-if="user?.avatar" :src="user.avatar" class="w-full h-full object-cover" />
                            <span v-else>{{ user?.name?.charAt(0) || 'U' }}</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-foreground">{{ user?.name }}</h4>
                            <p class="text-sm text-muted-foreground">{{ user?.email }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-border">
                        <Link href="/settings/profile" class="text-sm text-primary hover:underline">
                            Manage Profile
                        </Link>
                    </div>
                </div>

                <div v-if="activeTab === 'appearance'" class="space-y-4">
                    <p class="text-sm text-muted-foreground">Theme settings coming soon.</p>
                </div>

                <div v-if="activeTab === 'usage'" class="space-y-4">
                    <p class="text-sm text-muted-foreground">Usage statistics coming soon.</p>
                </div>
            </div>
        </div>
    </Modal>
</template>
