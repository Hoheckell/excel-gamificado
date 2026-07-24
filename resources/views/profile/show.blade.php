<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Perfil</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
            <x-section-border />
        @endif

        @if (auth()->user()->isAluno())
            <section class="mt-8 sm:mt-8" aria-labelledby="preferencia-emails-titulo">
                <div class="bg-white shadow-sm rounded-excel border border-[--border-light]">
                    <div class="px-6 py-5 border-b border-[--border-light]">
                        <h3 id="preferencia-emails-titulo" class="text-lg font-semibold text-[--text-main]">Conteúdos educacionais por e-mail</h3>
                        <p class="mt-1 text-sm text-[--text-muted]">Escolha livremente se deseja receber materiais complementares, dicas e convites enviados pelo professor.</p>
                    </div>
                    <form method="POST" action="{{ route('profile.educational-emails.update') }}" class="p-6">
                        @csrf
                        @method('PATCH')
                        <label for="profile_educational_emails_consent" class="flex items-start">
                            <x-checkbox name="educational_emails_consent" id="profile_educational_emails_consent" value="1" :checked="auth()->user()->educational_emails_consent" class="mt-0.5" />
                            <span class="ms-2 text-sm text-[--text-main]">Autorizo o recebimento de conteúdos educacionais opcionais por e-mail.</span>
                        </label>
                        <p class="ms-6 mt-2 text-xs leading-relaxed text-[--text-muted]">
                            Você pode consentir ou revogar gratuitamente a qualquer momento. A alteração não interfere em e-mails essenciais da conta, segurança, atividades ou certificados. O professor deve usar seu contato somente para a finalidade autorizada e não pode compartilhá-lo com terceiros.
                        </p>
                        <div class="mt-4 flex items-center justify-end">
                            <x-button>Salvar preferência</x-button>
                        </div>
                    </form>
                </div>
            </section>
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
