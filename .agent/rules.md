#
    Production-Ready Frontend Rules

This project follows strict "Big Tech" production standards for frontend development.

## 1. Core Web Vitals Enforcement

- **LCP (Largest Contentful Paint)**:
  - Always provide a high-priority placeholder (image/gradient) for heavy assets like 3D scenes or videos.
  - Use `<link rel="preload">` in the `<Head>` for critical assets and 3D runtimes.
- **INP (Interaction to Next Paint)**:
  - Use `IntersectionObserver` to suspend heavy rendering (like WebGL/Spline) when elements are out of view.
  - Prioritize `motion-v` for smooth 60fps animations over standard Vue reactivity for layout changes.
- **CLS (Cumulative Layout Shift)**:
  - Always use explicit aspect ratios or reserved height classes (e.g., `.h-hero-reserved`) for containers that load dynamic content.

## 2. Mobile & Responsive Stability

- **Dynamic Viewports**: Use `dvh` (Dynamic Viewport Height) instead of `vh` to prevent layout jumping caused by mobile browser UI (chrome/safari address bars).
- **Safe Areas**: Implement `env(safe-area-inset-*)` padding for fixed/sticky components to ensure visibility on notched devices.

## 3. Global Architecture

- **Centralized UI Config**: All design tokens, layout constants, and animation settings must be stored in `resources/js/config/ui.ts`.
- **Fluid Scales**: Typography and spacing must use `clamp()` or variables synchronized to the `ui` config to ensure seamless scaling between mobile and desktop.
- **Production Workflow**: Use semantic code, clean component encapsulation, and lifecycle hooks for resource cleanup.

## 4. Engineering Standards

- **Strict Logic**: Use TypeScript `as const` assertions for fixed configuration objects.
- **Clean Commits**: Commit individual files with short, descriptive, and non-semantic (no feat/fix prefix) messages.
- **No Placeholders**: Never use placeholder text or generic colors in production. Use real-world assets or generated demonstrations.
