<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Modelo do Certificado</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Visualização do modelo — somente consulta</p>
            </div>
        </div>
    </x-slot>

    <div class="flex justify-center p-6 relative" id="certificate-wrapper">
        {{-- Marca d'água anti-cópia --}}
        <div class="watermark-overlay"></div>

        <div style="width: 1123px; max-width: 100%;" class="relative">
            <div class="certificate-canvas relative">
                {{-- Overlay de proteção visual --}}
                <div class="cert-protection-layer" id="protection-layer"></div>

                <div class="cert-outer-frame"></div>
                <div class="cert-corner cert-corner-tl"></div>
                <div class="cert-corner cert-corner-tr"></div>
                <div class="cert-corner cert-corner-bl"></div>
                <div class="cert-corner cert-corner-br"></div>

                <div class="cert-inner">
                    <div class="cert-header">
                        <div class="cert-logo">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
                            </svg>
                            <span>EXCEL WORKSHOP</span>
                        </div>
                        <div class="cert-logo-right">
                            <img src="{{ asset('images/centec_academy.svg') }}" width="180" height="60" alt="CENTEC">
                        </div>
                    </div>

                    <div class="text-center">
                        <h1 class="cert-title">Certificado de Conquista</h1>
                        <p class="cert-subtitle">Certifica-se que</p>
                    </div>

                    <div class="student-section">
                        <div class="student-name">[NOME DO ALUNO]</div>
                        <div class="student-meta">CPF: [CPF DO ALUNO] &nbsp;&bull;&nbsp; Data de Conclusão: [DATA]</div>
                    </div>

                    <p class="cert-text">
                        integrante da equipe <span class="highlight">[NOME DA EQUIPE]</span>, pelo extraordinário desempenho técnico, raciocínio lógico e
                        trabalho em equipe demonstrados nas Missões Práticas, classificando-se honrosamente na categoria
                        <span class="highlight">[NOME DA CATEGORIA]</span> no torneio prático de gamificação do
                        curso de qualificação profissional em Excel Básico, realizado no período de <span class="highlight">[DATA INÍCIO]</span> a <span class="highlight">[DATA FIM]</span>.
                    </p>

                    <p class="text-center text-sm text-gray-600 -mt-2">
                        Iguatu - CE, <span class="highlight">[DATA DA ÚLTIMA AULA]</span> de 2026.
                        <br><small>Certificado de reconhecimento pedagógico de desempenho em sala de aula.</small>
                    </p>
                    

                    <div class="cert-footer">
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-name">[NOME DO PROFESSOR]</div>
                            <div class="signature-title">Professor Responsável</div>
                            <div class="text-xs text-[--text-muted] mt-0.5">CPF: [CPF DO PROFESSOR]</div>
                        </div>
                    </div>
                </div>

                <img src="https://quickchart.io/qr?text=https://excel-workshop.local/validar/EXEMPLO1234&size=160&margin=2" width="70" height="70" alt="QR Code de validação" class="cert-qrcode">

                {{-- Marcas d'água repetidas sobre o certificado --}}
                <div class="watermark-text watermark-1">CONSULTA · MODELO</div>
                <div class="watermark-text watermark-2">CONSULTA · MODELO</div>
                <div class="watermark-text watermark-3">CONSULTA · MODELO</div>
            </div>

            {{-- Aviso --}}
            <div class="mt-6 p-4 border border-red-300 bg-red-50 rounded-excel text-center">
                <p class="text-red-600 text-sm font-semibold">
                    Esta é apenas uma visualização do modelo. O certificado real será emitido pelo professor após a conclusão do curso.
                </p>
            </div>
        </div>
    </div>

    <style>
        .certificate-canvas {
            width: 100%;
            aspect-ratio: 1123/794;
            background-color: #ffffff;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border-radius: 6px;
            z-index: 1;
        }
        .cert-outer-frame {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 25px solid var(--excel-dark);
            background-image:
                linear-gradient(rgba(27, 138, 74, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(27, 138, 74, 0.12) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
            z-index: 1;
        }
        .cert-corner {
            position: absolute;
            width: 80px; height: 80px;
            border: 4px solid var(--excel-light);
            z-index: 2;
        }
        .cert-corner-tl { top: 33px; left: 33px; border-right: none; border-bottom: none; }
        .cert-corner-tr { top: 33px; right: 33px; border-left: none; border-bottom: none; }
        .cert-corner-bl { bottom: 33px; left: 33px; border-right: none; border-top: none; }
        .cert-corner-br { bottom: 33px; right: 33px; border-left: none; border-top: none; }
        .cert-inner {
            width: 100%; height: 100%;
            border: 2px solid var(--excel-dark);
            background-color: #ffffff;
            background-image:
                linear-gradient(#f3f9f6 1px, transparent 1px),
                linear-gradient(90deg, #f3f9f6 1px, transparent 1px);
            background-size: 45px 25px;
            z-index: 3;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
        }
        .cert-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--excel-dark);
            font-size: 16px;
        }
        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: var(--excel-dark);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            margin-top: 5px;
        }
        .cert-subtitle {
            font-size: 14px;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: -3px;
        }
        .student-section { text-align: center; width: 100%; }
        .student-name {
            font-size: 30px;
            font-weight: 700;
            color: #111111;
            border-bottom: 2px solid var(--excel-light);
            display: inline-block;
            padding: 5px 40px;
            margin: 8px 0;
            font-family: 'Playfair Display', serif;
            letter-spacing: 1px;
        }
        .student-meta { font-size: 13px; color: #666666; font-weight: 600; }
        .cert-text {
            text-align: center;
            font-size: 13px;
            line-height: 1.8;
            color: #222222;
            max-width: 750px;
            margin: 0 auto;
        }
        .highlight { color: var(--excel-dark); font-weight: 600; }
        .cert-badge { margin-bottom: -8px; color: var(--excel-light); }
        .cert-footer {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            width: 100%;
            margin-top: 50px;
        }
        .signature-block { width: 300px; text-align: center; }
        .signature-line { border-top: 1px solid #999999; margin-bottom: 6px; }
        .signature-name { font-size: 13px; font-weight: 700; color: #111111; }
        .signature-title { font-size: 11px; color: #666666; margin-top: 2px; }
        .cert-qrcode {
            position: absolute;
            bottom: 48px;
            right: 48px;
            border-radius: 4px;
            z-index: 4;
        }

        /* ─── PROTEÇÃO CONTRA IMPRESSÃO ─── */
        @media print {
            html, body { display: none !important; }
        }

        /* ─── PROTEÇÃO CONTRA SCREENSHOT ─── */
        .cert-protection-layer {
            position: absolute;
            inset: 0;
            z-index: 10;
            pointer-events: none;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(16, 124, 65, 0.015) 2px,
                rgba(16, 124, 65, 0.015) 4px
            );
        }

        .watermark-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: radial-gradient(
                circle at 30% 40%,
                rgba(16, 124, 65, 0.03) 0%,
                transparent 50%
            );
        }

        .watermark-text {
            position: absolute;
            z-index: 4;
            pointer-events: none;
            font-size: 48px;
            font-weight: 700;
            color: rgba(16, 124, 65, 0.06);
            text-transform: uppercase;
            letter-spacing: 12px;
            white-space: nowrap;
            transform: rotate(-25deg);
            font-family: 'Montserrat', sans-serif;
            user-select: none;
        }
        .watermark-1 { top: 20%; left: 5%; font-size: 52px; }
        .watermark-2 { top: 50%; left: 25%; font-size: 44px; transform: rotate(-18deg); }
        .watermark-3 { top: 70%; left: 8%; font-size: 50px; transform: rotate(-30deg); }

        /* ─── BLOQUEIO DE CONTEXTO / ARRASTO ─── */
        .certificate-canvas, .certificate-canvas * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            user-select: none !important;
        }
        .certificate-canvas {
            -webkit-user-drag: none !important;
            user-drag: none !important;
        }
    </style>

    <script>
        (function() {
            const protectedEl = document.querySelector('.certificate-canvas');

            // Bloqueia Print Screen (Ctrl+P, Ctrl+Shift+P, menu)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    e.stopPropagation();
                    showBlockAlert();
                    return false;
                }
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'P') {
                    e.preventDefault();
                    e.stopPropagation();
                    showBlockAlert();
                    return false;
                }
            });

            // Bloqueia botão direito (context menu → salvar/print)
            if (protectedEl) {
                protectedEl.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    showBlockAlert();
                    return false;
                });
            }

            // Bloqueia arrastar imagens/conteúdo
            document.addEventListener('dragstart', function(e) {
                if (e.target.closest('.certificate-canvas')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Bloqueia PrintScreen via window.print()
            const originalPrint = window.print;
            window.print = function() {
                showBlockAlert();
            };

            // Detecta antes da impressão (alguns navegadores)
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                document.body.style.display = 'none';
                setTimeout(function() {
                    document.body.style.display = '';
                }, 500);
                showBlockAlert();
            });

            // Bloqueia MediaDevices (webcam/gravação indireta)
            if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
                const origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
                navigator.mediaDevices.getDisplayMedia = function() {
                    showBlockAlert();
                    return Promise.reject(new Error('Captura de tela não permitida nesta página.'));
                };
            }

            // Alerta visual de bloqueio
            let alertTimeout = null;
            function showBlockAlert() {
                if (alertTimeout) return;
                const alert = document.createElement('div');
                alert.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999;background:#dc2626;color:#fff;padding:12px 28px;border-radius:6px;font-weight:700;font-size:14px;box-shadow:0 8px 30px rgba(220,38,38,0.4);font-family:Montserrat,sans-serif;transition:opacity 0.3s;';
                alert.textContent = 'IMPRESSÃO E CAPTURA DE TELA BLOQUEADAS';
                document.body.appendChild(alert);

                alertTimeout = setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 300);
                    alertTimeout = null;
                }, 2500);
            }
        })();
    </script>
</x-app-layout>
