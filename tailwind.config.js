/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./public/**/*.{html,php,js}",
    "./ressources/**/*.{html,php,js}",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
module.exports = {
  theme: {
    screens: {
      xs: "601px",
      sm: "640px",
      md: "768px",
      lg: "1024px",
    }
  }
};

