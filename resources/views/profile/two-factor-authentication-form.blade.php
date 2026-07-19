<x-action-section>
    <x-slot name="title">Autenticação de Dois Fatores</x-slot>

    <x-slot name="description">Adicione segurança extra à sua conta com autenticação de dois fatores.</x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-semibold text-[--text-main]">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    Finalize a ativação da autenticação de dois fatores.
                @else
                    Autenticação de dois fatores ativada.
                @endif
            @else
                Autenticação de dois fatores não está ativada.
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-[--text-muted]">
            <p>Quando ativada, será solicitado um token seguro durante o login. Você pode obter este token pelo Google Authenticator no seu celular.</p>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 max-w-xl text-sm text-[--text-muted]">
                    <p class="font-semibold">
                        @if ($showingConfirmation)
                            Para finalizar, escaneie o QR Code com seu aplicativo autenticador ou insira a chave de configuração e o código OTP.
                        @else
                            Autenticação de dois fatores ativada. Escaneie o QR Code ou use a chave abaixo.
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 inline-block bg-white border border-[--border-light] rounded-lg">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-[--text-muted]">
                    <p class="font-semibold">Chave de Configuração: {{ decrypt($this->user->two_factor_secret) }}</p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4">
                        <x-label for="code" value="Código" />
                        <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code" wire:keydown.enter="confirmTwoFactorAuthentication" />
                        <x-input-error for="code" class="mt-2" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 max-w-xl text-sm text-[--text-muted]">
                    <p class="font-semibold">Guarde estes códigos de recuperação em um local seguro. Eles permitem recuperar o acesso caso perca o dispositivo.</p>
                </div>

                <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-[#f8faf8] text-[--text-main] rounded-lg border border-[--border-light]">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-5 flex flex-wrap gap-3">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled">Ativar</x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button>Regenerar Códigos</x-secondary-button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <x-button type="button" wire:loading.attr="disabled">Confirmar</x-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button>Mostrar Códigos</x-secondary-button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-secondary-button wire:loading.attr="disabled">Cancelar</x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-danger-button wire:loading.attr="disabled">Desativar</x-danger-button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>
