
/**
 * UI Configuration
 * Centralized styles and motion settings for consistent design across the web app.
 */
export const ui = {

  /**
   * <Navigation>
   * Navigation specific configuration for the header and menu.
   *
   * @default
   * height: 'h-20',
   * maxWidth: 'max-w-[1400px]',
   * scrollThreshold: [0, 1000]
   */
  navigation: {
    height: 'h-14',
    maxWidth: 'max-w-[1400px]',
    scrollThreshold: [0, 600],

    /**
     * <Background>
     * Navigation background color transitions.
     *
     * @default
     * initial: '#00000000',
     * scrolled: '#000000ff'
     */
    background: {
      initial: '#00000000',
      scrolled: '#000000ff',
    },

    /**
     * <Border>
     * Navigation border color transitions.
     *
     * @default
     * initial: '#ffffff00',
     * scrolled: '#ffffff0d'
     */
    border: {
      initial: '#ffffff00',
      scrolled: '#ffffff0d',
    },

    /**
     * <Backdrop>
     * Backdrop filter values for the navigation bar.
     *
     * @default
     * initial: 'blur(0px)',
     * scrolled: 'blur(12px)'
     */
    backdrop: {
      initial: 'blur(0px)',
      scrolled: 'blur(8px)',
    },

    /**
     * <Classes>
     * CSS class presets for navigation elements.
     *
     * @default
     * nav: 'fixed top-0 left-0 right-0 z-50 border-b',
     * wrapper: 'max-w-[1400px] mx-auto px-6 h-16 flex items-center justify-between md:justify-center gap-8'
     */
    classes: {
      nav: 'fixed top-0 left-0 right-0 z-50 border-b',
      wrapper: 'max-w-[1400px] mx-auto px-6 h-16 flex items-center justify-between md:justify-center gap-8',
    },
  },

  /**
   * <Layout>
   * Layout constants and utility classes for layout components.
   *
   * @default
   * hero: 'relative z-20 text-center px-6 max-w-[90rem] mx-auto mt-20 w-full',
   * sectionContainer: 'max-w-[1400px] mx-auto',
   * sectionPadding: 'px-6',
   * sectionVertical: 'py-32 md:py-48',
   * clampWidth: 'mx-auto w-[clamp(320px,70%,1400px)]',
   * footerClamp: 'mx-auto w-[clamp(320px,70%,1600px)]',
   * button: 'group relative inline-flex items-center justify-center gap-2 px-4 py-1 rounded-sm bg-white text-black text-sm font-medium overflow-hidden active-press',
   * buttonOutline: 'group relative inline-flex items-center justify-center gap-2 px-4 py-2 border border-white/10 bg-white/5 text-white/90 text-sm font-medium rounded-lg overflow-hidden transition-all active-press',
   * pricingButton: 'w-full h-8 flex items-center justify-center rounded-sm bg-white text-black font-medium text-sm transition-colors active-press',
   * pricingButtonDisabled: 'w-full h-8 flex items-center justify-center rounded-sm border border-white/10 text-white/20 text-sm cursor-not-allowed'
   */
  layout: {
    hero: 'relative z-20 text-center px-6 max-w-[90rem] mx-auto mt-20 w-full',
    sectionContainer: 'max-w-[1400px] mx-auto',
    sectionPadding: 'px-6',
    sectionVertical: 'py-32 md:py-48',
    clampWidth: 'mx-auto w-[clamp(320px,70%,1400px)]',
    footerClamp: 'mx-auto w-[clamp(320px,70%,1600px)]',
    button: 'group relative inline-flex items-center justify-center gap-2 px-4 py-1 rounded-sm bg-white text-black text-sm font-medium overflow-hidden active-press',
    buttonOutline: 'group relative inline-flex items-center justify-center gap-2 px-4 py-2 border border-white/10 bg-white/5 text-white/90 text-sm font-medium rounded-lg overflow-hidden transition-all active-press',
    pricingButton: 'w-full h-8 flex items-center justify-center rounded-sm bg-white text-black font-medium text-sm transition-colors active-press',
    pricingButtonDisabled: 'w-full h-8 flex items-center justify-center rounded-sm border border-white/10 text-white/20 text-sm cursor-not-allowed',

    /**
     * <Card>
     * Card specific presets for common layout patterns.
     *
     * @default
     * base: 'group relative bg-[#080808] border border-white/5 rounded-2xl overflow-hidden transition-colors flex flex-col',
     * pricing: 'p-8 md:p-10 rounded-xl border border-white/5 bg-[#050505] flex flex-col relative overflow-hidden group/card transition-colors',
     * developer: 'relative bg-[#080808]/50 border border-white/5 rounded-2xl overflow-hidden group transition-colors flex flex-col'
     */
    card: {
      base: 'group relative bg-[#080808] border border-white/5 rounded-2xl overflow-hidden transition-colors flex flex-col',
      pricing: 'p-8 md:p-10 rounded-xl border border-white/5 bg-[#050505] flex flex-col relative overflow-hidden group/card transition-colors',
      developer: 'relative bg-[#080808]/50 border border-white/5 rounded-2xl overflow-hidden group transition-colors flex flex-col',
    },

    /**
     * <Section>
     * Section semantic groups.
     *
     * @default
     * dark: 'bg-[#050505]',
     * deeper: 'bg-black',
     * header: 'flex items-center gap-4 mb-12'
     */
    section: {
      dark: 'bg-[#050505]',
      deeper: 'bg-black',
      header: 'flex items-center gap-4 mb-12',
    },
  },

  /**
   * <Typography>
   * Typography presets for consistent text styling.
   *
   * @default
   * hero: 'text-hero font-medium tracking-tighter leading-[0.85] text-white',
   * manifesto: 'text-[clamp(2.5rem,8vw,7rem)] font-light tracking-tighter leading-[1] text-white text-center',
   * display: 'text-display font-light tracking-tight leading-[1.1] text-white/90',
   * title: 'text-title font-light tracking-tight text-white',
   * body: 'text-base font-light text-white/60 leading-relaxed',
   * accentHero: 'font-serif italic text-transparent bg-clip-text bg-gradient-to-b from-white to-white/40',
   * label: 'text-xs font-mono text-white/40 uppercase tracking-widest flex items-center gap-2',
   * cardTitle: 'text-xl font-medium text-white tracking-tight',
   * cardBody: 'text-[13px] text-white/40 leading-relaxed',
   * pricingPlan: 'text-[11px] font-bold uppercase tracking-[0.2em] text-white/30 block mb-6',
   * pricingPrice: 'text-5xl md:text-6xl font-light tracking-tighter'
   */
  typography: {
    hero: 'text-hero font-medium tracking-tighter leading-[0.85] text-white',
    manifesto: 'text-[clamp(2.5rem,8vw,7rem)] font-light tracking-tighter leading-[1] text-white text-center',
    display: 'text-display font-light tracking-tight leading-[1.1] text-white/90',
    title: 'text-title font-light tracking-tight text-white',
    body: 'text-base font-light text-white/60 leading-relaxed',
    accentHero: 'font-serif italic text-transparent bg-clip-text bg-gradient-to-b from-white to-white/40',
    label: 'text-xs font-mono text-white/40 uppercase tracking-widest flex items-center gap-2',
    cardTitle: 'text-xl font-medium text-white tracking-tight',
    cardBody: 'text-[13px] text-white/40 leading-relaxed',
    pricingPlan: 'text-[11px] font-bold uppercase tracking-[0.2em] text-white/30 block mb-6',
    pricingPrice: 'text-5xl md:text-6xl font-light tracking-tighter',
  },

  /**
   * <Features>
   * Configuration for the brand marquee and trusted partners section.
   */
  features: {
    section: 'py-24 border-y border-white/5 bg-black overflow-hidden select-none',
    track: 'flex gap-16 md:gap-32 items-center whitespace-nowrap min-w-full',
    brandIcon: 'w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center grayscale group-hover:grayscale-0 transition-all duration-500',
    brandName: 'text-white/10 group-hover:text-white/40 font-medium tracking-tight text-xl md:text-2xl transition-colors',
  },

  /**
   * <Developer>
   * Technical feature cards and living interface animations.
   */
  developer: {
    grid: 'grid grid-cols-1 md:grid-cols-12 gap-6',
    card: 'relative bg-[#080808]/50 border border-white/5 rounded-2xl overflow-hidden group transition-colors flex flex-col',
    imageWrapper: 'flex-1 relative overflow-hidden flex items-center justify-center p-12 mt-auto',
    glow: 'absolute -top-20 -left-20 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl pointer-events-none',
  },

  /**
   * <Animations>
   * Animation constants and motion presets.
   */
  animations: {
    /** Staggered entrance animations. */
    stagger: (idx: number) => ({
      delay: idx * 0.1,
      duration: 0.8,
      ease: [0.16, 1, 0.3, 1] as const,
    }),

    /** Function to generate word reveal variants. */
    wordReveal: (i: number) => ({
      initial: { y: '100%', opacity: 0, rotateZ: 5 },
      enter: {
        y: 0,
        opacity: 1,
        rotateZ: 0,
        transition: {
          duration: 0.8,
          ease: [0.33, 1, 0.68, 1] as const,
          delay: i * 0.03,
        },
      },
    }),

    /**
     * <Hover>
     * Global hover state configurations.
     */
    hover: {
      /**
       * <NavLink>
       * Hover state for navigation links.
       *
       * @default
       * color: '#ffffff',
       * transition: { duration: 0.2 }
       */
      navLink: {
        color: '#ffffff',
        transition: { duration: 0.2 },
      },

      /**
       * <Button>
       * Hover state for primary buttons.
       *
       * @default
       * scale: 1.05,
       * transition: { duration: 0.2 }
       */
      button: {
        scale: 1.05,
        transition: { duration: 0.2 },
      },

      /**
       * <ButtonOutline>
       * Hover state for outline buttons.
       *
       * @default
       * backgroundColor: '#ffffff',
       * color: '#000000',
       * borderColor: '#ffffff',
       * transition: { duration: 0.2 }
       */
      buttonOutline: {
        backgroundColor: '#ffffff',
        color: '#000000',
        borderColor: '#ffffff',
        transition: { duration: 0.2 },
      },

      /**
       * <Card>
       * Hover state for cards.
       *
       * @default
       * scale: 1.02,
       * borderColor: '#ffffff33',
       * transition: { duration: 0.3 }
       */
      card: {
        scale: 1.02,
        borderColor: '#ffffff33',
        transition: { duration: 0.3 },
      },

      /**
       * <Active>
       * Tap / Active state for interactive elements.
       *
       * @default
       * scale: 0.95,
       * transition: { duration: 0.1 }
       */
      active: {
        scale: 0.95,
        transition: { duration: 0.1 },
      },
    },

    /**
     * <Easing>
     * Easing curve definitions.
     *
     * @default
     * default: [0.16, 1, 0.3, 1],
     * snappy: [0.22, 1, 0.36, 1],
     * smooth: [0.33, 1, 0.68, 1],
     * elastic: [0.34, 1.56, 0.64, 1]
     */
    easing: {
      default: [0.16, 1, 0.3, 1] as const,
      snappy: [0.22, 1, 0.36, 1] as const,
      smooth: [0.33, 1, 0.68, 1] as const,
      elastic: [0.34, 1.56, 0.64, 1] as const,
    },

    /**
     * <PageTransition>
     * Standard page transition variants.
     *
     * @default
     * initial: { opacity: 0, y: 40, filter: 'blur(10px)' },
     * enter: '...'
     */
    pageTransition: {
      initial: { opacity: 0, y: 40, filter: 'blur(10px)' },
      /**
       * <Enter>
       * Page entrance animation.
       *
       * @default
       * opacity: 1,
       * y: 0,
       * filter: 'blur(0px)',
       * transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] }
       */
      enter: {
        opacity: 1,
        y: 0,
        filter: 'blur(0px)',
        transition: { duration: 1.2, ease: [0.16, 1, 0.3, 1] as const },
      },
    },

    /**
     * <ScrollEffects>
     * Scroll-based parallax and reveal effects.
     */
    scrollEffects: {

      /**
       * <Hero>
       * Scroll effects for the fixed hero section.
       *
       * @default
       * range: [0, 1000],
       * imageY: [0, 400],
       * imageScale: [1, 1.1],
       * headerY: [0, 200],
       * headerScale: [1, 1.1],
       * lineSpacing: ['0.9em', '4em'],
       * opacityRange: [0, 600],
       * blur: ['blur(0px)', 'blur(16px)'],
       * opacity: [1, 0]
       */
      hero: {
        range: [0, 1000],
        imageScale: [1, 1.15],
        headerY: [0, -400],
        headerScale: [1, 0.5],
        lineSpacing: ['0.9em', '4em'],
        opacityRange: [0, 500],
        blur: ['blur(0px)', 'blur(20px)'],
        opacity: [1, 0],
      },

      /**
       * <PreFooter>
       * Scroll effects for the local pre-footer section.
       *
       * @default
       * y: (v: number) => -50 + v * 0.05,
       * scale: (v: number) => 1 + v * 0.0001
       */
      preFooter: {
        y: (v: number) => -50 + v * 0.05,
        scale: (v: number) => 1 + v * 0.0001,
      },
    },
  },

  /**
   * <Pricing>
   * Pricing specific design tokens and styles.
   */
  pricing: {
    card: 'flex flex-col relative px-8 py-10 rounded-2xl border border-white/5 bg-[#050505] overflow-hidden',
    premiumCard: 'flex flex-col relative px-8 py-10 rounded-2xl border border-white/10 bg-[#080808] overflow-hidden',
    popularBadge: 'absolute top-4 right-4 px-3 py-1 rounded-full bg-white text-black text-[10px] font-bold uppercase tracking-wider',
    featureItem: 'flex items-center gap-3 text-sm text-white/40',
    toggleWrapper: 'bg-white/5 border border-white/10 rounded-full p-1 flex items-center relative w-full max-w-[280px] self-center md:self-auto',
    toggleActive: 'absolute inset-y-1 w-[calc(50%-4px)] bg-white/10 rounded-full',
  },

  /**
   * <Patterns>
   * Graphic patterns and utility styles.
   *
   * @default
   * blueprint: 'background-image: ...'
   */
  patterns: {
    blueprint:
      'background-image: linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 4rem 4rem;',
  },

  /**
   * <Colors>
   * Fundamental color palette tokens.
   *
   * @default
   * primary: '#ffffff',
   * muted: '#ffffffb2',
   * border: '#ffffff1a'
   */
  colors: {
    primary: '#ffffff',
    muted: '#ffffffb2',
    border: '#ffffff1a',
  },
};
