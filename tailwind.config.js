/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/views/**/*.php",
    "./app/views/*.php",
    "./public/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#145A32',
        secondary: '#3CB371',
        msuGold: '#FFD700',
        darkGreen: '#0d3b20'
      }
    },
  },
  plugins: [],
}