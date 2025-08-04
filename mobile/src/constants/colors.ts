export const colors = {
  // Primary palette - Airline theme
  jetBlack: {
    50: '#f6f6f6',
    900: '#3d3d3d',
    950: '#0A0A0A', // Dominant background
  },

  // Horizon Blue - Sky gradient range
  horizonBlue: {
    50: '#D0E9FF',
    500: '#2FA4FF',
    600: '#005BFF', // Primary brand blue
    700: '#0052E6',
  },

  // Sunset Orange - Warm accent range
  sunsetOrange: {
    50: '#FFE1CC',
    600: '#FF6600', // Primary orange
  },

  // Semantic colors
  success: '#22c55e',
  warning: '#f59e0b',
  error: '#ef4444',
  
  // Neutral grays
  white: '#ffffff',
  gray: {
    50: '#f5f5f5',
    100: '#e5e5e5',
    300: '#a3a3a3',
    500: '#525252',
    600: '#404040',
    800: '#171717',
  },
} as const;