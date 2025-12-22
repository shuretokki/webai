<script setup lang="ts">
import { ui } from '@/config/ui';
import { useDevice } from '@/composables/useDevice';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
  content: any;
}>();

const { isMobile } = useDevice();
</script>

<template>
  <div :class="[
    'relative w-full overflow-hidden',
    isMobile ? 'bg-[#050505]' : 'h-[500px]'
  ]" :style="!isMobile ? 'clip-path: polygon(0% 0, 100% 0, 100% 100%, 0 100%);' : ''">

    <div :class="[
      'bg-[#050505] w-full flex flex-col',
      isMobile ? 'relative py-20 px-6' : 'fixed bottom-0 left-0 right-0 h-[500px]'
    ]">
      <div
        class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.03),transparent_70%)] pointer-events-none">
      </div>

      <div class="relative z-10 flex-1 flex flex-col justify-between px-6 py-16 md:py-24">
        <div :class="ui.layout.footerClamp"
          class="flex flex-col md:flex-row justify-between items-start gap-12 md:gap-20">
          <div class="space-y-6 md:w-1/3">
            <Link href="/explore" class="flex items-center gap-2 group/logo hover:opacity-80 transition-opacity">
            <AppLogoIcon class="w-8 h-8 text-white" />
            <span class="text-2xl font-medium tracking-tight">{{ content.appName }}</span>
            </Link>
            <p class="text-white/30 text-sm leading-relaxed max-w-[260px]">
              Crafting clarity from the noise. Your AI companion for the next generation of work.
            </p>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-12 md:gap-20 w-full md:w-auto">
            <div class="space-y-6">
              <h4 class="text-xs uppercase tracking-widest text-white/20 font-medium">Platform</h4>
              <ul class="space-y-3">
                <li v-for="link in content.footer.links.index" :key="link.label">
                  <a :href="link.href"
                    class="text-white/50 hover:text-white transition-colors text-sm block relative group w-fit">
                    <span class="relative z-10">{{ link.label }}</span>
                    <span
                      class="absolute left-0 -bottom-0.5 w-0 h-[1px] bg-white group-hover:w-full transition-[width] duration-300"></span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="space-y-6">
              <h4 class="text-xs uppercase tracking-widest text-white/20 font-medium">Socials</h4>
              <ul class="space-y-3">
                <li v-for="link in content.footer.links.social" :key="link.label">
                  <a :href="link.href"
                    class="text-white/50 hover:text-white transition-colors text-sm flex items-center gap-2 group w-fit">
                    {{ link.label }}
                    <span
                      class="opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all text-[10px]">↗</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div :class="ui.layout.footerClamp"
          class="pt-12 mt-4 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
          <p class="text-white/20 text-xs text-center md:text-left">© 2025 {{ content.appName }} Studio All rights
            reserved.</p>
          <div class="flex items-center gap-6">
            <a href="/privacy" class="text-white/20 hover:text-white/40 text-xs transition-colors">Privacy</a>
            <a href="/terms" class="text-white/20 hover:text-white/40 text-xs transition-colors">Terms</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
