// tailwind.config.js
module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    container: { center: true, padding: '1rem' },
    extend: {
      colors: {
        brand: {
          50:'#eef4ff',100:'#dbe7ff',200:'#b7d0ff',300:'#8db5ff',400:'#6297ff',
          500:'#3b79ef',600:'#2c60c5',700:'#20499a',800:'#173673',900:'#0f2752',950:'#0b1d3d',
        },
      },
      boxShadow: { soft: '0 10px 30px -12px rgba(2,6,23,.25)' },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
  ],
};
