<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">Certificado Emitido</h2>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                Imprimir
            </button>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-6 px-5 py-3 bg-excel-tint border border-excel-light text-excel-dark rounded-excel text-sm font-semibold text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-[--border-color] rounded-lg overflow-hidden" style="box-shadow: 0 12px 35px rgba(16, 124, 65, 0.1);">
            <div class="excel-ribbon px-5 py-3">
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 text-white px-2 py-0.5 rounded text-xs font-mono font-bold tracking-wider">{{ $certificado->codigo_validacao }}</span>
                    <span class="text-white/60 text-xs">Emitido em {{ $certificado->emitido_em->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="p-6" style="border: 16px solid var(--excel-dark); position: relative;">
                <div style="position: absolute; top: 20px; left: 20px; width: 50px; height: 50px; border: 3px solid var(--excel-light); border-right: none; border-bottom: none; z-index: 2;"></div>
                <div style="position: absolute; top: 20px; right: 20px; width: 50px; height: 50px; border: 3px solid var(--excel-light); border-left: none; border-bottom: none; z-index: 2;"></div>
                <div style="position: absolute; bottom: 20px; left: 20px; width: 50px; height: 50px; border: 3px solid var(--excel-light); border-right: none; border-top: none; z-index: 2;"></div>
                <div style="position: absolute; bottom: 20px; right: 20px; width: 50px; height: 50px; border: 3px solid var(--excel-light); border-left: none; border-top: none; z-index: 2;"></div>

                <div style="border: 1.5px solid var(--excel-dark); padding: 35px 50px; text-align: center; background: linear-gradient(#f3f9f6 1px, transparent 1px) 0 0 / 100% 22px;">
                    <div class="cert-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--excel-dark); font-weight: 700; font-size: 14px;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/></svg>
                            EXCEL WORKSHOP
                        </div>
                        <img src="{{ asset('images/centec_academy.svg') }}" width="140" height="47" style="height: 47px; width: auto;" alt="CENTEC">
                    </div>

                    <h1 style="font-family: 'Playfair Display', serif; font-size: 30px; color: var(--excel-dark); text-transform: uppercase; letter-spacing: 2px; margin: 4px 0;">Certificado de Conquista</h1>
                    <p style="font-size: 12px; color: #555; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 12px;">Certifica-se que</p>

                    <div style="font-size: 24px; font-weight: 700; color: #111; border-bottom: 2px solid var(--excel-light); display: inline-block; padding: 4px 35px; margin: 6px 0; font-family: 'Playfair Display', serif; letter-spacing: 1px;">
                        {{ $certificado->nome_aluno }}
                    </div>
                    <br>
                    <span style="font-size: 12px; color: #666; font-weight: 600;">CPF: {{ $certificado->cpf_aluno }} &nbsp;&bull;&nbsp; Data de Conclusão: {{ $certificado->dt_ultima_aula->format('d/m/Y') }}</span>

                    <p style="font-size: 12px; line-height: 1.8; color: #222; max-width: 600px; margin: 12px auto 0;">
                        integrante da equipe <strong style="color: var(--excel-dark);">{{ $certificado->nome_equipe ?? '—' }}</strong>, pelo extraordinário desempenho técnico, raciocínio lógico e trabalho em equipe demonstrados nas Missões Práticas, classificando-se honrosamente na categoria <strong style="color: var(--excel-dark);">{{ $certificado->nome_categoria ?? '—' }}</strong> no torneio prático de gamificação do curso de qualificação profissional em Excel Básico, realizado no período de <strong style="color: var(--excel-dark);">{{ $certificado->dt_inicio->format('d/m/Y') }}</strong> a <strong style="color: var(--excel-dark);">{{ $certificado->dt_fim->format('d/m/Y') }}</strong>.
                    </p>
                    <p style="font-size: 11px; color: #666; margin-top: 4px;">Iguatu - CE, <strong style="color: var(--excel-dark);">{{ $certificado->dt_ultima_aula->format('d/m/Y') }}</strong> de 2026.</p>
                    <p style="font-size: 10px; color: #888; margin-top: 2px;">Certificado de reconhecimento pedagógico de desempenho em sala de aula.</p>

                    
                    <div style="margin-top: 70px; display: flex; justify-content: center;">
                        <div style="text-align: center;">
                            <div style="border-top: 1px solid #999; margin-bottom: 6px; width: 220px;"></div>
                            <div style="font-size: 12px; font-weight: 700; color: #111;">{{ $certificado->nome_professor ?? '—' }}</div>
                            <div style="font-size: 10px; color: #666;">Professor Responsável</div>
                            <div style="font-size: 10px; color: #888; margin-top: 2px;">CPF: {{ $certificado->cpf_professor ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <img src="{{ $certificado->qr_code_url }}"
                     style="position: absolute; bottom: 36px; right: 36px; width: 62px; height: 62px; border-radius: 4px; z-index: 4;"
                     alt="QR Code">
            </div>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('certificados.validar', $certificado->codigo_validacao) }}" target="_blank" class="text-sm text-excel-dark hover:text-excel-light transition font-medium underline underline-offset-4">
                Abrir página de validação
            </a>
            <a href="{{ route('dashboard') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Voltar ao Painel</a>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .bg-white.border, .bg-white.border * { visibility: visible; }
            .bg-white.border {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            @page { size: landscape; margin: 0; }
            .excel-ribbon { display: none; }
            [style*="position: absolute"][style*="top: 20px"] { display: block; }
        }
    </style>
</x-app-layout>
