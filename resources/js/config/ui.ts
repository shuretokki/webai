
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
    clampWidth: 'mx-auto w-[clamp(320px,70%,1400px)]',
    footerClamp: 'mx-auto w-[clamp(320px,70%,1600px)]',
    button: 'group relative inline-flex items-center justify-center gap-3 px-6 py-3 md:px-10 md:py-4 bg-white text-black text-sm md:text-base font-medium rounded-full overflow-hidden',
    buttonOutline: 'group relative inline-flex items-center justify-center gap-3 px-6 py-2.5 border border-white/20 text-white text-xs md:text-sm font-medium rounded-sm overflow-hidden hover:bg-white hover:text-black transition-colors',
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
    easing: {
      default: [0.16, 1, 0.3, 1] as const,
      snappy: [0.22, 1, 0.36, 1] as const,
      smooth: [0.33, 1, 0.68, 1] as const,
      elastic: [0.34, 1.56, 0.64, 1] as const,
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
        imageY: [0, 400],
        imageScale: [1, 1.1],
        headerY: [0, 200],
        headerScale: [1, 1.3],
        lineSpacing: ['0.9em', '4em'],
        opacityRange: [0, 600],
        blur: ['blur(0px)', 'blur(16px)'],
        opacity: [1, 0],
      },
      topStack: {
        range: [0, 800],
        scale: [1, 0.9],
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
