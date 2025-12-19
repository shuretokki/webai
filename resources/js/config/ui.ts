
/**
 * UI Configuration
 * Centralized styles and motion settings for consistent design across the web app.
 */

export const ui = {
  navigation: {
    height: 'h-20',
    maxWidth: 'max-w-[1400px]',
    scrollThreshold: [0, 1000],
    background: {
      initial: 'rgba(0, 0, 0, 0)',
      scrolled: 'rgba(0, 0, 0, 0.8)',
    },
    border: {
      initial: 'rgba(255, 255, 255, 0)',
      scrolled: 'rgba(255, 255, 255, 0.05)',
    },
    backdrop: {
      initial: 'blur(0px)',
      scrolled: 'blur(12px)',
    },
    classes: {
      nav: 'fixed top-0 left-0 right-0 z-50 border-b',
      wrapper: 'max-w-[1400px] mx-auto px-6 h-16 flex items-center justify-between md:justify-center gap-8',
    },
  },
  layout: {
    hero: 'relative z-20 text-center px-6 max-w-[90rem] mx-auto mt-20 w-full',
    sectionContainer: 'max-w-[1400px] mx-auto',
    sectionPadding: 'px-6',
    button: 'group relative inline-flex items-center justify-center gap-3 px-8 py-4 md:px-12 md:py-6 bg-white text-black text-base md:text-lg font-medium rounded-full overflow-hidden',
    buttonOutline: 'group relative inline-flex items-center justify-center gap-3 px-8 py-3 border border-white/20 text-white text-sm font-medium rounded-sm overflow-hidden hover:bg-white hover:text-black transition-colors',
  },
  typography: {
    hero: 'text-hero font-medium tracking-tighter leading-[0.85] text-white',
    display: 'text-display font-light tracking-tight leading-[1.1] text-white/90',
    title: 'text-title font-light tracking-tight text-white',
    body: 'text-base font-light text-white/60 leading-relaxed',
    accentHero: 'font-serif italic text-transparent bg-clip-text bg-gradient-to-b from-white to-white/40',
  },
  animations: {
    hover: {
      navLink: {
        color: '#ffffff',
        transition: { duration: 0.2 },
      },
      button: {
        scale: 1.05,
        transition: { duration: 0.2 },
      },
      buttonOutline: {
        backgroundColor: '#ffffff',
        color: '#000000',
        borderColor: '#ffffff',
        transition: { duration: 0.2 },
      },
      card: {
        scale: 1.02,
        borderColor: 'rgba(255, 255, 255, 0.2)',
        transition: { duration: 0.3 },
      },
    },
    pageTransition: {
      initial: { opacity: 0, y: 40, filter: 'blur(10px)' },
      enter: {
        opacity: 1,
        y: 0,
        filter: 'blur(0px)',
        transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] as const },
      },
    },
    scrollEffects: {
      hero: {
        range: [0, 1000],
        imageY: [0, 640],
        imageScale: [1, 2],
        headerY: [0, 1000],
        headerScale: [1, 0.2],
        opacityRange: [0, 500],
        blur: ['blur(0px)', 'blur(10px)'],
        opacity: [1, 0],
      },
      preFooter: {
        y: (v: number) => -50 + v * 0.05,
        scale: (v: number) => 1 + v * 0.0001,
      },
    },
  },
  colors: {
    primary: '#ffffff',
    muted: 'rgba(255, 255, 255, 0.7)',
    border: 'rgba(255, 255, 255, 0.1)',
  },
};
