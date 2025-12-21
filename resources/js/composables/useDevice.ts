import { useBreakpoints, breakpointsTailwind, useElementHover } from '@vueuse/core';
import { ref, onMounted } from 'vue';

/**
 * Global Device Detection Composable (VueUse based)
 * Uses matchMedia for superior performance over resize listeners.
 */

export function useDevice() {
  const breakpoints = useBreakpoints(breakpointsTailwind);

  const isMobile = breakpoints.smaller('md'); // < 768px
  const isTablet = breakpoints.between('md', 'lg'); // 768px - 1024px
  const isDesktop = breakpoints.greaterOrEqual('lg'); // >= 1024px

  const canHover = ref(false);

  onMounted(() => {
    if (typeof window !== 'undefined') {
      canHover.value = window.matchMedia('(hover: hover)').matches;
    }
  });

  return {
    isMobile,
    isTablet,
    isDesktop,
    canHover
  };
}
