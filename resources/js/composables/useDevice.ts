import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Global Device Detection Composable
 * Centralizes breakpoint logic and event listeners for better performance.
 */

const isMobile = ref(false);
const isTablet = ref(false);
const isDesktop = ref(false);
const canHover = ref(false);

const checkDevice = () => {
  if (typeof window === 'undefined') return;

  const width = window.innerWidth;
  isMobile.value = width < 768;
  isTablet.value = width >= 768 && width < 1024;
  isDesktop.value = width >= 1024;

  // Check if the device has a primary pointer that can hover (mouse/trackpad)
  canHover.value = window.matchMedia('(hover: hover)').matches;
};

// Singleton pattern: shared state across all components
export function useDevice() {
  onMounted(() => {
    checkDevice();
    window.addEventListener('resize', checkDevice);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', checkDevice);
  });

  return {
    isMobile,
    isTablet,
    isDesktop,
    canHover
  };
}
