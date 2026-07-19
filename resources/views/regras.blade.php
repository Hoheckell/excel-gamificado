<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Regras do Sistema</h2>
        <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Como funciona a pontuação e as categorias</p>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6 space-y-6">

        {{-- Introdução --}}
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold text-lg">Como funciona?</h3>
            </div>
            <div class="p-6">
                <p class="text-[--text-main] leading-relaxed">
                    Em vez de competir apenas para "vencer" os colegas, cada equipe acumula pontos ao longo das <strong class="text-excel-dark">5 missões</strong> para alcançar sua própria categoria de conquista. No total, são <strong class="text-excel-dark">500 pontos</strong> possíveis — e todo esforço é reconhecido no certificado final.
                </p>
            </div>
        </div>

        {{-- Categorias --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Ouro --}}
            <div class="portal-container border-t-4 border-yellow-400">
                <div class="bg-yellow-50 px-5 py-4 flex items-center gap-3">
                    <div class="w-[44px] h-[44px] rounded-full bg-yellow-400 text-white flex items-center justify-center font-bold text-lg">1</div>
                    <div>
                        <h3 class="font-bold text-base text-yellow-700">Mestres dos Dados</h3>
                        <span class="text-sm font-mono font-bold text-yellow-600">450 a 500 pontos</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-[--text-main] leading-relaxed">
                        <strong>O que significa:</strong> Sua equipe mandou muito bem nas fórmulas! Dominaram funções como <code class="bg-[#f8faf8] px-1.5 py-0.5 rounded text-excel-dark text-xs font-mono">SE</code> e <code class="bg-[#f8faf8] px-1.5 py-0.5 rounded text-excel-dark text-xs font-mono">PROCV</code> sem erros, e as planilhas ficaram com visual limpo e profissional.
                    </p>
                    <div class="p-3 rounded-excel bg-yellow-50 border border-yellow-200">
                        <span class="text-xs text-yellow-700 font-semibold uppercase tracking-wider">Título no Certificado</span>
                        <p class="text-sm text-yellow-800 mt-0.5"><em>Excelência Técnica e Domínio Analítico em Excel Básico</em></p>
                    </div>
                </div>
            </div>

            {{-- Prata --}}
            <div class="portal-container border-t-4 border-gray-300">
                <div class="bg-gray-50 px-5 py-4 flex items-center gap-3">
                    <div class="w-[44px] h-[44px] rounded-full bg-gray-400 text-white flex items-center justify-center font-bold text-lg">2</div>
                    <div>
                        <h3 class="font-bold text-base text-gray-700">Analistas de Planilhas</h3>
                        <span class="text-sm font-mono font-bold text-gray-500">380 a 449 pontos</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-[--text-main] leading-relaxed">
                        <strong>O que significa:</strong> Sua equipe domina a estrutura das tabelas e funções essenciais como <code class="bg-[#f8faf8] px-1.5 py-0.5 rounded text-excel-dark text-xs font-mono">SOMA</code>, <code class="bg-[#f8faf8] px-1.5 py-0.5 rounded text-excel-dark text-xs font-mono">MÉDIA</code> e <code class="bg-[#f8faf8] px-1.5 py-0.5 rounded text-excel-dark text-xs font-mono">MÁXIMO</code>, com apenas pequenos deslizes nas missões mais difíceis.
                    </p>
                    <div class="p-3 rounded-excel bg-gray-50 border border-gray-200">
                        <span class="text-xs text-gray-600 font-semibold uppercase tracking-wider">Título no Certificado</span>
                        <p class="text-sm text-gray-700 mt-0.5"><em>Alto Desempenho em Lógica e Estruturação de Planilhas</em></p>
                    </div>
                </div>
            </div>

            {{-- Bronze --}}
            <div class="portal-container border-t-4 border-orange-400">
                <div class="bg-orange-50 px-5 py-4 flex items-center gap-3">
                    <div class="w-[44px] h-[44px] rounded-full bg-orange-400 text-white flex items-center justify-center font-bold text-lg">3</div>
                    <div>
                        <h3 class="font-bold text-base text-orange-700">Especialistas em Formatação</h3>
                        <span class="text-sm font-mono font-bold text-orange-500">300 a 379 pontos</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-[--text-main] leading-relaxed">
                        <strong>O que significa:</strong> Talvez as fórmulas mais complexas tenham sido um desafio, mas sua equipe compensou com planilhas super organizadas, bem formatadas e gráficos claros — design nota 10!
                    </p>
                    <div class="p-3 rounded-excel bg-orange-50 border border-orange-200">
                        <span class="text-xs text-orange-700 font-semibold uppercase tracking-wider">Título no Certificado</span>
                        <p class="text-sm text-orange-800 mt-0.5"><em>Destaque em Organização Visual e Apresentação de Dados</em></p>
                    </div>
                </div>
            </div>

            {{-- Crescimento --}}
            <div class="portal-container border-t-4 border-green-500">
                <div class="bg-green-50 px-5 py-4 flex items-center gap-3">
                    <div class="w-[44px] h-[44px] rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-lg">&#9733;</div>
                    <div>
                        <h3 class="font-bold text-base text-green-700">Superação e Trabalho em Equipe</h3>
                        <span class="text-sm font-mono font-bold text-green-600">Até 299 pontos</span>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-[--text-main] leading-relaxed">
                        <strong>O que significa:</strong> Sua equipe enfrentou dificuldades, mas <strong class="text-green-700">não desistiu</strong>. O foco foi na colaboração, presença nas aulas e esforço para concluir as tarefas — e isso também merece reconhecimento!
                    </p>
                    <div class="p-3 rounded-excel bg-green-50 border border-green-200">
                        <span class="text-xs text-green-700 font-semibold uppercase tracking-wider">Título no Certificado</span>
                        <p class="text-sm text-green-800 mt-0.5"><em>Honra ao Mérito em Colaboração, Superação e Prática de Excel</em></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mecânicas Anti-Desmotivação --}}
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold text-lg">Como manter a motivação até o fim</h3>
            </div>
            <div class="p-6 space-y-5">
                <p class="text-sm text-[--text-muted]">Para ninguém desistir no meio do caminho, existem três regras especiais:</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Progressiva --}}
                    <div class="p-4 rounded-excel bg-[#f8faf8] border border-[--border-light]">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-2xl">&#128200;</span>
                            <h4 class="font-bold text-sm text-[--text-main]">Pontuação Progressiva</h4>
                        </div>
                        <p class="text-xs text-[--text-muted] leading-relaxed">
                            As primeiras missões valem menos (50 a 80 pontos) e as últimas valem <strong class="text-excel-dark">muito mais</strong> (até 150 pontos). Mesmo se começar devagar, dá tempo de alcançar as categorias mais altas!
                        </p>
                    </div>

                    {{-- Colaboração --}}
                    <div class="p-4 rounded-excel bg-[#f8faf8] border border-[--border-light]">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-2xl">&#129309;</span>
                            <h4 class="font-bold text-sm text-[--text-main]">Bônus de Colaboração</h4>
                        </div>
                        <p class="text-xs text-[--text-muted] leading-relaxed">
                            Terminou a missão cedo? Envie um colega para <strong class="text-excel-dark">ajudar outra equipe</strong> (só orientar, sem mexer no mouse). Ambas as equipes ganham <strong class="text-excel-dark">+20 pontos</strong> extras!
                        </p>
                    </div>

                    {{-- Recuperação --}}
                    <div class="p-4 rounded-excel bg-[#f8faf8] border border-[--border-light]">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-2xl">&#9889;</span>
                            <h4 class="font-bold text-sm text-[--text-main]">Desafio Relâmpago</h4>
                        </div>
                        <p class="text-xs text-[--text-muted] leading-relaxed">
                            No início de algumas aulas, um exercício rápido de 10 minutos. Equipes com menor pontuação que acertarem ganham <strong class="text-excel-dark">+15 pontos</strong> para subir de categoria!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumo visual --}}
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4">
                <h3 class="font-bold text-[--text-main]">Resumo das Categorias</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8faf8] border-b border-[--border-light]">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Nível</th>
                            <th class="text-left px-5 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Categoria</th>
                            <th class="text-left px-5 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Pontos</th>
                            <th class="text-left px-5 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Reconhece</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[--border-light]">
                        <tr class="hover:bg-yellow-50/50">
                            <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-yellow-400 text-white font-bold text-xs">1</span></td>
                            <td class="px-5 py-3 font-semibold text-[--text-main]">Mestres dos Dados</td>
                            <td class="px-5 py-3 font-mono font-bold text-yellow-600">450-500</td>
                            <td class="px-5 py-3 text-xs text-[--text-muted]">Fórmulas, lógica e visual profissional</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-400 text-white font-bold text-xs">2</span></td>
                            <td class="px-5 py-3 font-semibold text-[--text-main]">Analistas de Planilhas</td>
                            <td class="px-5 py-3 font-mono font-bold text-gray-500">380-449</td>
                            <td class="px-5 py-3 text-xs text-[--text-muted]">Estrutura e funções essenciais</td>
                        </tr>
                        <tr class="hover:bg-orange-50/50">
                            <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-400 text-white font-bold text-xs">3</span></td>
                            <td class="px-5 py-3 font-semibold text-[--text-main]">Especialistas em Formatação</td>
                            <td class="px-5 py-3 font-mono font-bold text-orange-500">300-379</td>
                            <td class="px-5 py-3 text-xs text-[--text-muted]">Organização, design e gráficos</td>
                        </tr>
                        <tr class="hover:bg-green-50/50">
                            <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-500 text-white font-bold text-xs">&#9733;</span></td>
                            <td class="px-5 py-3 font-semibold text-[--text-main]">Superação e Trabalho em Equipe</td>
                            <td class="px-5 py-3 font-mono font-bold text-green-600">0-299</td>
                            <td class="px-5 py-3 text-xs text-[--text-muted]">Colaboração, presença e esforço</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
