module.exports = {
    variants: {
        borderColor: ['responsive', 'hover', 'focus', 'group-hover'],
        visibility: ['responsive', 'group-hover'],
    },
    plugins: [
        require('tailwindcss-tables')(),
    ]
};