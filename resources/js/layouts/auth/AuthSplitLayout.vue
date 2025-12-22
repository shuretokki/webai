<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ui } from '@/config/ui';

const props = defineProps<{ title?: string; description?: string; }>();

const page = usePage();
const name = '';

const bgImage = '/images/auth.webp';
</script>

<template>
    <div :class="ui.auth.layout">
        <div :class="ui.auth.panel.wrapper">
            <div class="absolute inset-0 z-0">
                <div :class="ui.auth.panel.overlay"></div>
                <img :src="bgImage" class="w-full h-full object-cover opacity-75" />
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/40"></div>
            </div>

            <Link :href="home()" class="relative z-20 flex items-center gap-2 group">
                <AppLogoIcon class="w-8 h-8 text-white transition-opacity group-hover:opacity-80" />
                <span class="text-sm font-medium tracking-tight text-white/90">{{ name }}</span>
            </Link>

            <div :class="ui.auth.panel.content">
                <blockquote class="space-y-6">
                    <p :class="ui.auth.panel.quote">
                        "E = mc²"
                    </p>
                    <footer :class="ui.auth.panel.author">Albert Epstein</footer>
                </blockquote>
            </div>
        </div>

        <div :class="ui.auth.form.container">
            <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none" :style="ui.patterns.blueprint"></div>

            <div :class="ui.auth.form.wrapper">
                <div :class="ui.auth.form.header">
                    <h1 :class="ui.auth.form.title" v-if="title">{{ title }}</h1>
                    <p :class="ui.auth.form.description" v-if="description">{{ description }}</p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
