<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-[--text-muted]">
            {{ __('Antes de continuar, verifique seu e-mail clicando no link que enviamos. Se não recebeu o e-mail, enviaremos outro.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-excel-dark">
                {{ __('Um novo link de verificação foi enviado para o endereço de e-mail informado.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        {{ __('Reenviar E-mail de Verificação') }}
                    </x-button>
                </div>
            </form>

            <div>
                <a
                    href="{{ route('profile.show') }}"
                    class="underline text-sm text-[--text-muted] hover:text-excel-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-excel-light"
                >
                    {{ __('Editar Perfil') }}</a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="underline text-sm text-[--text-muted] hover:text-excel-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-excel-light ms-2">
                        {{ __('Sair') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
