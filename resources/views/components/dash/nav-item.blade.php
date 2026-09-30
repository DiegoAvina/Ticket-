@props(['item'])

<a href="{{ $item['href'] }}" @if ($item['active']) aria-current="page" @endif
   @class([
       'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors',
       'bg-primary-container text-on-primary-container font-semibold' => $item['active'],
       'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' => ! $item['active'],
   ])>
    <span @class(['material-symbols-outlined text-[20px]', 'icon-fill' => $item['active']])>{{ $item['icon'] }}</span>
    <span>{{ $item['label'] }}</span>
</a>
