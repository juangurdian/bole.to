/**
 * Bole.to Design System Tokens
 * Version: 1.0 (Phase 1)
 * Last Updated: 2025-08-03
 * 
 * Premium airline theme for event "flight" platform
 * Target: 18-35 tech-savvy users
 * Accessibility: WCAG 2.1 AA minimum
 */

// ===============================
// COLOR PALETTE
// ===============================

export const colors = {
  // Primary Palette - Airline Theme
  jetBlack: {
    50: '#f6f6f6',
    100: '#e7e7e7', 
    200: '#d1d1d1',
    300: '#b0b0b0',
    400: '#888888',
    500: '#6d6d6d',
    600: '#5d5d5d',
    700: '#4f4f4f',
    800: '#454545',
    900: '#3d3d3d',
    950: '#0A0A0A', // Dominant background
  },

  // Horizon Blue - Sky gradient range
  horizonBlue: {
    50: '#D0E9FF',  // Lightest sky
    100: '#B8DCFF',
    200: '#9CCFFF',
    300: '#7AC2FF',
    400: '#52B3FF',
    500: '#2FA4FF',
    600: '#005BFF', // Primary brand blue
    700: '#0052E6',
    800: '#0047CC',
    900: '#003CB3',
    950: '#002E85', // Darkest depth
  },

  // Sunset Orange - Warm accent range  
  sunsetOrange: {
    50: '#FFE1CC',  // Soft sunrise
    100: '#FFD4B8',
    200: '#FFC59C',
    300: '#FFB37A',
    400: '#FF9F52',
    500: '#FF8B2F',
    600: '#FF6600', // Primary orange
    700: '#E65C00',
    800: '#CC5200',
    900: '#B34700',
    950: '#853600', // Deep sunset
  },

  // Semantic Colors
  semantic: {
    success: {
      50: '#f0fdf4',
      500: '#22c55e',
      600: '#16a34a',
      700: '#15803d',
    },
    warning: {
      50: '#fffbeb',
      500: '#f59e0b',
      600: '#d97706',
      700: '#b45309',
    },
    error: {
      50: '#fef2f2',
      500: '#ef4444',
      600: '#dc2626',
      700: '#b91c1c',
    },
    info: {
      50: '#eff6ff',
      500: '#3b82f6',
      600: '#2563eb',
      700: '#1d4ed8',
    },
  },

  // Neutral Grays
  neutral: {
    0: '#ffffff',
    25: '#fafafa',
    50: '#f5f5f5',
    100: '#e5e5e5',
    200: '#d4d4d4',
    300: '#a3a3a3',
    400: '#737373',
    500: '#525252',
    600: '#404040',
    700: '#262626',
    800: '#171717',
    900: '#0a0a0a',
  },
} as const;

// ===============================
// TYPOGRAPHY
// ===============================

