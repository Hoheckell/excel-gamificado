<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Conheça o Sistema — {{ config('app.name', 'Excel Workshop') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        @endif
    </head>
    <body class="font-sans antialiased excel-grid-bg min-h-screen text-[--text-main]">
        <header class="w-full bg-excel-dark text-white">
            <div class="max-w-5xl mx-auto px-5 py-4 flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="font-bold tracking-tight">Curso de Excel</a>

                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="text-sm text-white/80 hover:text-white transition">Início</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-white text-excel-dark rounded-excel text-sm font-semibold hover:bg-green-50 transition">
                                Ir ao painel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-white text-excel-dark rounded-excel text-sm font-semibold hover:bg-green-50 transition">
                                Entrar
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-5 py-10 sm:py-16">
            <section class="portal-container overflow-hidden">
                <div class="excel-ribbon h-3 w-full"></div>

                <div class="px-6 py-10 sm:px-12 sm:py-14">
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-excel-dark">
                        Ambiente pedagógico
                    </span>

                    <h1 class="mt-5 max-w-3xl text-3xl sm:text-4xl font-bold leading-tight text-excel-dark">
                        Aprender Excel de forma prática, colaborativa e motivadora
                    </h1>

                    <p class="mt-5 max-w-3xl text-base sm:text-lg leading-relaxed text-[--text-muted]">
                        Este é um ambiente de apoio ao curso de Excel. Ele organiza as atividades da turma e ajuda alunos e professor a acompanharem a evolução da aprendizagem ao longo das aulas.
                    </p>
                </div>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6" aria-label="Objetivos do sistema">
                <article class="portal-container p-6">
                    <div class="text-3xl" aria-hidden="true">📊</div>
                    <h2 class="mt-4 text-lg font-bold text-excel-dark">Aprender fazendo</h2>
                    <p class="mt-2 text-sm leading-relaxed text-[--text-muted]">
                        As aulas valorizam a prática e a aplicação do Excel em atividades adequadas ao conteúdo do curso.
                    </p>
                </article>

                <article class="portal-container p-6">
                    <div class="text-3xl" aria-hidden="true">🤝</div>
                    <h2 class="mt-4 text-lg font-bold text-excel-dark">Colaborar</h2>
                    <p class="mt-2 text-sm leading-relaxed text-[--text-muted]">
                        A experiência incentiva participação, troca de conhecimentos e trabalho em equipe com acompanhamento do professor.
                    </p>
                </article>

                <article class="portal-container p-6">
                    <div class="text-3xl" aria-hidden="true">🌱</div>
                    <h2 class="mt-4 text-lg font-bold text-excel-dark">Acompanhar a evolução</h2>
                    <p class="mt-2 text-sm leading-relaxed text-[--text-muted]">
                        Cada participante pode perceber seu progresso, receber orientações e reconhecer as competências desenvolvidas.
                    </p>
                </article>
            </section>

            <section class="portal-container mt-6 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <h2 class="text-xl font-bold text-excel-dark">Para alunos e professor</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[--text-muted]">
                        O acesso é reservado às pessoas vinculadas ao curso. Depois de entrar, cada usuário encontra as informações e os recursos correspondentes à sua participação.
                    </p>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-excel shrink-0 text-center inline-block">Ir ao painel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-excel shrink-0 text-center inline-block">Entrar no sistema</a>
                    @endauth
                @endif
            </section>
        </main>

        <footer class="max-w-5xl mx-auto px-5 pb-8 text-center text-xs text-[--text-muted]">
            Sistema pedagógico do Curso de Excel
        </footer>
    </body>
</html>
