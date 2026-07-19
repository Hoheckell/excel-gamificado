<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Emitir Certificado</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Preencha os dados e confira o preview ao lado</p>
            </div>
            @if (!auth()->user()->isProfessor() && !auth()->user()->autorizado)
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-500/20 border border-yellow-400/30 text-yellow-100 rounded-excel text-xs font-semibold">
                    Aguardando autorização do professor
                </span>
            @elseif (auth()->user()->isProfessor() && !$turma)
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-500/20 border border-blue-400/30 text-blue-100 rounded-excel text-xs font-semibold">
                    Modo Teste — sem turmas com alunos e equipes
                </span>
            @endif
        </div>
    </x-slot>

    @if (!auth()->user()->isProfessor() && !auth()->user()->autorizado)
        <div class="max-w-2xl mx-auto p-6">
            <div class="portal-container text-center p-12">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-4 text-yellow-400">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <h3 class="text-lg font-bold text-[--text-main] mb-2">Acesso não autorizado</h3>
                <p class="text-sm text-[--text-muted]">Seu cadastro ainda não foi autorizado para emissão de certificados.<br>Solicite ao professor da sua turma.</p>
            </div>
        </div>
    @elseif (!auth()->user()->isProfessor() && !$turma)
        <div class="max-w-2xl mx-auto p-6">
            <div class="portal-container text-center p-12">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-4 text-[--excel-grid]">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <h3 class="text-lg font-bold text-[--text-main] mb-2">Emissão indisponível</h3>
                <p class="text-sm text-[--text-muted]">Sua turma ainda não possui equipes cadastradas.<br>Aguarde o professor realizar o sorteio.</p>
            </div>
        </div>
    @else
        <div class="max-w-[1200px] mx-auto p-6">
            <div class="flex flex-col lg:flex-row gap-6" x-data="certificadoForm()">
                {{-- Formulário --}}
                <div class="lg:w-[420px] shrink-0">
                    <div class="portal-container">
                        <div class="excel-ribbon px-5 py-3">
                            <h3 class="text-white font-semibold text-sm">Dados do Certificado</h3>
                        </div>

                        <form method="POST" action="{{ route('certificados.store') }}" class="p-5 space-y-4">
                            @csrf

                            <div>
                                <x-label value="Nome no Certificado" />
                                <input type="text" name="nome_aluno" x-model="nome" required
                                    class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                            </div>

                            <div>
                                <x-label value="CPF do Aluno" />
                                <input type="text" name="cpf_aluno" x-model="cpfAluno" x-on:input="cpfAluno = mascaraCPF($event.target.value)" required placeholder="000.000.000-00" maxlength="14"
                                    class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                                <p x-show="cpfAluno && !cpfAlunoValido" class="text-xs text-red-500 mt-1">CPF inválido</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-excel bg-[#f8faf8]">
                                    <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Equipe</span>
                                    <p class="text-sm font-semibold text-excel-dark" x-text="equipeNome"></p>
                                </div>
                                <div class="p-3 rounded-excel bg-[#f8faf8]">
                                    <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Categoria</span>
                                    <p class="text-sm font-semibold text-excel-dark" x-text="categoriaNome"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-2 rounded-excel bg-[#f8faf8]">
                                    <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Início</span>
                                    <p class="text-sm font-semibold" x-text="dtInicio"></p>
                                </div>
                                <div class="p-2 rounded-excel bg-[#f8faf8]">
                                    <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Término</span>
                                    <p class="text-sm font-semibold" x-text="dtFim"></p>
                                </div>
                            </div>

                            <div>
                                <x-label value="Data da Última Aula" />
                                <input type="date" name="dt_ultima_aula" x-model="dtUltimaAula" required
                                    class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                            </div>

                            <div>
                                <x-label value="CPF do Professor" />
                                <input type="text" name="cpf_professor" x-model="cpfProfessor" x-on:input="cpfProfessor = mascaraCPF($event.target.value)" required placeholder="000.000.000-00" maxlength="14"
                                    class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                                <p x-show="cpfProfessor && !cpfProfessorValido" class="text-xs text-red-500 mt-1">CPF inválido</p>
                            </div>

                            <div class="border-t border-[--border-light] pt-4">
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" name="confirmo" required class="mt-0.5 rounded border-gray-300 text-excel-dark focus:ring-excel-light">
                                    <span class="text-xs text-[--text-muted] leading-relaxed">
                                        Confirmo que os dados acima estão corretos. Entendo que este certificado será enviado para o e-mail <strong class="text-excel-dark">{{ auth()->user()->email }}</strong> em formato PDF.
                                    </span>
                                </label>
                            </div>

                            <x-button class="w-full justify-center py-3">
                                Emitir Certificado
                            </x-button>
                        </form>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="flex-1 min-w-0" id="cert-preview">
                    <div class="sticky top-6">
                        <p class="text-xs text-[--text-muted] uppercase tracking-wider mb-2 font-semibold text-center">Preview do Certificado</p>
                        <div class="bg-white rounded-excel shadow-xl overflow-hidden" style="border: 18px solid var(--excel-dark); position: relative;">
                            <div style="position: absolute; top: 24px; left: 24px; width: 60px; height: 60px; border: 3px solid var(--excel-light); border-right: none; border-bottom: none; z-index: 2;"></div>
                            <div style="position: absolute; top: 24px; right: 24px; width: 60px; height: 60px; border: 3px solid var(--excel-light); border-left: none; border-bottom: none; z-index: 2;"></div>
                            <div style="position: absolute; bottom: 24px; left: 24px; width: 60px; height: 60px; border: 3px solid var(--excel-light); border-right: none; border-top: none; z-index: 2;"></div>
                            <div style="position: absolute; bottom: 24px; right: 24px; width: 60px; height: 60px; border: 3px solid var(--excel-light); border-left: none; border-top: none; z-index: 2;"></div>

                            <div style="border: 1.5px solid var(--excel-dark); margin: 8px; padding: 28px 36px; text-align: center; background: linear-gradient(#f3f9f6 1px, transparent 1px) 0 0 / 100% 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 6px; color: var(--excel-dark); font-weight: 700; font-size: 12px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/></svg>
                                        EXCEL WORKSHOP
                                    </div>
                                    <img src="{{ asset('images/centec_academy.svg') }}" width="120" height="40" style="height: 40px; width: auto;" alt="CENTEC">
                                </div>

                                <h1 style="font-family: 'Playfair Display', serif; font-size: 22px; color: var(--excel-dark); text-transform: uppercase; letter-spacing: 1px; margin: 6px 0;">Certificado de Conquista</h1>
                                <p style="font-size: 10px; color: #555; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Certifica-se que</p>

                                <div x-text="nome || '[NOME DO ALUNO]'" style="font-size: 20px; font-weight: 700; color: #111; border-bottom: 1.5px solid var(--excel-light); display: inline-block; padding: 3px 24px; margin: 4px 0; font-family: 'Playfair Display', serif; letter-spacing: 1px;"></div>
                                <br>
                                <span x-text="cpfAluno ? 'CPF: ' + cpfAluno + '  •  Data de Conclusão: ' + dtUltimaAulaFormatada : 'CPF: [CPF]  •  Data de Conclusão: [DATA]'" style="font-size: 10px; color: #666; font-weight: 600;"></span>

                                <p style="font-size: 10px; line-height: 1.7; color: #222; max-width: 520px; margin: 10px auto 0;">
                                    integrante da equipe <strong style="color: var(--excel-dark);" x-text="equipeNome"></strong>, pelo extraordinário desempenho técnico, raciocínio lógico e trabalho em equipe demonstrados nas Missões Práticas, classificando-se honrosamente na categoria <strong style="color: var(--excel-dark);" x-text="categoriaNome"></strong> no torneio prático de gamificação do curso de qualificação profissional em Excel Básico, realizado no período de <strong style="color: var(--excel-dark);" x-text="dtInicio"></strong> a <strong style="color: var(--excel-dark);" x-text="dtFim"></strong>.
                                </p>
                                <p style="font-size: 9px; color: #666; margin-top: 2px;">Iguatu - CE, <strong style="color: var(--excel-dark);" x-text="dtUltimaAulaFormatada"></strong> de 2026.</p>
                                <p style="font-size: 8px; color: #888; margin-top: 2px;">Certificado de reconhecimento pedagógico de desempenho em sala de aula.</p>

                                
                                <div style="margin: 70px 0 4px; display: flex; justify-content: center; align-items: center;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #999; margin-bottom: 3px; width: 200px;"></div>
                                        <div style="font-size: 10px; font-weight: 700;" x-text="professorNome"></div>
                                        <div style="font-size: 8px; color: #666;">Professor Responsável</div>
                                        <div style="font-size: 8px; color: #666;" x-text="cpfProfessor ? 'CPF: ' + cpfProfessor : 'CPF: [CPF]'"></div>
                                    </div>
                                </div>
                            </div>
                            <img src="https://quickchart.io/qr?text={{ urlencode(route('certificados.validar', 'EXEMPLO12345678')) }}&size=150&margin=2"
                                 style="position: absolute; bottom: 44px; right: 44px; width: 62px; height: 62px; border-radius: 4px; z-index: 4;"
                                 alt="QR Code">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        function validarCPF(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11) return false;
            if (/^(\d)\1{10}$/.test(cpf)) return false;
            let soma = 0, resto;
            for (let i = 1; i <= 9; i++) soma += parseInt(cpf[i-1]) * (11 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf[9])) return false;
            soma = 0;
            for (let i = 1; i <= 10; i++) soma += parseInt(cpf[i-1]) * (12 - i);
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            return resto === parseInt(cpf[10]);
        }

        function mascaraCPF(valor) {
            valor = valor.replace(/\D/g, '');
            valor = valor.substring(0, 11);
            if (valor.length > 9) valor = valor.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2}).*/, '$1.$2.$3-$4');
            else if (valor.length > 6) valor = valor.replace(/^(\d{3})(\d{3})(\d{1,3}).*/, '$1.$2.$3');
            else if (valor.length > 3) valor = valor.replace(/^(\d{3})(\d{1,3}).*/, '$1.$2');
            return valor;
        }

        function certificadoForm() {
            return {
                nome: '{{ old('nome_aluno', $user->name) }}',
                cpfAluno: '',
                cpfProfessor: '',
                dtUltimaAula: '{{ old('dt_ultima_aula', $turma->dt_fim?->format('Y-m-d')) }}',
                equipeNome: '{{ $equipe?->nome ?? 'Sem equipe' }}',
                categoriaNome: '{{ $categoria?->nome ?? '—' }}',
                dtInicio: '{{ $turma->dt_inicio?->format('d/m/Y') ?? '—' }}',
                dtFim: '{{ $turma->dt_fim?->format('d/m/Y') ?? '—' }}',
                professorNome: '{{ $professor?->name ?? '[PROFESSOR]' }}',

                mascaraCPF,

                get cpfAlunoValido() {
                    return this.cpfAluno ? validarCPF(this.cpfAluno) : true;
                },
                get cpfProfessorValido() {
                    return this.cpfProfessor ? validarCPF(this.cpfProfessor) : true;
                },
                get dtUltimaAulaFormatada() {
                    if (!this.dtUltimaAula) return '[DATA]';
                    const [y, m, d] = this.dtUltimaAula.split('-');
                    return d + '/' + m + '/' + y;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
