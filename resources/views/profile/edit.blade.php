<x-app-layout title="Mi perfil">
    <x-slot name="header">
        <x-dash.page-header title="Mi perfil" icon="manage_accounts" :subtitle="Auth::user()->email" />
    </x-slot>

    <div class="max-w-3xl w-full flex flex-col gap-6">
        <div class="dash-card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="dash-card p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="dash-card p-6 ring-1 ring-error/30">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