export const typography = {
  fontFamily: {
    // Primary font stack optimized for readability
    sans: [
      'Inter',
      '-apple-system',
      'BlinkMacSystemFont',
      'Segoe UI',
      'Roboto',
      'Helvetica Neue',
      'Arial',
      'sans-serif',
    ],
    // Monospace for codes, tickets
    mono: [
      'JetBrains Mono',
      'Monaco',
      'Consolas',
      'Liberation Mono',
      'Courier New',
      'monospace',
    ],
  },

  fontSize: {
    // Display sizes for heroes and major headers
    'display-xl': ['3.75rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }], // 60px
    'display-lg': ['3rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],     // 48px
    'display-md': ['2.25rem', { lineHeight: '1.2', letterSpacing: '-0.02em' }],  // 36px
    
    // Heading hierarchy
    'heading-xl': ['1.875rem', { lineHeight: '1.3', letterSpacing: '-0.01em' }], // 30px
    'heading-lg': ['1.5rem', { lineHeight: '1.3', letterSpacing: '-0.01em' }],   // 24px
    'heading-md': ['1.25rem', { lineHeight: '1.4', letterSpacing: '-0.01em' }],  // 20px
    'heading-sm': ['1.125rem', { lineHeight: '1.4', letterSpacing: '0' }],       // 18px
    
    // Body text sizes
    'body-xl': ['1.125rem', { lineHeight: '1.6', letterSpacing: '0' }],          // 18px
    'body-lg': ['1rem', { lineHeight: '1.6', letterSpacing: '0' }],              // 16px (default)
    'body-md': ['0.875rem', { lineHeight: '1.5', letterSpacing: '0' }],          // 14px
    'body-sm': ['0.75rem', { lineHeight: '1.5', letterSpacing: '0' }],           // 12px
    
    // Specialized text
    'caption': ['0.75rem', { lineHeight: '1.4', letterSpacing: '0.01em' }],      // 12px
    'overline': ['0.625rem', { lineHeight: '1.4', letterSpacing: '0.08em', textTransform: 'uppercase' }], // 10px
  },

  fontWeight: {
    light: '300',
    normal: '400',
    medium: '500',
    semibold: '600',
    bold: '700',
    extrabold: '800',
  },
} as const;

// ===============================
// SPACING SYSTEM
// ===============================

export const spacing = {
  // Based on 4px grid system for consistency
  px: '1px',
  0: '0',
  0.5: '2px',   // 0.125rem
  1: '4px',     // 0.25rem
  1.5: '6px',   // 0.375rem
  2: '8px',     // 0.5rem
  2.5: '10px',  // 0.625rem
  3: '12px',    // 0.75rem
  3.5: '14px',  // 0.875rem
  4: '16px',    // 1rem
  5: '20px',    // 1.25rem
  6: '24px',    // 1.5rem
  7: '28px',    // 1.75rem
  8: '32px',    // 2rem
  9: '36px',    // 2.25rem
  10: '40px',   // 2.5rem
  11: '44px',   // 2.75rem (minimum tap target)
  12: '48px',   // 3rem
  14: '56px',   // 3.5rem
  16: '64px',   // 4rem
  20: '80px',   // 5rem
  24: '96px',   // 6rem
  28: '112px',  // 7rem
  32: '128px',  // 8rem
  36: '144px',  // 9rem
  40: '160px',  // 10rem
  44: '176px',  // 11rem
  48: '192px',  // 12rem
  52: '208px',  // 13rem
  56: '224px',  // 14rem
  60: '240px',  // 15rem
  64: '256px',  // 16rem
  72: '288px',  // 18rem
  80: '320px',  // 20rem
  96: '384px',  // 24rem
} as const;

// ===============================
// BORDER RADIUS
// ===============================

export const borderRadius = {
  none: '0',
  xs: '2px',
  sm: '4px',
  md: '6px',      // Default for cards
  lg: '8px',      // Buttons, inputs
  xl: '12px',     // Cards, modals
  '2xl': '16px',  // Large cards
  '3xl': '24px',  // Hero sections
  full: '9999px', // Pills, avatars
} as const;

// ===============================
// SHADOWS & ELEVATION
// ===============================

export const shadows = {
  // Subtle shadows for modern flat design
  xs: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
  sm: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)',
  md: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
  lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)',
  xl: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
  '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
  
  // Colored shadows for brand elements
  'brand-sm': '0 2px 4px 0 rgba(0, 91, 255, 0.2)',
  'brand-md': '0 4px 8px 0 rgba(0, 91, 255, 0.15)',
  'brand-lg': '0 8px 16px 0 rgba(0, 91, 255, 0.1)',
  
  // Inner shadows for inputs
  inner: 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.05)',
  none: '0 0 #0000',
} as const;

// ===============================
// BREAKPOINTS
// ===============================

export const breakpoints = {
  xs: '475px',   // Small phones
  sm: '640px',   // Large phones
  md: '768px',   // Tablets
  lg: '1024px',  // Small laptops
  xl: '1280px',  // Desktops
  '2xl': '1536px', // Large screens
} as const;

// ===============================
// Z-INDEX SYSTEM
// ===============================

export const zIndex = {
  auto: 'auto',
  base: 0,
  dropdown: 10,
  sticky: 20,
  fixed: 30,
  modal: 40,
  popover: 50,
  tooltip: 60,
  toast: 70,
  overlay: 80,
  max: 9999,
} as const;

// ===============================
// ANIMATION & TRANSITIONS
// ===============================

