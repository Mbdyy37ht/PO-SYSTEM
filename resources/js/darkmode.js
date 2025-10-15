// Dark Mode Toggle Script
document.addEventListener('alpine:init', () => {
    Alpine.data('darkMode', () => ({
        dark: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        
        init() {
            this.updateTheme();
        },
        
        toggle() {
            this.dark = !this.dark;
            localStorage.theme = this.dark ? 'dark' : 'light';
            this.updateTheme();
        },
        
        updateTheme() {
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }));
});
