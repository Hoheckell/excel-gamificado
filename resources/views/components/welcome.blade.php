<div class="bg-white border-b border-[--border-light]">
    <div class="px-6 py-4 flex items-center gap-4">
        <div class="icon-wrapper w-[48px] h-[48px]">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-xl font-bold text-[--text-main]">Painel Operacional</h1>
            <p class="text-xs uppercase tracking-[1px] text-excel-light">Sistema Pedagógico Gamificado</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 p-8">
    <div class="feature-card" onclick="window.location.href='/alunos'">
        <div class="icon-wrapper">
            <svg fill="currentColor" width="28" height="28" viewBox="0 0 45.532 45.532">
<g>
	<path d="M22.766,0.001C10.194,0.001,0,10.193,0,22.766s10.193,22.765,22.766,22.765c12.574,0,22.766-10.192,22.766-22.765
		S35.34,0.001,22.766,0.001z M22.766,6.808c4.16,0,7.531,3.372,7.531,7.53c0,4.159-3.371,7.53-7.531,7.53
		c-4.158,0-7.529-3.371-7.529-7.53C15.237,10.18,18.608,6.808,22.766,6.808z M22.761,39.579c-4.149,0-7.949-1.511-10.88-4.012
		c-0.714-0.609-1.126-1.502-1.126-2.439c0-4.217,3.413-7.592,7.631-7.592h8.762c4.219,0,7.619,3.375,7.619,7.592
		c0,0.938-0.41,1.829-1.125,2.438C30.712,38.068,26.911,39.579,22.761,39.579z"/>
</g>
</svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Alunos</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Gerencie a lista de participantes, dados de CPF e matrículas.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='/equipes'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.45 20 9.5V4h-5.5zm.33 11.65l-1.41-1.41 3.13-3.13 1.41 1.41-3.13 3.13z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Equipes</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Gerencie as equipes e seus membros.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='{{ route('placar.index') }}'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.992 5.992 0 0 0 11 15.9V19H7v2h10v-2h-4v-3.1c2.45-.39 4.34-2.31 4.76-4.79C20.14 10.74 21 8.97 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.39 5 9.3 5 8zm14 0c0 1.3-.84 2.39-2 2.82V7h2v1z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Placar Geral</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Acompanhe a pontuação ao vivo e o ranking de gamificação.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='{{ route('missoes.index') }}'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6h-5.6z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Missões</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Determine desafios de planilhas e critérios para bônus lúdicos.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='/regras'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Regras</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Configure o regulamento, penalidades e mecânicas da dinâmica.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='/categorias'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Categorias</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Gerencie os níveis de ranking (Bronze, Prata, Ouro) e metas.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='{{ route('certificado.modelo') }}'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Modelo do Certificado</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Customize as artes vetoriais, textos e assinaturas padrão.</p>
    </div>

    <div class="feature-card" onclick="window.location.href='{{ route('certificados.emitir') }}'">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Emitir Certificado</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Gere os arquivos finais individuais ou em lote para a turma.</p>
    </div>
    <div class="feature-card" onclick="window.location.href='/turmas'">
        <div class="icon-wrapper">
            <svg height="28" width="28" viewBox="0 0 512 512" fill="currentColor"> 
<g>
	<path class="st0" d="M505.837,180.418L279.265,76.124c-7.349-3.385-15.177-5.093-23.265-5.093c-8.088,0-15.914,1.708-23.265,5.093
		L6.163,180.418C2.418,182.149,0,185.922,0,190.045s2.418,7.896,6.163,9.627l226.572,104.294c7.349,3.385,15.177,5.101,23.265,5.101
		c8.088,0,15.916-1.716,23.267-5.101l178.812-82.306v82.881c-7.096,0.8-12.63,6.84-12.63,14.138c0,6.359,4.208,11.864,10.206,13.618
		l-12.092,79.791h55.676l-12.09-79.791c5.996-1.754,10.204-7.259,10.204-13.618c0-7.298-5.534-13.338-12.63-14.138v-95.148
		l21.116-9.721c3.744-1.731,6.163-5.504,6.163-9.627S509.582,182.149,505.837,180.418z"/>
	<path class="st0" d="M256,346.831c-11.246,0-22.143-2.391-32.386-7.104L112.793,288.71v101.638
		c0,22.314,67.426,50.621,143.207,50.621c75.782,0,143.209-28.308,143.209-50.621V288.71l-110.827,51.017
		C278.145,344.44,267.25,346.831,256,346.831z"/>
</g>
</svg>
        </div>
        <h2 class="text-base font-bold text-[--text-main] mb-1">Turmas</h2>
        <p class="text-xs text-[--text-muted] leading-relaxed">Gerencie as turmas e seus alunos.</p>
    </div>
</div>

<div class="sheet-tabs-bar">
    <div class="excel-tab-inactive">Início</div>
    <div class="excel-tab-active">Painel</div>
    <div class="excel-tab-inactive border-r-0">Configurações</div>
</div>