export const animations = {
  // Duration tokens
  duration: {
    instant: '0ms',
    fast: '150ms',
    normal: '300ms',
    slow: '500ms',
    slower: '750ms',
  },

  // Easing curves for premium feel
  easing: {
    linear: 'linear',
    ease: 'ease',
    'ease-in': 'cubic-bezier(0.4, 0, 1, 1)',
    'ease-out': 'cubic-bezier(0, 0, 0.2, 1)',
    'ease-in-out': 'cubic-bezier(0.4, 0, 0.2, 1)',
    // Custom airline-inspired easing
    'boarding': 'cubic-bezier(0.25, 0.46, 0.45, 0.94)', // Smooth takeoff
    'landing': 'cubic-bezier(0.55, 0.06, 0.68, 0.19)',  // Smooth landing
  },

  // Common transition properties
  transition: {
    colors: 'color 150ms ease, background-color 150ms ease, border-color 150ms ease',
    transform: 'transform 150ms ease',
    opacity: 'opacity 150ms ease',
    shadow: 'box-shadow 150ms ease',
    all: 'all 150ms ease',
  },
} as const;

// ===============================
// COMPONENT-SPECIFIC TOKENS
// ===============================

export const components = {
  // Button variants
  button: {
    height: {
      xs: spacing[6],  // 24px
      sm: spacing[8],  // 32px  
      md: spacing[10], // 40px
      lg: spacing[12], // 48px
      xl: spacing[14], // 56px
    },
    borderRadius: borderRadius.lg,
    fontSize: {
      xs: typography.fontSize['body-sm'],
      sm: typography.fontSize['body-md'],
      md: typography.fontSize['body-lg'],
      lg: typography.fontSize['heading-sm'],
      xl: typography.fontSize['heading-md'],
    },
  },

  // Input field styling
  input: {
    height: spacing[10], // 40px
    borderRadius: borderRadius.lg,
    borderWidth: '1px',
    fontSize: typography.fontSize['body-lg'],
    paddingX: spacing[3], // 12px
  },

  // Card component
  card: {
    borderRadius: borderRadius.xl,
    padding: spacing[6], // 24px
    shadow: shadows.md,
  },

  // Avatar sizes
  avatar: {
    xs: spacing[6],  // 24px
    sm: spacing[8],  // 32px
    md: spacing[10], // 40px
    lg: spacing[12], // 48px
    xl: spacing[16], // 64px
  },
} as const;

// ===============================
// ACCESSIBILITY TOKENS
// ===============================

export const accessibility = {
  // Minimum contrast ratios (WCAG 2.1 AA)
  contrast: {
    normal: 4.5,    // Normal text
    large: 3,       // Large text (18px+ or 14px+ bold)
    graphics: 3,    // Icons and graphics
  },

  // Minimum touch targets
  touchTarget: {
    minimum: spacing[11], // 44px minimum
    comfortable: spacing[12], // 48px comfortable
  },

  // Focus ring styling
  focusRing: {
    width: '2px',
    offset: '2px',
    color: colors.horizonBlue[500],
    style: 'solid',
  },

  // Motion preferences
  motion: {
    // Respects prefers-reduced-motion
    reduceMotion: {
      duration: animations.duration.instant,
      easing: animations.easing.linear,
    },
  },
} as const;

// ===============================
// EXPORT ALL TOKENS
// ===============================

export const tokens = {
  colors,
  typography,
  spacing,
  borderRadius,
  shadows,
  breakpoints,
  zIndex,
  animations,
  components,
  accessibility,
} as const;

export default tokens;

// ===============================
// TYPE DEFINITIONS
// ===============================

export type ColorToken = keyof typeof colors.jetBlack | keyof typeof colors.horizonBlue | keyof typeof colors.sunsetOrange;
export type SpacingToken = keyof typeof spacing;
export type TypographyToken = keyof typeof typography.fontSize;
export type ShadowToken = keyof typeof shadows;
export type RadiusToken = keyof typeof borderRadius;

// Utility type for accessing nested color values
export type ColorValue = 
  | typeof colors.jetBlack[keyof typeof colors.jetBlack]
  | typeof colors.horizonBlue[keyof typeof colors.horizonBlue] 
  | typeof colors.sunsetOrange[keyof typeof colors.sunsetOrange]
  | typeof colors.semantic.success[keyof typeof colors.semantic.success]
  | typeof colors.neutral[keyof typeof colors.neutral];