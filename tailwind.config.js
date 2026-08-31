/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')

module.exports = {
  mode: 'jit',
  corePlugins: {
   preflight: false,
  },
  content: [
    './public/index.php',
    './index.html',
    './frontend/**/*.{vue,js,ts,jsx,tsx}',
    './new-frontend/**/*.{vue,js,ts,jsx,tsx}',
  ],
  safelist: [
    { pattern: /grid-cols-\d+/ },
    { pattern: /col-span-\d+/ },
    // Properti CSS yang diperbolehkan: bg, text, border
    { pattern: /(bg|text|border)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900|950)/, }
  ],
  important: true,
  theme: {
    extend: {
      colors: {
        brand: '#167a78',  // Custom brand blue
      },
      fontFamily: {
       'montserrat': 'Montserrat, sans-serif',
       'avenir': 'avenir-lt-w01_35-light1475496, avenir-lt-w05_35-light, sans-serif',
       'gama': 'Gama Serif,"Georgia","Times New Roman",Times,serif',
       'noto': 'Noto Sans Arabic, sans-serif',
      },
      keyframes: {
        spin: {
          '0%': { transform: 'rotate(0deg)' },
          '50%': { transform: 'rotate(180deg)' },
          '100%': { transform: 'rotate(360deg)' },
        }
      }
    },

  },
  plugins: [
    plugin(function({ matchVariant }) {
      matchVariant(
        'nth',
        (value) => {
          return `&:nth-child(${value})`;
        },
      )
    }),
  ],
}

