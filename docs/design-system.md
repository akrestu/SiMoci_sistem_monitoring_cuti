# SiCuti Design System

This document provides comprehensive documentation for the design system used in the SiCuti application. The design system standardizes visual elements and UI components to ensure consistent user experience across the application.

## Table of Contents

1. [Design Tokens](#design-tokens)
   - [Colors](#colors)
   - [Typography](#typography)
   - [Spacing](#spacing)
   - [Borders](#borders)
   - [Shadows](#shadows)
   - [Transitions](#transitions)
   - [Z-Index](#z-index)
2. [Components](#components)
   - [Buttons](#buttons)
   - [Cards](#cards)
   - [Forms](#forms)
   - [Alerts](#alerts)
   - [Tables](#tables)
   - [Badges](#badges)
   - [Navbar](#navbar)
   - [Sidebar](#sidebar)
3. [Utility Classes](#utility-classes)
4. [How to Use](#how-to-use)
5. [Extending the Design System](#extending-the-design-system)

## Design Tokens

### Colors

The color system consists of primary brand colors and a neutral palette:

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-primary` | `#0d6efd` | Main brand color |
| `--sicuti-primary-dark` | `#0b5ed7` | Hover states, focused elements |
| `--sicuti-secondary` | `#6c757d` | Secondary actions |
| `--sicuti-success` | `#198754` | Success states, confirmations |
| `--sicuti-info` | `#0dcaf0` | Informational elements |
| `--sicuti-warning` | `#ffc107` | Warnings, cautions |
| `--sicuti-danger` | `#dc3545` | Error states, destructive actions |
| `--sicuti-light` | `#f8f9fa` | Light backgrounds |
| `--sicuti-dark` | `#212529` | Text, dark backgrounds |

#### Neutral Colors

A range of gray shades is available for various UI elements:

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-gray-100` | `#f8f9fa` | Backgrounds, hover states |
| `--sicuti-gray-200` | `#e9ecef` | Borders, dividers |
| `--sicuti-gray-300` | `#dee2e6` | Borders, separator lines |
| `--sicuti-gray-400` | `#ced4da` | Disabled elements |
| `--sicuti-gray-500` | `#adb5bd` | Placeholder text |
| `--sicuti-gray-600` | `#6c757d` | Secondary text |
| `--sicuti-gray-700` | `#495057` | Tertiary text |
| `--sicuti-gray-800` | `#343a40` | Secondary headings |
| `--sicuti-gray-900` | `#212529` | Primary text |

### Typography

#### Font Family

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-font-family-base` | `'Inter', sans-serif` | Base text |
| `--sicuti-font-family-headings` | `'Inter', sans-serif` | Headings |

#### Font Sizes

| Variable | Value | Pixel Equivalent | Usage |
|----------|-------|------------------|-------|
| `--sicuti-font-size-xs` | `0.75rem` | 12px | Small labels, badges |
| `--sicuti-font-size-sm` | `0.875rem` | 14px | Small text, secondary information |
| `--sicuti-font-size-base` | `1rem` | 16px | Body text |
| `--sicuti-font-size-lg` | `1.125rem` | 18px | Large text, emphasized elements |
| `--sicuti-font-size-xl` | `1.25rem` | 20px | Subheadings |
| `--sicuti-font-size-2xl` | `1.5rem` | 24px | H3 headings |
| `--sicuti-font-size-3xl` | `1.875rem` | 30px | H2 headings |
| `--sicuti-font-size-4xl` | `2.25rem` | 36px | H1 headings |

#### Font Weights

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-font-weight-light` | `300` | Light text |
| `--sicuti-font-weight-normal` | `400` | Regular body text |
| `--sicuti-font-weight-medium` | `500` | Medium emphasis, buttons |
| `--sicuti-font-weight-semibold` | `600` | Headings, important labels |
| `--sicuti-font-weight-bold` | `700` | Strong emphasis, brand elements |

#### Line Heights

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-line-height-tight` | `1.25` | Headings |
| `--sicuti-line-height-base` | `1.5` | Body text |
| `--sicuti-line-height-loose` | `1.75` | Large bodies of text |

### Spacing

A consistent spacing scale for margins, padding, and layout:

| Variable | Value | Pixel Equivalent | Usage |
|----------|-------|------------------|-------|
| `--sicuti-spacing-0` | `0` | 0px | No spacing |
| `--sicuti-spacing-1` | `0.25rem` | 4px | Tiny spacing, tight elements |
| `--sicuti-spacing-2` | `0.5rem` | 8px | Small spacing, compact elements |
| `--sicuti-spacing-3` | `0.75rem` | 12px | Default spacing for small elements |
| `--sicuti-spacing-4` | `1rem` | 16px | Default spacing for most elements |
| `--sicuti-spacing-5` | `1.25rem` | 20px | Medium spacing |
| `--sicuti-spacing-6` | `1.5rem` | 24px | Large spacing |
| `--sicuti-spacing-8` | `2rem` | 32px | Larger spacing |
| `--sicuti-spacing-10` | `2.5rem` | 40px | Section spacing |
| `--sicuti-spacing-12` | `3rem` | 48px | Large section spacing |
| `--sicuti-spacing-16` | `4rem` | 64px | Very large spacing |
| `--sicuti-spacing-20` | `5rem` | 80px | Extreme spacing |

### Borders

Border radius values for consistent corner treatment:

| Variable | Value | Pixel Equivalent | Usage |
|----------|-------|------------------|-------|
| `--sicuti-border-radius-sm` | `0.25rem` | 4px | Small elements |
| `--sicuti-border-radius` | `0.375rem` | 6px | Default elements |
| `--sicuti-border-radius-lg` | `0.5rem` | 8px | Large elements |
| `--sicuti-border-radius-xl` | `0.75rem` | 12px | Extra large elements |
| `--sicuti-border-radius-2xl` | `1rem` | 16px | Featured elements |
| `--sicuti-border-radius-pill` | `50rem` | - | Pill-shaped elements |
| `--sicuti-border-radius-circle` | `50%` | - | Circular elements |

### Shadows

Box shadow values for consistent elevation:

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-shadow-sm` | `0 1px 2px 0 rgba(0, 0, 0, 0.05)` | Subtle elements |
| `--sicuti-shadow` | `0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)` | Default elements |
| `--sicuti-shadow-md` | `0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)` | Elevated elements |
| `--sicuti-shadow-lg` | `0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)` | Prominent elements |
| `--sicuti-shadow-xl` | `0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)` | Modal dialogs, floating elements |

### Transitions

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-transition-fast` | `150ms ease-out` | Quick transitions |
| `--sicuti-transition-normal` | `200ms ease-out` | Standard transitions |
| `--sicuti-transition-slow` | `300ms ease-out` | Elaborate transitions |

### Z-Index

| Variable | Value | Usage |
|----------|-------|-------|
| `--sicuti-z-10` - `--sicuti-z-50` | `10` - `50` | Stacking context within components |
| `--sicuti-z-dropdown` | `1000` | Dropdown menus |
| `--sicuti-z-sticky` | `1020` | Sticky elements |
| `--sicuti-z-fixed` | `1030` | Fixed elements |
| `--sicuti-z-modal` | `1040` | Modal dialogs |
| `--sicuti-z-popover` | `1050` | Popovers |
| `--sicuti-z-tooltip` | `1060` | Tooltips |

## Components

### Buttons

Standardized button styles with variations:

```html
<!-- Primary Button -->
<button class="btn btn-primary">Primary Action</button>

<!-- Secondary Button -->
<button class="btn btn-secondary">Secondary Action</button>

<!-- Success Button -->
<button class="btn btn-success">Success Action</button>

<!-- Danger Button -->
<button class="btn btn-danger">Danger Action</button>

<!-- Small Button -->
<button class="btn btn-primary btn-sm">Small Button</button>

<!-- Large Button -->
<button class="btn btn-primary btn-lg">Large Button</button>
```

### Cards

Consistent card components for content containers:

```html
<div class="card">
  <div class="card-header">
    Card Header
  </div>
  <div class="card-body">
    <h5 class="card-title">Card Title</h5>
    <p class="card-text">Card content goes here.</p>
  </div>
  <div class="card-footer">
    Card Footer
  </div>
</div>
```

### Forms

Standardized form elements:

```html
<div class="mb-3">
  <label for="exampleInput" class="form-label">Input Label</label>
  <input type="text" class="form-control" id="exampleInput">
</div>

<div class="mb-3">
  <label for="exampleSelect" class="form-label">Select Label</label>
  <select class="form-select" id="exampleSelect">
    <option>Option 1</option>
    <option>Option 2</option>
  </select>
</div>
```

### Alerts

Standardized alert messages:

```html
<div class="alert alert-primary" role="alert">
  This is a primary alert
</div>

<div class="alert alert-success" role="alert">
  This is a success alert
</div>

<div class="alert alert-danger" role="alert">
  This is a danger alert
</div>

<div class="alert alert-warning" role="alert">
  This is a warning alert
</div>
```

### Tables

Standardized table styles:

```html
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Header 1</th>
      <th>Header 2</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Data 1</td>
      <td>Data 2</td>
    </tr>
  </tbody>
</table>
```

### Badges

```html
<span class="badge bg-primary">Primary</span>
<span class="badge bg-secondary">Secondary</span>
<span class="badge bg-success">Success</span>
<span class="badge bg-danger">Danger</span>
<span class="badge bg-warning text-dark">Warning</span>
<span class="badge bg-info text-dark">Info</span>
```

### Navbar

```html
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">SiCuti</a>
    <!-- Additional navbar content -->
  </div>
</nav>
```

### Sidebar

```html
<div class="sidebar">
  <div class="sidebar-header">
    <h5>Menu</h5>
  </div>
  <div class="sidebar-content">
    <a href="#" class="sidebar-link active">
      <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="#" class="sidebar-link">
      <i class="fas fa-user"></i> Profile
    </a>
    <!-- More sidebar links -->
  </div>
</div>
```

## Utility Classes

The design system includes utility classes for common styling needs:

### Shadows

```html
<div class="sicuti-shadow-sm">Small shadow</div>
<div class="sicuti-shadow">Default shadow</div>
<div class="sicuti-shadow-md">Medium shadow</div>
<div class="sicuti-shadow-lg">Large shadow</div>
<div class="sicuti-shadow-xl">Extra large shadow</div>
```

### Border Radius

```html
<div class="sicuti-rounded-sm">Small rounded corners</div>
<div class="sicuti-rounded">Default rounded corners</div>
<div class="sicuti-rounded-lg">Large rounded corners</div>
<div class="sicuti-rounded-xl">Extra large rounded corners</div>
<div class="sicuti-rounded-2xl">2XL rounded corners</div>
<div class="sicuti-rounded-pill">Pill shape</div>
<div class="sicuti-rounded-circle">Circle shape</div>
```

### Status Indicators

```html
<span class="status-circle status-pending"></span> Pending
<span class="status-circle status-approved"></span> Approved
<span class="status-circle status-rejected"></span> Rejected
```

## How to Use

To use the design system in your blade templates, the CSS is already imported in the main app.css file. You can simply use the classes and elements as described in this documentation.

1. For new components, refer to this documentation to maintain consistency
2. Use the design tokens through CSS variables where custom styling is needed
3. Compose complex UI elements from the standardized components in this documentation

Example:

```html
<div class="card sicuti-shadow-md">
  <div class="card-header">
    <h5>Card with custom styling</h5>
  </div>
  <div class="card-body">
    <p style="color: var(--sicuti-primary);">
      This text uses a design token for color.
    </p>
    <button class="btn btn-primary">
      Action Button
    </button>
  </div>
</div>
```

## Extending the Design System

When extending the design system, follow these principles:

1. **Consistency**: New elements should feel like they belong to the same family
2. **Reusability**: Create components that can be used in multiple contexts
3. **Documentation**: Document new tokens or components in this file
4. **Accessibility**: Ensure new components meet accessibility standards
5. **Mobile-first**: Design for mobile devices first, then enhance for larger screens

To add new design tokens:

1. Add the token to `resources/css/design-system.css` in the appropriate section
2. Document the token in this file with its purpose and usage
3. Use the token in your components

To add new components:

1. Create the component styling in `resources/css/design-system.css`
2. Document the component in this file with examples
3. Use the component consistently across the application 