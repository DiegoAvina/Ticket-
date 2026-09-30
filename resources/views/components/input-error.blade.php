@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-error space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}</li>
        @endforeach
    </ul>
@endif
