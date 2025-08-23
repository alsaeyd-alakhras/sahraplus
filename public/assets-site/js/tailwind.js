tailwind.config = {
    theme: {
        extend: {
            maxWidth: {
                'screen-95%': '95%',
            },
            backgroundColor: {
                'background-base': 'rgb(32, 29, 37)'
            },
            colors: {
                'fire-red': '#E50914',
                'dark-black': '#0F0F0F',
                'neon-green': '#00FF87',
                'gray-800': '#1F2937',
                'gray-900': '#111827',
                'gray-950': '#0F1113'
            },
            textShadow: {
                'shadow': '0 0 20px rgba(229, 9, 20, 0.5)',
            },
            fontFamily: {
                'arabic': ['Tajawal', 'sans-serif'],
                'english': ['Inter', 'sans-serif']
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out',
                'slide-up': 'slideUp 0.8s ease-out',
                'scale-in': 'scaleIn 0.5s ease-out',
                'glow': 'glow 2s ease-in-out infinite alternate',
                'slide-right': 'slideRight 0.8s ease-out',
                'stagger': 'stagger 0.6s ease-out'
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' }
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' }
                },
                slideRight: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' }
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' }
                },
                glow: {
                    '0%': { boxShadow: '0 0 20px rgba(229, 9, 20, 0.5)' },
                    '100%': { boxShadow: '0 0 30px rgba(229, 9, 20, 0.8)' }
                },
                stagger: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' }
                }
            }
        }
    }
}
