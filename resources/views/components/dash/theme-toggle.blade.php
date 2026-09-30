<button type="button" x-data @click="$store.theme.toggle()"
        :aria-label="$store.theme.dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
        :title="$store.theme.dark ? 'Modo claro' : 'Modo oscuro'"
        class="relative w-14 h-8 rounded-full bg-surface-container-high ring-1 ring-outline-variant/40 transition-colors">
    <span class="absolute top-1 left-1 w-6 h-6 rounded-full flex items-center justify-center shadow-md transition-all duration-300"
          :class="$store.theme.dark ? 'translate-x-6 bg-primary-container text-primary' : 'bg-white text-amber-500'">
        <span class="material-symbols-outlined icon-fill text-[16px]" x-text="$store.theme.dark ? 'dark_mode' : 'light_mode'">light_mode</span>
    </span>
</button>
