const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  purge: [
    './templates/**/*.html.twig'
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
    },

  },
  variants: {
    extend: {
      opacity: ['responsive', 'hover', 'focus', 'disabled'],
    },
  },
  plugins: [],
}
