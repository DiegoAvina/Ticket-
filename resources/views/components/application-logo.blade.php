{{-- Logo de la empresa (versión recortada y optimizada de public/asstes/logo.png). --}}
<img src="{{ asset('asstes/logo-256.png') }}" alt="{{ config('app.name', 'Help-Dasa') }}" width="256" height="256"
     {{ $attributes->merge(['class' => 'object-contain drop-shadow-sm']) }}>
