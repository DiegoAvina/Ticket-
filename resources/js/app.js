import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Tema claro/oscuro. El valor inicial ya lo aplicó el script inline del
// layout (para evitar parpadeo); aquí solo se expone el interruptor.
Alpine.store('theme', {
    dark: document.documentElement.classList.contains('dark'),
    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        try {
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        } catch (e) {
            // Sin almacenamiento disponible: el cambio aplica solo a esta visita.
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.dark } }));
    },
});

// Contador animado para los KPIs del dashboard: sube de 0 al valor final.
Alpine.data('countUp', (target = 0, decimals = 0, duration = 1200) => ({
    display: (0).toFixed(decimals),
    init() {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || target === 0) {
            this.display = Number(target).toFixed(decimals);
            return;
        }
        const start = performance.now();
        const tick = (now) => {
            const t = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - t, 3);
            this.display = (target * eased).toFixed(decimals);
            if (t < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    },
}));

Alpine.start();
