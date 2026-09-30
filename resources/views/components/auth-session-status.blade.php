@props(['status'])

@if ($status)
    <x-dash.alert type="success" {{ $attributes }}>{{ $status }}</x-dash.alert>
@endif
