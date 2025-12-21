# Production-Ready Frontend Rules

This project follows strict "Big Tech" production standards for frontend development.

## 1. Core Web Vitals Enforcement

- **LCP (Largest Contentful Paint)**:
  - Always provide a high-priority placeholder (image/gradient) for heavy assets like 3D scenes or videos.
  - Use <link rel="preload"> in the <Head> for critical assets and 3D runtimes.
- **INP (Interaction to Next Paint)**:
  - Use IntersectionObserver to suspend heavy rendering (like WebGL/Spline) when elements are out of view.
  - Prioritize motion-v for smooth 60fps animations over standard Vue reactivity for layout changes.
  - **Optimization**: Avoid animating expensive properties like `height: auto`. Use `max-height` with a fixed value or GPU-accelerated `transform: scaleY()` to prevent layout reflows and CPU spikes.
- **CLS (Cumulative Layout Shift)**:
  - Always use explicit aspect ratios or reserved height classes (e.g., .h-hero-reserved) for containers that load dynamic content.
  - Mandatory use of width, height, loading="lazy", and decoding="async" on all images (except LCP candidates).

## 2. Animation & Motion Architecture

- **Transform Clamping**: Always apply { clamp: true } to reactive motion transforms (opacity, scale, blur) to prevent extrapolated negative values that crash CSS parsing.
- **Hex-Only Animating**: Always use Hex (or 8-digit Hex) for color animations in motion-v. Avoid named colors or RGBA strings to prevent non-animatable warnings.
- **CSS Offloading**: Use pure CSS @keyframes for high-frequency, non-interactive visual loops (indicators, pulses, infinite bobs) instead of JS-driven motion to reduce main-thread overhead.
- **Pointer-Aware Logic**: Conditionally apply hover nodes and states based on canHover capability to eliminate "sticky hover" on touch devices.

## 3. Mobile & Responsive Stability

- **Dynamic Viewports**: Use dvh (Dynamic Viewport Height) instead of vh to prevent layout jumping caused by mobile browser UI (chrome/safari address bars).
- **Safe Areas**: Implement env(safe-area-inset-*) padding for fixed/sticky components to ensure visibility on notched devices.
- **Fixed Stacking**: Avoid complex reveal effects using `clip-path` and `fixed` positioning on mobile devices due to repaint costs; use simpler stacking contexts (z-index) instead.

## 4. Global Architecture

- **Centralized UI Config**: All design tokens, layout constants, and animation settings must be stored in resources/js/config/ui.ts.
- **Fluid Scales**: Typography and spacing must use clamp() or variables synchronized to the ui config to ensure seamless scaling between mobile and desktop.
- **Engineering Standards**: Use semantic code, clean component encapsulation, and lifecycle hooks for resource cleanup.
- **No Placeholders**: Never use placeholder text or generic colors in production. Use real-world assets or generated demonstrations.
