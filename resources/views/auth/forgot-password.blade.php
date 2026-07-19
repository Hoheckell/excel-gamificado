<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-[--text-muted]">
            {{ __('Esqueceu sua senha? Sem problemas. Informe seu endereço de e-mail e enviaremos um link de redefinição de senha.') }}
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-excel-dark">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Enviar Link de Redefinição') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
