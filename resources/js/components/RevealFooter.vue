<script setup lang="ts">
import { ui } from '@/config/ui';
import { Motion } from 'motion-v';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
  content: any;
}>();
</script>

<template>
  <div class="relative h-[80vh] md:h-[60vh]" style="clip-path: polygon(0% 0, 100% 0, 100% 100%, 0 100%);">
    <div
      class="fixed bottom-0 left-0 right-0 h-[80vh] md:h-[60vh] flex flex-col justify-between bg-[#050505] overflow-hidden">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,0.03),transparent_70%)]"></div>

      <div class="px-6 py-20 md:py-32 relative z-10 h-full flex flex-col justify-between">
        <div :class="ui.layout.footerClamp" class="flex flex-col md:flex-row justify-between gap-12 md:gap-20">
          <div class="space-y-6 md:w-1/3">
            <Motion class="flex items-center gap-2" :initial="{ opacity: 0, y: 20 }"
              :while-in-view="{ opacity: 1, y: 0 }" :transition="{ duration: 0.8 }">
              <AppLogoIcon class="w-8 h-8 text-white" />
              <span class="text-2xl font-medium tracking-tight">{{ content.appName }}</span>
            </Motion>
            <p class="text-white/40 text-sm leading-relaxed max-w-xs">
              Crafting clarity from the noise. Your AI companion for the next generation of work.
            </p>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-12 md:gap-20 w-full md:w-auto">
            <div class="space-y-6">
              <h4 class="text-xs uppercase tracking-widest text-white/30 font-medium">Platform</h4>
              <ul class="space-y-4">
                <li v-for="link in content.footer.links.index" :key="link.label">
                  <a :href="link.href"
                    class="text-white/60 hover:text-white transition-colors text-sm block relative group">
                    <span class="relative z-10">{{ link.label }}</span>
                    <span
                      class="absolute left-0 -bottom-1 w-0 h-[1px] bg-white group-hover:w-full transition-[width] duration-300"></span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="space-y-6">
              <h4 class="text-xs uppercase tracking-widest text-white/30 font-medium">Socials</h4>
              <ul class="space-y-4">
                <li v-for="link in content.footer.links.social" :key="link.label">
                  <a :href="link.href"
                    class="text-white/60 hover:text-white transition-colors text-sm flex items-center gap-2 group">
                    {{ link.label }}
                    <span
                      class="opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all text-xs">↗</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div :class="ui.layout.footerClamp"
          class="pt-20 md:pt-32 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 mt-12 md:mt-24">
          <p class="text-white/20 text-xs text-center md:text-left">© 2024 {{ content.appName }} AI Inc. All rights
            reserved.</p>
          <div class="flex items-center gap-6">
            <a href="#" class="text-white/20 hover:text-white/40 text-xs transition-colors">Privacy</a>
            <a href="#" class="text-white/20 hover:text-white/40 text-xs transition-colors">Terms</a>
            <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-xs text-white/30">All Systems Operational</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
