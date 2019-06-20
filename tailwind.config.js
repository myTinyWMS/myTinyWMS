module.exports = {
    variants: {
        borderColor: ['responsive', 'hover', 'focus', 'group-hover'],
        visibility: ['responsive', 'group-hover'],
    },
    theme: {
        screens: {
            '2xl': '1690px',
            '3xl': '1920px',
            '4xl': '2560px'
        }
    },
    plugins: [
        require('tailwindcss-tables')(),
    ]
};