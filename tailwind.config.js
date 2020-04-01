module.exports = {
    variants: {
        borderColor: ['responsive', 'hover', 'focus', 'group-hover'],
        visibility: ['responsive', 'group-hover'],
        display: ['responsive']
    },
    theme: {
        fontSize: {
            '3xs': '.5rem',
            '2xs': '.652rem',
            'xs': '.75rem',
            'sm': '.875rem',
            'base': '1rem',
            'lg': '1.125rem',
            'xl': '1.25rem',
            '2xl': '1.5rem',
            '3xl': '1.875rem',
            '4xl': '2.25rem',
            '5xl': '3rem',
            '6xl': '4rem',
        },
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1690px',
            '3xl': '1920px',
            '4xl': '2560px',
            'print': {'raw': 'print'}
        },
        height: {
            px: '1px',
            '0': '0',
            '1': '0.25rem',
            '2': '0.5rem',
            '3': '0.75rem',
            '4': '1rem',
            '5': '1.25rem',
            '6': '1.5rem',
            '8': '2rem',
            '9': '2.25rem',
            '10': '2.5rem',
            '12': '3rem',
            '16': '4rem',
            '20': '5rem',
            '24': '6rem',
            '32': '8rem',
            '40': '10rem',
            '48': '12rem',
            '56': '14rem',
            '64': '16rem',
            '128': '32rem',
            auto: 'auto',
            full: '100%',
            screen: '100vh'
        }
    },
    plugins: [
        require('tailwindcss-tables')(),
    ]
};