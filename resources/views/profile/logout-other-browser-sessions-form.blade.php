<x-action-section>
    <x-slot name="title">Sessões do Navegador</x-slot>

    <x-slot name="description">Gerencie e encerre suas sessões ativas em outros navegadores e dispositivos.</x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-[--text-muted]">
            Se necessário, você pode encerrar todas as outras sessões em todos os seus dispositivos. Algumas sessões recentes estão listadas abaixo. Se achar que sua conta foi comprometida, altere também sua senha.
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                @foreach ($this->sessions as $session)
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-[--text-muted]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-[--text-muted]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            @endif
                        </div>

                        <div class="ms-3">
                            <div class="text-sm text-[--text-muted]">
                                {{ $session->agent->platform() ?: 'Desconhecido' }} - {{ $session->agent->browser() ?: 'Desconhecido' }}
                            </div>
                            <div>
                                <div class="text-xs text-[--text-muted]">
                                    {{ $session->ip_address }},
                                    @if ($session->is_current_device)
                                        <span class="text-excel-dark font-semibold">Este dispositivo</span>
                                    @else
                                        Última atividade {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
                Encerrar Outras Sessões
            </x-button>
            <x-action-message class="ms-3" on="loggedOut">Concluído.</x-action-message>
        </div>

        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">Encerrar Outras Sessões</x-slot>
            <x-slot name="content">
                Digite sua senha para confirmar que deseja encerrar todas as outras sessões.
                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4" autocomplete="current-password" placeholder="Senha"
                        x-ref="password" wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">Cancelar</x-secondary-button>
                <x-button class="ms-3" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">Encerrar Sessões</x-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
