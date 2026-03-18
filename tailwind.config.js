/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        accent: {
          red: '#dc2626',
          'red-dark': '#b91c1c',
        },
        violet: {
          500: '#8b5cf6',
          600: '#7c3aed',
          700: '#6d28d9',
        },
        neon: {
          blue: '#00d4ff',
          purple: '#b794f6',
          pink: '#ff6b9d',
          orange: '#ff8c42',
          green: '#00ff88',
        },
        'ultra-bg': {
          dark: '#0a0e27',
          'dark-secondary': '#141b3d',
        },
      },
      fontFamily: {
        sans: ['Poppins', 'Plus Jakarta Sans', 'Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      boxShadow: {
        card: '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
        'card-hover': '0 4px 6px -1px rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.06)',
        elevated: '0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04)',
        nav: '0 -4px 24px -4px rgb(0 0 0 / 0.08)',
        'neon-blue': '0 0 20px rgba(0, 212, 255, 0.3)',
        'neon-purple': '0 0 20px rgba(183, 148, 246, 0.3)',
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.25rem',
      },
      animation: {
        'fade-in': 'ultra-fade-in 0.3s ease-out forwards',
        'slide-up': 'ultra-slide-up 0.35s cubic-bezier(0.32, 0.72, 0, 1) forwards',
      },
      keyframes: {
        'ultra-fade-in': {
          from: { opacity: '0' },
          to: { opacity: '1' },
        },
        'ultra-slide-up': {
          from: { opacity: '0', transform: 'translateY(100%)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
};
