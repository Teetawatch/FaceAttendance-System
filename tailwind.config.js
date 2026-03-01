import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Noto Sans Thai', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', 'Noto Sans Thai', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    50: '#F5F3FF',
                    100: '#EDE9FE',
                    200: '#DDD6FE',
                    300: '#C4B5FD',
                    400: '#A78BFA',
                    500: '#8B5CF6',
                    600: '#7C3AED',
                    700: '#6D28D9',
                    800: '#5B21B6',
                    900: '#4C1D95',
                    950: '#2E1065',
                },
                accent: {
                    50: '#FFF7ED',
                    100: '#FFEDD5',
                    200: '#FED7AA',
                    300: '#FDBA74',
                    400: '#FB923C',
                    500: '#F97316',
                    600: '#EA580C',
                    700: '#C2410C',
                    800: '#9A3412',
                    900: '#7C2D12',
                },
                surface: {
                    50: '#FAF5FF',
                    100: '#F3F0FF',
                    200: '#EDE9FE',
                },
                background: '#FAF5FF',
                card: '#FFFFFF',
                text: '#1E1B4B',
                muted: '#6B7280',
            },
            borderRadius: {
                'xl': '0.875rem',
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
            boxShadow: {
                'xs': '0 1px 2px 0 rgb(124 58 237 / 0.03)',
                'sm': '0 1px 3px 0 rgb(124 58 237 / 0.04), 0 1px 2px -1px rgb(124 58 237 / 0.04)',
                'md': '0 4px 6px -1px rgb(124 58 237 / 0.06), 0 2px 4px -2px rgb(124 58 237 / 0.04)',
                'lg': '0 10px 15px -3px rgb(124 58 237 / 0.06), 0 4px 6px -4px rgb(124 58 237 / 0.03)',
                'card': '0 1px 3px 0 rgb(124 58 237 / 0.04)',
                'card-hover': '0 4px 12px -2px rgb(124 58 237 / 0.08)',
            },
            transitionDuration: {
                '150': '150ms',
                '200': '200ms',
            },
            animation: {
                'fade-in': 'fadeIn 0.2s ease-out',
                'slide-up': 'slideUp 0.2s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
