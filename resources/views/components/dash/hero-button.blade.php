@props(['href', 'icon', 'primary' => false])

<a href="{{ $href }}"
   @class([
       'inline-flex items-center gap-1.5 px-4 py-2 rounded-lg font-medium transition-all shadow-sm',
       'bg-primary-container text-on-primary-container font-semibold shadow-lg shadow-primary/20 hover:brightness-105 hover:-translate-y-0.5' => $primary,
       'bg-surface-container-high text-on-surface hover:bg-surface-bright' => ! $primary,
   ])>
    <span @class(['material-symbols-outlined text-[18px]', 'text-primary' => $primary, 'text-tertiary' => ! $primary])>{{ $icon }}</span>
    {{ $slot }}
</a>
