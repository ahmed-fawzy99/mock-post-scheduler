/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ["class", ".dark"],
	theme: {
    	extend: {
    		colors: {
    			bg: '#f5f5f5',
    			bg_dark: '#262626',
    			foreground: 'hsl(var(--foreground))',
    		},
    	}
    },
	plugins: [require("tailwindcss-animate")],
}