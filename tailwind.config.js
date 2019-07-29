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
            '4xl': '2560px'
        }
    },
    plugins: [
        require('tailwindcss-tables')(),
    ]
};