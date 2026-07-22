<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Regras do Sistema & Manual da Consultoria</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Iguatu Agro&Comércio — Gamificação & RPG de Excel Básico</p>
            </div>
            {{-- Zequinhômetro / Medidor de Humor do Chefe --}}
            <div class="hidden sm:flex items-center gap-3 bg-white/10 px-4 py-2 rounded-full border border-white/20">
                <span class="text-2xl" title="Status atual da empresa">😎</span>
                <div class="text-right">
                    <span class="block text-[10px] uppercase font-bold text-white/80 tracking-widest">Humor do Juvenildo</span>
                    <span class="text-xs font-semibold text-green-300">Satisfeito com as Consultorias</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6 space-y-8">

        {{-- 1. O Cenário & Narrativa RPG (A Imersão) --}}
        <div class="portal-container border-l-4 border-excel-dark">
            <div class="excel-ribbon px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                    <span>🏢</span> O Cenário: Iguatu Agro&Comércio
                </h3>
                <span class="text-xs bg-white/20 text-white px-2.5 py-1 rounded-full font-mono">Missão Corporativa</span>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-[--text-main] leading-relaxed">
                    Vocês não são apenas alunos: são <strong class="text-excel-dark">Consultores de Inteligência de Dados</strong> contratados para salvar o escritório da <em>Iguatu Agro&Comércio</em>, uma distribuidora em plena expansão no Centro-Sul cearense.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-excel bg-[#f8faf8] border border-[--border-light] flex items-start gap-3.5">
                        <span class="text-3xl">👔</span>
                        <div>
                            <h4 class="font-bold text-sm text-[--text-main]">Juvenildo Canindé (O Gerente)</h4>
                            <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">
                                Exigente e focado em prazos. Ele trará os problemas reais da empresa e avaliará se a sua planilha é digna de ser apresentada para a diretoria.
                            </p>
                        </div>
                    </div>
                    <div class="p-4 rounded-excel bg-[#f8faf8] border border-[--border-light] flex items-start gap-3.5">
                        <span class="text-3xl">🤓</span>
                        <div>
                            <h4 class="font-bold text-sm text-[--text-main]">Zequinha (O Estagiário)</h4>
                            <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">
                                Esforçado, mas adora bagunçar os dados! Ele sempre pedirá ajuda com "tarefas secretas" que valem pontos extras para a equipe que ensiná-lo a usar o Excel.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Barra de XP Dinâmica com Checkpoints --}}
        <div class="portal-container">
            <div class="bg-white px-6 py-5 border-b border-[--border-light]">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-[--text-main] text-base">Progresso Geral da Conquista (XP)</h3>
                    <span class="text-xs font-mono font-bold bg-green-100 text-green-800 px-3 py-1 rounded-full">Meta Máxima: 500 Pontos</span>
                </div>
                <p class="text-xs text-[--text-muted] mb-6">Acumule pontos ao longo das 5 missões diárias para destravar o patamar da sua equipe no certificado de destaque prático.</p>
                
                {{-- XP Bar --}}
                <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden mb-6">
                    {{-- Altere o width (ex: w-[76%]) dinamicamente pelo banco de dados para mostrar o progresso da equipe logada --}}
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-green-500 via-orange-400 to-yellow-400 w-[76%] transition-all duration-500"></div>
                </div>

                {{-- Checkpoints --}}
                <div class="grid grid-cols-4 text-center text-xs font-semibold">
                    <div class="text-green-600 border-t-2 border-green-500 pt-1.5">
                        <span class="block font-bold">★ Crescimento</span>
                        <span class="font-mono text-[11px] text-gray-500">0 - 299 pts</span>
                    </div>
                    <div class="text-orange-500 border-t-2 border-orange-400 pt-1.5">
                        <span class="block font-bold">🥉 Bronze</span>
                        <span class="font-mono text-[11px] text-gray-500">300 pts</span>
                    </div>
                    <div class="text-gray-600 border-t-2 border-gray-400 pt-1.5">
                        <span class="block font-bold">🥈 Prata</span>
                        <span class="font-mono text-[11px] text-gray-500">380 pts</span>
                    </div>
                    <div class="text-yellow-600 border-t-2 border-yellow-400 pt-1.5">
                        <span class="block font-bold">🏆 Ouro</span>
                        <span class="font-mono text-[11px] text-gray-500">450 pts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Classes da Consultoria (RPG Roles) --}}
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold text-lg">Divisão de Papéis da Equipe (Rodízio Diário)</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-[--text-main] leading-relaxed mb-5">
                    No laboratório, cada membro assume uma especialidade. Os papéis devem ser rotacionados a cada aula para que todos dominem o Excel integralmente:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-excel border border-[--border-light] bg-white shadow-sm hover:shadow-md transition">
                        <div class="text-2xl mb-2">🛠️</div>
                        <h4 class="font-bold text-sm text-[--text-main]">Arquiteto de Dados</h4>
                        <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">Responsável pela digitação das fórmulas, lógica de funções (<code class="text-excel-dark font-mono">SE</code>, <code class="text-excel-dark font-mono">PROCV</code>) e integridade dos cálculos.</p>
                    </div>
                    <div class="p-4 rounded-excel border border-[--border-light] bg-white shadow-sm hover:shadow-md transition">
                        <div class="text-2xl mb-2">🔍</div>
                        <h4 class="font-bold text-sm text-[--text-main]">Auditor de Qualidade</h4>
                        <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">Caçador de falhas! Verifica se há células vazias, erros como <code class="text-red-500 font-mono">#VALOR!</code> e se os números estão formatados como moeda (R$).</p>
                    </div>
                    <div class="p-4 rounded-excel border border-[--border-light] bg-white shadow-sm hover:shadow-md transition">
                        <div class="text-2xl mb-2">🎨</div>
                        <h4 class="font-bold text-sm text-[--text-main]">Designer Visual</h4>
                        <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">Cuida da estética corporativa. Define paletas de cores, ajusta larguras, mescla cabeçalhos e garante a legibilidade da tabela.</p>
                    </div>
                    <div class="p-4 rounded-excel border border-[--border-light] bg-white shadow-sm hover:shadow-md transition">
                        <div class="text-2xl mb-2">⏱️</div>
                        <h4 class="font-bold text-sm text-[--text-main]">Gestor de Entregas</h4>
                        <p class="text-xs text-[--text-muted] mt-1 leading-relaxed">Gerencia o tempo, organiza a consolidação das abas individuais no Arquivo Mestre e realiza a entrega oficial para o professor.</p>
                    </div>
                </div>
                <div class="mt-5 p-4 rounded-excel bg-blue-50 border border-blue-200">
                    <h4 class="font-bold text-sm text-blue-800 flex items-center gap-2"><span>💬</span> O papel de consultor durante uma ajuda</h4>
                    <p class="text-xs text-blue-700 mt-1.5 leading-relaxed">
                        Quando uma equipe termina cedo e recebe autorização para colaborar, um integrante pode atuar como consultor em outra bancada. Ele deve <strong>observar, fazer perguntas, apontar a causa do problema e explicar o caminho</strong>, deixando que a equipe ajudada execute cada passo. O Auditor de Qualidade, por exemplo, pode explicar por que uma célula gera <code class="font-mono">#VALOR!</code>, mas não pode corrigir a planilha no lugar dos colegas.
                    </p>
                </div>
            </div>
        </div>

        {{-- 4. Estrutura das Missões Diárias --}}
        <div class="portal-container bg-slate-50 border border-slate-200 p-6">
            <h3 class="font-bold text-[--text-main] text-base mb-3 flex items-center gap-2">
                <span>📋</span> Estrutura das Missões (O Que Esperar de Cada Tarefa)
            </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-white p-4 rounded-excel border border-slate-200">
                    <span class="text-xs font-bold font-mono uppercase text-blue-600 block mb-1">1. O Chamado do Juvenildo</span>
                    <strong class="text-slate-800 text-xs block mb-1.5">Missão Principal (100 pts)</strong>
                    <p class="text-xs text-[--text-muted] leading-relaxed">A entrega obrigatória do dia. Tabela limpa, cálculos exatos e estrutura sem falhas para atender às metas da distribuidora.</p>
                </div>
                <div class="bg-white p-4 rounded-excel border border-slate-200">
                    <span class="text-xs font-bold font-mono uppercase text-purple-600 block mb-1">2. O Pedido do Zequinha</span>
                    <strong class="text-slate-800 text-xs block mb-1.5">Quest Secundária (+20 a +40 pts)</strong>
                    <p class="text-xs text-[--text-muted] leading-relaxed">Desafios extras e opcionais. Exige curiosidade, raciocínio avançado ou atalhos para automatizar o trabalho do estagiário.</p>
                </div>
                <div class="bg-white p-4 rounded-excel border border-slate-200">
                    <span class="text-xs font-bold font-mono uppercase text-amber-600 block mb-1">3. Conexão Contínua</span>
                    <strong class="text-slate-800 text-xs block mb-1.5">A Base Mestra Não Para!</strong>
                    <p class="text-xs text-[--text-muted] leading-relaxed">Nenhum arquivo é descartado. A planilha salva no final de uma aula é o arquivo de entrada obrigatório da missão seguinte.</p>
                </div>
            </div>
        </div>

        {{-- 5. Categorias de Pontuação (Patamares) --}}
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
                <div class="mt-4 p-4 rounded-excel bg-green-50 border border-green-200">
                    <h4 class="font-bold text-sm text-green-800">Como o Bônus de Colaboração entra na missão</h4>
                    <p class="text-xs text-green-700 mt-1.5 leading-relaxed">
                        Depois de confirmar que a orientação respeitou as regras, o professor acrescenta <strong>+20 pontos ao saldo da missão de cada equipe envolvida</strong>: quem ajudou e quem aceitou a ajuda. Cada auxílio distinto deve ser observado e aprovado pelo professor; assim, colaborações com equipes diferentes podem ser reconhecidas separadamente na mesma rodada.
                    </p>
                </div>
            </div>
        </div>

        {{-- 6. Painel de Conquistas & Badges (+15 pts cada) --}}
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4 flex items-center justify-between">
                <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                    <span>🏅</span> Badges e Conquistas Diárias (+15 pts cada)
                </h3>
                <span class="text-xs bg-white/20 text-white px-2.5 py-1 rounded-full font-mono">Bônus de Laboratório</span>
            </div>
            <div class="p-6">
                <p class="text-xs text-[--text-muted] mb-5">
                    Durante a circulação no laboratório, o professor avaliará o comportamento técnico da equipe. Desbloqueie medalhas especiais para acumular XP extra:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {{-- Desbloqueada (Exemplo Visual) --}}
                    <div class="p-3 rounded-excel border-2 border-green-500 bg-green-50/40 text-center relative overflow-hidden">
                        <div class="absolute top-1 right-1 text-[10px] bg-green-500 text-white px-1.5 rounded font-bold">DESTRAVADA</div>
                        <span class="text-3xl block my-1">⌨️</span>
                        <strong class="text-xs font-bold text-slate-800 block">Zero Mouse</strong>
                        <span class="text-[11px] text-slate-500 block mt-0.5">Navegação 100% via atalhos de teclado.</span>
                    </div>
                    {{-- Bloqueada --}}
                    <div class="p-3 rounded-excel border border-dashed border-gray-300 bg-gray-50/50 text-center opacity-60">
                        <span class="text-3xl block my-1">✨</span>
                        <strong class="text-xs font-bold text-slate-700 block">Código Limpo</strong>
                        <span class="text-[11px] text-slate-500 block mt-0.5">Zero erros #VALOR! ou #REF! na entrega.</span>
                    </div>
                    {{-- Bloqueada --}}
                    <div class="p-3 rounded-excel border border-dashed border-gray-300 bg-gray-50/50 text-center opacity-60">
                        <span class="text-3xl block my-1">📊</span>
                        <strong class="text-xs font-bold text-slate-700 block">Visual Executivo</strong>
                        <span class="text-[11px] text-slate-500 block mt-0.5">Formatação corporativa impecável.</span>
                    </div>
                    {{-- Bloqueada --}}
                    <div class="p-3 rounded-excel border border-dashed border-gray-300 bg-gray-50/50 text-center opacity-60">
                        <span class="text-3xl block my-1">🛟</span>
                        <strong class="text-xs font-bold text-slate-700 block">Salva-Vidas</strong>
                        <span class="text-[11px] text-slate-500 block mt-0.5">+15 XP somente para quem orientou sem tocar no mouse ou teclado.</span>
                    </div>
                </div>
                <div class="mt-5 p-4 rounded-excel bg-amber-50 border border-amber-200">
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong>Importante:</strong> a Badge Salva-Vidas não substitui o Bônus de Colaboração. O bônus pertence à missão e beneficia as duas equipes; a badge é uma conquista de comportamento, vale <strong>+15 XP</strong> e é desbloqueada apenas para a equipe que enviou o consultor. Mesmo que ela ajude mais de uma bancada, a badge permanece uma única conquista na vitrine.
                    </p>
                </div>
            </div>
        </div>

        {{-- 7. Mecânicas Anti-Desmotivação --}}
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
                            Terminou a missão cedo? Com autorização do professor, envie um colega para <strong class="text-excel-dark">orientar outra equipe</strong>. Quando a ajuda é validada, cada equipe recebe <strong class="text-excel-dark">+20 pontos</strong> na missão.
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

                <div class="border-t border-[--border-light] pt-5 space-y-4">
                    <div>
                        <h4 class="font-bold text-sm text-[--text-main] flex items-center gap-2"><span>🖐️</span> Regra de ouro: ensinar sem executar pelo colega</h4>
                        <p class="text-xs text-[--text-muted] mt-1.5 leading-relaxed">
                            O consultor pode explicar, indicar uma célula e sugerir um comando, mas <strong class="text-excel-dark">não pode tocar no mouse nem no teclado da equipe ajudada</strong>. Se fizer a tarefa por ela, nenhuma das equipes recebe o Bônus de Colaboração e a equipe que ajudou não desbloqueia a Badge Salva-Vidas. A equipe atendida deve desfazer a ação, quando necessário, e repetir o procedimento com as próprias mãos.
                        </p>
                    </div>

                    <div class="overflow-x-auto rounded-excel border border-[--border-light]">
                        <table class="w-full text-xs">
                            <thead class="bg-[#f8faf8] text-[--text-muted] uppercase tracking-wider">
                                <tr>
                                    <th class="text-left px-4 py-3">Reconhecimento</th>
                                    <th class="text-left px-4 py-3">Quem recebe</th>
                                    <th class="text-left px-4 py-3">Quando aplicar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[--border-light] text-[--text-main]">
                                <tr>
                                    <td class="px-4 py-3 font-semibold">Bônus de Colaboração (+20)</td>
                                    <td class="px-4 py-3">A equipe que ajudou e a equipe ajudada</td>
                                    <td class="px-4 py-3">Em cada auxílio distinto, autorizado e validado na missão</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold">Badge Salva-Vidas (+15 XP)</td>
                                    <td class="px-4 py-3">Somente a equipe que prestou a orientação</td>
                                    <td class="px-4 py-3">Ao ensinar corretamente, sem assumir mouse ou teclado</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 rounded-excel bg-emerald-50 border border-emerald-200">
                        <h4 class="font-bold text-sm text-emerald-800">Exemplo de pontuação</h4>
                        <p class="text-xs text-emerald-700 mt-1.5 leading-relaxed">
                            Em uma missão de 100 pontos, a equipe que termina cedo e orienta corretamente outra bancada pode receber <strong>100 + 20 + 15 = 135 pontos</strong>: missão, colaboração e Salva-Vidas. A equipe ajudada, depois de corrigir e entregar a própria planilha, pode receber <strong>100 + 20 = 120 pontos</strong>. Isso torna pedir ajuda seguro e recompensa quem compartilha conhecimento sem retirar a autonomia dos colegas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 8. Resumo visual --}}
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
