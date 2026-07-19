<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Perfil</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
            <x-section-border />
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="mt-8 sm:mt-8">
                @livewire('profile.update-password-form')
            </div>
            <x-section-border />
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="mt-8 sm:mt-8">
                @livewire('profile.two-factor-authentication-form')
            </div>
            <x-section-border />
        @endif

        <div class="mt-8 sm:mt-8">
            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-section-border />
            <div class="mt-8 sm:mt-8">
                @livewire('profile.delete-user-form')
            </div>
        @endif
    </div>
</x-app-layout>
