<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado - {{ $certificado->nome_aluno }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 0px !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            background-color: #ffffff;
        }

        /* 
           MOLDURA EXTERNA: Ocupa 92% da largura e tem altura fixa de 186mm.
        */
        .outer-frame {
            width: 92%;
            height: 186mm;
            margin: 8mm auto 0 auto;
            border: 3.5mm solid #107c41;
            position: relative;
        }

        /* 
           MOLDURA INTERNA CORRIGIDA:
           Em vez de 100%, travamos ela absolutamente a 2.5mm de cada lado da borda externa.
           É matematicamente impossível ela ficar maior que o outer-frame agora!
        */
        .inner-frame {
            position: absolute;
            top: 2.5mm;
            bottom: 2.5mm;
            left: 2.5mm;
            right: 2.5mm;
            border: 0.8mm solid #1f9a55;
            z-index: 2;
        }

        /* Cantos decorativos presos nos 4 vértices da moldura interna */
        .corner {
            position: absolute; 
            width: 8mm; 
            height: 8mm;
            border: 0.8mm solid #1f9a55; 
            z-index: 3;
        }
        .corner-tl { top: 4mm; left: 4mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 4mm; right: 4mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 8mm; left: 4mm; border-right: none; border-top: none; }
        .corner-br { bottom: 8mm; right: 4mm; border-left: none; border-top: none; }

        /* ÁREA DE CONTEÚDO: Fica dentro das molduras com recuo seguro */
        .content-area {
            position: absolute;
            top: 12mm;
            bottom: 6mm;
            left: 8mm;
            right: 8mm;
            z-index: 4;
        }

        /* TABELA MESTRE */
        .master-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .logo-text { font-weight: 700; color: #107c41; font-size: 11pt; }
        
        .main-title {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            font-size: 20pt; 
            color: #107c41; 
            text-transform: uppercase;
            letter-spacing: 2pt; 
            margin: 0;
        }
        .subtitle {
            font-size: 8.5pt; color: #666; text-transform: uppercase;
            letter-spacing: 3pt; margin: 1mm 0 2mm 0;
        }

        .student-name {
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
            font-size: 17pt; font-weight: 700; color: #111;
            border-bottom: 0.4mm solid #1f9a55;
            display: inline-block; padding: 1mm 8mm; margin-bottom: 2mm;
        }
        .student-meta { font-size: 8.5pt; color: #666; margin-bottom: 2mm; }

        .cert-text {
            font-size: 8.5pt; line-height: 1.5; color: #222;
            padding: 2mm; margin-bottom: 2mm;
            word-wrap: break-word;
        }
        .hl { color: #107c41; font-weight: 700; }

        .iguatu { font-size: 8pt; color: #666; margin-bottom: 0.5mm; }
        .small-note { font-size: 7pt; color: #999; margin-bottom: 1mm; }

        .sig-line { border-top: 0.3mm solid #888; width: 55mm; margin: 0 auto 1mm auto; }
        .sig-name { font-size: 9.5pt; font-weight: 700; color: #111; }
        .sig-role { font-size: 8pt; color: #666; }
        .sig-cpf  { font-size: 7pt; color: #999; margin-top: 0.5mm; }

        .qrcode-img { width: 20mm; height: 20mm; }
        
        .qrcode-label { 
            font-size: 6.5pt; 
            color: #666; 
            margin-top: 0.5mm; 
            display: inline-block; 
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div class="outer-frame">
        <!-- Moldura Interna agora ancorada por coordenadas, sem chance de vazar -->
        <div class="inner-frame">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
        </div>

        <!-- O Conteúdo agora fica em uma camada separada, bem protegida dentro dos quadros -->
        <div class="content-area">
            <table class="master-table">
                <!-- LINHA 1: CABEÇALHO -->
                <tr>
                    <td style="text-align: left; vertical-align: top; width: 30%;">
                        <span style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" style="fill:#107c41;">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
                            </svg>
                        </span>
                        <span class="logo-text">EXCEL WORKSHOP</span>
                    </td>
                    <td style="width: 40%;"></td>
                    <td style="text-align: right; vertical-align: top; width: 30%; padding-right: 2mm;">
                        <img src="{{ public_path('images/centec_academy.svg') }}" style="max-width: 32mm; height: auto;" alt="CENTEC">
                    </td>
                </tr>

                <!-- LINHA 2: CONTEÚDO PRINCIPAL -->
                <tr>
                    <td colspan="3" style="text-align: center; vertical-align: middle;">
                        <h1 class="main-title">Certificado de Conquista</h1>
                        <p class="subtitle">Certifica-se que</p>

                        <div class="student-name">{{ $certificado->nome_aluno }}</div>
                        <p class="student-meta">CPF: {{ $certificado->cpf_aluno }} &nbsp;&bull;&nbsp; Data de Conclusão: {{ $certificado->dt_ultima_aula->format('d/m/Y') }}</p>

                        <p class="cert-text">
                            Integrante da equipe <span class="hl">{{ $certificado->nome_equipe ?? '—' }}</span>, pelo extraordinário desempenho técnico, raciocínio lógico e trabalho em equipe demonstrados nas Missões Práticas, classificando-se honrosamente na categoria <span class="hl">{{ $certificado->nome_categoria ?? '—' }}</span> no torneio prático de gamificação do curso de qualificação profissional em Excel Básico, realizado no período de <span class="hl">{{ $certificado->dt_inicio->format('d/m/Y') }}</span> a <span class="hl">{{ $certificado->dt_fim->format('d/m/Y') }}</span>.
                        </p>

                        <p class="iguatu">Iguatu - CE, <span class="hl">{{ $certificado->dt_ultima_aula->format('d/m/Y') }}</span> de 2026.</p>
                        <p class="small-note">Certificado de reconhecimento pedagógico de desempenho em sala de aula.</p>

                        <div style="margin: 0.5mm 15mm;">
                            <svg width="15" height="15" viewBox="0 0 24 24" style="fill:#1f9a55;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>
                    </td>
                </tr>

                <!-- LINHA 3: RODAPÉ -->
                <tr>
                    <td style="height: 15mm;width: 25%; vertical-align: bottom;"></td>

                    <td style="height: 40mm;width: 50%; text-align: center; vertical-align: bottom;">
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $certificado->nome_professor ?? '—' }}</div>
                        <div class="sig-role">Professor Responsável</div>
                        <div class="sig-cpf">CPF: {{ $certificado->cpf_professor ?? '—' }}</div>
                    </td>

                    <td style="width: 25%; vertical-align: bottom;">
                        <div style="text-align: right; padding-right: 4mm;">
                            <img src="https://quickchart.io/qr?text=http://127.0.0.1:8001/certificados/validar/{{ $certificado->codigo_validacao }}" class="qrcode-img" alt="">
                            
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
                   