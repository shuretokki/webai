# Design System Rules

## 0. Figma Reference
- **URL**: `https://www.figma.com/design/wIrZ9PDq6m8VNZStVn1CoD/AI-WEB?node-id=0-1&t=zqcr4zex9ESEcmHk-1`
- **Preferences**:
  - **Roundness**: Square roundness (approx 4px / 0.25rem), with occasional full rounded elements.
  - **Palette**: Gruvbox Dark (Dark Mode) and Gruvbox Light (Light Mode).
  - **Priorities**: Chat Index, Settings (modal-like), Responsiveness.

## 1. Token Definitions
- **Location**: `resources/css/app.css`
- **Format**: CSS Variables defined in `@theme inline` (Tailwind v4)
- **Colors**:
  - **Theme**: Gruvbox (Retro/Warm).
  - **Semantic Mapping**:
    - `background`: Gruvbox BG (Light: #fbf1c7, Dark: #282828)
    - `foreground`: Gruvbox FG (Light: #3c3836, Dark: #ebdbb2)
    - `primary`: Gruvbox Orange (Light: #d65d0e, Dark: #fe8019)
    - `accent`: Gruvbox Yellow (#fabd2f)
    - `destructive`: Gruvbox Red (Light: #cc241d, Dark: #fb4934)
  - Values: Mapped to CSS variables (e.g., `--color-primary: var(--primary)`).
- **Typography**:
  - Sans: `Instrument Sans`
  - Mono: `JetBrains Mono`
  - Display: `Space Grotesk`, `Philosopher`
- **Radius**: `--radius: 0.25rem` (Square-ish).

## 2. Component Library
- **Location**: `resources/js/components`
- **Structure**:
  - Base UI components in `resources/js/components/ui/` (Shadcn-like structure).
  - Feature components in `resources/js/components/`.
- **Architecture**: Vue 3 Composition API, Functional components.
- **Styling**: Tailwind CSS classes, `class-variance-authority` (CVA) for variants, `clsx`/`tailwind-merge` for class merging.

## 3. Frameworks & Libraries
- **Frontend**: Vue 3, Inertia.js
- **Styling**: Tailwind CSS v4
- **Animation**: `motion-v` (Framer Motion for Vue)
- **Testing**: `vitest` (Unit/Component), `pest` (Feature/Browser)
- **Icons**: Solar Icons via `unplugin-icons` (`@iconify-json/solar`)
- **Utils**: `@vueuse/core`, `laravel-precognition-vue`

## 4. Asset Management
- **Images**: `public/` or `resources/images/`
- **Vite**: Handles asset optimization and versioning.

## 5. Icon System
- **Library**: Solar Icons
- **Usage**: Auto-imported components (e.g., `<i-solar-user-bold />`).
- **Resolver**: `unplugin-icons/resolver` configured in `vite.config.ts`.

## 6. Styling Approach
- **Methodology**: Utility-first (Tailwind CSS).
- **Dark Mode**: Class-based (`.dark`), handled by custom variant in `app.css`.
- **Animation**: `tw-animate-css` used in `app.css`.

## 7. Project Structure
- **Pages**: `resources/js/pages/`
- **Layouts**: `resources/js/layouts/`
- **Types**: `resources/js/types/`
