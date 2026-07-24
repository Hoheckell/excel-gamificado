<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4 space-y-2">
                    <x-label for="terms" class="flex items-start">
                        <x-checkbox name="terms" id="terms" value="1" required class="mt-0.5" />
                        <span class="ms-2 text-sm text-[--text-main]">
                            Declaro que, ao me cadastrar, concordo com os Termos de Uso.
                        </span>
                    </x-label>
                    <p class="ms-6 text-xs text-[--text-muted]">
                        Leia os
                        <a target="_blank" rel="noopener noreferrer" href="{{ route('terms.show') }}" class="font-semibold underline hover:text-excel-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-excel-light">
                            Termos de Uso
                        </a>
                        antes de concluir seu cadastro.
                    </p>
                    <p class="ms-6 text-xs text-[--text-muted]">
                        Consulte também nossa
                        <a target="_blank" rel="noopener noreferrer" href="{{ route('policy.show') }}" class="font-semibold underline hover:text-excel-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-excel-light">
                            Política de Privacidade
                        </a>.
                    </p>
                    @error('terms')
                        <p class="ms-6 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="mt-4 rounded-excel border border-[--border-light] bg-[#f8faf8] p-3">
                <x-label for="educational_emails_consent" class="flex items-start">
                    <x-checkbox name="educational_emails_consent" id="educational_emails_consent" value="1" :checked="old('educational_emails_consent')" class="mt-0.5" />
                    <span class="ms-2 text-sm text-[--text-main]">
                        Quero receber por e-mail conteúdos educacionais enviados pelo professor, como materiais complementares, dicas e convites para atividades.
                    </span>
                </x-label>
                <p class="ms-6 mt-2 text-xs leading-relaxed text-[--text-muted]">
                    Opcional. Recusar não afeta o cadastro, a participação, a avaliação ou o certificado. Você poderá revogar este consentimento gratuitamente no perfil. Ao utilizar este consentimento, o professor compromete-se a usar seu contato somente para essa finalidade e a não compartilhá-lo com terceiros.
                </p>
                @error('educational_emails_consent')
                    <p class="ms-6 mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-[--text-muted] hover:text-excel-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-excel-light" href="{{ route('login') }}">
                    {{ __('Já possui cadastro?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Registrar') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
