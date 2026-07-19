<x-guest-layout>
    <div class="w-full max-w-3xl space-y-6 pt-6 pb-12">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Validação de Certificado</h3>
            </div>

            <div class="p-6 space-y-5">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-excel-tint text-excel-dark flex items-center justify-center mx-auto mb-3">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-excel-dark">Certificado Válido</h2>
                    <p class="text-xs text-[--text-muted] mt-1">Código: <span class="font-mono font-bold text-[--text-main]">{{ $certificado->codigo_validacao }}</span></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Aluno</span>
                        <p class="font-semibold">{{ $certificado->nome_aluno }}</p>
                    </div>
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">CPF</span>
                        <p class="font-semibold">{{ $certificado->cpf_aluno }}</p>
                    </div>
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Equipe</span>
                        <p class="font-semibold text-excel-dark">{{ $certificado->nome_equipe ?? '—' }}</p>
                    </div>
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Categoria</span>
                        <p class="font-semibold text-excel-dark">{{ $certificado->nome_categoria ?? '—' }}</p>
                    </div>
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Período</span>
                        <p class="font-semibold text-sm">{{ $certificado->dt_inicio->format('d/m/Y') }} a {{ $certificado->dt_fim->format('d/m/Y') }}</p>
                    </div>
                    <div class="p-3 rounded-excel bg-[#f8faf8]">
                        <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Professor</span>
                        <p class="font-semibold">{{ $certificado->nome_professor ?? '—' }}</p>
                    </div>
                </div>

                <div class="p-3 rounded-excel bg-excel-tint text-center">
                    <span class="text-xs text-[--text-muted] uppercase tracking-wider">Título no Certificado</span>
                    <p class="font-semibold text-excel-dark mt-0.5"><em>{{ $certificado->titulo_certificado ?? '—' }}</em></p>
                </div>

                <div class="border-t border-[--border-light] pt-4 text-center text-xs text-[--text-muted]">
                    Emitido em {{ $certificado->emitido_em->format('d/m/Y à\s H:i') }} &middot; Sistema Pedagógico Excel Workshop
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
