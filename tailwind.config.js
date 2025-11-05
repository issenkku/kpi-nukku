/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,ts,jsx,tsx}",
        // './resources/**/*.vue', // if you use Vue
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./app/View/Components/**/*.php", // Blade components
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: "var(--font-sans)",
            },
        },
    },
    plugins: [],
};
