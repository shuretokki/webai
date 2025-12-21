# Developer Changelog - WebAI Frontend Journey

## [2025-12-21] - Core Performance & Architectural Refactor

### Added
- **Global Device Detection (`useDevice.ts`)**: Integrated VueUse's `useBreakpoints` (matchMedia-based) for high-performance responsive logic.
- **Pointer-Aware Interaction**: Introduced `canHover` state to distinguish between mouse and touch devices.

### Changed
- **Optimized "Manifesto" Section**: Conditionally disabling complex word-reveal animations on mobile to reduce DOM node count and CPU overhead.
- **Navigation Performance**: Disabled `backdrop-filter` on mobile to prevent scrolling stutter.
- **Unified Transitions**: Centralized animation easing and durations in `ui.ts`.
- **Widescreen Layout Optimization**: Fixed overlap between hero subtitle and scroll explorer by refactoring the hero section into a flex-column layout with explicit padding, ensuring stable spatial hierarchy on any screen size.
- **Enhanced Scroll Indicator**: Replaced simple chevron with a custom-engineered animated mouse pulse inside a pill container for a more premium interaction cue.

### Optimized
- **Image Performance (Core Web Vitals)**: Implemented explicit aspect ratios (width/height), lazy loading, and asynchronous decoding across all landing page assets to improve LCP and prevent CLS.

### Removed
- **Pricing Spotlight Effect**: Eliminated expensive mousemove listeners and associated MotionValues to improve INP performance.
- **Redundant Resize Listeners**: Migrated all local window listeners to the centralized `useDevice` composable.

---

## [Next Steps]
- [x] Refine FAQ interaction to use tap gestures instead of hover-heavy states.
- [x] Standardize `:active` (press) states across all touch-enabled components.
- [x] Audit remaining `hover:` classes in `ui.ts` layout tokens.
- [ ] Implement Point #5 (Image Format & Sizing Optimization)
